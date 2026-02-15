<?php

namespace App\Modules\Developer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Developer\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        return response()->json(ApiKey::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'nullable|array',
        ]);

        [$key, $token] = ApiKey::createWithToken([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->input('name'),
            'scopes' => $request->input('scopes', []),
        ]);

        return response()->json(['key' => $key, 'token' => $token], 201);
    }

    public function rotate(ApiKey $apiKey)
    {
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $token = \Illuminate\Support\Str::random(48);
        $apiKey->update(['token_hash' => hash('sha256', $token)]);
        return response()->json(['key' => $apiKey, 'token' => $token]);
    }

    public function revoke(ApiKey $apiKey)
    {
        if ($apiKey->tenant_id !== auth()->user()->tenant_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $apiKey->update(['active' => false]);
        return response()->json($apiKey);
    }
}

