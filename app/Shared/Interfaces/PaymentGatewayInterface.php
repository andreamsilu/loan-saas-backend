<?php

namespace App\Shared\Interfaces;

interface PaymentGatewayInterface
{
    public function process(float $amount, array $details): array;
    public function disburse(float $amount, array $details): array;
}
