<?php

namespace App\Providers;

use App\Models\Article;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class ApiBootFetcherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            try {
                $client = new Client();

                // Send GET request to the external API
                $response = $client->get('https://newsapi.org/v2/everything?q=gaza&apiKey=7b80534f05764a779377d5ed88b85a99');
        
                // Check if the response is successful
                if ($response->getStatusCode() == 200) {
                    // Decode the JSON data
                    $data = json_decode($response->getBody()->getContents(), true);

                    // Check if 'articles' key exists in the response
                    if (isset($data['articles']) && is_array($data['articles'])) {
                        foreach ($data['articles'] as $item) {
                            Article::create([
                                'title' => $item['title'] ?? null,
                                'author' => $item['author'] ?? null,
                                'description' => $item['description'] ?? null,
                                'url' => $item['url'] ?? null,
                                'urlToImage' => $item['urlToImage'] ?? null,
                                'source' => $item['source'] ?? null, // Assuming 'source' is an array with a 'name' key
                                'content' => $item['content'] ?? null,
                                'publishedAt' => $item['publishedAt'] ?? null,
                            ]);
                        }
                    }
                }
            } catch (Exception $e) {
                // Log the error for debugging
                Log::error('Failed to fetch data from API: ' . $e->getMessage());
            }
        }
    }
}
