<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var User|Employee */
    public User|Employee $employee;

    public function __construct(User|Employee $employee)
    {
        $this->employee = $employee;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Employee Updated')
            ->greeting("Hi {$notifiable->name},")
            ->line("Employee {$this->employee->name} was updated.")
            ->action('View Employee', route('branch-dashboard.employee.details', ['id' => $this->employee->id, 'employee_number' => $this->employee->employee_number, 'b_id' => $this->employee->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'employee_updated',
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'branch_name' => $this->employee->branch?->name,
            'message' => "Employee {$this->employee->name} updated.",
            'action_url' => route('branch-dashboard.employee.details', ['id' => $this->employee->id, 'employee_number' => $this->employee->employee_number, 'b_id' => $this->employee->branch_id]),
            'action_text' => 'View Employee',
        ];
    }
}
