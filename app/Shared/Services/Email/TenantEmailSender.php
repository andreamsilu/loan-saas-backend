<?php

namespace App\Shared\Services\Email;

use App\Shared\Interfaces\EmailSenderInterface;
use App\Shared\Services\TenantManager;
use Illuminate\Support\Facades\Mail;

class TenantEmailSender implements EmailSenderInterface
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function send(string $to, string $subject, string $body): void
    {
        $tenant = $this->tenantManager->getTenant();

        if (!$tenant) {
            throw new \RuntimeException('Email configuration not found for current tenant.');
        }

        $settings = $tenant->settings ?? [];
        $branding = $settings['branding'] ?? [];
        $support = $branding['support_contact'] ?? [];

        if (!is_array($support) || empty($support['email'])) {
            throw new \RuntimeException('Email configuration not found for current tenant.');
        }

        $fromEmail = $support['email'];
        $fromName = $branding['company_name'] ?? ($support['name'] ?? null);

        Mail::raw($body, function ($message) use ($to, $subject, $fromEmail, $fromName) {
            $message->to($to)->subject($subject);
            if ($fromEmail) {
                $message->from($fromEmail, $fromName);
            }
        });
    }
}
