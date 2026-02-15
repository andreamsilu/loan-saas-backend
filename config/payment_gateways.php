<?php

return [
    'providers' => [
        'aggregator' => \App\Shared\Services\Payment\AggregatedMobileMoneyGateway::class,
        'manual' => \App\Shared\Services\Payment\ManualGateway::class,
    ],
];
