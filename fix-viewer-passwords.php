<?php
// Script to fix viewer user passwords
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

try {
    // Connect to database directly
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=salesdashboard', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 Fixing viewer user passwords...\n\n";
    
    // Find users with placeholder password
    $stmt = $pdo->prepare("SELECT userId, name, email, role FROM users WHERE passwordHash = 'PLEASE_REPLACE_WITH_HASH'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "✅ No users found with placeholder passwords.\n";
    } else {
        echo "📋 Found " . count($users) . " users with placeholder passwords:\n";
        foreach ($users as $user) {
            echo "- {$user['name']} ({$user['email']}) - Role: {$user['role']}\n";
        }
        
        echo "\n🔑 Setting default password 'password123' for all users...\n";
        
        // Update passwords
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET passwordHash = ? WHERE passwordHash = 'PLEASE_REPLACE_WITH_HASH'");
        $result = $updateStmt->execute([$hashedPassword]);
        
        if ($result) {
            echo "✅ Successfully updated passwords for " . count($users) . " users.\n";
            echo "\n📝 Login credentials for viewer users:\n";
            echo "Password: password123\n";
            echo "\nUsers that can now login:\n";
            foreach ($users as $user) {
                echo "- Email: {$user['email']}, Password: password123\n";
            }
        } else {
            echo "❌ Failed to update passwords.\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
