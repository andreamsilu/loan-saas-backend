<?php

namespace App\Modules\Developer\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Developer\Models\ApiUsageLog;
use Illuminate\Http\Request;

class ApiUsageController extends Controller
{
    public function index(Request $request)
    {
        $query = ApiUsageLog::query();

        if ($request->filled('api_key_id')) {
            $query->where('api_key_id', $request->input('api_key_id'));
        }

        $query->orderByDesc('occurred_at');

        return response()->json($query->limit(100)->get());
    }
}

