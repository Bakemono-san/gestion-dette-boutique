<?php

namespace Database\Factories;

use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetteFactory extends Factory
{
    public function definition()
    {
        return [
            'client_id' => Client::factory(), // Create a new Client
            'montantTotal' => $this->faker->randomFloat(2, 100, 1000),
            'montantPayee' => $this->faker->randomFloat(2, 0, 500),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Define a method to attach articles to the Dette instance.
     *
     * @param int $count
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withArticles($count = 3)
    {
        return $this->afterCreating(function (Dette $dette) use ($count) {
            $articles = Article::factory()->count($count)->create(); // Create articles
            $dette->articles()->attach($articles->pluck('id')->toArray(), [
                'quantite' => $this->faker->numberBetween(1, 10),
                'prix_unitaire' => $this->faker->randomFloat(2, 5, 50),
            ]);
        });
    }
}
