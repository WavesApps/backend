<?php

namespace Database\Seeders;

use App\Models\UserGoogle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TanzanianUserGoogleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tanzanianUsers = [
            [
                'name' => 'John Mwanga',
                'email' => 'john.mwanga@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'Grace Karambwa',
                'email' => 'grace.karambwa@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'Michael Kimeta',
                'email' => 'michael.kimeta@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'Sarah Mgoya',
                'email' => 'sarah.mgoya@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'David Mwamunyi',
                'email' => 'david.mwamunyi@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
            [
                'name' => 'Aisha Mwangi',
                'email' => 'aisha.mwangi@gmail.com',
                'profile_image' => null,
                'is_verified' => true,
            ],
        ];

        foreach ($tanzanianUsers as $user) {
            UserGoogle::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
