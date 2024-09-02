<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'libelle' => $this->faker->word,
            'prix' => $this->faker->randomFloat(2, 1, 1000),
            'quantite' => $this->faker->numberBetween(1, 100),
            'user_id' => User::factory(), // Create a new User for each Article
        ];
    }
}
