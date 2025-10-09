<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'SweetTooth Calabar',
                'code' => 'CAL-001',
                'location' => '12 Marian Road, Calabar Municipal',
                'phone' => '+234-809-012-3456',
                'email' => 'calabar@sweettooth.com',
                'description' => 'Calabar flagship store with full production and sales',
                'country' => 'Nigeria',
                'state' => 'Cross River',
                'city' => 'Calabar',
                'postal_code' => '540001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ],
            [
                'name' => 'SweetTooth Port Harcourt',
                'code' => 'PHC-002',
                'location' => '78 Trans Amadi Industrial Layout',
                'phone' => '+234-803-456-7890',
                'email' => 'portharcourt@sweettooth.com',
                'description' => 'Port Harcourt main branch',
                'country' => 'Nigeria',
                'state' => 'Rivers',
                'city' => 'Port Harcourt',
                'postal_code' => '500001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ],
            [
                'name' => 'SweetTooth Lagos',
                'code' => 'LAG-003',
                'location' => '45 Admiralty Way, Lekki Phase 1',
                'phone' => '+234-801-234-5678',
                'email' => 'lagos@sweettooth.com',
                'description' => 'Lagos head office and production center',
                'country' => 'Nigeria',
                'state' => 'Lagos',
                'city' => 'Lagos',
                'postal_code' => '101001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ],
            [
                'name' => 'SweetTooth Abuja',
                'code' => 'ABJ-004',
                'location' => '23 Gimbiya Street, Area 11, Garki',
                'phone' => '+234-802-345-6789',
                'email' => 'abuja@sweettooth.com',
                'description' => 'Abuja branch with gelato specialty',
                'country' => 'Nigeria',
                'state' => 'FCT',
                'city' => 'Abuja',
                'postal_code' => '900001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ],
            [
                'name' => 'SweetTooth Enugu',
                'code' => 'ENU-005',
                'location' => '34 Ogui Road, New Haven',
                'phone' => '+234-806-789-0123',
                'email' => 'enugu@sweettooth.com',
                'description' => 'Enugu branch serving South-East region',
                'country' => 'Nigeria',
                'state' => 'Enugu',
                'city' => 'Enugu',
                'postal_code' => '400001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        $this->command->info('✅ 5 SweetTooth branches created successfully.');
    }
}
