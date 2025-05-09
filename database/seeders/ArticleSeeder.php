<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can use the Article factory to create multiple articles
        \App\Models\Article::factory(10)->create(); // Creates 10 articles
        // You can also create specific articles with specific attributes
    }
}
