<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Super Admin',
            'email' => 'admin@sweettooth.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Branch Manager',
            'email' => 'manager@sweettooth.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Department Head',
            'email' => 'depthead@sweettooth.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Employee',
            'email' => 'employee@sweettooth.local',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'remember_token' => Str::random(10),
        ]);
    }
}
