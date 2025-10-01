<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $regions = Region::orderBy('regionName', 'asc')->get();
            return response()->json([
                'success' => true,
                'data' => $regions,
                'message' => 'Regions retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve regions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'regionName' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $region = Region::create($validated);
            return response()->json([
                'success' => true,
                'data' => $region,
                'message' => 'Region created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $region = Region::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $region,
                'message' => 'Region retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Region not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $region = Region::findOrFail($id);
            $validated = $request->validate([
                'regionName' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $region->update($validated);
            return response()->json([
                'success' => true,
                'data' => $region,
                'message' => 'Region updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update region',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $region = Region::findOrFail($id);
            $region->delete();
            return response()->json([
                'success' => true,
                'message' => 'Region deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete region',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
