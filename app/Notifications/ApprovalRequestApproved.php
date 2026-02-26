<?php

namespace App\Notifications;

use App\Models\ApprovalAuditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequestApproved extends Notification implements ShouldQueue
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
        $approvedAt = optional($this->request->approved_at)->format('M d, Y g:i A');

        return (new MailMessage)
            ->subject("Approved: Your {$actionLabel} Request")
            ->greeting("Hi {$notifiable->name},")
            ->line('Great news! Your approval request has been approved.')
            ->line('Approval Details:')
            ->line("Request Number: {$requestNumber}")
            ->line("Action Type: {$actionLabel}")
            ->line("Approved By: {$approverName}")
            ->line("Branch: {$branchName}")
            ->line("Submitted: {$submittedAt}")
            ->line("Approved: {$approvedAt}")
            ->action('View Request Details', route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        $actionLabel = $this->formatActionLabel();
        $requestNumber = $this->formatRequestNumber();
        $approverName = $this->request->approver?->name ?? 'Approver';
        $branchName = $this->request->branch?->name ?? 'Unknown branch';

        return [
            'type' => 'approval_request_approved',
            'title' => 'Your Request Has Been Approved',
            'message' => "Your {$actionLabel} request has been approved.",
            'summary' => "Approved by {$approverName}",
            'context' => [
                'request_number' => $requestNumber,
                'action_type' => $actionLabel,
                'approved_by' => $approverName,
                'branch' => $branchName,
                'submitted_at' => optional($this->request->created_at)->format('M d, Y g:i A'),
                'approved_at' => optional($this->request->approved_at)->format('M d, Y g:i A'),
                'status' => 'Success',
            ],
            'action_url' => route('branch-dashboard.audit.index', ['b_id' => $this->request->branch_id]),
            'action_text' => 'View Request',
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
