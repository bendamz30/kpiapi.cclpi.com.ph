<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    // GET /api/areas
    public function index() {
        try {
            $areas = Area::orderBy('areaName', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $areas,
                'message' => 'Areas retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve areas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/areas
    public function store(Request $request) {
        // Optional: validate input
        $request->validate([
            'areaName' => 'required|string|max:255'
        ]);

        $area = Area::create([
            'areaName' => $request->areaName
        ]);

        return response()->json($area, 201);
    }

    // GET /api/areas/{id}
    public function show($id) {
        return Area::findOrFail($id);
    }

    // PUT /api/areas/{id}
    public function update(Request $request, $id) {
        $area = Area::findOrFail($id);
        $area->update($request->all());
        return response()->json($area);
    }

    // DELETE /api/areas/{id}
    public function destroy($id) {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(null, 204);
    }
}
