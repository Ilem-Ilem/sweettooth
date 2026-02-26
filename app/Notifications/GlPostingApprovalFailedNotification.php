<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GlPostingApprovalFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $referenceType,
        public int $referenceId,
        public ?string $referenceNumber,
        public ?string $branchId,
        public ?string $error
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $referenceLabel = $this->referenceNumber ?? class_basename($this->referenceType) . " #{$this->referenceId}";
        $errorMessage = $this->error ?? 'Unknown error';
        return (new MailMessage)
            ->subject('GL Posting Approval Failed')
            ->greeting("Hi {$notifiable->name},")
            ->line('A GL posting approval failed.')
            ->line("Reference: {$referenceLabel}")
            ->line("Error: {$errorMessage}")
            ->action('Review Posting', route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]));
    }

    public function toArray(object $notifiable): array
    {
        $referenceLabel = $this->referenceNumber ?? class_basename($this->referenceType) . " #{$this->referenceId}";
        $errorMessage = $this->error ?? 'Unknown error';
        return [
            'type' => 'gl_posting_approval_failed',
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'reference_number' => $this->referenceNumber,
            'branch_id' => $this->branchId,
            'error' => $this->error,
            'title' => 'GL Posting Approval Failed',
            'message' => "GL posting approval failed for {$referenceLabel}.",
            'summary' => 'Review the error and retry approval.',
            'context' => [
                'reference' => $referenceLabel,
                'error_message' => $errorMessage,
                'status' => 'Failed',
            ],
            'action_url' => route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]),
            'action_text' => 'Review Posting',
        ];
    }
}
