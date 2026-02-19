<?php

namespace App\Shared\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (method_exists($user, 'hasPermission') && $user->hasPermission($permission)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden. Missing permission.'], 403);
    }
}

