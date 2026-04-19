<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Starting manual migration...\n";
    
    $migrationClass = require __DIR__ . '/database/migrations/2026_03_28_131452_create_permission_tables.php';
    $migrationClass->up();
    
    echo "Manual migration successful!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
