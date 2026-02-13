<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;

class PaypalGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details): array
    {
        // Mocking PayPal payment logic
        return [
            'success' => true,
            'reference' => 'PAYPAL-' . uniqid(),
            'message' => 'Payment processed successfully via PayPal',
        ];
    }
}
