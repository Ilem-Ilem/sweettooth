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
        GlobalBusinessConfiguration::create([
            'business_name' => 'Sweet Tooth Inc.',
            'business_description' => 'Premium gelato and pastry shop',
            'business_registration_number' => 'ST-REG-001',
            'financial_year_start_month' => 1,
            'financial_year_end_month' => 12,
            'default_currency' => 'USD',
            'tax_percentage' => 10.00,
            'service_charge_percentage' => 5.00,
            'discount_percentage' => 2.00,
            'business_logo_url' => null,
            'website' => 'www.sweettooth.local',
            'support_email' => 'support@sweettooth.local',
            'support_phone' => '+1234567890',
            'default_invoice_prefix' => 'INV',
            'default_purchase_order_prefix' => 'PO',
            'default_receipt_prefix' => 'REC',
            'enable_loyalty_program' => true,
            'loyalty_points_per_purchase' => 1,
            'loyalty_points_per_dollar' => 10,
            'enable_table_management' => true,
            'enable_delivery' => true,
            'delivery_charge' => 2.50,
            'minimum_order_for_delivery' => 15.00,
        ]);

        GlobalBusinessConfiguration::create([
            'business_name' => 'Sweet Tooth Downtown',
            'business_description' => 'Downtown location of Sweet Tooth',
            'business_registration_number' => 'ST-REG-002',
            'financial_year_start_month' => 1,
            'financial_year_end_month' => 12,
            'default_currency' => 'USD',
            'tax_percentage' => 10.00,
            'service_charge_percentage' => 5.00,
            'discount_percentage' => 2.00,
            'business_logo_url' => null,
            'website' => 'www.sweettooth-dt.local',
            'support_email' => 'downtown@sweettooth.local',
            'support_phone' => '+1234567891',
            'default_invoice_prefix' => 'INV-DT',
            'default_purchase_order_prefix' => 'PO-DT',
            'default_receipt_prefix' => 'REC-DT',
            'enable_loyalty_program' => true,
            'loyalty_points_per_purchase' => 1,
            'loyalty_points_per_dollar' => 10,
            'enable_table_management' => true,
            'enable_delivery' => true,
            'delivery_charge' => 3.00,
            'minimum_order_for_delivery' => 20.00,
        ]);

        GlobalBusinessConfiguration::create([
            'business_name' => 'Sweet Tooth Airport',
            'business_description' => 'Airport location of Sweet Tooth',
            'business_registration_number' => 'ST-REG-003',
            'financial_year_start_month' => 1,
            'financial_year_end_month' => 12,
            'default_currency' => 'USD',
            'tax_percentage' => 10.00,
            'service_charge_percentage' => 5.00,
            'discount_percentage' => 1.50,
            'business_logo_url' => null,
            'website' => 'www.sweettooth-airport.local',
            'support_email' => 'airport@sweettooth.local',
            'support_phone' => '+1234567892',
            'default_invoice_prefix' => 'INV-AP',
            'default_purchase_order_prefix' => 'PO-AP',
            'default_receipt_prefix' => 'REC-AP',
            'enable_loyalty_program' => false,
            'loyalty_points_per_purchase' => 0,
            'loyalty_points_per_dollar' => 0,
            'enable_table_management' => false,
            'enable_delivery' => false,
            'delivery_charge' => 0,
            'minimum_order_for_delivery' => 0,
        ]);
    }
}
