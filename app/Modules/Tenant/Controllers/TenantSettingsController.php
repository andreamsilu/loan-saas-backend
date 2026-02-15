<?php

namespace App\Modules\Tenant\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\Models\Tenant;
use Illuminate\Http\Request;

class TenantSettingsController extends Controller
{
    public function updateBranding(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|string',
            'favicon' => 'nullable|string',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'company_name' => 'nullable|string',
            'support_contact' => 'nullable|array',
            'email_templates' => 'nullable|array',
            'sms_templates' => 'nullable|array',
        ]);

        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];
        $settings['branding'] = array_merge($settings['branding'] ?? [], $request->only([
            'logo',
            'favicon',
            'primary_color',
            'secondary_color',
            'company_name',
            'support_contact',
            'email_templates',
            'sms_templates',
        ]));
        $tenant->update(['settings' => $settings]);

        return response()->json($tenant->settings);
    }

    public function updateUiFlags(Request $request)
    {
        $request->validate([
            'borrower_self_registration' => 'sometimes|boolean',
            'manual_loan_approval' => 'sometimes|boolean',
            'automatic_disbursement' => 'sometimes|boolean',
            'late_fee_automation' => 'sometimes|boolean',
            'multi_level_approval' => 'sometimes|boolean',
        ]);

        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];
        $settings['ui_flags'] = array_merge($settings['ui_flags'] ?? [], $request->all());
        $tenant->update(['settings' => $settings]);

        return response()->json($tenant->settings);
    }

    public function updateSmsConfig(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|in:nextsms',
            'api_key' => 'required|string',
            'secret_key' => 'required|string',
            'from' => 'required|string',
            'base_url' => 'nullable|string',
        ]);

        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];
        $settings['sms'] = $request->only([
            'provider',
            'api_key',
            'secret_key',
            'from',
            'base_url',
        ]);
        $tenant->update(['settings' => $settings]);

        return response()->json($tenant->settings);
    }

    public function updateDomain(Request $request)
    {
        $request->validate([
            'subdomain' => 'nullable|string|max:255|unique:tenants,subdomain,' . auth()->user()->tenant_id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . auth()->user()->tenant_id,
        ]);

        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $tenant->subdomain = $request->input('subdomain', $tenant->subdomain);
        $tenant->domain = $request->input('domain', $tenant->domain);
        $tenant->save();

        return response()->json($tenant);
    }
}
