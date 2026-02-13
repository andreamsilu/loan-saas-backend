<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class StripeGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        // Mocking Stripe payment logic
        return [
            'success' => true,
            'reference' => 'STRIPE-' . uniqid(),
            'message' => 'Payment processed successfully via Stripe',
        ];
    }
}
