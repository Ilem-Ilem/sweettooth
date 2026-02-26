<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $employee;

    public function __construct(User $employee)
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
            ->subject('New Employee Created')
            ->greeting("Hi {$notifiable->name},")
            ->line("Employee {$this->employee->name} has been created.")
            ->action('View Employees', route('branch-dashboard.employee.index', ['b_id' => $this->employee->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'employee_created',
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'branch_name' => $this->employee->branch?->name,
            'title' => 'New Employee Created',
            'message' => "Employee {$this->employee->name} has been created.",
            'summary' => 'Employee profile is now active.',
            'context' => [
                'employee' => $this->employee->name,
                'branch' => $this->employee->branch?->name,
                'status' => 'Success',
            ],
            'action_url' => route('branch-dashboard.employee.index', ['b_id' => $this->employee->branch_id]),
            'action_text' => 'View Employees',
        ];
    }
}
