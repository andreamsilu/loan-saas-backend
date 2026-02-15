<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class AggregatedMobileMoneyGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        $operator = $details['operator'] ?? 'aggregator';
        return [
            'success' => true,
            'reference' => strtoupper($operator) . '-' . uniqid(),
            'message' => 'Payment processed via aggregated mobile money',
        ];
    }
}

