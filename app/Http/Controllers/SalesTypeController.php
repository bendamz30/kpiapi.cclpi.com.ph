<?php

namespace App\Http\Controllers;

use App\Models\SalesType;
use Illuminate\Http\Request;

class SalesTypeController extends Controller
{
    // GET /api/sales-types
    public function index() {
        try {
            $salesTypes = SalesType::orderBy('salesTypeName', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $salesTypes,
                'message' => 'Sales types retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/sales-types
    public function store(Request $request) {
        $salesType = SalesType::create($request->all());
        return response()->json($salesType, 201);
    }

    // GET /api/sales-types/{id}
    public function show($id) {
        return SalesType::findOrFail($id);
    }

    // PUT /api/sales-types/{id}
    public function update(Request $request, $id) {
        $salesType = SalesType::findOrFail($id);
        $salesType->update($request->all());
        return response()->json($salesType);
    }

    // DELETE /api/sales-types/{id}
    public function destroy($id) {
        $salesType = SalesType::findOrFail($id);
        $salesType->delete();
        return response()->json(null, 204);
    }
}
