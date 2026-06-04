<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@hesmb.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'francis',
            'last_name' => 'bamugileki',
            'email' => 'francis@hesmb.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);
    }
}
