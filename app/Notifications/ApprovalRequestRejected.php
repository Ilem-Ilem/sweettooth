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
        $actionLabel = $this->formatActionLabel();
        $requestNumber = $this->formatRequestNumber();
        $approverName = $this->request->approver?->name ?? 'an approver';
        $branchName = $this->request->branch?->name ?? 'Unknown branch';
        $submittedAt = optional($this->request->created_at)->format('M d, Y g:i A');
        $rejectedAt = optional($this->request->denied_at)->format('M d, Y g:i A');

        return (new MailMessage)
            ->subject("Update Needed: Your {$actionLabel} Request")
            ->greeting("Hi {$notifiable->name},")
            ->line('Your approval request was not approved.')
            ->line('Request Details:')
            ->line("Request Number: {$requestNumber}")
            ->line("Action Type: {$actionLabel}")
            ->line("Reviewed By: {$approverName}")
            ->line("Branch: {$branchName}")
            ->line("Submitted: {$submittedAt}")
            ->line("Rejected: {$rejectedAt}")
            ->line('Please review the feedback and update your request if needed.')
            ->action('View Feedback', route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        $actionLabel = $this->formatActionLabel();
        $requestNumber = $this->formatRequestNumber();
        $approverName = $this->request->approver?->name ?? 'Approver';
        $branchName = $this->request->branch?->name ?? 'Unknown branch';

        return [
            'type' => 'approval_request_rejected',
            'title' => 'Your Request Needs Attention',
            'message' => "Your {$actionLabel} request was not approved.",
            'summary' => "Review feedback from {$approverName}",
            'context' => [
                'request_number' => $requestNumber,
                'action_type' => $actionLabel,
                'rejected_by' => $approverName,
                'branch' => $branchName,
                'submitted_at' => optional($this->request->created_at)->format('M d, Y g:i A'),
                'rejected_at' => optional($this->request->denied_at)->format('M d, Y g:i A'),
                'rejection_reason' => $this->request->rejection_comment,
                'status' => 'Failed',
            ],
            'action_url' => route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]),
            'action_text' => 'View Feedback',
            'approval_request_id' => $this->request->id,
            'branch_id' => $this->request->branch_id,
            'raw_action' => $this->request->action,
        ];
    }

    private function formatRequestNumber(): string
    {
        return 'APR-' . str_pad((string) $this->request->id, 6, '0', STR_PAD_LEFT);
    }

    private function formatActionLabel(): string
    {
        $raw = (string) $this->request->action;
        $parts = explode(':', $raw);
        $actionKey = $parts[1] ?? ($parts[0] ?? 'request');
        $actionKey = trim($actionKey);

        if ($actionKey === '') {
            $actionKey = 'request';
        }

        return ucwords(str_replace('_', ' ', $actionKey));
    }
}
