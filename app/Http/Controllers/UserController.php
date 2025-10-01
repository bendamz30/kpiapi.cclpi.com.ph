<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\SalesTarget;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // POST /api/login
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            $user = User::where('email', $request->email)->first();
            
            // Debug logging
            \Log::info('Login attempt:', [
                'email' => $request->email,
                'user_found' => $user ? true : false,
                'user_id' => $user ? $user->userId : null,
                'password_hash' => $user ? $user->passwordHash : null
            ]);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            if (!password_verify($request->password, $user->passwordHash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password. Please contact the administrator if you forgot your password.'
                ], 401);
            }

            // Generate a simple token (in production, use Laravel Sanctum or Passport)
            $token = base64_encode(json_encode([
                'userId' => $user->userId,
                'email' => $user->email,
                'timestamp' => time()
            ]));

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'userId' => $user->userId,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'regionId' => $user->regionId,
                        'areaId' => $user->areaId,
                        'salesTypeId' => $user->salesTypeId,
                        'profile_picture' => $user->profile_picture,
                        'profile_picture_url' => $user->profile_picture_url,
                    ],
                    'token' => $token
                ],
                'message' => 'Login successful'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/users
public function index() {
    try {
        $users = User::orderBy('name', 'asc')
            ->get()
            ->map(function ($user) {
                return [
                    'userId' => $user->userId,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'contact_number' => $user->contact_number,
                    'address' => $user->address,
                    'profile_picture' => $user->profile_picture,
                    'profile_picture_url' => $user->profile_picture_url,
                    'passwordHash' => $user->passwordHash,
                    'role' => $user->role,
                    'regionId' => $user->regionId ? (int) $user->regionId : null,
                    'areaId' => $user->areaId ? (int) $user->areaId : null,
                    'createdAt' => $user->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->created_at)) : null,
                    'updatedAt' => $user->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->updated_at)) : null,
                    'salesTypeId' => $user->salesTypeId ? (int) $user->salesTypeId : null,
                ];
            });
        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve users',
            'error' => $e->getMessage()
        ], 500);
    }
}

// GET /api/users/{id}
public function show($id) {
    try {
        $user = User::findOrFail($id);
        
        // Get sales target for current year
        $salesTarget = SalesTarget::where('salesRepId', $user->userId)
            ->where('year', date('Y'))
            ->first();
        
        $formattedUser = [
            'userId' => $user->userId,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url,
            'passwordHash' => $user->passwordHash,
            'role' => $user->role,
            'regionId' => $user->regionId ? (int) $user->regionId : null,
            'areaId' => $user->areaId ? (int) $user->areaId : null,
            'createdAt' => $user->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->created_at)) : null,
            'updatedAt' => $user->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->updated_at)) : null,
            'salesTypeId' => $user->salesTypeId ? (int) $user->salesTypeId : null,
            // Include sales target data
            'annualTarget' => $salesTarget ? (float) $salesTarget->premiumTarget : null,
            'salesCounselorTarget' => $salesTarget ? (int) $salesTarget->salesCounselorTarget : null,
            'policySoldTarget' => $salesTarget ? (int) $salesTarget->policySoldTarget : null,
            'agencyCoopTarget' => $salesTarget ? (int) $salesTarget->agencyCoopTarget : null,
        ];
        return response()->json([
            'success' => true,
            'data' => $formattedUser,
            'message' => 'User retrieved successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'User not found',
            'error' => $e->getMessage()
        ], 404);
    }
}

