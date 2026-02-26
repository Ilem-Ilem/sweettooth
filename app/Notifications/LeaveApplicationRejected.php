<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApplicationRejected extends Notification implements ShouldQueue
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
            ->subject('Leave Application Rejected')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your leave application {$this->leave->application_number} was rejected.")
            ->line("Reason: {$this->leave->rejection_reason}")
            ->action('View My Leaves', route('branch-dashboard.leave.my-leaves', ['b_id' => $this->leave->employee?->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'leave_application_rejected',
            'leave_application_id' => $this->leave->id,
            'branch_id' => $this->leave->employee?->branch_id,
            'branch_name' => $this->leave->employee?->branch?->name,
            'title' => 'Leave Rejected',
            'message' => "Your leave application {$this->leave->application_number} was rejected.",
            'summary' => 'Review the rejection reason.',
            'context' => [
                'application' => $this->leave->application_number,
                'reason' => $this->leave->rejection_reason,
                'branch' => $this->leave->employee?->branch?->name,
                'status' => 'Failed',
            ],
            'action_url' => route('branch-dashboard.leave.my-leaves', ['b_id' => $this->leave->employee?->branch_id]),
            'action_text' => 'View My Leaves',
        ];
    }
}
