<?php

namespace App\Http\Controllers\Callbacks;

use App\Helpers\ValidationDefaults;
use App\Jobs\ProcessBunqAccountEvents;
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
use bunq\Model\Generated\Object\NotificationUrl;
use bunq\Model\Generated\Object\Pointer;
use bunq\Util\BunqEnumApiEnvironmentType;

class BunqController extends Controller {

    /**
     * bunq callback index.
     *
     * @return Response
     */
    public function index(Request $request) {
        // Get the JSON body content as string, return if no data is given
        $json = $request->getContent();
        if(empty(trim($json)))
            return 'OK';

        // Get the NotificationUrl section to parse
        $json = json_encode(json_decode($json, true)['NotificationUrl']);

        // Create the NotificationUrl object
        $notification = NotificationUrl::createFromJsonString($json);
        $category = $notification->getCategory();
        $object = $notification->getObject();

        // Only handle specific types
        if($category != 'PAYMENT' && $category != 'BUNQME_TAB')
            return 'OK';

        // Handle the notification, update bunq events for its monetary account
        if(($payment = $object->getPayment()) != null)
            Self::processEventsForAccount($payment->getMonetaryAccountId());
        else if(($bunqMeTab = $object->getBunqMeTab()) != null)
            Self::processEventsForAccount($bunqMeTab->getMonetaryAccountId());
        else
            throw new \Exception('Unhandled notification type');

        return 'OK';
    }

    /**
     * Process all new events for the given monetary account ID through bunq.
     *
     * If no linked bunq account is found for the given monetary account ID it
     * is ignored. Notifications are commonly received for monetary accounts not
     * under control by BARbapAPPa.
     *
     * @param int $monetaryAccountId The ID of the monetary account.
     */
    // TODO: differentiate by production/sandbox account IDs
    private static function processEventsForAccount(int $monetaryAccountId) {
        // Find the account, skip if it is not linked
        // TODO: handle deleted accounts here as well?
        $account = BunqAccount::where('monetary_account_id', $monetaryAccountId)->first();
        if($account == null)
            return;

        // Dispatch a job for processing all new events on this monetary account
        ProcessBunqAccountEvents::dispatch($account);
    }
}