// POST /api/users
public function store(Request $request) {
    try {
        // Debug: Log the request data
        \Log::info('User creation request data:', $request->all());
        \Log::info('Request method: ' . $request->method());
        \Log::info('Content-Type: ' . $request->header('Content-Type'));
        \Log::info('Has file: ' . ($request->hasFile('profile_picture') ? 'true' : 'false'));
        
        // Simplified validation to get basic functionality working
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|string',
            'regionId' => 'nullable|integer',
            'areaId' => 'nullable|integer',
            'salesTypeId' => 'nullable|integer',
            'annualTarget' => 'nullable|numeric|min:0',
            'salesCounselorTarget' => 'nullable|integer|min:0',
            'policySoldTarget' => 'nullable|integer|min:0',
            'agencyCoopTarget' => 'nullable|integer|min:0',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile_pictures', $filename, 'public');
            $validated['profile_picture'] = $path;
        }

        // Set default password for new users (password: "cclpi")
        $validated['passwordHash'] = password_hash('cclpi', PASSWORD_DEFAULT);

        // Create user with only the fields that exist in the database
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'passwordHash' => $validated['passwordHash']
        ];

        // Add optional fields only if they exist in the database
        if (isset($validated['username'])) {
            $userData['username'] = $validated['username'];
        }
        if (isset($validated['contact_number'])) {
            $userData['contact_number'] = $validated['contact_number'];
        }
        if (isset($validated['address'])) {
            $userData['address'] = $validated['address'];
        }
        if (isset($validated['profile_picture'])) {
            $userData['profile_picture'] = $validated['profile_picture'];
        }
        if (isset($validated['regionId'])) {
            $userData['regionId'] = $validated['regionId'];
        }
        if (isset($validated['areaId'])) {
            $userData['areaId'] = $validated['areaId'];
        }
        if (isset($validated['salesTypeId'])) {
            $userData['salesTypeId'] = $validated['salesTypeId'];
        }

        try {
            $user = User::create($userData);
            \Log::info('User created successfully with ID: ' . $user->userId);
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            \Log::error('User data: ' . json_encode($userData));
            throw $e;
        }

        // Create sales target if targets are provided
        try {
            if (isset($validated['annualTarget']) || isset($validated['salesCounselorTarget']) || 
                isset($validated['policySoldTarget']) || isset($validated['agencyCoopTarget'])) {
                
                SalesTarget::create([
                    'salesRepId' => $user->userId,
                    'year' => date('Y'),
                    'premiumTarget' => $validated['annualTarget'] ?? 0,
                    'salesCounselorTarget' => $validated['salesCounselorTarget'] ?? 0,
                    'policySoldTarget' => $validated['policySoldTarget'] ?? 0,
                    'agencyCoopTarget' => $validated['agencyCoopTarget'] ?? 0,
                    'createdBy' => 1, // You might want to get this from auth
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating sales target: ' . $e->getMessage());
            // Continue without sales target if it fails
        }
        $formattedUser = [
            'userId' => $user->userId,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username ?? null,
            'contact_number' => $user->contact_number ?? null,
            'address' => $user->address ?? null,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url,
            'passwordHash' => $user->passwordHash,
            'role' => $user->role,
            'regionId' => $user->regionId ? (int) $user->regionId : null,
            'areaId' => $user->areaId ? (int) $user->areaId : null,
            'createdAt' => $user->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->created_at)) : null,
            'updatedAt' => $user->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->updated_at)) : null,
            'salesTypeId' => $user->salesTypeId ? (int) $user->salesTypeId : null,
        ];
        return response()->json([
            'success' => true,
            'data' => $formattedUser,
            'message' => 'User created successfully'
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to create user',
            'error' => $e->getMessage()
        ], 500);
    }
}

