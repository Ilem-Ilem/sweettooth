<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Collection;

class NotificationCenter extends Component
{
    public int $limit = 10;

    public function getNotificationsProperty(): Collection
    {
        $user = auth()->user();

        if (!$user) {
            return collect();
        }

        return $user->notifications()
            ->latest()
            ->limit($this->limit)
            ->get();
    }

    public function getUnreadCountProperty(): int
    {
        $user = auth()->user();

        if (!$user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();
        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.components.notification-center');
    }
}
