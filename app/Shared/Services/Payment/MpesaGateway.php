<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class MpesaGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'MPESA-' . uniqid(),
            'message' => 'Payment processed via M-Pesa',
        ];
    }
}