// PUT /api/users/{id}
public function update(Request $request, $id) {
    try {
        $user = User::findOrFail($id);
        
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',userId',
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',userId',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'passwordHash' => 'nullable|string',
            'role' => 'required|string',
            'regionId' => 'nullable|integer',
            'areaId' => 'nullable|integer',
            'salesTypeId' => 'nullable|integer',
            'annualTarget' => 'nullable|numeric|min:0',
            'salesCounselorTarget' => 'nullable|integer|min:0',
            'policySoldTarget' => 'nullable|integer|min:0',
            'agencyCoopTarget' => 'nullable|integer|min:0',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profile_pictures', $filename, 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle password update - only update if a new password is provided
        if (isset($validated['passwordHash']) && !empty($validated['passwordHash'])) {
            // Keep passwordHash as is since that's the actual column name
            // Don't rename it to password
        }

        $user->update($validated);

        // Update or create sales target if targets are provided
        if (isset($validated['annualTarget']) || isset($validated['salesCounselorTarget']) || 
            isset($validated['policySoldTarget']) || isset($validated['agencyCoopTarget'])) {
            
            $salesTarget = SalesTarget::where('salesRepId', $user->userId)
                ->where('year', date('Y'))
                ->first();

            if ($salesTarget) {
                // Update existing target
                $salesTarget->update([
                    'premiumTarget' => $validated['annualTarget'] ?? $salesTarget->premiumTarget,
                    'salesCounselorTarget' => $validated['salesCounselorTarget'] ?? $salesTarget->salesCounselorTarget,
                    'policySoldTarget' => $validated['policySoldTarget'] ?? $salesTarget->policySoldTarget,
                    'agencyCoopTarget' => $validated['agencyCoopTarget'] ?? $salesTarget->agencyCoopTarget,
                ]);
            } else {
                // Create new target
                SalesTarget::create([
                    'salesRepId' => $user->userId,
                    'year' => date('Y'),
                    'premiumTarget' => $validated['annualTarget'] ?? 0,
                    'salesCounselorTarget' => $validated['salesCounselorTarget'] ?? 0,
                    'policySoldTarget' => $validated['policySoldTarget'] ?? 0,
                    'agencyCoopTarget' => $validated['agencyCoopTarget'] ?? 0,
                    'createdBy' => 1, // You might want to get this from auth
                ]);
            }
        }
        $formattedUser = [
            'userId' => $user->userId,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'contact_number' => $user->contact_number,
            'address' => $user->address,
            'profile_picture' => $user->profile_picture,
            'profile_picture_url' => $user->profile_picture_url,
            'passwordHash' => $user->passwordHash,
            'role' => $user->role,
            'regionId' => $user->regionId ? (int) $user->regionId : null,
            'areaId' => $user->areaId ? (int) $user->areaId : null,
            'createdAt' => $user->created_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->created_at)) : null,
            'updatedAt' => $user->updated_at ? date('Y-m-d\TH:i:s\Z', strtotime($user->updated_at)) : null,
            'salesTypeId' => $user->salesTypeId ? (int) $user->salesTypeId : null,
        ];
        return response()->json([
            'success' => true,
            'data' => $formattedUser,
            'message' => 'User updated successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update user',
            'error' => $e->getMessage()
        ], 500);
    }
}

// DELETE /api/users/{id}
    public function destroy($id) {
        try {
            $user = User::findOrFail($id);
            
            // Check if user has sales reports
            $salesReportsCount = \App\Models\Sale::where('salesRepId', $user->userId)->count();
            
            // Move user to trash before deleting
            \App\Models\DeletedUser::create([
                'original_user_id' => $user->userId,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'contact_number' => $user->contact_number,
                'address' => $user->address,
                'profile_picture' => $user->profile_picture,
                'passwordHash' => $user->passwordHash,
                'role' => $user->role,
                'regionId' => $user->regionId,
                'areaId' => $user->areaId,
                'salesTypeId' => $user->salesTypeId,
                'deleted_by' => 1, // TODO: Get from authenticated user
                'deleted_at' => now(),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
            
            // Now permanently delete the user
            // Note: Sales reports are preserved to maintain KPI data integrity
            $user->delete();
            
            $message = 'User moved to trash successfully';
            if ($salesReportsCount > 0) {
                $message .= ". {$salesReportsCount} sales report(s) preserved for KPI calculations.";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'preserved_sales_reports' => $salesReportsCount
                ]
            ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete user',
            'error' => $e->getMessage()
        ], 500);
    }
}

