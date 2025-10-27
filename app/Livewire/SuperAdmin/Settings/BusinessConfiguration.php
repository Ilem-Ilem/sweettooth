<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\GlobalBusinessConfiguration;
use App\Helpers\Settings;
use TallStackUi\Traits\Interactions;

class BusinessConfiguration extends Component
{
    use WithFileUploads, Interactions;

    public $companyName;
    public $phone;
    public $email;
    public $vatNumber;
    public $logo;
    public $auto_backup;
    public $backup_interval;
    public $backup_period;

    public function mount()
    {
        $settings = GlobalBusinessConfiguration::first();
        
        if ($settings) {
            $this->companyName = $settings->company_name;
            $contactDetails = $settings->contact_details ?? [];
            $this->phone = $contactDetails['phone'] ?? '';
            $this->email = $contactDetails['email'] ?? '';
            $this->vatNumber = $contactDetails['vat_number'] ?? '';
            $this->auto_backup = $settings->auto_backup ?? null;
            $this->backup_interval = $settings->backup_interval ?? 2;
            $this->backup_period = $settings->backup_period ?? 'weeks';

        } else {
            // Set defaults
            $this->companyName = 'Your Business Name';
         
        }
    }

    public function save()
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'email' => 'nullable|email',
        ]);

        $contactDetails = [
            'phone' => $this->phone,
            'email' => $this->email,
            'vat_number' => $this->vatNumber,
        ];

        GlobalBusinessConfiguration::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => $this->companyName,
                'contact_details' => $contactDetails,
                'auto_backup'=> $this->auto_backup,
                'backup_interval' => $this->backup_interval,
                'backup_period'=> $this->backup_period
            ]
        );

        // Clear settings cache so new values take effect immediately
        Settings::clearCache();

        session()->flash('message', 'Business configuration updated successfully.');
        $this->toast()->success("Done!!", "Settings saved and cache cleared")->send();
    }

    public function render()
    {
        return view('livewire.super-admin.settings.business-configuration');
    }
}
