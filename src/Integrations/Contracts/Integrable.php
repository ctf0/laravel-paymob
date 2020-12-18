<?php

namespace ctf0\PayMob\Integrations\Contracts;

interface Integrable
{
    public function getPaymentTypeName(): string;
}
