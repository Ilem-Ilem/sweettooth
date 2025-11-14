<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Main Branch',
            'code' => 'MB001',
            'location' => '123 Sweet Street, Downtown',
            'phone' => '+1234567890',
            'email' => 'main@sweettooth.local',
            'description' => 'Main branch of Sweet Tooth',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'postal_code' => '10001',
            'timezone' => 'UTC',
            'is_active' => true,
        ]);

        Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Downtown Branch',
            'code' => 'DB002',
            'location' => '456 Dessert Avenue, Downtown',
            'phone' => '+1234567891',
            'email' => 'downtown@sweettooth.local',
            'description' => 'Downtown location',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'Downtown',
            'postal_code' => '10002',
            'timezone' => 'UTC',
            'is_active' => true,
        ]);

        Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Airport Branch',
            'code' => 'AB003',
            'location' => '789 Terminal Road, Airport',
            'phone' => '+1234567892',
            'email' => 'airport@sweettooth.local',
            'description' => 'Airport location',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'Airport Area',
            'postal_code' => '10003',
            'timezone' => 'UTC',
            'is_active' => true,
        ]);
    }
}
