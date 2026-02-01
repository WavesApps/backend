<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'kelvinandersonpaulo@gmail.com'],
            [
                'name' => 'Kelvin Anderson Paulo',
                'username' => 'kelvinanderson',
                'email' => 'kelvinandersonpaulo@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
            ]
        );

        User::firstOrCreate(
            ['email' => 'nkindwarashid@gmail.com'],
            [
                'name' => 'Admin User',
                'username' => 'nkindwarashid',
                'email' => 'nkindwarashid@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
            ]
        );
    }
}

