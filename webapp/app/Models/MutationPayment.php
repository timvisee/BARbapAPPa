<?php

namespace App\Models;

use BarPay\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation payment model.
 * This defines additional information for a payment mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int|null payment_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationPayment extends Model {

    use Mutationable;

    protected $table = 'mutation_payment';

    protected $with = ['payment'];

    protected $fillable = [
        'payment_id',
    ];

    /**
     * Get the payment this mutation had an effect on.
     *
     * @return The affected payment.
     */
    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Handle changes as effect of a state change.
     * This method is called when the state of the mutation changes.
     *
     * For a wallet mutation, this method would change the wallet balance when
     * the new state defines success.
     *
     * @param Mutation $mutation The mutation, parent of this instance.
     * @param int $oldState The old state.
     * @param int $newState The new, current state.
     */
    public function applyState(Mutation $mutation, int $oldState, int $newState) {
        // TODO: the new state must be related to the payment state
    }

    /**
     * Find a list of communities this mutation took part in.
     *
     * This will always be empty for this type of transaction.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        return collect();
    }

    /**
     * Get a list of all relevant and related objects to this mutation.
     * Can be used to generate a list of links on a mutation inspection page, to
     * the respective objects.
     *
     * This will return the related payment.
     *
     * This is an expensive function.
     *
     * @return Collection List of objects.
     */
    public function getRelatedObjects() {
        if($this->payment_id != null)
            return [$this->payment];
        return [];
    }
}
