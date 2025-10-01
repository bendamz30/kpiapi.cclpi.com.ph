<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Checking users with profile pictures...\n\n";

$users = User::whereNotNull('profile_picture')->get(['userId', 'name', 'profile_picture']);

foreach ($users as $user) {
    echo "User ID: {$user->userId}\n";
    echo "Name: {$user->name}\n";
    echo "Current profile_picture: {$user->profile_picture}\n";
    
    // Check if the file exists in storage
    $storagePath = storage_path('app/public/profile_pictures');
    $files = glob($storagePath . '/*');
    
    echo "Available files in storage:\n";
    foreach ($files as $file) {
        echo "  - " . basename($file) . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "Done!\n";
