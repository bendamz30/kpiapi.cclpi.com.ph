<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the sales records.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $sales = Sale::with(['salesRep.salesType'])
                ->orderBy('reportDate', 'desc')
                ->get()
                ->map(function ($sale) {
                    return [
                        'reportId' => $sale->reportId,
                        'salesRepId' => $sale->salesRepId,
                        'salesRepName' => $sale->salesRep ? $sale->salesRep->name : 'Unknown',
                        'reportDate' => $sale->reportDate,
                        'salesTypeId' => $sale->salesRep ? $sale->salesRep->salesTypeId : null,
                        'salesTypeName' => $sale->salesRep && $sale->salesRep->salesType ? $sale->salesRep->salesType->salesTypeName : 'Unknown',
                        'premiumActual' => (float) $sale->premiumActual,
                        'salesCounselorActual' => (int) $sale->salesCounselorActual,
                        'policySoldActual' => (int) $sale->policySoldActual,
                        'agencyCoopActual' => (int) $sale->agencyCoopActual,
                        'createdBy' => (int) $sale->createdBy,
                        'createdAt' => $sale->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($sale->created_at)) : null,
                        'updatedAt' => $sale->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($sale->updated_at)) : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sales,
                'message' => 'Sales records retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales records',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created sale record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'salesRepId' => 'required|integer|exists:users,userId',
                'reportDate' => 'required|date',
                'premiumActual' => 'required|numeric|min:0',
                'salesCounselorActual' => 'required|integer|min:0',
                'policySoldActual' => 'required|integer|min:0',
                'agencyCoopActual' => 'required|integer|min:0',
                'createdBy' => 'required|integer|exists:users,userId',
            ]);

            $sale = Sale::create($validated);

            $formattedSale = [
                'reportId' => $sale->reportId,
                'salesRepId' => $sale->salesRepId,
                'reportDate' => $sale->reportDate,
                'premiumActual' => (float) $sale->premiumActual,
                'salesCounselorActual' => (int) $sale->salesCounselorActual,
                'policySoldActual' => (int) $sale->policySoldActual,
                'agencyCoopActual' => (int) $sale->agencyCoopActual,
                'createdBy' => (int) $sale->createdBy,
                'createdAt' => $sale->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($sale->created_at)) : null,
                'updatedAt' => $sale->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($sale->updated_at)) : null,
                'deletedBy' => $sale->deletedBy ? (int) $sale->deletedBy : null,
            ];
            
            return response()->json([
                'success' => true,
                'data' => $formattedSale,
                'message' => 'Sale record created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sale record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $sale = Sale::with(['salesRep', 'creator'])
                ->where('reportId', $id)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale record not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Sale record retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sale record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified sale record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $sale = Sale::where('reportId', $id)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale record not found'
                ], 404);
            }

            $validated = $request->validate([
                'salesRepId' => 'sometimes|integer|exists:users,userId',
                'reportDate' => 'sometimes|date',
                'premiumActual' => 'sometimes|numeric|min:0',
                'salesCounselorActual' => 'sometimes|integer|min:0',
                'policySoldActual' => 'sometimes|integer|min:0',
                'agencyCoopActual' => 'sometimes|integer|min:0',
            ]);

            $sale->update($validated);

            return response()->json([
                'success' => true,
                'data' => $sale->load(['salesRep', 'creator']),
                'message' => 'Sale record updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sale record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified sale record (move to trash).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $sale = Sale::where('reportId', $id)
                ->first();

            if (!$sale) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale record not found'
                ], 404);
            }

            // Move sale report to trash before deleting
            \App\Models\DeletedSalesReport::create([
                'original_report_id' => $sale->reportId,
                'salesRepId' => $sale->salesRepId,
                'reportDate' => $sale->reportDate,
                'premiumActual' => $sale->premiumActual,
                'salesCounselorActual' => $sale->salesCounselorActual,
                'policySoldActual' => $sale->policySoldActual,
                'agencyCoopActual' => $sale->agencyCoopActual,
                'createdBy' => $sale->createdBy,
                'deleted_by' => 1, // TODO: Get from authenticated user
                'deleted_at' => now(),
                'created_at' => $sale->created_at,
                'updated_at' => $sale->updated_at,
            ]);

            // Now permanently delete the sale record
            $sale->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sale record moved to trash successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