// POST /api/users/{user}/change-password
public function changePassword(Request $request, $user) {
    try {
        $user = User::findOrFail($user);
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:4|confirmed',
            'new_password_confirmation' => 'required|string|min:4'
        ]);

        // Verify current password
        if (!$user->passwordHash || !password_verify($validated['current_password'], $user->passwordHash)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Check if new password is different from current
        if (password_verify($validated['new_password'], $user->passwordHash)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password'
            ], 400);
        }

        // Update password
        $user->update([
            'passwordHash' => password_hash($validated['new_password'], PASSWORD_DEFAULT)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
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
            'message' => 'Failed to change password',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function fixViewerPassword($email)
{
    try {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $newHash = password_hash('cclpi', PASSWORD_DEFAULT);
        $user->passwordHash = $newHash;
        $user->save();
        
        // Verify the new password works
        $isValid = password_verify('cclpi', $newHash);
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
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
}

public function updateAllPasswordsToCclpi()
{
    try {
        $users = User::all();
        $newHash = password_hash('cclpi', PASSWORD_DEFAULT);
        $updatedCount = 0;
        
        foreach ($users as $user) {
            $user->passwordHash = $newHash;
            $user->save();
            $updatedCount++;
        }
        
        // Verify the new password works
        $isValid = password_verify('cclpi', $newHash);
        
        return response()->json([
            'success' => true,
            'message' => "Successfully updated passwords for {$updatedCount} users",
            'data' => [
                'updated_count' => $updatedCount,
                'default_password' => 'cclpi',
                'password_verified' => $isValid,
                'users_updated' => $users->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ];
                })
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error updating all passwords',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // GET /api/users/{user}/kpi-target
    public function getKpiTarget(Request $request, $userId) {
        try {
            $startDate = $request->query('start'); // e.g. '2025-08-01'
            $endDate = $request->query('end');     // e.g. '2025-12-31'
            $year = $request->query('year', date('Y')); // Default to current year

            // Validate required parameters
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date and end date are required'
                ], 400);
            }

            // Find the user
            $user = User::findOrFail($userId);

            // Get the user's annual targets from sales_targets table
            $salesTarget = SalesTarget::where('salesRepId', $userId)
                ->where('year', $year)
                ->first();

            if (!$salesTarget) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sales targets found for this user and year'
                ], 404);
            }

            // Calculate months in range
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $months = $start->diffInMonths($end) + 1; // +1 to include both start and end months

            // Calculate dynamic targets
            $monthlyPremiumTarget = $salesTarget->premiumTarget / 12;
            $monthlySalesCounselorTarget = $salesTarget->salesCounselorTarget / 12;
            $monthlyPolicySoldTarget = $salesTarget->policySoldTarget / 12;
            $monthlyAgencyCoopTarget = $salesTarget->agencyCoopTarget / 12;

            $dynamicPremiumTarget = round($monthlyPremiumTarget * $months, 2);
            $dynamicSalesCounselorTarget = round($monthlySalesCounselorTarget * $months, 2);
            $dynamicPolicySoldTarget = round($monthlyPolicySoldTarget * $months, 2);
            $dynamicAgencyCoopTarget = round($monthlyAgencyCoopTarget * $months, 2);

            return response()->json([
                'success' => true,
                'data' => [
                    'userId' => (int) $userId,
                    'userName' => $user->name,
                    'year' => (int) $year,
                    'dateRange' => [
                        'start' => $startDate,
                        'end' => $endDate,
                        'months' => $months
                    ],
                    'annualTargets' => [
                        'premiumTarget' => (float) $salesTarget->premiumTarget,
                        'salesCounselorTarget' => (int) $salesTarget->salesCounselorTarget,
                        'policySoldTarget' => (int) $salesTarget->policySoldTarget,
                        'agencyCoopTarget' => (int) $salesTarget->agencyCoopTarget,
                    ],
                    'monthlyTargets' => [
                        'premiumTarget' => (float) $monthlyPremiumTarget,
                        'salesCounselorTarget' => (float) $monthlySalesCounselorTarget,
                        'policySoldTarget' => (float) $monthlyPolicySoldTarget,
                        'agencyCoopTarget' => (float) $monthlyAgencyCoopTarget,
                    ],
                    'dynamicTargets' => [
                        'premiumTarget' => (float) $dynamicPremiumTarget,
                        'salesCounselorTarget' => (float) $dynamicSalesCounselorTarget,
                        'policySoldTarget' => (float) $dynamicPolicySoldTarget,
                        'agencyCoopTarget' => (float) $dynamicAgencyCoopTarget,
                    ],
                    'calculation' => [
                        'formula' => 'Dynamic Target = (Annual Target ÷ 12) × Months in Range',
                        'premiumCalculation' => "({$salesTarget->premiumTarget} ÷ 12) × {$months} = {$dynamicPremiumTarget}",
                        'salesCounselorCalculation' => "({$salesTarget->salesCounselorTarget} ÷ 12) × {$months} = {$dynamicSalesCounselorTarget}",
                    ]
                ],
                'message' => 'Dynamic KPI targets calculated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate dynamic KPI targets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /api/regions/{region}/kpi-target
    public function getRegionalKpiTarget(Request $request, $regionId) {
        try {
            $startDate = $request->query('start');
            $endDate = $request->query('end');
            $year = $request->query('year', date('Y'));

            if (!$startDate || !$endDate) {
                return response()->json(['success' => false, 'message' => 'Start date and end date are required'], 400);
            }

            // Get all users in the region with RegionalUser role
            $users = User::where('regionId', $regionId)
                ->where('role', 'RegionalUser')
                ->get();

            if ($users->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales officers found in this region'], 404);
            }

            // Get sales targets for all users in the region
            $userIds = $users->pluck('userId')->toArray();
            $salesTargets = SalesTarget::whereIn('salesRepId', $userIds)
                ->where('year', $year)
                ->get();

            if ($salesTargets->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales targets found for users in this region'], 404);
            }

            // Calculate date range
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $months = $start->diffInMonths($end) + 1;

            // Aggregate targets
            $totalPremiumTarget = $salesTargets->sum('premiumTarget');
            $totalSalesCounselorTarget = $salesTargets->sum('salesCounselorTarget');
            $totalPolicySoldTarget = $salesTargets->sum('policySoldTarget');
            $totalAgencyCoopTarget = $salesTargets->sum('agencyCoopTarget');

            // Calculate monthly targets
            $monthlyPremiumTarget = $totalPremiumTarget / 12;
            $monthlySalesCounselorTarget = $totalSalesCounselorTarget / 12;
            $monthlyPolicySoldTarget = $totalPolicySoldTarget / 12;
            $monthlyAgencyCoopTarget = $totalAgencyCoopTarget / 12;

            // Calculate dynamic targets based on date range
            $dynamicPremiumTarget = round($monthlyPremiumTarget * $months, 2);
            $dynamicSalesCounselorTarget = round($monthlySalesCounselorTarget * $months, 2);
            $dynamicPolicySoldTarget = round($monthlyPolicySoldTarget * $months, 2);
            $dynamicAgencyCoopTarget = round($monthlyAgencyCoopTarget * $months, 2);

            return response()->json([
                'success' => true,
                'data' => [
                    'regionId' => (int) $regionId,
                    'year' => (int) $year,
                    'dateRange' => [
                        'start' => $startDate,
                        'end' => $endDate,
                        'months' => $months
                    ],
                    'users' => $users->map(function($user) {
                        return [
                            'userId' => $user->userId,
                            'name' => $user->name,
                            'email' => $user->email
                        ];
                    }),
                    'annualTargets' => [
                        'premiumTarget' => (float) $totalPremiumTarget,
                        'salesCounselorTarget' => (int) $totalSalesCounselorTarget,
                        'policySoldTarget' => (int) $totalPolicySoldTarget,
                        'agencyCoopTarget' => (int) $totalAgencyCoopTarget,
                    ],
                    'monthlyTargets' => [
                        'premiumTarget' => (float) $monthlyPremiumTarget,
                        'salesCounselorTarget' => (float) $monthlySalesCounselorTarget,
                        'policySoldTarget' => (float) $monthlyPolicySoldTarget,
                        'agencyCoopTarget' => (float) $monthlyAgencyCoopTarget,
                    ],
                    'dynamicTargets' => [
                        'premiumTarget' => (float) $dynamicPremiumTarget,
                        'salesCounselorTarget' => (float) $dynamicSalesCounselorTarget,
                        'policySoldTarget' => (float) $dynamicPolicySoldTarget,
                        'agencyCoopTarget' => (float) $dynamicAgencyCoopTarget,
                    ],
                    'calculation' => [
                        'formula' => 'Dynamic Target = (Sum of Annual Targets ÷ 12) × Months in Range',
                        'policySoldCalculation' => "({$totalPolicySoldTarget} ÷ 12) × {$months} = {$dynamicPolicySoldTarget}",
                    ]
                ],
                'message' => 'Regional KPI targets calculated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to calculate regional KPI targets', 'error' => $e->getMessage()], 500);
        }
    }

    // GET /api/areas/{area}/kpi-target
    public function getAreaKpiTarget(Request $request, $areaId) {
        try {
            $startDate = $request->query('start');
            $endDate = $request->query('end');
            $year = $request->query('year', date('Y'));

            if (!$startDate || !$endDate) {
                return response()->json(['success' => false, 'message' => 'Start date and end date are required'], 400);
            }

            // Get all users in the area with RegionalUser role
            $users = User::where('areaId', $areaId)
                ->where('role', 'RegionalUser')
                ->get();

            if ($users->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales officers found in this area'], 404);
            }

            // Get sales targets for all users in the area
            $userIds = $users->pluck('userId')->toArray();
            $salesTargets = SalesTarget::whereIn('salesRepId', $userIds)
                ->where('year', $year)
                ->get();

            if ($salesTargets->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales targets found for users in this area'], 404);
            }

            // Calculate date range
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $months = $start->diffInMonths($end) + 1;

            // Aggregate targets
            $totalPremiumTarget = $salesTargets->sum('premiumTarget');
            $totalSalesCounselorTarget = $salesTargets->sum('salesCounselorTarget');
            $totalPolicySoldTarget = $salesTargets->sum('policySoldTarget');
            $totalAgencyCoopTarget = $salesTargets->sum('agencyCoopTarget');

            // Calculate monthly targets
            $monthlyPremiumTarget = $totalPremiumTarget / 12;
            $monthlySalesCounselorTarget = $totalSalesCounselorTarget / 12;
            $monthlyPolicySoldTarget = $totalPolicySoldTarget / 12;
            $monthlyAgencyCoopTarget = $totalAgencyCoopTarget / 12;

            // Calculate dynamic targets based on date range
            $dynamicPremiumTarget = round($monthlyPremiumTarget * $months, 2);
            $dynamicSalesCounselorTarget = round($monthlySalesCounselorTarget * $months, 2);
            $dynamicPolicySoldTarget = round($monthlyPolicySoldTarget * $months, 2);
            $dynamicAgencyCoopTarget = round($monthlyAgencyCoopTarget * $months, 2);

            return response()->json([
                'success' => true,
                'data' => [
                    'areaId' => (int) $areaId,
                    'year' => (int) $year,
                    'dateRange' => [
                        'start' => $startDate,
                        'end' => $endDate,
                        'months' => $months
                    ],
                    'users' => $users->map(function($user) {
                        return [
                            'userId' => $user->userId,
                            'name' => $user->name,
                            'email' => $user->email,
                            'regionId' => $user->regionId
                        ];
                    }),
                    'annualTargets' => [
                        'premiumTarget' => (float) $totalPremiumTarget,
                        'salesCounselorTarget' => (int) $totalSalesCounselorTarget,
                        'policySoldTarget' => (int) $totalPolicySoldTarget,
                        'agencyCoopTarget' => (int) $totalAgencyCoopTarget,
                    ],
                    'monthlyTargets' => [
                        'premiumTarget' => (float) $monthlyPremiumTarget,
                        'salesCounselorTarget' => (float) $monthlySalesCounselorTarget,
                        'policySoldTarget' => (float) $monthlyPolicySoldTarget,
                        'agencyCoopTarget' => (float) $monthlyAgencyCoopTarget,
                    ],
                    'dynamicTargets' => [
                        'premiumTarget' => (float) $dynamicPremiumTarget,
                        'salesCounselorTarget' => (float) $dynamicSalesCounselorTarget,
                        'policySoldTarget' => (float) $dynamicPolicySoldTarget,
                        'agencyCoopTarget' => (float) $dynamicAgencyCoopTarget,
                    ],
                    'calculation' => [
                        'formula' => 'Dynamic Target = (Sum of Annual Targets ÷ 12) × Months in Range',
                        'policySoldCalculation' => "({$totalPolicySoldTarget} ÷ 12) × {$months} = {$dynamicPolicySoldTarget}",
                    ]
                ],
                'message' => 'Area KPI targets calculated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to calculate area KPI targets', 'error' => $e->getMessage()], 500);
        }
    }

    // GET /api/kpi-targets - Hierarchical filtering
    public function getHierarchicalKpiTargets(Request $request) {
        try {
            $startDate = $request->query('start');
            $endDate = $request->query('end');
            $year = $request->query('year', date('Y'));
            $salesTypeId = $request->query('salesTypeId');
            $areaId = $request->query('areaId');
            $regionId = $request->query('regionId');
            $salesRepId = $request->query('salesRepId');

            if (!$startDate || !$endDate) {
                return response()->json(['success' => false, 'message' => 'Start date and end date are required'], 400);
            }

            // Calculate date range
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $months = $start->diffInMonths($end) + 1;

            // Build query based on hierarchical filters
            $query = User::where('role', 'RegionalUser');

            // Apply filters in hierarchical order
            if ($salesTypeId && $salesTypeId !== 'all') {
                $query->where('salesTypeId', $salesTypeId);
            }

            if ($areaId && $areaId !== 'all') {
                $query->where('areaId', $areaId);
            }

            if ($regionId && $regionId !== 'all') {
                $query->where('regionId', $regionId);
            }

            if ($salesRepId && $salesRepId !== 'all') {
                $query->where('userId', $salesRepId);
            }

            $users = $query->get();

            if ($users->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales officers found with the specified filters'], 404);
            }

            // Get sales targets for filtered users
            $userIds = $users->pluck('userId')->toArray();
            $salesTargets = SalesTarget::whereIn('salesRepId', $userIds)
                ->where('year', $year)
                ->get();

            if ($salesTargets->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No sales targets found for the filtered users'], 404);
            }

            // Aggregate targets
            $totalPremiumTarget = $salesTargets->sum('premiumTarget');
            $totalSalesCounselorTarget = $salesTargets->sum('salesCounselorTarget');
            $totalPolicySoldTarget = $salesTargets->sum('policySoldTarget');
            $totalAgencyCoopTarget = $salesTargets->sum('agencyCoopTarget');

            // Calculate monthly targets
            $monthlyPremiumTarget = $totalPremiumTarget / 12;
            $monthlySalesCounselorTarget = $totalSalesCounselorTarget / 12;
            $monthlyPolicySoldTarget = $totalPolicySoldTarget / 12;
            $monthlyAgencyCoopTarget = $totalAgencyCoopTarget / 12;

            // Calculate dynamic targets based on date range
            $dynamicPremiumTarget = round($monthlyPremiumTarget * $months, 2);
            $dynamicSalesCounselorTarget = round($monthlySalesCounselorTarget * $months, 2);
            $dynamicPolicySoldTarget = round($monthlyPolicySoldTarget * $months, 2);
            $dynamicAgencyCoopTarget = round($monthlyAgencyCoopTarget * $months, 2);

            // Determine filter level for description
            $filterLevel = 'all';
            if ($salesRepId && $salesRepId !== 'all') {
                $filterLevel = 'salesOfficer';
            } elseif ($regionId && $regionId !== 'all') {
                $filterLevel = 'region';
            } elseif ($areaId && $areaId !== 'all') {
                $filterLevel = 'area';
            } elseif ($salesTypeId && $salesTypeId !== 'all') {
                $filterLevel = 'salesType';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'filterLevel' => $filterLevel,
                    'filters' => [
                        'salesTypeId' => $salesTypeId,
                        'areaId' => $areaId,
                        'regionId' => $regionId,
                        'salesRepId' => $salesRepId,
                    ],
                    'year' => (int) $year,
                    'dateRange' => [
                        'start' => $startDate,
                        'end' => $endDate,
                        'months' => $months
                    ],
                    'users' => $users->map(function($user) {
                        return [
                            'userId' => $user->userId,
                            'name' => $user->name,
                            'email' => $user->email,
                            'areaId' => $user->areaId,
                            'regionId' => $user->regionId,
                            'salesTypeId' => $user->salesTypeId
                        ];
                    }),
                    'annualTargets' => [
                        'premiumTarget' => (float) $totalPremiumTarget,
                        'salesCounselorTarget' => (int) $totalSalesCounselorTarget,
                        'policySoldTarget' => (int) $totalPolicySoldTarget,
                        'agencyCoopTarget' => (int) $totalAgencyCoopTarget,
                    ],
                    'monthlyTargets' => [
                        'premiumTarget' => (float) $monthlyPremiumTarget,
                        'salesCounselorTarget' => (float) $monthlySalesCounselorTarget,
                        'policySoldTarget' => (float) $monthlyPolicySoldTarget,
                        'agencyCoopTarget' => (float) $monthlyAgencyCoopTarget,
                    ],
                    'dynamicTargets' => [
                        'premiumTarget' => (float) $dynamicPremiumTarget,
                        'salesCounselorTarget' => (float) $dynamicSalesCounselorTarget,
                        'policySoldTarget' => (float) $dynamicPolicySoldTarget,
                        'agencyCoopTarget' => (float) $dynamicAgencyCoopTarget,
                    ],
                    'calculation' => [
                        'formula' => 'Dynamic Target = (Sum of Annual Targets ÷ 12) × Months in Range',
                        'premiumCalculation' => "({$totalPremiumTarget} ÷ 12) × {$months} = {$dynamicPremiumTarget}",
                        'policySoldCalculation' => "({$totalPolicySoldTarget} ÷ 12) × {$months} = {$dynamicPolicySoldTarget}",
                    ]
                ],
                'message' => 'Hierarchical KPI targets calculated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to calculate hierarchical KPI targets', 'error' => $e->getMessage()], 500);
        }
    }

    // POST /api/verify-password
    public function verifyPassword(Request $request) {
        try {
            $validated = $request->validate([
                'password' => 'required|string',
                'hash' => 'required|string'
            ]);

            $isValid = password_verify($validated['password'], $validated['hash']);

            return response()->json([
                'success' => $isValid,
                'message' => $isValid ? 'Password is valid' : 'Password is invalid'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/users/{user}/reset-password
    public function resetPassword(Request $request, $user) {
        try {
            $user = User::findOrFail($user);
            
            $validated = $request->validate([
                'new_password' => 'required|string|min:4'
            ]);

            // Update password to the specified value
            $user->update([
                'passwordHash' => password_hash($validated['new_password'], PASSWORD_DEFAULT)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'data' => [
                    'userId' => $user->userId,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
