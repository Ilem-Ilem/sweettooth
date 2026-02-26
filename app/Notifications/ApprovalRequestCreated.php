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
        $actionLabel = $this->formatActionLabel();
        $requestNumber = $this->formatRequestNumber();
        $requesterName = $this->request->requester?->name ?? 'Someone';
        $branchName = $this->request->branch?->name ?? 'Unknown branch';
        $submittedAt = optional($this->request->created_at)->format('M d, Y g:i A');

        return (new MailMessage)
            ->subject('Action Required: New Approval Request Submitted')
            ->greeting("Hi {$notifiable->name},")
            ->line('A new approval request has been submitted and requires your review.')
            ->line('Request Details:')
            ->line("Request Number: {$requestNumber}")
            ->line("Action Type: {$actionLabel}")
            ->line("Requested By: {$requesterName}")
            ->line("Branch: {$branchName}")
            ->line("Submitted: {$submittedAt}")
            ->action('Review Request', route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]))
            ->line('Please review and take action at your earliest convenience.');
    }

    public function toArray(object $notifiable): array
    {
        $actionLabel = $this->formatActionLabel();
        $requestNumber = $this->formatRequestNumber();
        $requesterName = $this->request->requester?->name ?? 'Someone';
        $branchName = $this->request->branch?->name ?? 'Unknown branch';

        return [
            'type' => 'approval_request_created',
            'title' => 'New Approval Request Submitted',
            'message' => 'A new approval request requires your review.',
            'summary' => "Request to {$actionLabel}",
            'context' => [
                'request_number' => $requestNumber,
                'action_type' => $actionLabel,
                'requested_by' => $requesterName,
                'branch' => $branchName,
                'submitted_at' => optional($this->request->created_at)->format('M d, Y g:i A'),
                'priority' => 'Normal',
            ],
            'action_url' => route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]),
            'action_text' => 'Review Request',
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
