<?php

namespace Database\Seeders;

use App\Models\GlobalBusinessConfiguration;
use Illuminate\Database\Seeder;

class GlobalBusinessConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if a global business configuration already exists
        if (!GlobalBusinessConfiguration::exists()) {
            GlobalBusinessConfiguration::create([
                'company_name' => 'SweetTooth',
                'contact_details' => [
                    'phone' => '',
                    'email' => '',
                    'vat_number' => '',
                ],
                'auto_backup' => false,
                'backup_interval' => '2',
                'backup_period' => 'months',
            ]);
            
            $this->command->info('Global Business Configuration created successfully.');
        } else {
            $this->command->info('Global Business Configuration already exists. Skipping...');
        }
    }
}