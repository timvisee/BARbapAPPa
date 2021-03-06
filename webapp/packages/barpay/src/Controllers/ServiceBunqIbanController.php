<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Service;
use BarPay\Models\ServiceBunqIban;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class ServiceBunqIbanController {

    /**
     * Validate the form input for creating the new serviceable.
     *
     * @param Request $request The request.
     */
    public static function validateCreate(Request $request) {
        // TODO: validate bunq account here!
        $request->validate([
            'bunq_account' => 'required|numeric|exists:bunq_account,id',
            'account_holder' => 'required|' . ValidationDefaults::NAME,
            'iban' => 'required|iban|unique:bunq_account,iban',
            'bic' => 'nullable|bic',
        ], [
            'iban.unique' => __('barpay::service.bunq.ibanCannotBeReceivingBunqAccount'),
        ]);
    }

    /**
     * Create the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     *
     * @return ServiceBunqIban The created serviceable.
     */
    public static function create(Request $request, Service $service) {
        // Create the serviceable
        $serviceable = new ServiceBunqIban();
        $serviceable->bunq_account_id = $request->input('bunq_account');
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();

        // Update serviceable link on service
        $service->setServiceable($serviceable);

        return $serviceable;
    }

    /**
     * Edit the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     * @param ServiceBunqIban $serviceable The serviceable.
     */
    public static function edit(Request $request, Service $service, ServiceBunqIban $serviceable) {
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();
    }
}
