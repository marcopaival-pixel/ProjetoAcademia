<?php

use App\Http\Controllers\Api\V1\AssessmentController;
use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\ClientErrorController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\EvolutionPhotoController;
use App\Http\Controllers\Api\V1\ExerciseLogController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\MediaUploadController;
use App\Http\Controllers\Api\V1\NutritionDiaryController;
use App\Http\Controllers\Api\V1\OrchestratorController;
use App\Http\Controllers\Api\V1\PaymentStatusController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProfessionalAlertController;
use App\Http\Controllers\Api\V1\ProfessionalAppointmentController;
use App\Http\Controllers\Api\V1\ProfessionalDashboardController;
use App\Http\Controllers\Api\V1\ProfessionalPatientAssessmentController;
use App\Http\Controllers\Api\V1\ProfessionalPatientEvolutionController;
use App\Http\Controllers\Api\V1\ProfessionalPatientController;
use App\Http\Controllers\Api\V1\ProfessionalPatientTrainingController;
use App\Http\Controllers\Api\V1\ProfessionalProtocolController;
use App\Http\Controllers\Api\V1\StudentAppointmentController;
use App\Http\Controllers\Api\V1\StudentProfessionalController;
use App\Http\Controllers\Api\V1\SubscriptionCheckoutController;
use App\Http\Controllers\Api\V1\TrainingPlanController;
use App\Http\Controllers\Api\V1\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

