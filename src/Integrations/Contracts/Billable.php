<?php

namespace ctf0\PayMob\Integrations\Contracts;

interface Billable
{
    public function getBillingData(): array;
}
