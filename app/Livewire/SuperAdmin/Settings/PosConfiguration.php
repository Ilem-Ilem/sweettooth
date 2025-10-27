<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalPosConfiguration;

class PosConfiguration extends Component
{
    public $posInterface = true;
    public $paymentModesAdd = true;
    public $paymentModesEdit = true;
    public $receiptTemplate = 'custom';
    public $salesReturns = true;
    public $offlineMode = true;
    public $onlineShopSync = true;

    public function mount()
    {
        $settings = GlobalPosConfiguration::first();
        
        if ($settings) {
            $this->posInterface = $settings->pos_interface === 'enabled';
            
            $paymentModes = $settings->payment_modes ?? [];
            $this->paymentModesAdd = in_array('add', $paymentModes);
            $this->paymentModesEdit = in_array('edit', $paymentModes);
            
            $this->receiptTemplate = $settings->receipt_template ?? 'custom';
            $this->salesReturns = $settings->sales_returns === 'enabled';
            $this->offlineMode = $settings->offline_mode === 'enabled';
            $this->onlineShopSync = $settings->online_shop_sync === 'enabled';
        }
    }

    public function save()
    {
        $paymentModes = [];
        if ($this->paymentModesAdd) $paymentModes[] = 'add';
        if ($this->paymentModesEdit) $paymentModes[] = 'edit';

        GlobalPosConfiguration::updateOrCreate(
            ['id' => 1],
            [
                'pos_interface' => $this->posInterface ? 'enabled' : 'disabled',
                'payment_modes' => $paymentModes,
                'receipt_template' => $this->receiptTemplate,
                'sales_returns' => $this->salesReturns ? 'enabled' : 'disabled',
                'offline_mode' => $this->offlineMode ? 'enabled' : 'disabled',
                'online_shop_sync' => $this->onlineShopSync ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'POS Configuration settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.pos-configuration');
    }
}
