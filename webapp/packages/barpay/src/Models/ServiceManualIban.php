<?php

namespace BarPay\Models;

use App\Models\Currency;
use BarPay\Controllers\ServiceManualIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Manual IBAN service class.
 *
 * This represents a payment service for a manual IBAN transfer.
 *
 * @property int id
 * @property string account_holder Name of the account holder.
 * @property string iban IBAN to transfer to.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ServiceManualIban extends Model {

    use Serviceable;

    protected $table = "service_manual_iban";

    /**
     * The controller to use for this service.
     */
    public const CONTROLLER = ServiceManualIbanController::class;

    /**
     * The payment model for this service.
     */
    public const PAYMENT_MODEL = PaymentManualIban::class;

    /**
     * The root for language values related to this service.
     */
    public const LANG_ROOT = 'barpay::service.manualiban';

    /**
     * The root for views related to this service.
     */
    public const VIEW_ROOT = 'barpay::service.manualiban';

    /**
     * Block direclty deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete serviceable, delete the owning service instead');
    }

    /**
     * Check whether the given currency is supported.
     *
     * @param Currency $currency The currenty to check.
     *
     * @return bool True if supported, false if not.
     */
    public static function isSupportedCurrency(Currency $currency) {
        return true;
    }
}
