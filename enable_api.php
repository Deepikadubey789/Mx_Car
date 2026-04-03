<?php
require __DIR__.'/bootstrap/app.php';

$app = new \Illuminate\Container\Container();
$app = require __DIR__.'/bootstrap/app.php';

// Use tinker to execute command
echo shell_exec('cd '.escapeshellarg(__DIR__).' && php artisan tinker --execute "Setting::updateSetting(\'api_enabled\', 1); echo \'API Enabled!\'"');
