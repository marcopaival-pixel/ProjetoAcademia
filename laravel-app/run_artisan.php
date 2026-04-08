<?php
echo shell_exec('php artisan route:clear 2>&1');
echo shell_exec('php artisan migrate 2>&1');
