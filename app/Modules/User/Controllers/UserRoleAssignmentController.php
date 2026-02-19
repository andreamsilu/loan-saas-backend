<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserRoleAssignment;
use Illuminate\Http\Request;

class UserRoleAssignmentController extends Controller
{
    public function index(User $user)
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser->tenant || $authUser->tenant_id !== $user->tenant_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $roles = UserRoleAssignment::where('tenant_id', $authUser->tenant_id)
            ->where('user_id', $user->id)
            ->pluck('role');

        return response()->json([
            'user_id' => $user->id,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $authUser = auth()->user();
        if (!$authUser || !$authUser->tenant || $authUser->tenant_id !== $user->tenant_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $assignment = UserRoleAssignment::firstOrCreate([
            'tenant_id' => $authUser->tenant_id,
            'user_id' => $user->id,
            'role' => $request->input('role'),
        ]);

        return response()->json($assignment, 201);
    }

    public function destroy(User $user, string $role)
    {
        $authUser = auth()->user();
        if (!$authUser || !$authUser->tenant || $authUser->tenant_id !== $user->tenant_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        UserRoleAssignment::where('tenant_id', $authUser->tenant_id)
            ->where('user_id', $user->id)
            ->where('role', $role)
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

