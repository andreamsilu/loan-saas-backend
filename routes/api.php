<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

// Dynamically load module routes
$modulesPath = app_path('Modules');
if (File::exists($modulesPath)) {
    $modules = File::directories($modulesPath);

    foreach ($modules as $modulePath) {
        $moduleName = basename($modulePath);
        $routeFile = $modulePath . '/Routes/api.php';

        if (File::exists($routeFile)) {
            Route::prefix(strtolower($moduleName))
                ->middleware(['api', 'tenant', 'api.limit'])
                ->group($routeFile);
        }
    }
}
