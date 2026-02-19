<?php

namespace App\Shared\Interfaces;

interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $body): void;
}

