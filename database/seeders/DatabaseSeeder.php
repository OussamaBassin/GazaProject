<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\FavoriteSeeder as SeedersFavoriteSeeder;
use Database\Seeders\FavoriteSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

<<<<<<< HEAD
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
=======
        $this->call([
            UserSeeder::class,
            ArticleSeeder::class,
            CommentSeeder::class,
            FavoriteSeeder::class,
>>>>>>> a411296 (nearly there)
        ]);
    }
}
