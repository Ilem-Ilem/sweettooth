<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeRoleUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $employee;
    public array $roles;

    public function __construct(User $employee, array $roles)
    {
        $this->employee = $employee;
        $this->roles = $roles;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $roleList = implode(', ', $this->roles);

        return (new MailMessage)
            ->subject('Employee Roles Updated')
            ->greeting("Hi {$notifiable->name},")
            ->line("Roles for {$this->employee->name} were updated.")
            ->line("Roles: {$roleList}")
            ->action('View Employee', route('branch-dashboard.employee.details', ['id' => $this->employee->id, 'employee_number' => $this->employee->employee_number, 'b_id' => $this->employee->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'employee_roles_updated',
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'branch_name' => $this->employee->branch?->name,
            'roles' => $this->roles,
            'message' => "Roles updated for {$this->employee->name}.",
            'action_url' => route('branch-dashboard.employee.details', ['id' => $this->employee->id, 'employee_number' => $this->employee->employee_number, 'b_id' => $this->employee->branch_id]),
            'action_text' => 'View Employee',
        ];
    }
}
