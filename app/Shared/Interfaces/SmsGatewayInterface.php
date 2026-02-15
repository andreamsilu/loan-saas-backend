<?php

namespace App\Shared\Interfaces;

interface SmsGatewayInterface
{
    public function send(string $to, string $message): array;
}

