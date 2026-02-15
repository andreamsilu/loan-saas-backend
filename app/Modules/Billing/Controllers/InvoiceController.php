<?php

namespace App\Modules\Billing\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json(
            Invoice::orderByDesc('created_at')->get()
        );
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => auth()->user()->tenant_id,
            'subscription_id' => $request->input('subscription_id'),
            'amount' => $request->input('amount'),
            'tax' => $request->input('tax', 0),
            'status' => 'unpaid',
            'due_date' => $request->input('due_date'),
            'metadata' => $request->input('metadata'),
        ]);

        return response()->json($invoice, 201);
    }

    public function markPaid(Request $request, Invoice $invoice)
    {
        $request->validate([
            'paid_at' => 'nullable|date',
            'metadata' => 'nullable|array',
        ]);

        $data = [
            'status' => 'paid',
            'paid_at' => $request->input('paid_at', now()),
        ];

        if ($request->has('metadata')) {
            $meta = $invoice->metadata ?? [];
            $meta = array_merge($meta, $request->input('metadata', []));
            $data['metadata'] = $meta;
        }

        $invoice->update($data);

        if ($invoice->subscription_id) {
            $subscription = $invoice->subscription;
            if ($subscription && in_array($subscription->status, ['trial', 'suspended', 'expired'], true)) {
                $subscription->status = 'active';
                $subscription->save();
            }
        }

        return response()->json($invoice);
    }

    public function dashboard()
    {
        $query = Invoice::query();

        $totalAmount = (clone $query)->sum('amount');
        $totalTax = (clone $query)->sum('tax');
        $unpaidAmount = (clone $query)->where('status', 'unpaid')->sum('amount');
        $unpaidCount = (clone $query)->where('status', 'unpaid')->count();
        $paidAmount = (clone $query)->where('status', 'paid')->sum('amount');

        return response()->json([
            'total_amount' => $totalAmount,
            'total_tax' => $totalTax,
            'unpaid_amount' => $unpaidAmount,
            'unpaid_count' => $unpaidCount,
            'paid_amount' => $paidAmount,
        ]);
    }
}
