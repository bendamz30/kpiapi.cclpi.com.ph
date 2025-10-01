<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 PROFILE PICTURE DIAGNOSTIC REPORT\n";
echo str_repeat("=", 50) . "\n\n";

try {
    // 1. Check Laravel Configuration
    echo "1️⃣ LARAVEL CONFIGURATION\n";
    echo "APP_URL: " . config('app.url') . "\n";
    echo "APP_ENV: " . config('app.env') . "\n";
    echo "Storage Disk: " . config('filesystems.default') . "\n";
    echo "Public Disk URL: " . config('filesystems.disks.public.url') . "\n\n";
    
    // 2. Check Storage Link
    echo "2️⃣ STORAGE LINK STATUS\n";
    $storageLink = public_path('storage');
    $storageTarget = storage_path('app/public');
    echo "Link Path: {$storageLink}\n";
    echo "Target Path: {$storageTarget}\n";
    echo "Link Exists: " . (file_exists($storageLink) ? 'Yes' : 'No') . "\n";
    echo "Target Exists: " . (is_dir($storageTarget) ? 'Yes' : 'No') . "\n";
    
    if (is_link($storageLink)) {
        $linkTarget = readlink($storageLink);
        echo "Link Points To: {$linkTarget}\n";
        echo "Link Valid: " . ($linkTarget === $storageTarget ? 'Yes' : 'No') . "\n";
    } elseif (is_dir($storageLink)) {
        echo "⚠️  Storage link is a directory, not a symbolic link!\n";
    }
    echo "\n";
    
    // 3. Check Profile Picture Files
    echo "3️⃣ PROFILE PICTURE FILES\n";
    $profilePicturesPath = storage_path('app/public/profile_pictures');
    if (is_dir($profilePicturesPath)) {
        $files = glob($profilePicturesPath . '/*');
        echo "Files Found: " . count($files) . "\n";
        foreach (array_slice($files, 0, 3) as $file) {
            echo "  - " . basename($file) . " (" . number_format(filesize($file)) . " bytes)\n";
        }
        if (count($files) > 3) {
            echo "  ... and " . (count($files) - 3) . " more files\n";
        }
    } else {
        echo "❌ Profile pictures directory not found!\n";
    }
    echo "\n";
    
    // 4. Check Users with Profile Pictures
    echo "4️⃣ USERS WITH PROFILE PICTURES\n";
    $users = \App\Models\User::whereNotNull('profile_picture')->take(5)->get();
    echo "Users with profile pictures: " . $users->count() . "\n";
    
    foreach ($users as $user) {
        echo "\n👤 User: {$user->name} (ID: {$user->userId})\n";
        echo "   Profile Picture Path: {$user->profile_picture}\n";
        echo "   Profile Picture URL: {$user->profile_picture_url}\n";
        echo "   File Exists: " . ($user->hasValidProfilePicture() ? 'Yes' : 'No') . "\n";
        
        // Test URL accessibility
        $headers = @get_headers($user->profile_picture_url);
        $isAccessible = $headers && strpos($headers[0], '200') !== false;
        echo "   URL Accessible: " . ($isAccessible ? 'Yes' : 'No') . "\n";
        
        if (!$isAccessible) {
            echo "   ⚠️  URL not accessible - this is the problem!\n";
        }
    }
    echo "\n";
    
    // 5. Test API Endpoint
    echo "5️⃣ API ENDPOINT TEST\n";
    $apiUrl = config('app.url') . '/api/users';
    echo "API URL: {$apiUrl}\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Accept: application/json',
            'timeout' => 10
        ]
    ]);
    
    $response = @file_get_contents($apiUrl, false, $context);
    if ($response !== false) {
        $data = json_decode($response, true);
        echo "API Response: Success\n";
        echo "Users in API: " . (isset($data['data']) ? count($data['data']) : 0) . "\n";
        
        if (isset($data['data']) && count($data['data']) > 0) {
            $firstUser = $data['data'][0];
            echo "First User Profile Picture URL: " . ($firstUser['profile_picture_url'] ?? 'Not set') . "\n";
        }
    } else {
        echo "❌ API endpoint not accessible!\n";
        echo "Make sure Laravel server is running: php artisan serve\n";
    }
    echo "\n";
    
    // 6. Recommendations
    echo "6️⃣ RECOMMENDATIONS\n";
    if (!is_link($storageLink)) {
        echo "🔧 Run: php artisan storage:link\n";
    }
    if (!file_exists($storageLink)) {
        echo "🔧 Create storage link\n";
    }
    if (config('app.env') === 'production') {
        echo "🔧 Set APP_URL to your production domain\n";
        echo "🔧 Enable HTTPS for profile picture URLs\n";
    }
    echo "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "✅ Diagnostic completed!\n";
