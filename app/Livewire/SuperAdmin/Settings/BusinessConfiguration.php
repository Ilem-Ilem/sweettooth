<?php

namespace App\Livewire\SuperAdmin\Settings;

use App\Models\GlobalBusinessConfiguration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;

class BusinessConfiguration extends Component
{
    use WithFileUploads;

    public $companyName;
    public $logo; // New uploaded file
    public $existingLogo; // Current logo path
    public $phone;
    public $email;
    public $vatNumber;
    public $auto_backup;
    public $backup_interval;
    public $backup_period;

    protected $rules = [
        'companyName' => 'required|string|max:255',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'vatNumber' => 'nullable|string|max:50',
        'auto_backup' => 'boolean',
        'backup_interval' => 'nullable|integer|min:1|max:999',
        'backup_period' => 'nullable|string|max:50',
    ];

    public function mount()
    {
        $settings = GlobalBusinessConfiguration::first();

        if ($settings) {
            $this->companyName = $settings->company_name;
            $this->existingLogo = $settings->logo_upload;
            $contactDetails = $settings->contact_details ?? [];
            $this->phone = $contactDetails['phone'] ?? '';
            $this->email = $contactDetails['email'] ?? '';
            $this->vatNumber = $contactDetails['vat_number'] ?? '';
            $this->auto_backup = $settings->auto_backup ?? false;
            $this->backup_interval = $settings->backup_interval ?? '';
            $this->backup_period = $settings->backup_period ?? '';
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $settings = GlobalBusinessConfiguration::first();

            if (!$settings) {
                $settings = new GlobalBusinessConfiguration();
            }

            $settings->company_name = $this->companyName;

            // Handle file upload
            if ($this->logo) {
                // Delete old logo if exists
                if ($settings->logo_upload && Storage::disk('public')->exists($settings->logo_upload)) {
                    Storage::disk('public')->delete($settings->logo_upload);
                }

                $path = $this->logo->store('logos', 'public');
                $settings->logo_upload = $path;
                $this->existingLogo = $path;
            }

            $settings->contact_details = [
                'phone' => $this->phone,
                'email' => $this->email,
                'vat_number' => $this->vatNumber,
            ];

            // Store backup settings in the same format as BranchBusinessConfiguration for consistency
            $settings->auto_backup = $this->auto_backup;
            $settings->backup_interval = $this->backup_interval;
            $settings->backup_period = $this->backup_period;

            $settings->save();

            // Clear settings cache to ensure changes take effect immediately
            \App\Helpers\Settings::clearCache();

            // Update existingLogo to reflect the new logo that was just saved
            if ($this->logo) {
                $this->existingLogo = $settings->logo_upload;
            }

            // Reset the file input
            $this->logo = null;

            session()->flash('message', 'Global business configuration saved successfully! These settings will apply to all branches.');
        } catch (\Exception $e) {
            \Log::error('Failed to save global business configuration: ' . $e->getMessage());
            session()->flash('error', 'Failed to save business configuration. Please try again.');
        }
    }

    public function removeLogo()
    {
        try {
            if ($this->existingLogo && Storage::disk('public')->exists($this->existingLogo)) {
                Storage::disk('public')->delete($this->existingLogo);
            }

            $settings = GlobalBusinessConfiguration::first();

            if ($settings) {
                $settings->logo_upload = null;
                $settings->save();
            }

            // Clear settings cache
            \App\Helpers\Settings::clearCache();

            $this->existingLogo = null;
            session()->flash('message', 'Logo removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to remove logo: ' . $e->getMessage());
            session()->flash('error', 'Failed to remove logo. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.super-admin.settings.business-configuration');
    }
}
