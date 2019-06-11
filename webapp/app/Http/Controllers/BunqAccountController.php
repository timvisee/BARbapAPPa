<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\BunqAccount;
use BarPay\Models\Service as PayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rule;
use Validator;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Exception\ApiException;
use bunq\Exception\BadRequestException;
use bunq\Http\Pagination;
use bunq\Model\Generated\Endpoint\MonetaryAccountBank;
use bunq\Model\Generated\Object\Pointer;
use bunq\Util\BunqEnumApiEnvironmentType;

class BunqAccountController extends Controller {

    // TODO: make this controller generic, also support it for application
    //       glboal configuration?

    /**
     * Bunq account index page for communities.
     *
     * @return Response
     */
    public function index(Request $request, $communityId) {
        $user = barauth()->getUser();
        $community = \Request::get('community');
        // TODO: also list disabled accounts
        $accounts = $community->bunqAccounts;

        return view('community.bunqAccount.index')
            ->with('accounts', $accounts);
    }

    /**
     * bunq account creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId) {
        return view('community.bunqAccount.create');
    }

    /**
     * Payment service create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId) {
        // Get the community
        $community = \Request::get('community');

        // Validate service and serviceable fields
        $request->validate([
            'name' => 'required|' . ValidationDefaults::NAME,
            'token' => 'required|' . ValidationDefaults::BUNQ_TOKEN,
            'account_holder' => 'required|' . ValidationDefaults::NAME,
            'iban' => 'required|iban|regex:/[A-Z]{2}\d\dBUNQ[0-9]+/|unique:bunq_accounts,iban',
            'bic' => 'nullable|bic',
            'confirm' => 'accepted',
        ], [
            'iban.regex' => __('pages.bunqAccounts.mustEnterBunqIban'),
            'iban.unique' => __('pages.bunqAccounts.accountAlreadyUsed'),
        ]);

        // Gather fats
        $account_holder = $request->input('account_holder');
        $iban = $request->input('iban');
        $bic = $request->input('bic');

        // Create an API context for this application instance, load the context
        try {
            $apiContext = ApiContext::create(
                BunqEnumApiEnvironmentType::PRODUCTION(),
                $request->input('token'),
                config('app.name') . ' ' . config('app.url'),
                []
            );
            BunqContext::loadApiContext($apiContext);
        } catch(BadRequestException $e) {
            add_session_error('token', __('pages.bunqAccounts.invalidApiToken'));
            return redirect()->back()->withInput();
        }

        // Find a monetary account that matches the given IBAN
        $monetaryAccount = Self::findBunqAccountWithIban($iban);
        if($monetaryAccount == null) {
            add_session_error('iban', __('pages.bunqAccounts.noAccountWithIban'));
            return redirect()->back()->withInput();
        }

        // TODO: account must be euro
        // TODO: account must have 0 balance

        // Add the bunq account to the database
        $account = new BunqAccount();
        $account->community_id = $community->id;
        $account->enabled = is_checked($request->input('enabled'));
        $account->name = $request->input('name');
        $account->api_context = $apiContext;
        $account->monetary_account_id = $monetaryAccount->getId();
        $account->account_holder = $account_holder;
        $account->iban = $iban;
        $account->bic = $bic;
        $account->save();

        // Redirect to services index
        return redirect()
            // TODO: link to show page of new account instead
            ->route('community.bunqAccount.index', [
                'communityId' => $community->human_id,
                'accountId' => $account->id,
            ])
            ->with('success', __('pages.bunqAccounts.added'));
    }

    /**
     * List all active monetary accounts within the current bunq context, and
     * find a monetary account that has the given IBAN as alias.
     *
     * If none was found, `null` is returned.
     *
     * @param string $iban IBAN to look for, must be normalized.
     * @return MonetaryAccountBank|null The monetary bank account, or null.
     */
    private static function findBunqAccountWithIban(string $iban) {
        // Configure pagination
        $pagination = new Pagination();
        $pagination->setCount(200);

        // List all monetary accounts, filter to active accounts
        $monetaryAccounts = MonetaryAccountBank::listing(
            [],
            $pagination->getUrlParamsCountOnly()
        )->getValue();
        $monetaryAccounts = collect($monetaryAccounts)
            ->filter(function($a)  {
                return $a->getStatus() === 'ACTIVE';
            });

        // Find an account with this IBAN
        return $monetaryAccounts
            ->filter(function($a) use($iban) {
                // Get the account IBAN
                $a_iban = Self::getBunqMonetaryAccountIban($a);
                return $a_iban != null && $a_iban == $iban;
            })
            ->first();
    }

    /**
     * Get the IBAN for a given monetary bank account.
     * If the account does not have an IBAN configured, null is returned.
     *
     * @param MonetaryAccountBank $monetaryAccount The monetary account.
     * @return string|null The IBAN for this account, or null.
     */
    private static function getBunqMonetaryAccountIban(MonetaryAccountBank $monetaryAccount) {
        return collect($monetaryAccount->getAlias())
            ->filter(function($alias) {
                return $alias->getType() === 'IBAN';
            })
            ->map(function($alias) {
                return $alias->getValue();
            })
            ->first();
    }

    // /**
    //  * Show a payment service.
    //  *
    //  * @return Response
    //  */
    // public function show($communityId, $economyId, $serviceId) {
    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withDisabled()
    //         ->withTrashed()
    //         ->findOrFail($serviceId);
    //     $serviceable = $service->serviceable;

