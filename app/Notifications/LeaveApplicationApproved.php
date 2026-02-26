<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApplicationApproved extends Notification implements ShouldQueue
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
            ->subject('Leave Application Approved')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your leave application {$this->leave->application_number} has been approved.")
            ->line("Dates: {$this->leave->start_date} to {$this->leave->end_date}")
            ->action('View My Leaves', route('branch-dashboard.leave.my-leaves', ['b_id' => $this->leave->employee?->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_application_approved',
            'leave_application_id' => $this->leave->id,
            'branch_id' => $this->leave->employee?->branch_id,
            'branch_name' => $this->leave->employee?->branch?->name,
            'title' => 'Leave Approved',
            'message' => "Your leave application {$this->leave->application_number} was approved.",
            'summary' => "Dates: {$this->leave->start_date} to {$this->leave->end_date}.",
            'context' => [
                'application' => $this->leave->application_number,
                'dates' => "{$this->leave->start_date} to {$this->leave->end_date}",
                'branch' => $this->leave->employee?->branch?->name,
                'status' => 'Success',
            ],
            'action_url' => route('branch-dashboard.leave.my-leaves', ['b_id' => $this->leave->employee?->branch_id]),
            'action_text' => 'View My Leaves',
        ];
    }
}
