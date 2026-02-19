<?php

namespace App\Modules\User\Models;

use App\Shared\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'role',
        'permission',
    ];
}