    //     return view('community.economy.paymentservice.show')
    //         ->with('economy', $economy)
    //         ->with('service', $service)
    //         ->with('serviceable', $serviceable);
    // }

    // /**
    //  * Edit a payment service.
    //  *
    //  * @return Response
    //  */
    // public function edit($communityId, $economyId, $serviceId) {
    //     // TODO: with trashed?

    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withDisabled()
    //         ->findOrFail($serviceId);
    //     $serviceable = $service->serviceable;

    //     // List the currencies that can be used
    //     $currencies = $economy->currencies;

    //     return view('community.economy.paymentservice.edit')
    //         ->with('economy', $economy)
    //         ->with('service', $service)
    //         ->with('serviceable', $serviceable)
    //         ->with('currencies', $currencies);
    // }

    // /**
    //  * Payment service update endpoint.
    //  *
    //  * @param Request $request Request.
    //  *
    //  * @return Response
    //  */
    // public function doEdit(Request $request, $communityId, $economyId, $serviceId) {
    //     // TODO: with trashed?

    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withDisabled()
    //         ->findOrFail($serviceId);
    //     $serviceable = $service->serviceable;

    //     // Validate service and serviceable fields
    //     $request->validate([
    //         'currency' => array_merge(['required'], ValidationDefaults::economyCurrency($economy, false)),
    //         'withdraw' => 'required_without:deposit',
    //     ]);
    //     ($serviceable::CONTROLLER)::validateCreate($request);

    //     // Find the selected economy currency, get it's currency ID
    //     // TODO: did we get Currency id from form? Should be economycurren?
    //     $currencyId = EconomyCurrency::findOrFail($request->input('currency'))->currency_id;

    //     // Change service and serviceable properties and sync prices in transaction
    //     DB::transaction(function() use($request, $service, $serviceable, $currencyId) {
    //         // Update service properties
    //         $service->currency_id = $currencyId;
    //         $service->enabled = is_checked($request->input('enabled'));
    //         $service->deposit = is_checked($request->input('deposit'));
    //         $service->withdraw = is_checked($request->input('withdraw'));
    //         $service->save();

    //         // Update serviceable
    //         $serviceable = ($serviceable::CONTROLLER)::edit($request, $service, $serviceable);
    //     });

    //     // Redirect the user to the payment service page
    //     return redirect()
    //         ->route('community.economy.payservice.show', [
    //             'communityId' => $community->human_id,
    //             'economyId' => $economy->id,
    //             'serviceId' => $service->id,
    //         ])
    //         ->with('success', __('pages.paymentService.changed'));
    // }

    // // /**
    // //  * Page for confirming restoring a product.
    // //  *
    // //  * @return Response
    // //  */
    // // public function restore($communityId, $economyId, $productId) {
    // //     // Get the user, community, find the product
    // //     $user = barauth()->getUser();
    // //     $community = \Request::get('community');
    // //     $economy = $community->economies()->findOrFail($economyId);
    // //     $product = $economy->products()->withTrashed()->findOrFail($productId);

    // //     // If already restored, redirect to the product
    // //     if(!$product->trashed())
    // //         return redirect()
    // //             ->route('community.economy.product.show', [
    // //                 'communityId' => $community->human_id,
    // //                 'economyId' => $economy->id,
    // //                 'productId' => $product->id,
    // //             ])
    // //             ->with('success', __('pages.products.restored'));

    // //     return view('community.economy.product.restore')
    // //         ->with('economy', $economy)
    // //         ->with('product', $product);
    // // }

    // // /**
    // //  * Restore a product.
    // //  *
    // //  * @return Response
    // //  */
    // // public function doRestore($communityId, $economyId, $productId) {
    // //     // TODO: delete trashed, and allow trashing?

    // //     // Get the user, community, find the product
    // //     $user = barauth()->getUser();
    // //     $community = \Request::get('community');
    // //     $economy = $community->economies()->findOrFail($economyId);
    // //     $product = $economy->products()->withTrashed()->findOrFail($productId);

    // //     // Restore the product
    // //     $product->restore();

    // //     // Redirect to the product index
    // //     return redirect()
    // //         ->route('community.economy.product.show', [
    // //             'communityId' => $community->human_id,
    // //             'economyId' => $economy->id,
    // //             'productId' => $product->id,
    // //         ])
    // //         ->with('success', __('pages.products.restored'));
    // // }

    // /**
    //  * Page for confirming the deletion of the payment service.
    //  *
    //  * @return Response
    //  */
    // public function delete($communityId, $economyId, $serviceId) {
    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withDisabled()
    //         ->withTrashed()
    //         ->findOrFail($serviceId);

    //     // TODO: ensure there are no other constraints that prevent deleting the
    //     // product

    //     return view('community.economy.paymentservice.delete')
    //         ->with('economy', $economy)
    //         ->with('service', $service);
    // }

    // /**
    //  * Delete a payment service.
    //  *
    //  * @return Response
    //  */
    // public function doDelete(Request $request, $communityId, $economyId, $serviceId) {
    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withDisabled()
    //         ->withTrashed()
    //         ->findOrFail($serviceId);

    //     // TODO: ensure there are no other constraints that prevent deleting the
    //     // product

    //     // Soft delete
    //     $service->delete();

    //     // Redirect to the payment service index
    //     return redirect()
    //         ->route('community.economy.payservice.index', [
    //             'communityId' => $community->human_id,
    //             'economyId' => $economy->id,
    //         ])
    //         ->with('success', __('pages.paymentService.deleted'));
    // }

    // TODO: set proper perms here!
    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    // TODO: set proper perms here!
    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
