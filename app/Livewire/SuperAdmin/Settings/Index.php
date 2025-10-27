<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;

class Index extends Component
{
    public $activeTab = 'business-config';
    
    public $tabs = [
        ['id' => 'business-config', 'name' => 'Business Configuration'],
        ['id' => 'currency-localization', 'name' => 'Currency & Localization'],
        ['id' => 'branch-management', 'name' => 'Branch Management'],
        ['id' => 'inventory-management', 'name' => 'Inventory Management'],
        ['id' => 'employee-management', 'name' => 'Employee Management'],
        ['id' => 'pos-configuration', 'name' => 'POS Configuration'],
        ['id' => 'accounting-cash', 'name' => 'Accounting & Cash'],
        ['id' => 'customer-supplier', 'name' => 'Customer & Supplier Management'],
        ['id' => 'reports-analytics', 'name' => 'Reports & Analytics'],
        ['id' => 'security-access', 'name' => 'Security & Access'],
        ['id' => 'notifications-alerts', 'name' => 'Notifications & Alerts'],
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.super-admin.settings.index');
    }
}
