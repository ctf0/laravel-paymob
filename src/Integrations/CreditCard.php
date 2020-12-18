<?php

namespace ctf0\PayMob\Integrations;

class CreditCard extends Accept
{
    public function getPaymentTypeName(): string
    {
        return 'card_payment';
    }

    /**
     * finish checkout process.
     *
     * https://acceptdocs.paymobsolutions.com/docs/card-payments
     *
     * @param float|int $total
     * @param array     $items
     */
    public function checkOut($total, $items = [])
    {
        $this->checkForMinAmount($total);

        $order_id      = $this->orderRegistration($items, $total);
        $payment_token = $this->paymentKeyRequest($order_id, $total);

        return $this->getConfigKey($this->getPaymentTypeConfig('url')) .
                '?' .
                http_build_query(['payment_token' => $payment_token]);
    }
}