/*
| API v1 — autenticação via Laravel Sanctum (Bearer token).
| Documentação resumida: docs/API_V1.md
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', HealthController::class)->name('health');

    Route::post('/client-errors', [ClientErrorController::class, 'store'])
        ->middleware('throttle:client-errors')
        ->name('client-errors.store');

    Route::post('/auth/token', [AuthTokenController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('auth.token');

    Route::post('/referral/verify', [\App\Http\Controllers\Api\ReferralCodeController::class, 'verify'])
        ->middleware('throttle:15,1')
        ->name('referral.verify');

    Route::middleware([
        'auth:sanctum',
        \App\Http\Middleware\SetApiTenantContext::class,
        'api.active_patient',
        'throttle:api',
    ])->group(function () {
        Route::get('/me', [ProfileController::class, 'show'])->name('me');
        Route::patch('/me', [ProfileController::class, 'update'])->name('me.update');
        Route::post('/auth/refresh', [AuthTokenController::class, 'refresh'])->name('auth.refresh');
        Route::delete('/auth/token', [AuthTokenController::class, 'destroy'])->name('auth.token.revoke');

        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::delete('/devices', [DeviceController::class, 'destroy'])->name('devices.destroy');

        Route::get('/payments/status', PaymentStatusController::class)->name('payments.status');

        Route::get('/media/{type}/{id}', [MediaController::class, 'show'])->name('media.show');

        Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/history', [ChatController::class, 'history'])->name('chat.history');
        Route::delete('/chat/history', [ChatController::class, 'clear'])->name('chat.clear');
        Route::post('/chat/actions', [ChatController::class, 'executeAction'])->name('chat.actions');

        Route::post('/ai/orchestrator', [OrchestratorController::class, 'process'])->name('ai.orchestrator');
        Route::get('/ai/orchestrator/status/{jobKey}', [OrchestratorController::class, 'status'])->name('ai.orchestrator.status');

        Route::middleware('api.role:aluno,paciente')->group(function () {
            Route::get('/training-plans', [TrainingPlanController::class, 'index'])->name('training-plans.index');
            Route::get('/training-plans/{training_plan}', [TrainingPlanController::class, 'show'])->name('training-plans.show');

            Route::get('/exercise-logs', [ExerciseLogController::class, 'index'])->name('exercise-logs.index');
            Route::post('/exercise-logs/sync', [ExerciseLogController::class, 'sync'])->name('exercise-logs.sync');
            Route::delete('/exercise-logs/{id}', [ExerciseLogController::class, 'destroy'])->name('exercise-logs.destroy');

            Route::get('/nutrition/diary', [NutritionDiaryController::class, 'index'])->name('nutrition.diary');
            Route::post('/nutrition/diary', [NutritionDiaryController::class, 'store'])->name('nutrition.diary.store');
            Route::put('/nutrition/diary/{foodEntry}', [NutritionDiaryController::class, 'update'])->name('nutrition.diary.update');
            Route::delete('/nutrition/diary/{foodEntry}', [NutritionDiaryController::class, 'destroy'])->name('nutrition.diary.destroy');

            Route::get('/workout-sessions', [WorkoutSessionController::class, 'index'])->name('workout-sessions.index');
            Route::post('/workout-sessions', [WorkoutSessionController::class, 'store'])->name('workout-sessions.store');

            Route::get('/assessments', [AssessmentController::class, 'index'])->name('assessments.index');
            Route::post('/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
            Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');

            Route::get('/evolution-photos', [EvolutionPhotoController::class, 'index'])->name('evolution-photos.index');
            Route::post('/evolution-photos', [EvolutionPhotoController::class, 'store'])->name('evolution-photos.store');
            Route::delete('/evolution-photos/{photo}', [EvolutionPhotoController::class, 'destroy'])->name('evolution-photos.destroy');

            Route::post('/uploads/workout-photo', [MediaUploadController::class, 'workoutPhoto'])->name('uploads.workout-photo');
            Route::post('/uploads/nutrition-photo', [MediaUploadController::class, 'nutritionPhoto'])->name('uploads.nutrition-photo');

            Route::get('/subscriptions/plans', [SubscriptionCheckoutController::class, 'plans'])->name('subscriptions.plans');
            Route::post('/subscriptions/checkout', [SubscriptionCheckoutController::class, 'checkout'])->name('subscriptions.checkout');

            Route::get('/student/professionals', [StudentProfessionalController::class, 'index'])->name('student.professionals.index');
            Route::get('/student/appointments/slots', [StudentAppointmentController::class, 'slots'])->name('student.appointments.slots');
            Route::get('/student/appointments', [StudentAppointmentController::class, 'index'])->name('student.appointments.index');
            Route::post('/student/appointments', [StudentAppointmentController::class, 'store'])->name('student.appointments.store');
        });

        Route::middleware('api.role:professional,instructor,supervisor')->prefix('professional')->name('professional.')->group(function () {
            Route::get('/dashboard', [ProfessionalDashboardController::class, 'index'])->name('dashboard');
            Route::get('/patients', [ProfessionalPatientController::class, 'index'])->name('patients.index');
            Route::get('/patients/{patient}', [ProfessionalPatientController::class, 'show'])->name('patients.show');
            Route::get('/appointments', [ProfessionalAppointmentController::class, 'index'])->name('appointments.index');
            Route::patch('/appointments/{appointment}/status', [ProfessionalAppointmentController::class, 'updateStatus'])->name('appointments.status');
            Route::get('/protocols', [ProfessionalProtocolController::class, 'index'])->name('protocols.index');
            Route::get('/patients/{patient}/training-plans', [ProfessionalPatientTrainingController::class, 'index'])->name('patients.training-plans.index');
            Route::post('/patients/{patient}/training-plans', [ProfessionalPatientTrainingController::class, 'store'])->name('patients.training-plans.store');
            Route::get('/patients/{patient}/training-plans/{training_plan}', [ProfessionalPatientTrainingController::class, 'show'])->name('patients.training-plans.show');
            Route::get('/patients/{patient}/assessments', [ProfessionalPatientAssessmentController::class, 'index'])->name('patients.assessments.index');
            Route::post('/patients/{patient}/assessments', [ProfessionalPatientAssessmentController::class, 'store'])->name('patients.assessments.store');
            Route::get('/patients/{patient}/evolution-photos', [ProfessionalPatientEvolutionController::class, 'index'])->name('patients.evolution-photos.index');
            Route::post('/patients/{patient}/evolution-photos', [ProfessionalPatientEvolutionController::class, 'store'])->name('patients.evolution-photos.store');
            Route::get('/alerts', [ProfessionalAlertController::class, 'index'])->name('alerts.index');
            Route::patch('/alerts/{alert}/read', [ProfessionalAlertController::class, 'markRead'])->name('alerts.read');
        });
    });
});
