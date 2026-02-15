<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class AirtelMoneyGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'AIRTEL-' . uniqid(),
            'message' => 'Payment processed via Airtel Money',
        ];
    }
}

