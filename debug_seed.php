<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting full seeding...\n";
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = ['view_profit', 'manage_marketing', 'manage_orders', 'manage_team'];
    foreach ($permissions as $p) {
        Permission::findOrCreate($p, 'web');
    }

    Role::findOrCreate('Manager', 'web')
        ->syncPermissions(Permission::all());

    Role::findOrCreate('Support', 'web')
        ->syncPermissions(['manage_orders']);

    Role::findOrCreate('Marketer', 'web')
        ->syncPermissions(['manage_marketing']);

    echo "Full Seeding successful!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}