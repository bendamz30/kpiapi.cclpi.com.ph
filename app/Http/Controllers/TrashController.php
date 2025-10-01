<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeletedUser;
use App\Models\DeletedSalesReport;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrashController extends Controller
{
    /**
     * Get all deleted users with pagination
     */
    public function getDeletedUsers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', '');
            
            $query = DeletedUser::with('deletedBy')
                ->orderBy('deleted_at', 'desc');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }
            
            $deletedUsers = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $deletedUsers,
                'message' => 'Deleted users retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving deleted users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deleted users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all deleted sales reports with pagination
     */
    public function getDeletedSalesReports(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search', '');
            
            $query = DeletedSalesReport::with(['deletedBy', 'salesRep', 'createdByUser'])
                ->orderBy('deleted_at', 'desc');
            
            if ($search) {
                $query->whereHas('salesRep', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $deletedReports = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $deletedReports,
                'message' => 'Deleted sales reports retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving deleted sales reports: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deleted sales reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a deleted user
     */
    public function restoreUser(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $deletedUser = DeletedUser::findOrFail($id);
            
            // Check if user with same email already exists
            $existingUser = User::where('email', $deletedUser->email)->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'A user with this email already exists'
                ], 400);
            }
            
            // Check if there are sales reports for this user
            $salesReportsCount = Sale::where('salesRepId', $deletedUser->original_user_id)->count();
            
            // Create new user from deleted user data
            $user = User::create([
                'name' => $deletedUser->name,
                'email' => $deletedUser->email,
                'username' => $deletedUser->username,
                'contact_number' => $deletedUser->contact_number,
                'address' => $deletedUser->address,
                'profile_picture' => $deletedUser->profile_picture,
                'passwordHash' => $deletedUser->passwordHash,
                'role' => $deletedUser->role,
                'regionId' => $deletedUser->regionId,
                'areaId' => $deletedUser->areaId,
                'salesTypeId' => $deletedUser->salesTypeId,
            ]);
            
            // Update sales reports to point to the new user ID
            if ($salesReportsCount > 0) {
                Sale::where('salesRepId', $deletedUser->original_user_id)
                    ->update(['salesRepId' => $user->userId]);
            }
            
            // Delete from trash
            $deletedUser->delete();
            
            DB::commit();
            
            $message = 'User restored successfully';
            if ($salesReportsCount > 0) {
                $message .= ". {$salesReportsCount} sales report(s) re-linked to restored user.";
            }
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => $message,
                're_linked_sales_reports' => $salesReportsCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a deleted sales report
     */
    public function restoreSalesReport(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            $deletedReport = DeletedSalesReport::findOrFail($id);
            
            // Check if sales rep still exists
            $salesRep = User::find($deletedReport->salesRepId);
            if (!$salesRep) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales representative no longer exists'
                ], 400);
            }
            
            // Create new sales report from deleted report data
            $salesReport = Sale::create([
                'salesRepId' => $deletedReport->salesRepId,
                'reportDate' => $deletedReport->reportDate,
                'premiumActual' => $deletedReport->premiumActual,
                'salesCounselorActual' => $deletedReport->salesCounselorActual,
                'policySoldActual' => $deletedReport->policySoldActual,
                'agencyCoopActual' => $deletedReport->agencyCoopActual,
                'createdBy' => $deletedReport->createdBy,
            ]);
            
            // Delete from trash
            $deletedReport->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $salesReport,
                'message' => 'Sales report restored successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring sales report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a user from trash
     */
    public function permanentlyDeleteUser($id)
    {
        try {
            $deletedUser = DeletedUser::findOrFail($id);
            
            // Delete profile picture if exists
            if ($deletedUser->profile_picture) {
                \Storage::disk('public')->delete($deletedUser->profile_picture);
            }
            
            $deletedUser->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User permanently deleted'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error permanently deleting user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete a sales report from trash
     */
    public function permanentlyDeleteSalesReport($id)
    {
        try {
            $deletedReport = DeletedSalesReport::findOrFail($id);
            $deletedReport->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Sales report permanently deleted'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error permanently deleting sales report: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to permanently delete sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trash statistics
     */
    public function getTrashStats()
    {
        try {
            $stats = [
                'deleted_users_count' => DeletedUser::count(),
                'deleted_sales_reports_count' => DeletedSalesReport::count(),
                'total_deleted_count' => DeletedUser::count() + DeletedSalesReport::count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Trash statistics retrieved successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error retrieving trash statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve trash statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}