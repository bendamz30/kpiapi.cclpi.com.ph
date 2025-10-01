<?php

// Simple script to update profile picture paths in database
$host = '127.0.0.1';
$dbname = 'sales_dashboard';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Get all users with profile pictures
    $stmt = $pdo->query("SELECT userId, name, profile_picture FROM users WHERE profile_picture IS NOT NULL");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($users) . " users with profile pictures:\n\n";
    
    foreach ($users as $user) {
        echo "User ID: {$user['userId']}\n";
        echo "Name: {$user['name']}\n";
        echo "Current path: {$user['profile_picture']}\n";
        
        // Check if the current path is accessible
        $currentUrl = $user['profile_picture'];
        $headers = @get_headers($currentUrl);
        $isAccessible = $headers && strpos($headers[0], '200') !== false;
        
        echo "Current path accessible: " . ($isAccessible ? "YES" : "NO") . "\n";
        
        if (!$isAccessible) {
            // Try to find a matching file in storage
            $storagePath = __DIR__ . '/storage/app/public/profile_pictures/';
            $files = glob($storagePath . '*');
            
            echo "Available files in storage:\n";
            foreach ($files as $file) {
                echo "  - " . basename($file) . "\n";
            }
            
            // For now, let's update to use one of the available files
            if (!empty($files)) {
                $newFileName = basename($files[0]);
                $newPath = "profile_pictures/" . $newFileName;
                
                echo "Updating to: $newPath\n";
                
                $updateStmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE userId = ?");
                $updateStmt->execute([$newPath, $user['userId']]);
                
                echo "Updated successfully!\n";
            }
        }
        
        echo str_repeat('-', 50) . "\n\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
