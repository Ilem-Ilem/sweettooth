<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Models\GlobalNotificationsAlerts;

class NotificationsAlerts extends Component
{
    public $alertsEmail = true;
    public $alertsSms = true;
    public $taskTodo = true;

    public function mount()
    {
        $settings = GlobalNotificationsAlerts::first();
        
        if ($settings) {
            $alerts = $settings->alerts ?? [];
            $this->alertsEmail = in_array('email', $alerts);
            $this->alertsSms = in_array('sms', $alerts);
            
            $this->taskTodo = $settings->task_todo === 'enabled';
        }
    }

    public function save()
    {
        $alerts = [];
        if ($this->alertsEmail) $alerts[] = 'email';
        if ($this->alertsSms) $alerts[] = 'sms';

        GlobalNotificationsAlerts::updateOrCreate(
            ['id' => 1],
            [
                'alerts' => $alerts,
                'task_todo' => $this->taskTodo ? 'enabled' : 'disabled',
            ]
        );

        session()->flash('message', 'Notifications & Alerts settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.super-admin.settings.notifications-alerts');
    }
}
