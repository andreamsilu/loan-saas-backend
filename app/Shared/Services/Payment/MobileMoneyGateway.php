<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class MobileMoneyGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'MM-' . uniqid(),
            'message' => 'Payment processed via Mobile Money',
        ];
    }
}

