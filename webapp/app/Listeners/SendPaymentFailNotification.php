<?php

namespace App\Listeners;

use App\Events\PaymentFailed;
use App\Mail\Email\Payment\Failed;
use BarPay\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentFailNotification implements ShouldQueue {

    public $queue = 'normal';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(PaymentFailed $event) {
        // Gather facts
        $payment = Payment::findOrFail($event->payment_id);
        $user = $payment->user;

        // Create the mailable for the failure, send the mailable
        Mail::send(
            new Failed($user->buildEmailRecipients(), $payment)
        );
    }
}