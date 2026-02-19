<?php

namespace App\Modules\User\Models;

use App\Modules\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class UserRoleAssignment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
    ];
}

