<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;

class ClientSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users to associate with clients
        User::factory()->count(5)->create();

        Client::factory()->count(10)->withUser()->create();
    }
}
