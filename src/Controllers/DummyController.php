<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ctf0\PayMob\Facades\PayMob;
use ctf0\PayMob\Integrations\CreditCard;
use Illuminate\Http\Client\RequestException;

class DummyController extends Controller
{
    /**
     * show the order details to the user.
     *
     * @return \Illuminate\View\View
     */
    public function checkOut()
    {
        return view('paymob::checkout');
    }

    /**
     * process the order on the gateway side.
     */
    public function process(Request $request)
    {
        $request->validate([
            'payment_type' => [
                'required',
                'string',
            ],
        ]);

        $payment_type = $request->payment_type;
        $user         = $request->user();
        $total        = 0; // order total

        try {
            return (new CreditCard($user))->checkOut($total); // or MobileWallet, etc..
        } catch (RequestException $e) {
            return __('something went wrong, please try again later');
        }
    }

    /**
     * validate and complete the order.
     *
     * https://acceptdocs.paymobsolutions.com/docs/transaction-callbacks#transaction-response-callback.
     *
     * @return \Illuminate\View\View
     */
    public function complete(Request $request)
    {
        PayMob::validateHmac($request->hmac, $request->id);

        // save the transaction data to the server
        $data = $request->all();

        return view('paymob::complete');
    }
}
