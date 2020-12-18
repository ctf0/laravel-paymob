<?php

namespace ctf0\PayMob;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PayMob
{
    protected $auth_token;

    public function __construct()
    {
        $this->auth_token = $this->getAuthToken();
    }

    /* -------------------------------------------------------------------------- */
    /*                                   HELPERS                                  */
    /* -------------------------------------------------------------------------- */

    protected function getConfigKey($key)
    {
        return Arr::get(
            config('paymob.accept'),
            str_replace('..', '.', $key)
        );
    }

    protected function getAmountInCents($amount)
    {
        return $amount * $this->getConfigKey('conversion_rate');
    }

    protected function getCurrency()
    {
        return $this->getConfigKey('currency');
    }

    /* -------------------------------------------------------------------------- */
    /*                                    AUTH                                    */
    /* -------------------------------------------------------------------------- */

    /**
     * 1. https://acceptdocs.paymobsolutions.com/docs/accept-standard-redirect#1-authentication-request.
     */
    protected function getAuthToken()
    {
        $response = Http::post(
            $this->getConfigKey('url.token'),
            ['api_key' => $this->getConfigKey('api_key')]
        )->throw();

        return $response['token'];
    }

    /* -------------------------------------------------------------------------- */
    /*                                    MISC                                    */
    /* -------------------------------------------------------------------------- */

    /**
     * validate hmac for data integrity check.
     *
     * https://acceptdocs.paymobsolutions.com/docs/hmac-calculation.
     *
     * @param string $hmac
     * @param int    $trans_id
     *
     * @return bool
     */
    public function validateHmac($hmac, $trans_id)
    {
        $url = $this->getConfigKey('url.hmac');

        $response = Http::withToken($this->auth_token)
            ->get("$url/$trans_id/hmac_calc")
            ->throw();

        return $response['hmac'] == $hmac ?: abort(400, __('paymob::messages.incorrect_hmac'));
    }

    /**
     * check for minimum amount limits.
     *
     * @param [type] $total
     */
    public function checkForMinAmount($total)
    {
        $min  = $this->getConfigKey('min_amount');
        $curr = $this->getCurrency();

        if ($total < $min) {
            abort(422, __('paymob::messages.min_amount', ['attr' => "$min $curr"]));
        }

        return true;
    }

    /**
     * https://acceptdocs.paymobsolutions.com/docs/refund-transaction.
     *
     * @param int       $trans_id
     * @param float|int $amount
     */
    public function refund($trans_id, $amount)
    {
        $response = Http::withToken($this->auth_token)
            ->post($this->getConfigKey('url.refund'), [
                'transaction_id' => $trans_id,
                'amount_cents'   => $this->getAmountInCents($amount),
            ])
            ->throw();

        return $response;
    }
}
