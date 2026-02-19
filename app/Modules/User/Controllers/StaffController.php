<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $users = User::where('tenant_id', $tenant->id)
            ->whereIn('role', [
                UserRole::STAFF->value,
                UserRole::TENANT_ADMIN->value,
            ])
            ->get();

        return response()->json($users);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:' . implode(',', [
                UserRole::STAFF->value,
                UserRole::TENANT_ADMIN->value,
            ]),
        ]);

        $actor = auth()->user();
        if (!$actor->tenant || $actor->tenant_id !== $user->tenant_id) {
            return response()->json(['message' => 'User not in your tenant'], 403);
        }

        if ($user->isOwner()) {
            return response()->json(['message' => 'Cannot change owner role'], 403);
        }

        $user->role = $request->input('role');
        $user->save();

        return response()->json($user);
    }
}

