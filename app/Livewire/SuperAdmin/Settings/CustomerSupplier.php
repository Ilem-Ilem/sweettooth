<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalCustomerSupplierManagement;

class CustomerSupplier extends Component
{
    public $customersAdd = true;
    public $customersEdit = true;
    public $customersGroups = true;
    public $suppliersAdd = true;
    public $suppliersEdit = true;
    public $partyImport = true;

    public function mount()
    {
        $settings = GlobalCustomerSupplierManagement::first();
        
        if ($settings) {
            $customers = $settings->customers ?? [];
            $this->customersAdd = in_array('add', $customers);
            $this->customersEdit = in_array('edit', $customers);
            $this->customersGroups = in_array('groups', $customers);
            
            $suppliers = $settings->suppliers ?? [];
            $this->suppliersAdd = in_array('add', $suppliers);
            $this->suppliersEdit = in_array('edit', $suppliers);
            
            $this->partyImport = $settings->party_import === 'enabled';
        }
    }

    public function save()
    {
        $customers = [];
        if ($this->customersAdd) $customers[] = 'add';
        if ($this->customersEdit) $customers[] = 'edit';
        if ($this->customersGroups) $customers[] = 'groups';
        
        $suppliers = [];
        if ($this->suppliersAdd) $suppliers[] = 'add';
        if ($this->suppliersEdit) $suppliers[] = 'edit';

        GlobalCustomerSupplierManagement::updateOrCreate(
            ['id' => 1],
            [
                'customers' => $customers,
                'suppliers' => $suppliers,
                'party_import' => $this->partyImport ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'Customer & Supplier Management settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.customer-supplier');
    }
}
