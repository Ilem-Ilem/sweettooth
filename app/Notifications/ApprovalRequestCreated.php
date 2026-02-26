<?php

namespace App\Notifications;

use App\Models\ApprovalAuditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public ApprovalAuditRequest $request;

    public function __construct(ApprovalAuditRequest $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Approval Request')
            ->greeting("Hi {$notifiable->name},")
            ->line('A new approval request is awaiting review.')
            ->line("Action: {$this->request->action}")
            ->action('Review Requests', route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]))
            ->line('Please review and take action.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_request_created',
            'approval_request_id' => $this->request->id,
            'action' => $this->request->action,
            'status' => $this->request->status,
            'branch_id' => $this->request->branch_id,
            'branch_name' => $this->request->branch?->name,
            'message' => 'New approval request awaiting review.',
            'action_url' => route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]),
            'action_text' => 'Review Requests',
        ];
    }
}
