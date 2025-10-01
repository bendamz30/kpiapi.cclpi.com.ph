<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Sale;

echo "Testing sales report creation...\n\n";

try {
    $sale = Sale::create([
        'salesRepId' => 120, // Belinda Gadon
        'reportDate' => '2025-09-11',
        'premiumActual' => 540,
        'salesCounselorActual' => 1,
        'policySoldActual' => 1,
        'agencyCoopActual' => 1,
        'createdBy' => 113, // Alvin Damasco
    ]);
    
    echo "✅ Sales report created successfully!\n";
    echo "Report ID: {$sale->reportId}\n";
    echo "Sales Rep ID: {$sale->salesRepId}\n";
    echo "Created By: {$sale->createdBy}\n";
    echo "Premium Actual: {$sale->premiumActual}\n";
    
    // Clean up - delete the test record
    $sale->delete();
    echo "🧹 Test record deleted.\n";
    
} catch (Exception $e) {
    echo "❌ Error creating sales report: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";



