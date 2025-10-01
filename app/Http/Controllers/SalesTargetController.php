<?php

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use Illuminate\Http\Request;

class SalesTargetController extends Controller
{
    // GET /api/sales-targets
    public function index() {
        try {
            $salesTargets = SalesTarget::orderBy('targetId', 'desc')
                ->get()
                ->map(function ($target) {
                    return [
                        'targetId' => $target->targetId,
                        'salesRepId' => $target->salesRepId,
                        'year' => $target->year,
                        'premiumTarget' => (float) $target->premiumTarget,
                        'salesCounselorTarget' => (int) $target->salesCounselorTarget,
                        'policySoldTarget' => (int) $target->policySoldTarget,
                        'agencyCoopTarget' => (int) $target->agencyCoopTarget,
                        'createdBy' => (int) $target->createdBy,
                        'createdAt' => $target->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($target->created_at)) : null,
                        'updatedAt' => $target->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($target->updated_at)) : null,
                    ];
                });
            return response()->json([
                'success' => true,
                'data' => $salesTargets,
                'message' => 'Sales targets retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales targets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/sales-targets/{id}
    public function show($id) {
        try {
            $target = SalesTarget::findOrFail($id);
            $formattedTarget = [
                'targetId' => $target->targetId,
                'salesRepId' => $target->salesRepId,
                'year' => $target->year,
                'premiumTarget' => (float) $target->premiumTarget,
                'salesCounselorTarget' => (int) $target->salesCounselorTarget,
                'policySoldTarget' => (int) $target->policySoldTarget,
                'agencyCoopTarget' => (int) $target->agencyCoopTarget,
                'createdBy' => (int) $target->createdBy,
                'createdAt' => $target->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($target->created_at)) : null,
                'updatedAt' => $target->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($target->updated_at)) : null,
            ];
            return response()->json([
                'success' => true,
                'data' => $formattedTarget,
                'message' => 'Sales target retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sales target not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // POST /api/sales-targets
    public function store(Request $request) {
        $salesTarget = SalesTarget::create($request->all());
        return response()->json($salesTarget, 201);
    }

    // PUT /api/sales-targets/{id}
    public function update(Request $request, $id) {
        $salesTarget = SalesTarget::findOrFail($id);
        $salesTarget->update($request->all());
        return response()->json($salesTarget);
    }

    // DELETE /api/sales-targets/{id}
    public function destroy($id) {
        $salesTarget = SalesTarget::findOrFail($id);
        $salesTarget->delete();
        return response()->json(null, 204);
    }
}
