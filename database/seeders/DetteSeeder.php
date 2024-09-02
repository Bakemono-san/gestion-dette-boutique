<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;

class DetteSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are clients and articles to associate with debts
        Client::factory()->count(5)->create();
        Article::factory()->count(5)->create();

        Dette::factory()->count(10)
            ->withArticles(3) // Attach 3 articles to each debt
            ->create();
    }
}
