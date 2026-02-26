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
        return (new MailMessage)
            ->subject('Journal Entry Posted')
            ->greeting("Hi {$notifiable->name},")
            ->line("Journal entry {$this->entry->reference_number} was posted.")
            ->line("Amount: " . number_format((float) $this->entry->debit + (float) $this->entry->credit, 2));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'journal_entry_posted',
            'gl_entry_id' => $this->entry->id,
            'branch_id' => $this->entry->branch_id,
            'branch_name' => $this->entry->branch?->name,
            'message' => 'Journal entry posted.',
            'action_url' => route('branch-dashboard.accounting.journal-entry', ['b_id' => $this->entry->branch_id]),
            'action_text' => 'View Journal',
        ];
    }
}
