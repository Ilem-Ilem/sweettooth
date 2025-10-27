<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalInventoryManagement;

class InventoryManagement extends Component
{
    public $categoriesAdd = true;
    public $categoriesEdit = true;
    public $categoriesDelete = true;
    public $brandsAdd = true;
    public $brandsEdit = true;
    public $brandsDelete = true;
    public $productsAdd = true;
    public $productsEdit = true;
    public $skuAuto = true;
    public $multiVariant = true;
    public $stockAdjustment = true;
    public $purchaseReturns = true;
    public $supplierAdd = true;
    public $supplierEdit = true;
    public $supplierLink = true;
    public $lowStockAlert = true;
    public $lowStockThreshold = 10;
    public $expiryTracking = true;
    public $importCsv = true;

    public function mount()
    {
        $settings = GlobalInventoryManagement::first();
        
        if ($settings) {
            $categories = $settings->categories ?? [];
            $this->categoriesAdd = in_array('add', $categories);
            $this->categoriesEdit = in_array('edit', $categories);
            $this->categoriesDelete = in_array('delete', $categories);
            
            $brands = $settings->brands ?? [];
            $this->brandsAdd = in_array('add', $brands);
            $this->brandsEdit = in_array('edit', $brands);
            $this->brandsDelete = in_array('delete', $brands);
            
            $products = $settings->products ?? [];
            $this->productsAdd = in_array('add', $products);
            $this->productsEdit = in_array('edit', $products);
            $this->skuAuto = in_array('sku_auto', $products);
            
            $this->multiVariant = $settings->multi_variant === 'enabled';
            $this->stockAdjustment = $settings->stock_adjustment === 'enabled';
            $this->purchaseReturns = $settings->purchase_returns === 'enabled';
            
            $supplier = $settings->supplier_management ?? [];
            $this->supplierAdd = in_array('add', $supplier);
            $this->supplierEdit = in_array('edit', $supplier);
            $this->supplierLink = in_array('link', $supplier);
            
            $this->expiryTracking = $settings->expiry_tracking === 'enabled';
            $this->importCsv = $settings->import_csv === 'enabled';
        }
    }

    public function save()
    {
        $categories = [];
        if ($this->categoriesAdd) $categories[] = 'add';
        if ($this->categoriesEdit) $categories[] = 'edit';
        if ($this->categoriesDelete) $categories[] = 'delete';
        
        $brands = [];
        if ($this->brandsAdd) $brands[] = 'add';
        if ($this->brandsEdit) $brands[] = 'edit';
        if ($this->brandsDelete) $brands[] = 'delete';
        
        $products = [];
        if ($this->productsAdd) $products[] = 'add';
        if ($this->productsEdit) $products[] = 'edit';
        if ($this->skuAuto) $products[] = 'sku_auto';
        
        $supplier = [];
        if ($this->supplierAdd) $supplier[] = 'add';
        if ($this->supplierEdit) $supplier[] = 'edit';
        if ($this->supplierLink) $supplier[] = 'link';

        GlobalInventoryManagement::updateOrCreate(
            ['id' => 1],
            [
                'categories' => $categories,
                'brands' => $brands,
                'products' => $products,
                'multi_variant' => $this->multiVariant ? 'enabled' : 'disabled',
                'stock_adjustment' => $this->stockAdjustment ? 'enabled' : 'disabled',
                'purchase_returns' => $this->purchaseReturns ? 'enabled' : 'disabled',
                'supplier_management' => $supplier,
                'low_stock_alert' => "threshold:{$this->lowStockThreshold}%",
                'expiry_tracking' => $this->expiryTracking ? 'enabled' : 'disabled',
                'import_csv' => $this->importCsv ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'Inventory Management settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.inventory-management');
    }
}
