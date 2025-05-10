<?php

namespace App\Http\Controllers;
use App\Models\Article;
use GuzzleHttp\Client;
use Illuminate\Http\Request;


class FetchingController extends Controller
{
    public function fetchDataAndInsert()
    {
        // Create a Guzzle client
        $client = new Client();

        // Send GET request to the external API
        $response = $client->get('https://newsapi.org/v2/everything?q=gaza&apiKey=7b80534f05764a779377d5ed88b85a99'); // Replace with the actual API URL

        // Check if the response is successful
        if ($response->getStatusCode() == 200) {
            // Get the JSON data
            $data = json_decode($response->getBody()->getContents(), true);

            // Insert the data into the database
            foreach ($data as $item) {
                Article::create([
                    'title' => $item['title'], // Replace with the appropriate keys
                    'author' => $item['author'], // Replace with the appropriate keys
                    'description' => $item['description'], // Replace with the appropriate keys
                    'url' => $item['url'], // Replace with the appropriate keys
                    'urlToImage' => $item['urlToImage'], // Replace with the appropriate keys
                    'source' => $item['source'], // Replace with the appropriate keys
                    'content' => $item['content'], // Replace with the appropriate keys
                    'publishedAt' => $item['publishedAt'], // Replace with the appropriate keys
                    // Add more columns as necessary
                ]);
            }

            return response()->json(['message' => 'Data inserted successfully']);
        }

        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
    
}
