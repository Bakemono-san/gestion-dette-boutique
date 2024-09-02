<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'prenom' => $this->faker->firstName,
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'login' => $this->faker->userName,
            'role_id' => Role::factory(), // Create a new Role
            'password' => $this->faker->password, // Make sure to use a hashed password if necessary
            'etat' => $this->faker->randomElement(['true', 'false']),
        ];
    }

    /**
     * Define a method to create and associate a Client with the User.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withClient()
    {
        return $this->afterCreating(function (User $user) {
            Client::factory()->create(['user_id' => $user->id]); // Create a Client associated with this User
        });
    }
}
