<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Services\DealService;
use Illuminate\Http\Request;

class DealApiController extends Controller
{
    public function __construct(private DealService $dealService) {}

    public function index(Request $request)
    {
        $deals = Deal::orderBy('created_at', 'desc')->paginate(20);
        return response()->json($deals);
    }

    public function pipeline(Request $request)
    {
        $assignedTo = $request->input('assigned_to');
        $pipeline = $this->dealService->getPipelineData($assignedTo);
        return response()->json($pipeline);
    }

    public function updateStage(Request $request, string $id)
    {
        $deal = Deal::findOrFail($id);
        $request->validate(['stage_id' => 'required|string']);

        $this->dealService->updateStage($deal, $request->input('stage_id'), auth()->user());

        return response()->json(['success' => true, 'deal' => $deal]);
    }
}
