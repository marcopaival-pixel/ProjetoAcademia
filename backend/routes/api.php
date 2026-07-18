<?php

use App\Http\Controllers\Api\V1\MobileAuthController;
use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\ActiveRestController;
use App\Http\Controllers\Api\V1\AgendaController;
use App\Http\Controllers\Api\V1\AiCreditController;
use App\Http\Controllers\Api\V1\AssessmentController;
use App\Http\Controllers\Api\V1\BodyAnalysisController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\CommunityController;
use App\Http\Controllers\Api\V1\EvolutionController;
use App\Http\Controllers\Api\V1\GamificationController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\HydrationController;
use App\Http\Controllers\Api\V1\MedicalDocumentController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\MobileUtilityController;
use App\Http\Controllers\Api\V1\NutritionDiaryController;
use App\Http\Controllers\Api\V1\NutritionGoalController;
use App\Http\Controllers\Api\V1\MealTemplateController;
use App\Http\Controllers\Api\V1\ProfessionalConnectionController;
use App\Http\Controllers\Api\V1\SupplementController;
use App\Http\Controllers\Api\V1\CommunicationGroupController;
use App\Http\Controllers\Api\V1\PaymentStatusController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\TrainingLogController;
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

    Route::post('/auth/register', [MobileAuthController::class, 'register'])->name('auth.register');
    Route::post('/auth/google', [MobileAuthController::class, 'google'])->name('auth.google');
    Route::post('/auth/forgot-password', [MobileAuthController::class, 'forgotPassword'])->name('auth.forgot-password');
    Route::post('/auth/reset-password', [MobileAuthController::class, 'resetPassword'])->name('auth.reset-password');

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
        Route::get('/ai/credits', [AiCreditController::class, 'show'])->name('ai.credits.show');
        Route::post('/devices', [MobileUtilityController::class, 'registerDevice'])->name('devices.store');
        Route::get('/notifications/unread-counts', [MobileUtilityController::class, 'notificationCounts'])->name('notifications.unread-counts');
        Route::post('/client-errors', [MobileUtilityController::class, 'clientError'])->name('client-errors.store');
        Route::get('/chat/history', [ChatController::class, 'history'])->name('chat.history');
        Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

        Route::get('/nutrition/diary', [NutritionDiaryController::class, 'index'])->name('nutrition.diary');
        Route::post('/nutrition/diary', [NutritionDiaryController::class, 'store'])->name('nutrition.diary.store');
        Route::put('/nutrition/diary/{entry}', [NutritionDiaryController::class, 'update'])->name('nutrition.diary.update');
        Route::delete('/nutrition/diary/{entry}', [NutritionDiaryController::class, 'destroy'])->name('nutrition.diary.destroy');
        Route::post('/uploads/nutrition-photo', [NutritionDiaryController::class, 'uploadPhoto'])->name('uploads.nutrition-photo');
        Route::post('/nutrition/analyze-meal', [NutritionDiaryController::class, 'analyzeMeal'])->name('nutrition.analyze-meal');
        Route::post('/nutrition/analyze-photo', [NutritionDiaryController::class, 'analyzePhoto'])->name('nutrition.analyze-photo');
        Route::post('/nutrition/suggest-meal', [NutritionDiaryController::class, 'suggestMeal'])->name('nutrition.suggest-meal');
        Route::post('/nutrition/weekly-audit', [NutritionDiaryController::class, 'weeklyAudit'])->name('nutrition.weekly-audit');

        Route::get('/nutrition/goal', [NutritionGoalController::class, 'show'])->name('nutrition.goal.show');
        Route::put('/nutrition/goal', [NutritionGoalController::class, 'update'])->name('nutrition.goal.update');

        Route::get('/nutrition/meal-templates', [MealTemplateController::class, 'index'])->name('nutrition.meal-templates.index');
        Route::post('/nutrition/meal-templates', [MealTemplateController::class, 'store'])->name('nutrition.meal-templates.store');
        Route::post('/nutrition/meal-templates/{template}/apply', [MealTemplateController::class, 'apply'])->name('nutrition.meal-templates.apply');

        Route::get('/hydration/status', [HydrationController::class, 'status'])->name('hydration.status');
        Route::post('/hydration/entries', [HydrationController::class, 'store'])->name('hydration.entries.store');
        Route::delete('/hydration/entries/{entry}', [HydrationController::class, 'destroy'])->name('hydration.entries.destroy');

        Route::get('/workout-sessions', [WorkoutSessionController::class, 'index'])->name('workout-sessions.index');
        Route::get('/workout-sessions/active', [WorkoutSessionController::class, 'active'])->name('workout-sessions.active');
        Route::post('/workout-sessions', [WorkoutSessionController::class, 'store'])->name('workout-sessions.store');
        Route::post('/workout-sessions/start', [WorkoutSessionController::class, 'start'])->name('workout-sessions.start');
        Route::patch('/workout-sessions/{session}', [WorkoutSessionController::class, 'update'])->name('workout-sessions.update');
        Route::post('/load-logs', [TrainingLogController::class, 'store'])->name('load-logs.store');
        Route::post('/exercise-logs/sync', [TrainingLogController::class, 'sync'])->name('exercise-logs.sync');

        Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::post('/assessments/{assessment}/re-analyze', [AssessmentController::class, 'reAnalyze'])->name('assessments.re-analyze');
        Route::get('/assessments/summary', [AssessmentController::class, 'summary'])->name('assessments.summary');
        Route::get('/student/assessments/{id}/pdf', [AssessmentController::class, 'downloadPdf'])->name('student.assessments.pdf');
        Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');

        Route::get('/evolution-photos', [EvolutionController::class, 'photos'])->name('evolution-photos.index');
        Route::get('/evolution-report', [EvolutionController::class, 'report'])->name('evolution-report.show');
        Route::get('/evolution-reports/consent', [EvolutionController::class, 'reportConsent'])->name('evolution-reports.consent');
        Route::post('/evolution-reports', [EvolutionController::class, 'requestReport'])->name('evolution-reports.store');
        Route::get('/evolution/{report}/comparison', [EvolutionController::class, 'compare'])->name('evolution.compare');
        Route::post('/evolution/batch-analyze', [EvolutionController::class, 'batchAnalyze'])->name('evolution.batch-analyze');
        Route::get('/student/evolution-reports/{id}/pdf', [EvolutionController::class, 'downloadPdf'])->name('student.evolution-reports.pdf');
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

        Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
        Route::post('/subscriptions/checkout', [SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
        Route::get('/subscriptions/current', [SubscriptionController::class, 'current'])->name('subscriptions.current');
        Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        Route::get('/student/professionals', [ProfessionalConnectionController::class, 'index'])->name('student.professionals.index');
        Route::post('/student/professionals/request', [ProfessionalConnectionController::class, 'requestConnection'])->name('student.professionals.request');
        Route::patch('/student/professionals/{professionalPatient}/permissions', [ProfessionalConnectionController::class, 'updatePermissions'])->name('student.professionals.permissions');
        Route::get('/student/professionals/search', [AgendaController::class, 'professionals'])->name('student.professionals.search');
        Route::get('/student/appointments', [AgendaController::class, 'index'])->name('student.appointments.index');
        Route::get('/student/appointments/slots', [AgendaController::class, 'slots'])->name('student.appointments.slots');
        Route::post('/student/appointments', [AgendaController::class, 'store'])->name('student.appointments.store');
        Route::post('/student/appointments/waitlist', [AgendaController::class, 'waitlist'])->name('student.appointments.waitlist');

        Route::get('/student/medical-documents', [MedicalDocumentController::class, 'index'])->name('medical-documents.index');
        Route::get('/student/medical-documents/{type}/{id}/download', [MedicalDocumentController::class, 'download'])->name('medical-documents.download');
        Route::get('/evolution/history', [EvolutionController::class, 'history'])->name('evolution.history');
        Route::get('/student/gamification', [GamificationController::class, 'show'])->name('student.gamification.show');
        Route::get('/student/active-rest', [ActiveRestController::class, 'index'])->name('student.active-rest.index');
        Route::post('/student/active-rest/{routine}/favorite', [ActiveRestController::class, 'favorite'])->name('student.active-rest.favorite');
        Route::post('/student/active-rest/{routine}/log', [ActiveRestController::class, 'log'])->name('student.active-rest.log');

        Route::get('/student/supplements', [SupplementController::class, 'index'])->name('student.supplements.index');
        Route::post('/student/supplements/{id}/log', [SupplementController::class, 'log'])->name('student.supplements.log');

        Route::get('/student/communication-groups', [CommunicationGroupController::class, 'index'])->name('student.communication-groups.index');

        Route::get('/messages/conversations', [MessageController::class, 'index'])->name('messages.conversations.index');
        Route::post('/messages/conversations/support', [MessageController::class, 'startSupport'])->name('messages.conversations.support');
        Route::get('/messages/conversations/{conversation}', [MessageController::class, 'show'])->name('messages.conversations.show');
        Route::post('/messages/conversations/{conversation}', [MessageController::class, 'store'])->name('messages.conversations.store');
    });
});
