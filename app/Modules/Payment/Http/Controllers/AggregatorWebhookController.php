<?php

namespace App\Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Shared\Services\Payment\AggregatorClient;
use App\Modules\Transaction\Models\Transaction;
use Illuminate\Support\Facades\Log;

class AggregatorWebhookController extends Controller
{
    public function ipn(Request $request, AggregatorClient $client)
    {
        try {
            $trackingId = $request->input('orderTrackingId') ?? $request->input('order_tracking_id') ?? $request->query('orderTrackingId');
            if (!$trackingId) {
                return response()->json(['ok' => false, 'error' => 'missing_tracking_id'], 400);
            }
            $status = $client->getStatus($trackingId);
            $tx = Transaction::where('reference', $trackingId)->first();
            if ($tx) {
                $meta = $tx->metadata ?? [];
                $meta['aggregator_status'] = $status['status'] ?? ($status['payment_status'] ?? null);
                $tx->update(['metadata' => $meta]);
            }
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            Log::error('Aggregator IPN error', ['message' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => 'internal_error'], 500);
        }
    }
}
