<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\CommunityController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NutritionDiaryController;
use App\Http\Controllers\Api\V1\PaymentStatusController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TrainingPlanController;
use App\Http\Controllers\Api\V1\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

/*
| API v1 — autenticação via Laravel Sanctum (Bearer token).
| Documentação resumida: docs/API_V1.md
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', HealthController::class)->name('health');

    Route::post('/auth/token', [AuthTokenController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('auth.token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [ProfileController::class, 'show'])->name('me');
        Route::delete('/auth/token', [AuthTokenController::class, 'destroy'])->name('auth.token.revoke');

        Route::get('/training-plans', [TrainingPlanController::class, 'index'])->name('training-plans.index');
        Route::post('/training-plans', [TrainingPlanController::class, 'store'])->name('training-plans.store');
        Route::get('/exercise-catalog', [TrainingPlanController::class, 'exerciseCatalog'])->name('exercise-catalog.index');
        Route::get('/training-plans/{training_plan}', [TrainingPlanController::class, 'show'])->name('training-plans.show');
        Route::put('/training-plans/{training_plan}', [TrainingPlanController::class, 'update'])->name('training-plans.update');
        Route::delete('/training-plans/{training_plan}', [TrainingPlanController::class, 'destroy'])->name('training-plans.destroy');

        Route::get('/payments/status', PaymentStatusController::class)->name('payments.status');

        Route::get('/nutrition/diary', [NutritionDiaryController::class, 'index'])->name('nutrition.diary');

        Route::get('/workout-sessions', [WorkoutSessionController::class, 'index'])->name('workout-sessions.index');
        Route::post('/workout-sessions', [WorkoutSessionController::class, 'store'])->name('workout-sessions.store');

        Route::get('/community/posts', [CommunityController::class, 'index'])->name('community.posts.index');
        Route::post('/community/posts', [CommunityController::class, 'store'])->name('community.posts.store');
        Route::post('/community/posts/{post}/comments', [CommunityController::class, 'comment'])->name('community.posts.comments.store');

        Route::get('/messages/conversations', [MessageController::class, 'index'])->name('messages.conversations.index');
        Route::post('/messages/conversations/support', [MessageController::class, 'startSupport'])->name('messages.conversations.support');
        Route::get('/messages/conversations/{conversation}', [MessageController::class, 'show'])->name('messages.conversations.show');
        Route::post('/messages/conversations/{conversation}', [MessageController::class, 'store'])->name('messages.conversations.store');
    });
});
