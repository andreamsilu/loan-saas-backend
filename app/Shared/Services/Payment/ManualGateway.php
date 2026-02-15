<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class ManualGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'MANUAL-' . uniqid(),
            'message' => 'Payment recorded manually',
        ];
    }

    public function disburse(float $amount, array $details): array
    {
        return [
            'success' => true,
            'reference' => 'PAYOUT-' . uniqid(),
            'message' => 'Disbursement recorded manually',
        ];
    }
}
