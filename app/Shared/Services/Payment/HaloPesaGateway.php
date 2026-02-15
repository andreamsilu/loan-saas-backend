<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class HaloPesaGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'HALOPESA-' . uniqid(),
            'message' => 'Payment processed via HaloPesa',
        ];
    }
}

