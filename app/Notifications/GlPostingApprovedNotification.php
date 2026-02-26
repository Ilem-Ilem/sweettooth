<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GlPostingApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('GL Posting Approved and Posted')
            ->greeting("Hi {$notifiable->name},")
            ->line('A GL posting draft was approved and posted.')
            ->line("Reference: {$referenceLabel}")
            ->action('View Posting Status', route('branch-dashboard.accounting.posting-status', ['b_id' => $this->branchId]));
    }

    public function toArray(object $notifiable): array
    {
        $referenceLabel = $this->referenceNumber ?? class_basename($this->referenceType) . " #{$this->referenceId}";
        return [
            'type' => 'gl_posting_approved',
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'reference_number' => $this->referenceNumber,
            'branch_id' => $this->branchId,
            'title' => 'GL Posting Approved',
            'message' => "GL posting approved and posted for {$referenceLabel}.",
            'summary' => 'Entries have been posted to the ledger.',
            'context' => [
                'reference' => $referenceLabel,
                'status' => 'Success',
            ],
            'action_url' => route('branch-dashboard.accounting.posting-status', ['b_id' => $this->branchId]),
            'action_text' => 'View Posting Status',
        ];
    }
}
