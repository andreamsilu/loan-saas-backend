<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class MixxByYasGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'MIXX-' . uniqid(),
            'message' => 'Payment processed via Mixx by YAS',
        ];
    }
}

