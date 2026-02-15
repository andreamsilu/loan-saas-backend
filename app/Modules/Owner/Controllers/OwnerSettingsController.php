<?php

namespace App\Modules\Owner\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Owner\Models\GlobalSetting;
use Illuminate\Http\Request;

class OwnerSettingsController extends Controller
{
    public function index()
    {
        $settings = GlobalSetting::all()
            ->pluck('value', 'key');

        return response()->json($settings);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($data['settings'] as $key => $value) {
            GlobalSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $settings = GlobalSetting::all()
            ->pluck('value', 'key');

        return response()->json($settings);
    }
}

