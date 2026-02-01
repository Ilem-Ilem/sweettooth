<?php

namespace Database\Seeders;

use App\Models\GlobalCurrencyLocalization;
use Illuminate\Database\Seeder;

class CurrencyLocalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if a record already exists
        $existing = GlobalCurrencyLocalization::first();
        
        if (!$existing) {
            // Create the default currency localization settings
            GlobalCurrencyLocalization::create([
                'multi_currency' => 'enabled',
                'primary_currency' => 'NGN', // Nigerian Naira
                'primary_currency_exchange_rate' => null,
                'currency_list' => ['USD', 'EUR', 'GBP', 'INR', 'NGN'],
                'multi_tax' => 'enabled',
                'default_language' => 'en',
                'language_options' => ['en', 'es', 'fr', 'ar'],
                'date_format' => 'MM/DD/YYYY',
                'units_of_measure' => ['piece', 'kg', 'liter'],
            ]);
            
            $this->command->info('Global Currency Localization created with NGN as primary currency.');
        } else {
            // Update existing record to ensure primary currency is NGN
            $existing->update([
                'primary_currency' => 'NGN',
                'currency_list' => ['USD', 'EUR', 'GBP', 'INR', 'NGN'],
            ]);
            
            $this->command->info('Global Currency Localization updated to use NGN as primary currency.');
        }
    }
}