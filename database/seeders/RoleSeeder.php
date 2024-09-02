<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles using the RoleFactory
        Role::factory()->count(3)->create();
    }
}
