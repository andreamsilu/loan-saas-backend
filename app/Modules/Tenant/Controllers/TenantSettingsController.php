<?php

namespace App\Modules\Tenant\Controllers;

use App\Http\Controllers\Controller;
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
}

