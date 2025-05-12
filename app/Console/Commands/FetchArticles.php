<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles {--force : Force fetch even if rate limit would be exceeded}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest articles about Gaza from News API';

    private const RATE_LIMIT_KEY = 'news_api_last_fetch';
    private const RATE_LIMIT_MINUTES = 60; // Adjust based on your API plan

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check rate limiting unless --force is used
            if (!$this->option('force') && !$this->canMakeRequest()) {
                $this->error('Rate limit would be exceeded. Wait or use --force to override.');
                return 1;
            }

            // Get API configuration from environment
            $apiKey = config('services.news_api.key') ?? env('NEWS_API_KEY');
            $apiUrl = config('services.news_api.url') ?? env('NEWS_API_URL', 'https://newsapi.org/v2/everything');

            if (!$apiKey) {
                throw new \Exception('News API key not configured. Please set NEWS_API_KEY in .env');
            }

            $client = new \GuzzleHttp\Client([
                'timeout' => 30, // Set reasonable timeout
                'connect_timeout' => 10
            ]);

            $response = $client->get($apiUrl, [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'GazaNewsAggregator/1.0', // Identify your application
                ],
                'query' => [
                    'q' => 'gaza',
                    'apiKey' => $apiKey,
                    'language' => 'en', // Filter for English articles
                    'sortBy' => 'publishedAt', // Get latest articles first
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['articles']) && is_array($data['articles'])) {
                    $this->info('Found ' . count($data['articles']) . ' articles');
                    $newArticles = 0;
                    $duplicates = 0;
                    
                    foreach ($data['articles'] as $item) {
                        try {
                            // Check for duplicates using URL as unique identifier
                            if (!empty($item['url']) && Article::where('url', $item['url'])->exists()) {
                                $duplicates++;
                                continue;
                            }

                            // Validate required fields
                            if (empty($item['title'])) {
                                Log::warning('Skipping article with empty title', ['article' => $item]);
                                continue;
                            }

                            Article::create([
                                'title' => $item['title'] ?? null,
                                'author' => $item['author'] ?? 'Unknown',
                                'description' => $item['description'] ?? null,
                                'url' => $item['url'] ?? null,
                                'urlToImage' => $item['urlToImage'] ?? null,
                                'source' => isset($item['source']['name']) ? $item['source']['name'] : 'Unknown',
                                'content' => $item['content'] ?? null,
                                'publishedAt' => $this->parseDate($item['publishedAt'] ?? null),
                                'likes' => 0,
                                'dislikes' => 0
                            ]);
                            
                            $newArticles++;
                        } catch (\Exception $e) {
                            Log::error("Error saving article", [
                                'error' => $e->getMessage(),
                                'article' => $item
                            ]);
                        }
                    }

                    // Update rate limit timestamp
                    Cache::put(self::RATE_LIMIT_KEY, now(), Carbon::now()->addMinutes(self::RATE_LIMIT_MINUTES));
                    
                    $this->info("Successfully saved {$newArticles} new articles. Skipped {$duplicates} duplicates.");
                } else {
                    $this->warn('No articles found in the API response');
                    Log::warning('No articles found in API response', ['data' => $data]);
                }
            }
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        } catch (\Exception $e) {
            $this->error('Error fetching articles: ' . $e->getMessage());
            Log::error('Error fetching articles', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    private function canMakeRequest(): bool
    {
        $lastFetch = Cache::get(self::RATE_LIMIT_KEY);
        return !$lastFetch || now()->diffInMinutes($lastFetch) >= self::RATE_LIMIT_MINUTES;
    }

    private function handleRequestException(RequestException $e): void
    {
        $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'Unknown';
        $message = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

        $errorMessage = "API request failed (Status {$statusCode}): {$message}";
        $this->error($errorMessage);
        
        Log::error('News API request failed', [
            'status' => $statusCode,
            'message' => $message,
            'trace' => $e->getTraceAsString()
        ]);
    }

    private function parseDate(?string $date): string
    {
        try {
            return $date ? Carbon::parse($date)->toDateTimeString() : now()->toDateTimeString();
        } catch (\Exception $e) {
            return now()->toDateTimeString();
        }
    }
}
