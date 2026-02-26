<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GlPostingDraftReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $referenceType,
        public int $referenceId,
        public ?string $referenceNumber,
        public ?string $branchId
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $referenceLabel = $this->referenceNumber ?? class_basename($this->referenceType) . " #{$this->referenceId}";
        return (new MailMessage)
            ->subject('GL Posting Draft Ready')
            ->greeting("Hi {$notifiable->name},")
            ->line('A GL posting draft is ready for approval.')
            ->line("Reference: {$referenceLabel}")
            ->action('Review Posting', route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]));
    }

    public function toArray(object $notifiable): array
    {
        $referenceLabel = $this->referenceNumber ?? class_basename($this->referenceType) . " #{$this->referenceId}";
        return [
            'type' => 'gl_posting_draft_ready',
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'reference_number' => $this->referenceNumber,
            'branch_id' => $this->branchId,
            'title' => 'GL Posting Draft Ready',
            'message' => "A GL posting draft is ready for approval for {$referenceLabel}.",
            'summary' => 'Review and approve the draft entries.',
            'context' => [
                'reference' => $referenceLabel,
                'status' => 'Pending',
            ],
            'action_url' => route('branch-dashboard.accounting.posting-approvals', ['b_id' => $this->branchId]),
            'action_text' => 'Review Posting',
        ];
    }
}
