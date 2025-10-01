<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SalesTypeController;
use App\Http\Controllers\SalesTargetController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TrashController;

Route::apiResource('areas', AreaController::class);
Route::apiResource('regions', RegionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('sales-types', SalesTypeController::class);
Route::apiResource('sales-targets', SalesTargetController::class);
Route::apiResource('sales', SaleController::class);

// Authentication routes
Route::post('login', [UserController::class, 'login']);

// Change password route
Route::post('users/{user}/change-password', [UserController::class, 'changePassword']);

// Fix viewer password route
Route::post('fix-viewer-password/{email}', [UserController::class, 'fixViewerPassword']);

// Update all passwords to cclpi route
Route::post('update-all-passwords-cclpi', [UserController::class, 'updateAllPasswordsToCclpi']);

// Test user creation endpoint
Route::post('test-user-creation', function (Request $request) {
    try {
        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'Viewer',
            'passwordHash' => password_hash('cclpi', PASSWORD_DEFAULT)
        ];
        
        $user = \App\Models\User::create($testData);
        
        return response()->json([
            'success' => true,
            'message' => 'Test user created successfully',
            'data' => $user
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating test user',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Reset password route
Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword']);

// Handle FormData with _method=PUT for user updates (file uploads)
Route::post('users/{user}', [UserController::class, 'update']);


// Dynamic KPI target calculation route
Route::get('users/{user}/kpi-target', [UserController::class, 'getKpiTarget']);

// Regional KPI target aggregation route
Route::get('regions/{region}/kpi-target', [UserController::class, 'getRegionalKpiTarget']);

// Area KPI target aggregation route
Route::get('areas/{area}/kpi-target', [UserController::class, 'getAreaKpiTarget']);

// Hierarchical KPI target aggregation route
Route::get('kpi-targets', [UserController::class, 'getHierarchicalKpiTargets']);

// Sample test route
Route::get('/', function () {
    return response()->json([
        'message' => 'Hello from Laravel API!',
        'status' => 'success'
    ]);
});

// Test login route
Route::post('test-login', function (Request $request) {
    return response()->json([
        'message' => 'Test login route working',
        'data' => $request->all()
    ]);
});

// Fix profile picture paths
Route::get('fix-profile-pictures', function () {
    try {
        $users = \App\Models\User::whereNotNull('profile_picture')->get();
        $storagePath = storage_path('app/public/profile_pictures/');
        $files = glob($storagePath . '*');
        
        $results = [];
        
        foreach ($users as $user) {
            $currentPath = $user->profile_picture;
            $currentUrl = asset('storage/' . $currentPath);
            
            // Check if current path is accessible
            $headers = @get_headers($currentUrl);
            $isAccessible = $headers && strpos($headers[0], '200') !== false;
            
            if (!$isAccessible && !empty($files)) {
                // Update to first available file
                $newFileName = basename($files[0]);
                $newPath = 'profile_pictures/' . $newFileName;
                
                $user->profile_picture = $newPath;
                $user->save();
                
                $results[] = [
                    'userId' => $user->userId,
                    'name' => $user->name,
                    'oldPath' => $currentPath,
                    'newPath' => $newPath,
                    'updated' => true
                ];
            } else {
                $results[] = [
                    'userId' => $user->userId,
                    'name' => $user->name,
                    'path' => $currentPath,
                    'accessible' => $isAccessible,
                    'updated' => false
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Profile picture paths checked and updated',
            'data' => $results
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fixing profile pictures',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Fix viewer user passwords endpoint
Route::post('fix-viewer-passwords', function () {
    try {
        $users = \App\Models\User::where('passwordHash', 'PLEASE_REPLACE_WITH_HASH')->get();
        
        if ($users->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No users found with placeholder passwords',
                'updated_count' => 0
            ]);
        }
        
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $updatedCount = 0;
        
        foreach ($users as $user) {
            $user->passwordHash = $hashedPassword;
            $user->save();
            $updatedCount++;
        }
        
        return response()->json([
            'success' => true,
            'message' => "Successfully updated passwords for {$updatedCount} users",
            'updated_count' => $updatedCount,
            'default_password' => 'password123',
            'users_updated' => $users->map(function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ];
            })
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fixing passwords',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Debug endpoint to test bendamz user data
Route::get('debug-bendamz', function () {
    try {
        $user = \App\Models\User::where('name', 'bendamz')->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User bendamz not found'
            ]);
        }
        
        $userArray = $user->toArray();
        
        return response()->json([
            'success' => true,
            'data' => [
                'raw_user' => $userArray,
                'profile_picture_raw' => $user->profile_picture,
                'profile_picture_url' => $user->profile_picture_url,
                'file_exists' => $user->profile_picture ? file_exists(storage_path('app/public/' . $user->profile_picture)) : false,
                'available_files' => glob(storage_path('app/public/profile_pictures/*'))
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error debugging bendamz',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Test password verification endpoint
Route::get('test-password/{email}', function ($email) {
    try {
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        
        $testPassword = 'password123';
        $isValid = password_verify($testPassword, $user->passwordHash);
        
        return response()->json([
            'success' => true,
            'data' => [
                'email' => $user->email,
                'password_hash' => $user->passwordHash,
                'hash_length' => strlen($user->passwordHash),
                'test_password' => $testPassword,
                'password_valid' => $isValid,
                'hash_starts_with' => substr($user->passwordHash, 0, 10)
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error testing password',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Fix specific user password endpoint
Route::post('fix-password/{email}', function ($email) {
    try {
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        
        $newHash = password_hash('password123', PASSWORD_DEFAULT);
        $user->passwordHash = $newHash;
        $user->save();
        
        // Verify the new password works
        $isValid = password_verify('password123', $newHash);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'email' => $user->email,
                'new_hash' => $newHash,
                'hash_length' => strlen($newHash),
                'password_verified' => $isValid
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fixing password',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Trash Bin Routes
Route::prefix('trash')->group(function () {
    // Get deleted records
    Route::get('users', [TrashController::class, 'getDeletedUsers']);
    Route::get('sales-reports', [TrashController::class, 'getDeletedSalesReports']);
    Route::get('stats', [TrashController::class, 'getTrashStats']);
    
    // Restore records
    Route::post('users/{id}/restore', [TrashController::class, 'restoreUser']);
    Route::post('sales-reports/{id}/restore', [TrashController::class, 'restoreSalesReport']);
    
    // Permanently delete records
    Route::delete('users/{id}', [TrashController::class, 'permanentlyDeleteUser']);
    Route::delete('sales-reports/{id}', [TrashController::class, 'permanentlyDeleteSalesReport']);
});