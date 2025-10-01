<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Profile Picture Configuration...\n\n";

try {
    // Test profile picture URL generation
    $user = \App\Models\User::whereNotNull('profile_picture')->first();
    
    if ($user) {
        echo "✅ User found: {$user->name}\n";
        echo "📁 Profile picture path: {$user->profile_picture}\n";
        echo "🔗 Profile picture URL: {$user->profile_picture_url}\n";
        echo "📄 File exists: " . ($user->hasValidProfilePicture() ? 'Yes' : 'No') . "\n";
        
        // Test fallback
        $fallbackUrl = $user->getProfilePictureWithFallback();
        echo "🔄 Fallback URL: " . ($fallbackUrl ?: 'None') . "\n";
        
        // Test URL accessibility
        $headers = @get_headers($user->profile_picture_url);
        $isAccessible = $headers && strpos($headers[0], '200') !== false;
        echo "🌐 URL accessible: " . ($isAccessible ? 'Yes' : 'No') . "\n";
        
    } else {
        echo "❌ No users with profile pictures found.\n";
    }
    
    // Test configuration
    echo "\n⚙️ Configuration:\n";
    echo "APP_URL: " . config('app.url') . "\n";
    echo "APP_ENV: " . config('app.env') . "\n";
    echo "Storage disk: " . config('filesystems.default') . "\n";
    echo "Public disk URL: " . config('filesystems.disks.public.url') . "\n";
    
    // Test storage link
    $storageLink = public_path('storage');
    $storageTarget = storage_path('app/public');
    echo "\n🔗 Storage link:\n";
    echo "Link path: {$storageLink}\n";
    echo "Target path: {$storageTarget}\n";
    echo "Link exists: " . (is_link($storageLink) ? 'Yes' : 'No') . "\n";
    echo "Target exists: " . (is_dir($storageTarget) ? 'Yes' : 'No') . "\n";
    
    if (is_link($storageLink)) {
        $linkTarget = readlink($storageLink);
        echo "Link points to: {$linkTarget}\n";
        echo "Link valid: " . ($linkTarget === $storageTarget ? 'Yes' : 'No') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n✅ Test completed!\n";
