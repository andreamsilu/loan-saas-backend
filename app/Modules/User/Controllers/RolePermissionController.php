<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\RolePermission;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $perms = RolePermission::where('tenant_id', $tenant->id)->get();

        return response()->json($perms);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
            'permission' => 'required|string',
        ]);

        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $perm = RolePermission::firstOrCreate([
            'tenant_id' => $tenant->id,
            'role' => $request->input('role'),
            'permission' => $request->input('permission'),
        ]);

        return response()->json($perm, 201);
    }

    public function destroy(RolePermission $rolePermission)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || $tenant->id !== $rolePermission->tenant_id) {
            return response()->json(['message' => 'Permission not in your tenant'], 403);
        }

        $rolePermission->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
