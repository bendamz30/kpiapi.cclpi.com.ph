<?php
// Simple test script to check bendamz user data
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Testing bendamz user data ===\n\n";

// Find bendamz user
$user = User::where('name', 'bendamz')->first();

if ($user) {
    echo "User found:\n";
    echo "ID: " . $user->userId . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Profile Picture (raw): " . ($user->profile_picture ?? 'NULL') . "\n";
    echo "Profile Picture URL: " . ($user->profile_picture_url ?? 'NULL') . "\n";
    
    // Check if file exists
    if ($user->profile_picture) {
        $filePath = storage_path('app/public/' . $user->profile_picture);
        echo "File path: " . $filePath . "\n";
        echo "File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "\n";
        
        if (file_exists($filePath)) {
            echo "File size: " . filesize($filePath) . " bytes\n";
        }
    }
    
    // Test the accessor
    echo "\n=== Testing accessor ===\n";
    $userArray = $user->toArray();
    echo "profile_picture_url in array: " . ($userArray['profile_picture_url'] ?? 'NOT FOUND') . "\n";
    
} else {
    echo "User 'bendamz' not found!\n";
}

echo "\n=== All users with profile pictures ===\n";
$usersWithPics = User::whereNotNull('profile_picture')->get(['userId', 'name', 'profile_picture']);
foreach ($usersWithPics as $u) {
    echo "ID: {$u->userId}, Name: {$u->name}, Pic: {$u->profile_picture}\n";
}
