<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Superstar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TanzanianSuperstarSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create the users
        $tanzanianUsers = [
            [
                'name' => 'Diamond Platnumz',
                'username' => 'diamondplatnumz',
                'email' => 'diamond@platnumz.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian Bongo Flava artist and music producer',
                
            ],
            [
                'name' => 'Alikiba',
                'username' => 'alikiba',
                'email' => 'alikiba@music.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian singer and songwriter',
                
            ],
            [
                'name' => 'Nandy',
                'username' => 'nandy',
                'email' => 'nandy@music.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian singer known for Bongo Flava music',
            
            ],
            [
                'name' => 'Ray C',
                'username' => 'rayc',
                'email' => 'rayc@music.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian singer and performer',
                
            ],
            [
                'name' => 'Juma Jux',
                'username' => 'jumajux',
                'email' => 'jux@music.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian Bongo Flava artist',
                
            ],
            [
                'name' => 'Harmonize',
                'username' => 'harmonize',
                'email' => 'harmonize@music.co.tz',
                'password' => bcrypt('password'),
                'role' => 'superstar',
                'is_verified' => true,
                'is_blocked' => false,
                'profile_image' => null,
                'bio' => 'Tanzanian Bongo Flava music group',
                
            ],
        ];

        foreach ($tanzanianUsers as $user) {
            $createdUser = User::firstOrCreate(
                ['email' => $user['email']],
                Arr::except($user, ['bio'])
            );

            // Then create the superstar profile (keep bio on the superstar)
            Superstar::firstOrCreate(
                ['user_id' => $createdUser->id],
                [
                    'display_name' => $user['name'],
                    'bio' => $user['bio'],
                    'is_available' => true,
                    'rating' => 4.5,
                    'total_followers' => rand(10000, 500000),
                    'status' => 'active',
                ]
            );
        }
    }
}
