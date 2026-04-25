<?php

/**
 * Rotas de funcionalidades da app (avaliações, progressão, mensagens, correio interno, etc.)
 * Carregadas dentro do grupo middleware auth em web.php.
 */

use App\Http\Controllers\ActiveRestController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CommunicationGroupController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExerciseCatalogController;
use App\Http\Controllers\InternalEmailController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\LoadProgressionController;
use App\Http\Controllers\MenuPreferenceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NutritionController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\TrainingPlanController;
use App\Models\ExerciseCatalog;
use Illuminate\Support\Facades\Route;

// Registro de Treino — via Menu lateral (Performance HUD)
Route::match(['get', 'post'], '/exercise', [ExerciseController::class, 'index'])->name('exercise');
Route::prefix('api/exercise')->name('api.exercise.')->group(function () {
    Route::get('/search', [ExerciseController::class, 'apiSearch'])->name('search');
    Route::get('/list-all', [ExerciseController::class, 'apiListAll'])->name('list-all');
    Route::get('/history', [ExerciseController::class, 'apiHistory'])->name('history');
    Route::get('/last', [ExerciseController::class, 'apiLastWorkout'])->name('last');
    Route::post('/calculate-calories', [ExerciseController::class, 'apiCalculateCalories'])->name('calculate-calories');
    Route::post('/sync', [ExerciseController::class, 'apiSync'])->name('sync');
});
Route::get('/exercise-catalog', [ExerciseCatalogController::class, 'index'])->name('exercise.catalog');
Route::get('/exercise-catalog/{exercise}', [ExerciseCatalogController::class, 'show'])->name('exercise.show');

// URLs antigas /exercises-catalog → /exercise
Route::redirect('/exercises-catalog', '/exercise', 301);
Route::get('/exercises-catalog/{exercise}', function (ExerciseCatalog $exercise) {
    return redirect()->route('exercise.show', $exercise, 301);
});

// Avaliações corporais
Route::prefix('assessments')->name('assessments.')->group(function () {
    Route::get('/', [AssessmentController::class, 'index'])->name('index');
    Route::get('/create', [AssessmentController::class, 'create'])->name('create');
    Route::post('/', [AssessmentController::class, 'store'])->name('store');
    Route::get('/{assessment}', [AssessmentController::class, 'show'])->name('show');
    Route::delete('/{assessment}', [AssessmentController::class, 'destroy'])->name('destroy');
});

// Nutrição (metas e dashboard parcial)
Route::get('/nutrition', [NutritionController::class, 'index'])->name('nutrition.index');
Route::post('/nutrition/update-goal', [NutritionController::class, 'updateGoal'])->name('nutrition.update-goal');
Route::get('/nutrition/generate-meal', [NutritionController::class, 'suggestMeal'])->name('nutrition.suggest-meal');
Route::post('/nutrition/add-water', [NutritionController::class, 'addWater'])->name('nutrition.add-water');
Route::post('/nutrition/adopt-meal', [NutritionController::class, 'adoptMeal'])->name('nutrition.adopt-meal');
Route::get('/nutrition/audit', [NutritionController::class, 'weeklyAudit'])->name('nutrition.audit');

Route::prefix('nutrition/api')->name('nutrition.api.')->group(function () {
    Route::post('/repeat-meal', [NutritionController::class, 'repeatMeal'])->name('repeat-meal');
    Route::get('/favorites', [NutritionController::class, 'getFavorites'])->name('favorites');
    Route::post('/natural-language', [NutritionController::class, 'naturalLanguageRegistry'])->name('natural-language');
    Route::post('/process-photo', [NutritionController::class, 'processPhoto'])->name('process-photo');
});

Route::match(['get', 'post'], '/diary', [NutritionController::class, 'manageDiary'])->name('diary');
// Ranking e Peso
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index')->middleware('premium');
Route::get('/weight', [AssessmentController::class, 'index'])->name('weight');
Route::get('/peso', [AssessmentController::class, 'index'])->name('peso'); // Alias para compatibilidade


// Descanso ativo
Route::prefix('active-rest')->name('active-rest.')->middleware('premium')->group(function () {
    Route::get('/', [ActiveRestController::class, 'index'])->name('index');
    Route::get('/history', [ActiveRestController::class, 'history'])->name('history');
    Route::post('/toggle-favorite/{id}', [ActiveRestController::class, 'toggleFavorite'])->name('toggle-favorite');
    Route::post('/{id}/log', [ActiveRestController::class, 'storeLog'])->name('store-log');
    Route::get('/{id}', [ActiveRestController::class, 'show'])->whereNumber('id')->name('show');
});

