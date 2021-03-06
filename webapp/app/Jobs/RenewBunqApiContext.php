<?php

namespace App\Jobs;

use App\Models\BunqAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class RenewBunqApiContext implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'high';

    /**
     * The ID of the bunq account, which the money is sent from.
     *
     * @var int
     */
    private $account_id;

    /**
     * Create a new job instance.
     *
     * @param BunqAccount $account The bunq account to renew the API context
     *      session for.
     *
     * @return void
     */
    public function __construct(BunqAccount $account) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->account_id = $account->id;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [
            new RateLimited('bunq-api'),
            (new WithoutOverlapping($this->account_id))
                // Release exclusive lock after half a minute (failure)
                ->expireAfter(30)
                ->dontRelease()
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Obtain the account ID, get the API context
        $account = BunqAccount::withoutGlobalScopes()
            ->findOrFail($this->account_id);
        $apiContext = $account->api_context;

        // Renew the context, update it in the database
        $apiContext->resetSession();
        $account->api_context = $apiContext;
        $account->renewed_at = now();
        $account->save();
    }

    /**
     * Backoff times in seconds.
     *
     * @return array
     */
    public function backoff() {
        // The bunq API has a 30-second cooldown when throttling, retry quickly
        // a few times because this has high priority for users, then backoff
        return [2, 3, 5, 10, 32, 60];
    }
}
