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
        return (new MailMessage)
            ->subject('GL Posting Approval Failed')
            ->greeting("Hi {$notifiable->name},")
            ->line('A GL posting approval failed.')
            ->line('Reference: ' . ($this->referenceNumber ?? "{$this->referenceType} #{$this->referenceId}"))
            ->line('Error: ' . ($this->error ?? 'Unknown error'))
            ->action('Review Posting', route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'gl_posting_approval_failed',
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'reference_number' => $this->referenceNumber,
            'branch_id' => $this->branchId,
            'error' => $this->error,
            'message' => 'GL posting approval failed.',
            'action_url' => route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]),
            'action_text' => 'Review Posting',
        ];
    }
}