// Progressão de carga / planos de treino
Route::prefix('progression')->name('progression.')->group(function () {
    Route::get('/charts', [LoadProgressionController::class, 'charts'])->name('charts')->middleware('premium');
    Route::post('/log', [LoadProgressionController::class, 'storeLog'])->name('log.store');
    Route::get('/plans/{plan}/log', [LoadProgressionController::class, 'logSession'])->name('log');
    Route::get('/plans/{plan}/session-log', [LoadProgressionController::class, 'logSession'])->name('session-log');

    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [TrainingPlanController::class, 'index'])->name('index');
        Route::get('/target-selection', [TrainingPlanController::class, 'targetSelection'])->name('target-selection');
        Route::post('/target-selection', [TrainingPlanController::class, 'storeTargetSelection'])->name('store-target-selection');
        Route::get('/create', [TrainingPlanController::class, 'create'])->name('create');
        Route::get('/muscles/search', [TrainingPlanController::class, 'searchMuscles'])->name('muscles.search');
        Route::post('/', [TrainingPlanController::class, 'store'])->name('store');
        Route::get('/{plan}/edit', [TrainingPlanController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [TrainingPlanController::class, 'update'])->name('update');
        Route::post('/{plan}/duplicate', [TrainingPlanController::class, 'duplicate'])->name('duplicate');
        Route::delete('/{plan}', [TrainingPlanController::class, 'destroy'])->name('destroy');
        Route::get('/{plan}/pdf', [TrainingPlanController::class, 'exportPdf'])->name('pdf');
        Route::get('/{plan}', [TrainingPlanController::class, 'show'])->name('show');
    });
});

// Mensagens diretas
Route::post('/user/block/{user}', [MessageController::class, 'blockUser'])->name('user.block');

Route::prefix('messages')->name('messages.')->group(function () {
    Route::get('/create', [MessageController::class, 'create'])->name('create');
    Route::post('/start', [MessageController::class, 'startConversation'])->name('start');
    Route::post('/bulk-delete', [MessageController::class, 'bulkDelete'])->name('bulk-delete');
    Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
    Route::post('/{conversation}', [MessageController::class, 'store'])->name('store');
});

// Correio interno (rotas nomeadas internal-email.*)
Route::prefix('internal-email')->name('internal-email.')->group(function () {
    Route::get('/', [InternalEmailController::class, 'inbox'])->name('inbox');
    Route::get('/sent', [InternalEmailController::class, 'sent'])->name('sent');
    Route::get('/outbox', [InternalEmailController::class, 'outbox'])->name('outbox');
    Route::get('/trash', [InternalEmailController::class, 'trash'])->name('trash');
    Route::get('/create', [InternalEmailController::class, 'create'])->name('create');
    Route::post('/', [InternalEmailController::class, 'store'])->name('store');
    Route::post('/{message}/restore', [InternalEmailController::class, 'restore'])->name('restore');
    Route::delete('/{message}/permanent', [InternalEmailController::class, 'permanentDelete'])->name('permanent');
    Route::post('/{message}/unread', [InternalEmailController::class, 'markAsUnread'])->name('unread');
    Route::delete('/{message}', [InternalEmailController::class, 'destroy'])->name('destroy');
    Route::get('/{message}', [InternalEmailController::class, 'show'])->name('show');
});

// Grupos de comunicação (mensagens)
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [CommunicationGroupController::class, 'index'])->name('index');
    Route::post('/{group}/join', [CommunicationGroupController::class, 'join'])->name('join');
    Route::post('/{group}/leave', [CommunicationGroupController::class, 'leave'])->name('leave');
});

// Preferências de menu do utilizador
Route::prefix('menu/preferences')->name('menu.preferences.')->group(function () {
    Route::get('/', [MenuPreferenceController::class, 'index'])->name('index');
    Route::post('/', [MenuPreferenceController::class, 'store'])->name('store');
    Route::post('/restore', [MenuPreferenceController::class, 'restore'])->name('restore');
});

