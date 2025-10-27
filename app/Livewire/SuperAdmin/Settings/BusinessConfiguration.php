<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GlobalBusinessConfiguration;

class BusinessConfiguration extends Component
{
    use WithFileUploads;

    public $companyName;
    public $businessType;
    public $phone;
    public $email;
    public $website;
    public $vatNumber;
    public $storageSettings;
    public $subscriptionPlan;
    public $logo;

    public function mount()
    {
        $settings = GlobalBusinessConfiguration::first();
        
        if ($settings) {
            $this->companyName = $settings->company_name;
            $this->businessType = $settings->business_type[0] ?? 'retail';
            $contactDetails = $settings->contact_details ?? [];
            $this->phone = $contactDetails['phone'] ?? '';
            $this->email = $contactDetails['email'] ?? '';
            $this->website = $contactDetails['website'] ?? '';
            $this->vatNumber = $contactDetails['vat_number'] ?? '';
            $this->storageSettings = $settings->storage_settings[0] ?? 'local';
            $this->subscriptionPlan = $settings->subscription_plan;
        } else {
            // Set defaults
            $this->companyName = 'Your Business Name';
            $this->businessType = 'retail';
            $this->storageSettings = 'local';
            $this->subscriptionPlan = 'basic';
        }
    }

    public function save()
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'businessType' => 'required|in:retail,wholesale,services',
            'email' => 'nullable|email',
            'storageSettings' => 'required|in:local,s3',
            'subscriptionPlan' => 'required|in:basic,pro,enterprise',
        ]);

        $contactDetails = [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'vat_number' => $this->vatNumber,
        ];

        GlobalBusinessConfiguration::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => $this->companyName,
                'business_type' => [$this->businessType],
                'contact_details' => $contactDetails,
                'storage_settings' => [$this->storageSettings],
                'subscription_plan' => $this->subscriptionPlan,
            ]
        );

        session()->flash('message', 'Business configuration updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.business-configuration');
    }
}
