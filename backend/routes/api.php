<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\BodyAnalysisController;
use App\Http\Controllers\Api\V1\CommunityController;
use App\Http\Controllers\Api\V1\EvolutionController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\NutritionDiaryController;
use App\Http\Controllers\Api\V1\PaymentStatusController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\TrainingPlanController;
use App\Http\Controllers\Api\V1\WorkoutImportController;
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
        Route::post('/workout-import/validate', [WorkoutImportController::class, 'validatePhoto'])->name('workout-import.validate');
        Route::post('/workout-import/process', [WorkoutImportController::class, 'process'])->name('workout-import.process');
        Route::post('/workout-import/orchestrated/initialize', [WorkoutImportController::class, 'orchInitialize'])->name('workout-import.orch-initialize');
        Route::post('/workout-import/orchestrated/validate/{uuid}', [WorkoutImportController::class, 'orchValidate'])->name('workout-import.orch-validate');
        Route::post('/workout-import/orchestrated/substitute/{uuid}', [WorkoutImportController::class, 'orchSubstitute'])->name('workout-import.orch-substitute');
        Route::post('/workout-import/orchestrated/process/{uuid}', [WorkoutImportController::class, 'orchProcess'])->name('workout-import.orch-process');
        Route::get('/workout-import/orchestrated/status/{uuid}', [WorkoutImportController::class, 'orchStatus'])->name('workout-import.orch-status');
        Route::post('/workout-import/save', [WorkoutImportController::class, 'save'])->name('workout-import.save');

        Route::get('/payments/status', PaymentStatusController::class)->name('payments.status');

        Route::get('/nutrition/diary', [NutritionDiaryController::class, 'index'])->name('nutrition.diary');
        Route::post('/nutrition/diary', [NutritionDiaryController::class, 'store'])->name('nutrition.diary.store');
        Route::put('/nutrition/diary/{entry}', [NutritionDiaryController::class, 'update'])->name('nutrition.diary.update');
        Route::delete('/nutrition/diary/{entry}', [NutritionDiaryController::class, 'destroy'])->name('nutrition.diary.destroy');
        Route::post('/nutrition/analyze-meal', [NutritionDiaryController::class, 'analyzeMeal'])->name('nutrition.analyze-meal');
        Route::post('/nutrition/analyze-photo', [NutritionDiaryController::class, 'analyzePhoto'])->name('nutrition.analyze-photo');

        Route::get('/workout-sessions', [WorkoutSessionController::class, 'index'])->name('workout-sessions.index');
        Route::post('/workout-sessions', [WorkoutSessionController::class, 'store'])->name('workout-sessions.store');

        Route::get('/evolution-photos', [EvolutionController::class, 'photos'])->name('evolution-photos.index');
        Route::get('/evolution-report', [EvolutionController::class, 'report'])->name('evolution-report.show');
        Route::get('/evolution-reports/consent', [EvolutionController::class, 'reportConsent'])->name('evolution-reports.consent');
        Route::post('/evolution-reports', [EvolutionController::class, 'requestReport'])->name('evolution-reports.store');
        Route::get('/evolution-reports/{report}', [EvolutionController::class, 'showReport'])->name('evolution-reports.show');
        Route::post('/evolution-photos/validate', [EvolutionController::class, 'validatePhoto'])->name('evolution-photos.validate');
        Route::post('/evolution-photos/analyze-session', [EvolutionController::class, 'analyzeSession'])->name('evolution-photos.analyze-session');
        Route::post('/evolution-photos', [EvolutionController::class, 'uploadPhoto'])->name('evolution-photos.store');
        Route::delete('/evolution-photos/{photo}', [EvolutionController::class, 'deletePhoto'])->name('evolution-photos.destroy');

        Route::get('/body-analysis', [BodyAnalysisController::class, 'index'])->name('body-analysis.index');
        Route::post('/body-analysis', [BodyAnalysisController::class, 'store'])->name('body-analysis.store');
        Route::get('/body-analysis/compare', [BodyAnalysisController::class, 'compare'])->name('body-analysis.compare');
        Route::get('/body-analysis/{analysis}', [BodyAnalysisController::class, 'show'])->whereNumber('analysis')->name('body-analysis.show');

        Route::get('/community/posts', [CommunityController::class, 'index'])->name('community.posts.index');
        Route::post('/community/posts', [CommunityController::class, 'store'])->name('community.posts.store');
        Route::post('/community/posts/{post}/comments', [CommunityController::class, 'comment'])->name('community.posts.comments.store');

        Route::get('/messages/conversations', [MessageController::class, 'index'])->name('messages.conversations.index');
        Route::post('/messages/conversations/support', [MessageController::class, 'startSupport'])->name('messages.conversations.support');
        Route::get('/messages/conversations/{conversation}', [MessageController::class, 'show'])->name('messages.conversations.show');
        Route::post('/messages/conversations/{conversation}', [MessageController::class, 'store'])->name('messages.conversations.store');
    });
});
