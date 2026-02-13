<?php

namespace App\Shared\Enums;

enum UserRole: string
{
    case OWNER = 'owner';
    case TENANT_ADMIN = 'tenant_admin';
    case STAFF = 'staff';
    case BORROWER = 'borrower';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
