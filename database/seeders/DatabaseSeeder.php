<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('123456'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'User Test',
            'email'    => 'user@test.com',
            'password' => Hash::make('123456'),
            'role'     => 'user',
        ]);
    }
}