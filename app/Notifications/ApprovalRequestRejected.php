<?php

namespace App\Notifications;

use App\Models\ApprovalAuditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestRejected extends Notification implements ShouldQueue
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
            ->subject('Approval Request Rejected')
            ->greeting("Hi {$notifiable->name},")
            ->line('An approval request has been rejected.')
            ->line("Action: {$this->request->action}")
            ->action('View Audit', route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_request_rejected',
            'approval_request_id' => $this->request->id,
            'action' => $this->request->action,
            'status' => $this->request->status,
            'branch_id' => $this->request->branch_id,
            'branch_name' => $this->request->branch?->name,
            'message' => 'Approval request rejected.',
            'action_url' => route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]),
            'action_text' => 'View Audit',
        ];
    }
}
