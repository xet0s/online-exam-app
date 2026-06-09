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
            'name' => 'Admin',
            'email' => 'admin@exam.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'approved',
            'department_id' => null,
        ]);
    }
}
