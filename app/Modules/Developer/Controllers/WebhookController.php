<?php

namespace App\Modules\Developer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Developer\Models\WebhookEndpoint;
use App\Modules\Developer\Models\WebhookLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function index()
    {
        return response()->json(WebhookEndpoint::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array',
            'events.*' => 'string',
            'active' => 'sometimes|boolean',
            'secret' => 'nullable|string|max:255',
        ]);

        $endpoint = WebhookEndpoint::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'events' => $request->input('events'),
            'active' => $request->boolean('active', true),
            'secret' => $request->input('secret'),
        ]);

        return response()->json($endpoint, 201);
    }

    public function update(Request $request, WebhookEndpoint $endpoint)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'url' => 'sometimes|url',
            'events' => 'sometimes|array',
            'events.*' => 'string',
            'active' => 'sometimes|boolean',
            'secret' => 'nullable|string|max:255',
        ]);

        $endpoint->update($request->only(['name', 'url', 'events', 'active', 'secret']));

        return response()->json($endpoint);
    }

    public function logs(WebhookEndpoint $endpoint)
    {
        $logs = WebhookLog::where('webhook_endpoint_id', $endpoint->id)
            ->orderByDesc('last_attempt_at')
            ->limit(50)
            ->get();

        return response()->json($logs);
    }
}

