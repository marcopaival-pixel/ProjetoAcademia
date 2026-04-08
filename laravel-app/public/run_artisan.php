<?php
chdir('..');
echo "<pre>";
echo "Routing clear:\n";
echo shell_exec('php artisan route:clear 2>&1');
echo "\nMigrations:\n";
echo shell_exec('php artisan migrate --force 2>&1');
echo "</pre>";
