<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public LeaveApplication $leave;

    public function __construct(LeaveApplication $leave)
    {
        $this->leave = $leave;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Leave Application')
            ->greeting("Hi {$notifiable->name},")
            ->line('A new leave application has been submitted.')
            ->line("Employee: {$this->leave->employee?->name}")
            ->line("Dates: {$this->leave->start_date} to {$this->leave->end_date}")
            ->action('Review Leave Requests', route('branch-dashboard.leave.approve', ['b_id' => $this->leave->employee?->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_application_submitted',
            'leave_application_id' => $this->leave->id,
            'employee_id' => $this->leave->employee_id,
            'branch_id' => $this->leave->employee?->branch_id,
            'branch_name' => $this->leave->employee?->branch?->name,
            'title' => 'New Leave Application',
            'message' => 'A new leave application has been submitted.',
            'summary' => $this->leave->employee?->name
                ? "{$this->leave->employee->name} requested leave."
                : 'Leave request submitted.',
            'context' => [
                'employee' => $this->leave->employee?->name,
                'dates' => "{$this->leave->start_date} to {$this->leave->end_date}",
                'branch' => $this->leave->employee?->branch?->name,
                'status' => 'Pending',
            ],
            'action_url' => route('branch-dashboard.leave.approve', ['b_id' => $this->leave->employee?->branch_id]),
            'action_text' => 'Review Leave Requests',
        ];
    }
}
