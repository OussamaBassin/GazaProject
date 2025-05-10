<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use the FavoriteFactory to create fake data for the favorites table
        \App\Models\Favorite::factory(0)// Adjust the number of records you want to create
            ->create();
        
        // Alternatively, you can manually create records if needed
        // \App\Models\Favorite::create([
        //     'user_id' => 1,
        //     'article_id' => 1,
        // ]);
        
        // Add more records as needed
    }
}