// Passos extra do onboarding (além do grupo base em web.php)
Route::prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/step2', [OnboardingController::class, 'step2'])->name('step2');
    Route::post('/step2', [OnboardingController::class, 'saveStep2'])->name('step2.save');
    Route::get('/step2/feedback', [OnboardingController::class, 'step2Feedback'])->name('step2.feedback');
    Route::get('/step2/obstacles', [OnboardingController::class, 'step2Obstacles'])->name('step2.obstacles');
    Route::post('/step2/obstacles', [OnboardingController::class, 'saveStep2Obstacles'])->name('step2.obstacles.save');
    Route::get('/step2/understanding', [OnboardingController::class, 'step2Understanding'])->name('step2.understanding');
    Route::get('/step3', [OnboardingController::class, 'step3'])->name('step3');
    Route::post('/step3', [OnboardingController::class, 'saveStep3'])->name('step3.save');
    Route::get('/step4', [OnboardingController::class, 'step4'])->name('step4');
    Route::post('/step4', [OnboardingController::class, 'saveStep4'])->name('step4.save');
    Route::get('/step5', [OnboardingController::class, 'step5'])->name('step5');
    Route::post('/step5', [OnboardingController::class, 'saveStep5'])->name('step5.save');
    Route::get('/step6', [OnboardingController::class, 'step6'])->name('step6');
    Route::post('/step6', [OnboardingController::class, 'saveStep6'])->name('step6.save');
    Route::get('/step7', [OnboardingController::class, 'step7'])->name('step7');
    Route::post('/step7', [OnboardingController::class, 'saveStep7'])->name('step7.save');
    Route::get('/step8', [OnboardingController::class, 'step8'])->name('step8');
    Route::post('/step8', [OnboardingController::class, 'saveStep8'])->name('step8.save');
});

// Agenda do Sistema
Route::prefix('agenda')->name('agenda.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AgendaController::class, 'index'])->name('index');
    Route::post('/store', [\App\Http\Controllers\AgendaController::class, 'store'])->name('store');
    Route::post('/cancel/{appointment}', [\App\Http\Controllers\AgendaController::class, 'cancel'])->name('cancel');
    Route::post('/waitlist', [\App\Http\Controllers\AgendaController::class, 'waitlist'])->name('waitlist');
});
// Galeria de Evolução NexShape
Route::prefix('evolution')->name('evolution.')->group(function () {
    Route::get('/', [\App\Http\Controllers\EvolutionController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\EvolutionController::class, 'store'])->name('store');
    Route::post('/analyze', [\App\Http\Controllers\EvolutionController::class, 'analyze'])->name('analyze');
    Route::delete('/{id}', [\App\Http\Controllers\EvolutionController::class, 'destroy'])->name('destroy');
});

// Conquistas e Troféus
Route::get('/trophies', [\App\Http\Controllers\TrophyController::class, 'index'])->name('trophies.index')->middleware('premium');

// Esforço do Treino (RPE)
Route::post('/workout-sessions', [\App\Http\Controllers\WorkoutSessionController::class, 'store'])->name('workout-sessions.store');

// Suplementação Evoluída (Smart Stack)
Route::prefix('smart-stacks')->name('smart-stacks.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SmartStackController::class, 'index'])->name('index');
    Route::get('/search-catalog', [\App\Http\Controllers\SmartStackController::class, 'searchCatalog'])->name('search-catalog');
    Route::post('/', [\App\Http\Controllers\SmartStackController::class, 'store'])->name('store');
    Route::post('/suggest', [\App\Http\Controllers\SmartStackController::class, 'suggest'])->name('suggest')->middleware('premium');
    Route::post('/adopt-suggestion', [\App\Http\Controllers\SmartStackController::class, 'adoptSuggestion'])->name('adopt-suggestion')->middleware('premium');
    Route::get('/{stack}', [\App\Http\Controllers\SmartStackController::class, 'show'])->name('show');
    Route::put('/{stack}', [\App\Http\Controllers\SmartStackController::class, 'update'])->name('update');
    Route::delete('/{stack}', [\App\Http\Controllers\SmartStackController::class, 'destroy'])->name('destroy');
    Route::post('/{stack}/supplements', [\App\Http\Controllers\SmartStackController::class, 'addSupplement'])->name('add-supplement');
    Route::delete('/supplements/{supplement}', [\App\Http\Controllers\SupplementController::class, 'destroy'])->name('remove-supplement');
});

// Suplementação (Atalhos legados/rápidos)
Route::prefix('supplements')->name('supplements.')->group(function () {
    Route::post('/', [\App\Http\Controllers\SupplementController::class, 'store'])->name('store');
    Route::post('/{supplement}/take', [\App\Http\Controllers\SupplementController::class, 'take'])->name('take');
    Route::delete('/{supplement}', [\App\Http\Controllers\SupplementController::class, 'destroy'])->name('destroy');
});

