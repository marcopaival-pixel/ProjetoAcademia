<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Plan;

$plans = Plan::all();
foreach ($plans as $plan) {
    echo "ID: {$plan->id} | Name: {$plan->name} | Type: {$plan->type} | Max Patients: {$plan->max_patients} | Max Students: {$plan->max_students} | Max Workouts: {$plan->max_workouts} | Max Diets: {$plan->max_diets}\n";
}
