<?php

namespace App\Livewire\BranchDashboard\Settings;

use App\Models\GlobalCurrencyLocalization;
use Livewire\Component;

class CurrencyLocalization extends Component
{
    public $multiCurrency;
    public $primaryCurrency;
    public $defaultLanguage;
    public $dateFormat;
    public $multiTax;

    protected $rules = [
        'multiCurrency' => 'boolean',
        'primaryCurrency' => 'required|string|max:3',
        'defaultLanguage' => 'required|string|max:5',
        'dateFormat' => 'required|string|max:20',
        'multiTax' => 'boolean',
    ];

    public function mount()
    {
        $settings = GlobalCurrencyLocalization::first();

        if ($settings) {
            $this->multiCurrency = $settings->multi_currency === 'enabled';
            $this->primaryCurrency = $settings->primary_currency;
            $this->defaultLanguage = $settings->default_language;
            $this->dateFormat = $settings->date_format;
            $this->multiTax = $settings->multi_tax === 'enabled';
        } else {
            $this->multiCurrency = true;
            $this->primaryCurrency = 'NGN';
            $this->defaultLanguage = 'en';
            $this->dateFormat = 'MM/DD/YYYY';
            $this->multiTax = true;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $settings = GlobalCurrencyLocalization::first();

            if (!$settings) {
                $settings = new GlobalCurrencyLocalization();
            }

            $settings->multi_currency = $this->multiCurrency ? 'enabled' : 'disabled';
            $settings->primary_currency = $this->primaryCurrency;
            $settings->default_language = $this->defaultLanguage;
            $settings->date_format = $this->dateFormat;
            $settings->multi_tax = $this->multiTax ? 'enabled' : 'disabled';

            $settings->save();

            session()->flash('message', 'Currency & Localization settings saved successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to save currency localization settings: ' . $e->getMessage());
            session()->flash('error', 'Failed to save currency localization settings. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.branch-dashboard.settings.currency-localization');
    }
}