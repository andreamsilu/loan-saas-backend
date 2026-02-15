<?php

namespace App\Shared\Services\Payment;

use App\Shared\Interfaces\PaymentGatewayInterface;
use Exception;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        $key = strtolower(preg_replace('/[^a-z0-9]+/', '', $gateway));
        return match ($key) {
            'mpesa' => new AggregatedMobileMoneyGateway(),
            'airtelmoney' => new AggregatedMobileMoneyGateway(),
            'mixxbyyas', 'mixx' => new AggregatedMobileMoneyGateway(),
            'halopesa' => new AggregatedMobileMoneyGateway(),
            'mobilemoney' => new AggregatedMobileMoneyGateway(),
            'aggregator', 'unified', 'mobilemoneyaggregator' => new AggregatedMobileMoneyGateway(),
            'manual' => new ManualGateway(),
            default => new ManualGateway(),
        };
    }
}
