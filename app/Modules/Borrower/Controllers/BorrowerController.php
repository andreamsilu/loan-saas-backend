<?php

namespace App\Modules\Borrower\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Borrower\Models\Borrower;
use App\Modules\Borrower\Services\BorrowerService;
use Illuminate\Http\Request;

class BorrowerController extends Controller
{
    protected $borrowerService;

    public function __construct(BorrowerService $borrowerService)
    {
        $this->borrowerService = $borrowerService;
    }

    public function index()
    {
        return response()->json($this->borrowerService->getTenantBorrowers());
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:50',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'metadata' => 'nullable|array',
        ]);

        $borrower = $this->borrowerService->createBorrower($request->all());

        return response()->json($borrower, 201);
    }

    public function show(Borrower $borrower)
    {
        return response()->json($borrower);
    }

    public function update(Request $request, Borrower $borrower)
    {
        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'id_number' => 'sometimes|string|max:50',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string|max:20',
        ]);

        $updatedBorrower = $this->borrowerService->updateBorrower($borrower, $request->all());

        return response()->json($updatedBorrower);
    }

    public function blacklist(Borrower $borrower)
    {
        $blacklisted = $this->borrowerService->blacklistBorrower($borrower);
        return response()->json($blacklisted);
    }
}
