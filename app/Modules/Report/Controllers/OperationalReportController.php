<?php

namespace App\Modules\Report\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Report\Services\OperationalReportService;
use App\Shared\Enums\UserRole;
use Illuminate\Http\Request;

class OperationalReportController extends Controller
{
    protected $reportService;

    public function __construct(OperationalReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function dashboard()
    {
        return response()->json($this->reportService->getDashboardStats());
    }

    public function disbursementTrends()
    {
        return response()->json($this->reportService->getLoanDisbursementTrends());
    }
}
