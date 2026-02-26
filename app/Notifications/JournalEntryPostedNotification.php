<?php

namespace App\Notifications;

use App\Models\GlEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JournalEntryPostedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public GlEntry $entry;

    public function __construct(GlEntry $entry)
    {
        $this->entry = $entry;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $referenceNumber = $this->entry->reference_number ?? "JE-{$this->entry->id}";
        $amount = number_format((float) $this->entry->debit + (float) $this->entry->credit, 2);
        return (new MailMessage)
            ->subject('Journal Entry Posted')
            ->greeting("Hi {$notifiable->name},")
            ->line("Journal entry {$referenceNumber} was posted.")
            ->line("Amount: {$amount}")
            ->action('View Journal', route('branch-dashboard.accounting.journal-entry', ['b_id' => $this->entry->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        $referenceNumber = $this->entry->reference_number ?? "JE-{$this->entry->id}";
        $amount = number_format((float) $this->entry->debit + (float) $this->entry->credit, 2);
        return [
            'type' => 'journal_entry_posted',
            'gl_entry_id' => $this->entry->id,
            'branch_id' => $this->entry->branch_id,
            'branch_name' => $this->entry->branch?->name,
            'title' => 'Journal Entry Posted',
            'message' => "Journal entry {$referenceNumber} was posted.",
            'summary' => "Total amount {$amount}.",
            'context' => [
                'reference' => $referenceNumber,
                'amount' => $amount,
                'branch' => $this->entry->branch?->name,
                'status' => 'Success',
            ],
            'action_url' => route('branch-dashboard.accounting.journal-entry', ['b_id' => $this->entry->branch_id]),
            'action_text' => 'View Journal',
        ];
    }
}
