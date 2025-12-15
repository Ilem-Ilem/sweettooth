<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\AccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostPaymentToGl implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private AccountingService $accountingService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;

        // Post payment entry (Cash/Bank debit, AR credit)
        $this->accountingService->postPaymentTransaction($payment);
    }
}
