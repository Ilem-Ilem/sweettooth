<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalSecurityAccess;

class SecurityAccess extends Component
{
    public $authentication = '2fa';
    public $auditLogs = true;
    public $dataIsolation = 'saas_company';

    public function mount()
    {
        $settings = GlobalSecurityAccess::first();
        
        if ($settings) {
            $this->authentication = $settings->authentication ?? '2fa';
            $this->auditLogs = $settings->audit_logs === 'enabled';
            $this->dataIsolation = $settings->data_isolation ?? 'saas_company';
        }
    }

    public function save()
    {
        GlobalSecurityAccess::updateOrCreate(
            ['id' => 1],
            [
                'authentication' => $this->authentication,
                'audit_logs' => $this->auditLogs ? 'enabled' : 'disabled',
                'data_isolation' => $this->dataIsolation,
            ]
        );

        session()->flash('message', 'Security & Access settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.security-access');
    }
}
