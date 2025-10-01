<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking users table schema:\n";

$columns = DB::select('DESCRIBE users');
foreach($columns as $column) {
    echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Default . "\n";
}

