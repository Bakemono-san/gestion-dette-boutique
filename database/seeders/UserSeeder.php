<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roles = Role::all(); // Retrieve all roles

        User::factory()->count(10)->create([
            'role_id' => $roles->random()->id, // Assign a random role to each user
        ]);
    }
}
