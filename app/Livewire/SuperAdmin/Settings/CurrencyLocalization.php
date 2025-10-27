<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalCurrencyLocalization;

class CurrencyLocalization extends Component
{
    public $multiCurrency = true;
    public $primaryCurrency = 'NGN';
    public $multiTax = true;
    public $defaultLanguage = 'en';
    public $dateFormat = 'MM/DD/YYYY';

    public function mount()
    {
        $settings = GlobalCurrencyLocalization::first();
        
        if ($settings) {
            $this->multiCurrency = $settings->multi_currency === 'enabled';
            $this->primaryCurrency = $settings->primary_currency;
            $this->multiTax = $settings->multi_tax === 'enabled';
            $this->defaultLanguage = $settings->default_language;
            $this->dateFormat = $settings->date_format;
        }
    }

    public function save()
    {
        GlobalCurrencyLocalization::updateOrCreate(
            ['id' => 1],
            [
                'multi_currency' => $this->multiCurrency ? 'enabled' : 'disabled',
                'primary_currency' => $this->primaryCurrency,
                'currency_list' => ['USD', 'EUR', 'GBP', 'INR', 'NGN'],
                'multi_tax' => $this->multiTax ? 'enabled' : 'disabled',
                'default_language' => $this->defaultLanguage,
                'language_options' => ['en', 'es', 'fr', 'ar'],
                'date_format' => $this->dateFormat,
                'units_of_measure' => ['piece', 'kg', 'liter'],
            ]
        );

        session()->flash('message', 'Currency and Localization settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.currency-localization');
    }
}
