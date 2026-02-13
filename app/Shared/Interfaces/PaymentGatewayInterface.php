<?php

namespace App\Shared\Interfaces;

interface PaymentGatewayInterface
{
    /**
     * Process a payment.
     *
     * @param float $amount
     * @param array $details
     * @return array [success => bool, reference => string, message => string]
     */
    public function process(float $amount, array $details): array;
}
