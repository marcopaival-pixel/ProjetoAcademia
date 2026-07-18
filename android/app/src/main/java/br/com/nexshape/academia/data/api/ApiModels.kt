package br.com.nexshape.academia.data.api

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class ApiSuccessResponse<T>(
    val data: T,
    val meta: Map<String, Any?>? = null,
)

@JsonClass(generateAdapter = true)
data class ApiErrorResponse(
    val error: ApiErrorBody,
)

@JsonClass(generateAdapter = true)
data class ApiErrorBody(
    val message: String,
    val code: String? = null,
    val errors: Map<String, List<String>>? = null,
)

@JsonClass(generateAdapter = true)
data class AuthTokenResponse(
    @Json(name = "token_type") val tokenType: String,
    @Json(name = "access_token") val accessToken: String,
    @Json(name = "expires_at") val expiresAt: String? = null,
    val user: AuthUserDto,
)

@JsonClass(generateAdapter = true)
data class AuthUserDto(
    val id: Int,
    val name: String,
    val email: String,
    val roles: List<String>? = null,
)

@JsonClass(generateAdapter = true)
data class LoginRequest(
    val email: String,
    val password: String,
    @Json(name = "device_name") val deviceName: String = "nexshape-android",
)

@JsonClass(generateAdapter = true)
data class GoogleLoginRequest(
    @Json(name = "id_token") val idToken: String,
    @Json(name = "device_name") val deviceName: String = "nexshape-android-google",
)

@JsonClass(generateAdapter = true)
data class ForgotPasswordRequest(
    val email: String,
)

@JsonClass(generateAdapter = true)
data class MessageResponse(
    val message: String,
)

@JsonClass(generateAdapter = true)
data class RegisterRequest(
    val name: String,
    val email: String,
    val password: String,
    @Json(name = "password_confirmation") val passwordConfirmation: String,
    @Json(name = "account_type") val accountType: String,
    @Json(name = "device_name") val deviceName: String = "nexshape-android",
    @Json(name = "birth_date") val birthDate: String? = null,
    val phone: String? = null,
    val cpf: String? = null,
    val sex: String? = null,
    @Json(name = "height_cm") val heightCm: Int? = null,
    @Json(name = "current_weight_kg") val currentWeightKg: Double? = null,
    val goal: String? = null,
    @Json(name = "activity_level") val activityLevel: String? = null,
    @Json(name = "has_injury") val hasInjury: Boolean? = null,
    @Json(name = "injury_details") val injuryDetails: String? = null,
    @Json(name = "has_disease") val hasDisease: Boolean? = null,
    @Json(name = "disease_details") val diseaseDetails: String? = null,
    @Json(name = "uses_medication") val usesMedication: Boolean? = null,
    @Json(name = "medication_details") val medicationDetails: String? = null,
    @Json(name = "fitness_notes") val fitnessNotes: String? = null,
    @Json(name = "accepted_terms") val acceptedTerms: Boolean? = null,
)

@JsonClass(generateAdapter = true)
data class RegisterResponse(
    val message: String,
    @Json(name = "token_type") val tokenType: String? = null,
    @Json(name = "access_token") val accessToken: String? = null,
    @Json(name = "expires_at") val expiresAt: String? = null,
    val user: AuthUserDto? = null,
)

@JsonClass(generateAdapter = true)
data class OnboardingProfileRequest(
    @Json(name = "birth_date") val birthDate: String,
    val phone: String,
    val cpf: String,
    val sex: String,
    @Json(name = "height_cm") val heightCm: Int,
    @Json(name = "current_weight_kg") val currentWeightKg: Double,
    val goal: String,
    @Json(name = "activity_level") val activityLevel: String,
    @Json(name = "has_injury") val hasInjury: Boolean,
    @Json(name = "injury_details") val injuryDetails: String? = null,
    @Json(name = "has_disease") val hasDisease: Boolean,
    @Json(name = "disease_details") val diseaseDetails: String? = null,
    @Json(name = "uses_medication") val usesMedication: Boolean,
    @Json(name = "medication_details") val medicationDetails: String? = null,
    @Json(name = "fitness_notes") val fitnessNotes: String? = null,
    @Json(name = "accepted_terms") val acceptedTerms: Boolean,
)

@JsonClass(generateAdapter = true)
data class OnboardingProfileResponse(
    val message: String,
    @Json(name = "profile_completion_percentage") val profileCompletionPercentage: Int,
    @Json(name = "onboarding_status") val onboardingStatus: String,
)

@JsonClass(generateAdapter = true)
data class RefreshRequest(
    @Json(name = "device_name") val deviceName: String = "nexshape-android",
)

@JsonClass(generateAdapter = true)
data class DeviceRegisterRequest(
    val token: String,
    val platform: String = "android",
    @Json(name = "device_name") val deviceName: String = "nexshape-android",
    @Json(name = "app_version") val appVersion: String = "1.0.0",
)

@JsonClass(generateAdapter = true)
data class AccessContextDto(
    val type: String,
    val id: String,
    val label: String,
)

@JsonClass(generateAdapter = true)
data class ProfileDto(
    val id: Int,
    val name: String,
    val email: String,
    val roles: List<String>? = null,
    @Json(name = "access_contexts") val accessContexts: List<AccessContextDto>? = null,
    @Json(name = "is_premium") val isPremium: Boolean = false,
    @Json(name = "is_student") val isStudent: Boolean = false,
    @Json(name = "is_professional") val isProfessional: Boolean = false,
    val panels: List<String>? = null,
    val modules: List<String>? = null,
    @Json(name = "active_patient_id") val activePatientId: Int? = null,
    @Json(name = "clinic_id") val clinicId: Int? = null,
    @Json(name = "academy_company_id") val academyCompanyId: Int? = null,
    val status: String? = null,
    val branding: BrandingDto? = null,
    val organizations: List<OrganizationDto>? = null,
    val vinculos: List<VinculoDto>? = null,
    @Json(name = "student_status") val studentStatus: String? = null,
    val profile: ProfileDetailsDto? = null,
)

@JsonClass(generateAdapter = true)
data class VinculoDto(
    val id: Int,
    val name: String,
    val specialty: String? = null,
)

@JsonClass(generateAdapter = true)
data class ProfileDetailsDto(
    @Json(name = "birth_date") val birthDate: String? = null,
    val sex: String? = null,
    @Json(name = "height_cm") val heightCm: Int? = null,
    @Json(name = "current_weight_kg") val currentWeightKg: Double? = null,
    @Json(name = "target_weight_kg") val targetWeightKg: Double? = null,
    @Json(name = "activity_level") val activityLevel: String? = null,
    val climate: String? = null,
    val goal: String? = null,
    @Json(name = "daily_calorie_target") val dailyCalorieTarget: Int? = null,
    @Json(name = "water_target_ml") val waterTargetMl: Int? = null,
    @Json(name = "is_water_target_auto") val isWaterTargetAuto: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class UpdateProfileRequest(
    val name: String? = null,
    @Json(name = "birth_date") val birthDate: String? = null,
    val sex: String? = null,
    @Json(name = "height_cm") val heightCm: Int? = null,
    @Json(name = "current_weight_kg") val currentWeightKg: Double? = null,
    @Json(name = "target_weight_kg") val targetWeightKg: Double? = null,
    @Json(name = "activity_level") val activityLevel: String? = null,
    val climate: String? = null,
    val goal: String? = null,
    @Json(name = "daily_calorie_target") val dailyCalorieTarget: Int? = null,
    @Json(name = "water_target_ml") val waterTargetMl: Int? = null,
    @Json(name = "is_water_target_auto") val isWaterTargetAuto: Boolean? = null,
)

@JsonClass(generateAdapter = true)
data class BrandingDto(
    @Json(name = "primary_color") val primaryColor: String? = null,
    @Json(name = "accent_color") val accentColor: String? = null,
    @Json(name = "clinic_name") val clinicName: String? = null,
)

@JsonClass(generateAdapter = true)
data class OrganizationDto(
    val id: Int,
    val name: String,
    val type: String? = null,
    val role: String? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingPlanSummaryDto(
    val id: Int,
    val name: String,
    @Json(name = "plan_label") val planLabel: String? = null,
    val goal: String? = null,
    val status: String? = null,
    @Json(name = "is_active") val isActive: Boolean = false,
    @Json(name = "exercises_count") val exercisesCount: Int = 0,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingPlansResponse(
    val data: List<TrainingPlanSummaryDto>,
    val meta: TrainingPlansMeta? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingPlansMeta(
    @Json(name = "is_premium") val isPremium: Boolean = false,
    val count: Int = 0,
    @Json(name = "has_professional_link") val hasProfessionalLink: Boolean = false,
    @Json(name = "can_create_own_workout") val canCreateOwnWorkout: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class CreateTrainingPlanRequest(
    val name: String,
    @Json(name = "plan_label") val planLabel: String? = null,
    val goal: String? = null,
    val description: String? = null,
    val frequency: Int? = null,
    val difficulty: String? = null,
    @Json(name = "estimated_duration") val estimatedDuration: Int? = null,
    @Json(name = "student_profile") val studentProfile: String? = null,
    @Json(name = "split_type") val splitType: String? = null,
    val status: String? = null,
    @Json(name = "days_of_week") val daysOfWeek: List<String>? = null,
    @Json(name = "total_volume") val totalVolume: Double? = null,
    @Json(name = "muscles_worked") val musclesWorked: List<String>? = null,
    @Json(name = "target_areas") val targetAreas: List<TrainingTargetAreaRequest>? = null,
    @Json(name = "is_template") val isTemplate: Boolean = false,
    val exercises: List<CreateTrainingExerciseRequest>? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingTargetAreaRequest(
    val id: Int? = null,
    val name: String,
)

@JsonClass(generateAdapter = true)
data class CreateTrainingExerciseRequest(
    val id: Int,
    val notes: String? = null,
    val sets: List<CreateTrainingSetRequest>,
)

@JsonClass(generateAdapter = true)
data class CreateTrainingSetRequest(
    val type: String = "work",
    val reps: Int? = null,
    val weight: Double? = null,
    val rest: Int? = null,
    val rpe: Int? = null,
    val cadence: String? = null,
)

@JsonClass(generateAdapter = true)
data class ExerciseCatalogData(
    val exercises: List<ExerciseCatalogDto>,
)

@JsonClass(generateAdapter = true)
data class ExerciseCatalogDto(
    val id: Int,
    val name: String,
    @Json(name = "muscle_group") val muscleGroup: String? = null,
    val equipment: String? = null,
    val difficulty: String? = null,
    val muscles: List<String> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class WorkoutImportExerciseDto(
    @Json(name = "nome_exercicio") val nomeExercicio: String,
    val series: String? = null,
    val repeticoes: String? = null,
    val carga: String? = null,
    val observacoes: String? = null,
    val day: String? = null,
)

@JsonClass(generateAdapter = true)
data class WorkoutImportData(
    val exercises: List<WorkoutImportExerciseDto>,
    @Json(name = "log_id") val logId: Int? = null,
    val status: String? = null,
)

@JsonClass(generateAdapter = true)
data class WorkoutOrchestratedImportData(
    val uuid: String? = null,
    val status: String,
    @Json(name = "error_message") val errorMessage: String? = null,
    @Json(name = "log_id") val logId: Int? = null,
    val exercises: List<WorkoutImportExerciseDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class WorkoutValidationData(
    @Json(name = "is_workout") val isWorkout: Boolean,
    val confidence: Double,
    @Json(name = "document_type") val documentType: String? = null,
    val reason: String? = null,
    @Json(name = "detected_elements") val detectedElements: Map<String, Boolean>? = null,
)

@JsonClass(generateAdapter = true)
data class SaveWorkoutImportRequest(
    @Json(name = "workout_name") val workoutName: String,
    val exercises: List<WorkoutImportExerciseDto>,
)

@JsonClass(generateAdapter = true)
data class SaveWorkoutImportData(
    val message: String,
    @Json(name = "plan_id") val planId: Int,
)

@JsonClass(generateAdapter = true)
data class TrainingPlanDetailDto(
    val id: Int,
    val name: String,
    @Json(name = "plan_label") val planLabel: String? = null,
    val goal: String? = null,
    val status: String? = null,
    val description: String? = null,
    val frequency: String? = null,
    val difficulty: String? = null,
    val exercises: List<TrainingExerciseDto>? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingExerciseDto(
    val id: Int,
    @Json(name = "exercise_id") val exerciseId: Int? = null,
    val position: Int? = null,
    val name: String? = null,
    @Json(name = "muscle_group") val muscleGroup: String? = null,
    val notes: String? = null,
    @Json(name = "last_log") val lastLog: LoadLogDto? = null,
    val sets: List<ExerciseSetDto>? = null,
)

@JsonClass(generateAdapter = true)
data class ExerciseSetDto(
    val id: Int,
    @Json(name = "set_number") val setNumber: Int? = null,
    @Json(name = "reps_target") val repsTarget: Int? = null,
    @Json(name = "rest_seconds") val restSeconds: Int? = null,
)

@JsonClass(generateAdapter = true)
data class LoadLogDto(
    val id: Int,
    @Json(name = "training_plan_exercise_id") val trainingPlanExerciseId: Int,
    @Json(name = "exercise_id") val exerciseId: Int,
    @Json(name = "log_date") val logDate: String? = null,
    @Json(name = "set_number") val setNumber: Int,
    @Json(name = "reps_done") val repsDone: Int,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    val rpe: Int? = null,
    @Json(name = "to_failure") val toFailure: Boolean = false,
    @Json(name = "one_rm") val oneRm: Double? = null,
)

@JsonClass(generateAdapter = true)
data class CreateLoadLogRequest(
    @Json(name = "training_plan_exercise_id") val trainingPlanExerciseId: Int,
    @Json(name = "exercise_id") val exerciseId: Int,
    @Json(name = "log_date") val logDate: String,
    @Json(name = "set_number") val setNumber: Int,
    @Json(name = "reps_done") val repsDone: Int,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    val rpe: Int? = null,
    @Json(name = "to_failure") val toFailure: Boolean = false,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class FoodEntryDto(
    val id: Int,
    @Json(name = "meal_type") val mealType: String,
    @Json(name = "food_name") val foodName: String,
    val amount: Double? = null,
    val unit: String? = null,
    val calories: Int,
    @Json(name = "protein_g") val proteinG: Double? = null,
    @Json(name = "carbs_g") val carbsG: Double? = null,
    @Json(name = "fat_g") val fatG: Double? = null,
    @Json(name = "entry_date") val entryDate: String? = null,
)

@JsonClass(generateAdapter = true)
data class NutritionDiaryData(
    val date: String,
    val totals: NutritionTotalsDto,
    val targets: NutritionTargetsDto? = null,
    val entries: List<FoodEntryDto>,
)

@JsonClass(generateAdapter = true)
data class NutritionTargetsDto(
    val goal: String? = null,
    val calories: Int? = null,
    @Json(name = "protein_g") val proteinG: Double? = null,
    @Json(name = "carbs_g") val carbsG: Double? = null,
    @Json(name = "fat_g") val fatG: Double? = null,
)

@JsonClass(generateAdapter = true)
data class UpdateNutritionGoalRequest(
    val goal: String,
    val split: String,
)

@JsonClass(generateAdapter = true)
data class UpdateNutritionGoalData(
    val message: String? = null,
    val targets: NutritionTargetsDto,
)

@JsonClass(generateAdapter = true)
data class NutritionTotalsDto(
    val calories: Int,
    @Json(name = "protein_g") val proteinG: Double,
    @Json(name = "carbs_g") val carbsG: Double,
    @Json(name = "fat_g") val fatG: Double,
)

@JsonClass(generateAdapter = true)
data class CreateFoodEntryRequest(
    @Json(name = "entry_date") val entryDate: String,
    @Json(name = "food_name") val foodName: String,
    val calories: Int,
    @Json(name = "meal_type") val mealType: String,
    val amount: Double? = 1.0,
    val unit: String? = "g",
    @Json(name = "protein_g") val proteinG: Double? = 0.0,
    @Json(name = "carbs_g") val carbsG: Double? = 0.0,
    @Json(name = "fat_g") val fatG: Double? = 0.0,
)

@JsonClass(generateAdapter = true)
data class AnalyzeMealRequest(
    val description: String,
    @Json(name = "meal_type") val mealType: String,
)

@JsonClass(generateAdapter = true)
data class AnalyzeMealData(
    @Json(name = "meal_type") val mealType: String,
    @Json(name = "food_name") val foodName: String,
    val amount: Double? = null,
    val unit: String? = null,
    val calories: Int,
    @Json(name = "protein_g") val proteinG: Double,
    @Json(name = "carbs_g") val carbsG: Double,
    @Json(name = "fat_g") val fatG: Double,
    val confidence: Double,
    val notes: String? = null,
    val source: String? = null,
)

@JsonClass(generateAdapter = true)
data class MealSuggestionData(
    val suggestion: String,
    val remaining: NutritionRemainingDto,
)

@JsonClass(generateAdapter = true)
data class NutritionRemainingDto(
    @Json(name = "remaining_kcal") val remainingKcal: Double,
    @Json(name = "remaining_p") val remainingProteinG: Double,
    @Json(name = "remaining_c") val remainingCarbsG: Double,
    @Json(name = "remaining_f") val remainingFatG: Double,
)

@JsonClass(generateAdapter = true)
data class WeeklyAuditData(
    val audit: String,
    @Json(name = "days_analyzed") val daysAnalyzed: Int,
)

@JsonClass(generateAdapter = true)
data class HydrationStatusData(
    val date: String,
    @Json(name = "target_ml") val targetMl: Int,
    @Json(name = "consumed_ml") val consumedMl: Int,
    val percentage: Int,
    @Json(name = "expected_now_ml") val expectedNowMl: Int,
    val status: String,
    @Json(name = "is_auto") val isAuto: Boolean,
    val entries: List<HydrationEntryDto>,
)

@JsonClass(generateAdapter = true)
data class HydrationEntryDto(
    val id: Int,
    @Json(name = "entry_date") val entryDate: String? = null,
    @Json(name = "drank_at") val drankAt: String? = null,
    @Json(name = "amount_ml") val amountMl: Int,
    val source: String? = null,
)

@JsonClass(generateAdapter = true)
data class CreateHydrationEntryRequest(
    @Json(name = "amount_ml") val amountMl: Int,
    val source: String = "android",
    @Json(name = "entry_date") val entryDate: String? = null,
)

@JsonClass(generateAdapter = true)
data class MealTemplatesData(
    val templates: List<MealTemplateDto>,
)

@JsonClass(generateAdapter = true)
data class MealTemplateDto(
    val id: Int,
    val name: String,
    @Json(name = "professional_id") val professionalId: Int? = null,
    @Json(name = "created_at") val createdAt: String? = null,
    val items: List<MealTemplateItemDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class MealTemplateItemDto(
    val id: Int,
    @Json(name = "meal_type") val mealType: String,
    @Json(name = "food_name") val foodName: String,
    val calories: Int,
    @Json(name = "protein_g") val proteinG: Double,
    @Json(name = "carbs_g") val carbsG: Double,
    @Json(name = "fat_g") val fatG: Double,
    val position: Int,
)

@JsonClass(generateAdapter = true)
data class ApplyMealTemplateRequest(
    @Json(name = "entry_date") val entryDate: String,
)

@JsonClass(generateAdapter = true)
data class ApplyMealTemplateData(
    val applied: Int,
)

@JsonClass(generateAdapter = true)
data class ChatMessageDto(
    val id: Int? = null,
    val role: String,
    val message: String,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class ChatHistoryData(
    val messages: List<ChatMessageDto>,
)

@JsonClass(generateAdapter = true)
data class ChatSendRequest(
    val message: String,
    @Json(name = "force_ia") val forceIa: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class ChatSendData(
    val message: String,
    val source: String? = null,
)

@JsonClass(generateAdapter = true)
data class AiCreditBalanceDto(
    val balance: Int,
    @Json(name = "monthly_allowance") val monthlyAllowance: Int = 0,
    @Json(name = "extra_credits") val extraCredits: Int = 0,
    @Json(name = "renewal_date") val renewalDate: String? = null,
    @Json(name = "expires_at") val expiresAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class ExerciseSyncRequest(
    val id: Int? = null,
    @Json(name = "entry_date") val entryDate: String,
    @Json(name = "activity_type") val activityType: String? = null,
    @Json(name = "duration_min") val durationMin: Int? = null,
    @Json(name = "calories_burned") val caloriesBurned: Int? = null,
    val rpe: Int? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class ExerciseSyncData(
    val id: Int,
    val synced: Boolean,
)

@JsonClass(generateAdapter = true)
data class WorkoutSessionDto(
    val id: Int,
    @Json(name = "training_plan_id") val trainingPlanId: Int? = null,
    @Json(name = "session_date") val sessionDate: String,
    val status: String? = null,
    @Json(name = "started_at") val startedAt: String? = null,
    @Json(name = "ended_at") val endedAt: String? = null,
    @Json(name = "completion_percent") val completionPercent: Int = 0,
    @Json(name = "completed_exercise_ids") val completedExerciseIds: List<Int> = emptyList(),
    @Json(name = "rpe_score") val rpeScore: Int? = null,
    val mood: String? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class WorkoutSessionRequest(
    @Json(name = "session_date") val sessionDate: String,
    @Json(name = "rpe_score") val rpeScore: Int,
    val mood: String? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class StartWorkoutSessionRequest(
    @Json(name = "training_plan_id") val trainingPlanId: Int,
)

@JsonClass(generateAdapter = true)
data class UpdateWorkoutSessionRequest(
    val status: String,
    @Json(name = "completion_percent") val completionPercent: Int? = null,
    @Json(name = "completed_exercise_ids") val completedExerciseIds: List<Int>? = null,
    @Json(name = "rpe_score") val rpeScore: Int? = null,
    val mood: String? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class BodyAssessmentDto(
    val id: Int,
    @Json(name = "assessment_date") val assessmentDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
    val neck: Double? = null,
    val chest: Double? = null,
    val waist: Double? = null,
    val abdomen: Double? = null,
    val hips: Double? = null,
    @Json(name = "bicep_l") val bicepL: Double? = null,
    @Json(name = "bicep_r") val bicepR: Double? = null,
    @Json(name = "forearm_l") val forearmL: Double? = null,
    @Json(name = "forearm_r") val forearmR: Double? = null,
    @Json(name = "thigh_l") val thighL: Double? = null,
    @Json(name = "thigh_r") val thighR: Double? = null,
    @Json(name = "calf_l") val calfL: Double? = null,
    @Json(name = "calf_r") val calfR: Double? = null,
    @Json(name = "blood_pressure") val bloodPressure: String? = null,
    @Json(name = "heart_rate") val heartRate: Int? = null,
    val notes: String? = null,
    val status: String? = null,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class AssessmentsData(
    val assessments: List<BodyAssessmentDto>,
)

@JsonClass(generateAdapter = true)
data class AssessmentSummaryDto(
    @Json(name = "current_weight_kg") val currentWeightKg: Double? = null,
    @Json(name = "initial_weight_kg") val initialWeightKg: Double? = null,
    @Json(name = "target_weight_kg") val targetWeightKg: Double? = null,
    @Json(name = "height_cm") val heightCm: Int? = null,
    val bmi: Double? = null,
    @Json(name = "current_bf_percent") val currentBfPercent: Double? = null,
    @Json(name = "current_muscle_percent") val currentMusclePercent: Double? = null,
    @Json(name = "last_assessment_date") val lastAssessmentDate: String? = null,
    @Json(name = "next_assessment_date") val nextAssessmentDate: String? = null,
    val deltas: AssessmentDeltasDto? = null,
    @Json(name = "goal_progress_percent") val goalProgressPercent: Int? = null,
    @Json(name = "assessments_count") val assessmentsCount: Int = 0,
)

@JsonClass(generateAdapter = true)
data class AssessmentDeltasDto(
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
)

@JsonClass(generateAdapter = true)
data class CreateAssessmentRequest(
    @Json(name = "assessment_date") val assessmentDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
    val neck: Double? = null,
    val chest: Double? = null,
    val waist: Double? = null,
    val abdomen: Double? = null,
    val hips: Double? = null,
    @Json(name = "bicep_l") val bicepL: Double? = null,
    @Json(name = "bicep_r") val bicepR: Double? = null,
    @Json(name = "forearm_l") val forearmL: Double? = null,
    @Json(name = "forearm_r") val forearmR: Double? = null,
    @Json(name = "thigh_l") val thighL: Double? = null,
    @Json(name = "thigh_r") val thighR: Double? = null,
    @Json(name = "calf_l") val calfL: Double? = null,
    @Json(name = "calf_r") val calfR: Double? = null,
    @Json(name = "blood_pressure") val bloodPressure: String? = null,
    @Json(name = "heart_rate") val heartRate: Int? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionPhotoDto(
    val id: Int,
    val type: String,
    @Json(name = "registered_date") val registeredDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "media_url") val mediaUrl: String,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionPhotosData(
    val photos: List<EvolutionPhotoDto>,
)

@JsonClass(generateAdapter = true)
data class BodyAnalysisDto(
    val id: Int,
    @Json(name = "photo_url") val photoUrl: String? = null,
    @Json(name = "view_type") val viewType: String,
    val metrics: Map<String, Double?> = emptyMap(),
    val summary: String? = null,
    val diet: String? = null,
    val workout: String? = null,
    val exercises: List<String> = emptyList(),
    @Json(name = "attention_points") val attentionPoints: List<String> = emptyList(),
    val limitations: List<String> = emptyList(),
    @Json(name = "vision_summary") val visionSummary: String? = null,
    @Json(name = "vision_model") val visionModel: String? = null,
    @Json(name = "vision_confidence") val visionConfidence: Double? = null,
    @Json(name = "analysis_version") val analysisVersion: String? = null,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class BodyAnalysisListData(
    val analyses: List<BodyAnalysisDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class BodyAnalysisData(
    val analysis: BodyAnalysisDto,
)

@JsonClass(generateAdapter = true)
data class BodyAnalysisMetricDeltaDto(
    val key: String,
    val label: String,
    val first: Double? = null,
    val second: Double? = null,
    val diff: Double? = null,
    @Json(name = "higher_is_better") val higherIsBetter: Boolean = true,
    val status: String = "stable",
)

@JsonClass(generateAdapter = true)
data class BodyAnalysisCompareData(
    val first: BodyAnalysisDto,
    val second: BodyAnalysisDto,
    val metrics: List<BodyAnalysisMetricDeltaDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class BodyPhotoValidationDto(
    val approved: Boolean = false,
    val status: String? = null,
    val messages: List<String> = emptyList(),
    val warnings: List<String> = emptyList(),
    @Json(name = "next_suggestions") val nextSuggestions: List<String> = emptyList(),
    val confidence: Double? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionSessionAnalyzeRequest(
    val date: String,
)

@JsonClass(generateAdapter = true)
data class EvolutionSessionAnalysisData(
    val analysis: EvolutionSessionAnalysisDto,
    val cached: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class EvolutionSessionAnalysisDto(
    val summary: String? = null,
    val message: String? = null,
    @Json(name = "next_recommendations") val nextRecommendations: List<String> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class EvolutionReportDto(
    val status: String,
    val nome: String? = null,
    @Json(name = "periodo_comparado") val comparedPeriod: EvolutionComparedPeriodDto? = null,
    val modo: String? = null,
    @Json(name = "dados_confirmados") val confirmedData: List<EvolutionConfirmedDataDto> = emptyList(),
    @Json(name = "observacoes_visuais") val visualObservations: List<EvolutionVisualObservationDto> = emptyList(),
    @Json(name = "qualidade_fotos") val photoQuality: Map<String, Any?> = emptyMap(),
    val limitacoes: List<String> = emptyList(),
    val recomendacoes: List<String> = emptyList(),
    @Json(name = "confianca_geral") val generalConfidence: Double = 0.0,
    val alertas: List<String> = emptyList(),
    @Json(name = "auditoria_aprovada") val auditApproved: Boolean = false,
    val auditoria: EvolutionAuditDto? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionReportConsentDto(
    val required: Boolean = true,
    val type: String,
    val version: String,
    val accepted: Boolean = false,
    @Json(name = "accepted_at") val acceptedAt: String? = null,
    val copy: EvolutionReportConsentCopyDto? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionReportConsentCopyDto(
    val title: String? = null,
    val summary: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionReportRequest(
    @Json(name = "accept_ai_body_photo_analysis") val acceptAiBodyPhotoAnalysis: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class EvolutionReportRequestData(
    @Json(name = "report_id") val reportId: Int,
    val status: String,
    @Json(name = "current_session_date") val currentSessionDate: String? = null,
    @Json(name = "previous_session_date") val previousSessionDate: String? = null,
    val message: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionReportStatusData(
    val id: Int,
    val status: String,
    @Json(name = "current_session_date") val currentSessionDate: String? = null,
    @Json(name = "previous_session_date") val previousSessionDate: String? = null,
    val confidence: Double? = null,
    @Json(name = "failure_reason") val failureReason: String? = null,
    @Json(name = "limited_reason") val limitedReason: String? = null,
    @Json(name = "final_report") val finalReport: EvolutionReportDto? = null,
    @Json(name = "started_at") val startedAt: String? = null,
    @Json(name = "completed_at") val completedAt: String? = null,
    @Json(name = "published_at") val publishedAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionComparedPeriodDto(
    @Json(name = "registro_anterior") val previousRecord: String? = null,
    @Json(name = "registro_atual") val currentRecord: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionConfirmedDataDto(
    val metrica: String,
    val atual: Double? = null,
    val anterior: Double? = null,
    @Json(name = "variacao_absoluta") val absoluteVariation: Double? = null,
    @Json(name = "variacao_percentual") val percentVariation: Double? = null,
    val fonte: String? = null,
    val confianca: Double? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionVisualObservationDto(
    val texto: String,
    val fonte: String? = null,
    @Json(name = "registro_atual") val currentRecord: String? = null,
    @Json(name = "registro_anterior") val previousRecord: String? = null,
    val confianca: Double? = null,
    val limitacao: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionAuditDto(
    val modelo: String? = null,
    val agentes: List<String> = emptyList(),
    val fontes: List<String> = emptyList(),
    val falhas: List<String> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class PaymentMethodsDto(
    @Json(name = "credit_card") val creditCard: Boolean = false,
    val pix: Boolean = false,
    val boleto: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class PaymentStatusDto(
    @Json(name = "active_gateway") val activeGateway: String? = null,
    @Json(name = "active_label") val activeLabel: String? = null,
    val methods: PaymentMethodsDto? = null,
)

@JsonClass(generateAdapter = true)
data class SubscriptionPlanDto(
    val id: Int,
    val name: String,
    val price: Double,
    @Json(name = "billing_cycle") val billingCycle: String? = null,
    val description: String? = null,
)

@JsonClass(generateAdapter = true)
data class SubscriptionPlansData(
    val plans: List<SubscriptionPlanDto>,
)

@JsonClass(generateAdapter = true)
data class CheckoutRequest(
    @Json(name = "plan_id") val planId: Int,
    @Json(name = "payment_method") val paymentMethod: String? = "pix",
)

@JsonClass(generateAdapter = true)
data class CheckoutData(
    val status: String,
    @Json(name = "subscription_id") val subscriptionId: Int? = null,
    val plan: String? = null,
    @Json(name = "checkout_url") val checkoutUrl: String? = null,
    val gateway: String? = null,
    @Json(name = "app_return_links") val appReturnLinks: Map<String, String>? = null,
)

@JsonClass(generateAdapter = true)
data class ClientErrorRequest(
    val type: String? = "error",
    val message: String,
    val stack: String? = null,
    val url: String? = null,
)

@JsonClass(generateAdapter = true)
data class NotificationCountsDto(
    val emails: Int = 0,
    val messages: Int = 0,
    val total: Int = 0,
)

@JsonClass(generateAdapter = true)
data class LinkedProfessionalDto(
    val id: Int,
    @Json(name = "link_id") val linkId: Int? = null,
    val name: String,
    val email: String? = null,
    val specialty: String? = null,
    @Json(name = "service_types") val serviceTypes: List<String>? = null,
    val branding: BrandingDto? = null,
    val permissions: Map<String, Boolean>? = null,
)

@JsonClass(generateAdapter = true)
data class LinkedProfessionalsData(
    val professionals: List<LinkedProfessionalDto>,
)

@JsonClass(generateAdapter = true)
data class UpdatePermissionsRequest(
    val permissions: Map<String, Boolean>,
)

@JsonClass(generateAdapter = true)
data class AppointmentDto(
    val id: Int,
    @Json(name = "professional_id") val professionalId: Int,
    @Json(name = "professional_name") val professionalName: String? = null,
    @Json(name = "appointment_at") val appointmentAt: String,
    val status: String,
    @Json(name = "status_label") val statusLabel: String? = null,
    @Json(name = "service_type") val serviceType: String? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class AppointmentsData(
    val appointments: List<AppointmentDto>,
)

@JsonClass(generateAdapter = true)
data class AppointmentSlotDto(
    val time: String,
    val available: Boolean,
)

@JsonClass(generateAdapter = true)
data class AppointmentSlotsData(
    val date: String,
    @Json(name = "professional_id") val professionalId: Int,
    val slots: List<AppointmentSlotDto>,
)

@JsonClass(generateAdapter = true)
data class CreateAppointmentRequest(
    @Json(name = "professional_id") val professionalId: Int,
    @Json(name = "appointment_at") val appointmentAt: String,
    @Json(name = "service_type") val serviceType: String = "Avaliação",
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class AppointmentWaitlistRequest(
    @Json(name = "professional_id") val professionalId: Int,
    val date: String,
)

@JsonClass(generateAdapter = true)
data class AppointmentWaitlistDto(
    val id: Int,
    @Json(name = "professional_id") val professionalId: Int,
    @Json(name = "requested_date") val requestedDate: String,
    val status: String,
)

@JsonClass(generateAdapter = true)
data class ProfessionalPatientDto(
    val id: Int,
    val name: String,
    val email: String? = null,
    val status: String? = null,
    @Json(name = "last_activity_at") val lastActivityAt: String? = null,
    val goal: String? = null,
    @Json(name = "birth_date") val birthDate: String? = null,
    @Json(name = "last_weight_kg") val lastWeightKg: Double? = null,
    @Json(name = "last_assessment_date") val lastAssessmentDate: String? = null,
    @Json(name = "last_bf_percent") val lastBfPercent: Double? = null,
)

@JsonClass(generateAdapter = true)
data class ProfessionalPatientsData(
    val patients: List<ProfessionalPatientDto>,
)

@JsonClass(generateAdapter = true)
data class ProfessionalPatientDetailData(
    val patient: ProfessionalPatientDto,
)

@JsonClass(generateAdapter = true)
data class ProfessionalDashboardStatsDto(
    @Json(name = "total_patients") val totalPatients: Int = 0,
    @Json(name = "active_patients_30d") val activePatients30d: Int = 0,
    @Json(name = "today_appointments") val todayAppointments: Int = 0,
    @Json(name = "pending_appointments") val pendingAppointments: Int = 0,
    @Json(name = "assessments_this_month") val assessmentsThisMonth: Int = 0,
    @Json(name = "active_training_plans") val activeTrainingPlans: Int = 0,
    @Json(name = "unread_alerts") val unreadAlerts: Int = 0,
)

@JsonClass(generateAdapter = true)
data class ProfessionalDashboardData(
    val stats: ProfessionalDashboardStatsDto,
)

@JsonClass(generateAdapter = true)
data class ProfessionalAppointmentDto(
    val id: Int,
    @Json(name = "patient_id") val patientId: Int,
    @Json(name = "patient_name") val patientName: String? = null,
    @Json(name = "appointment_at") val appointmentAt: String,
    val status: String,
    @Json(name = "status_label") val statusLabel: String? = null,
    @Json(name = "service_type") val serviceType: String? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class ProfessionalAppointmentsData(
    val appointments: List<ProfessionalAppointmentDto>,
)

@JsonClass(generateAdapter = true)
data class UpdateAppointmentStatusRequest(
    val status: String,
)

@JsonClass(generateAdapter = true)
data class ProfessionalAlertDto(
    val id: Int,
    @Json(name = "patient_id") val patientId: Int,
    @Json(name = "patient_name") val patientName: String? = null,
    val type: String? = null,
    val severity: String? = null,
    val message: String,
    @Json(name = "is_read") val isRead: Boolean = false,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class ProfessionalAlertsData(
    val alerts: List<ProfessionalAlertDto>,
)

@JsonClass(generateAdapter = true)
data class ClinicProtocolDto(
    val id: Int,
    val name: String,
    val type: String? = null,
    val description: String? = null,
    val objective: String? = null,
    val frequency: Int? = null,
    val duration: Int? = null,
)

@JsonClass(generateAdapter = true)
data class ClinicProtocolsData(
    val protocols: List<ClinicProtocolDto>,
)

@JsonClass(generateAdapter = true)
data class CreatePatientTrainingPlanRequest(
    val name: String,
    val goal: String? = null,
    val description: String? = null,
    val frequency: Int? = null,
    @Json(name = "protocol_id") val protocolId: Int? = null,
)

@JsonClass(generateAdapter = true)
data class CreatePatientAssessmentRequest(
    @Json(name = "assessment_date") val assessmentDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
    val neck: Double? = null,
    val chest: Double? = null,
    val waist: Double? = null,
    val abdomen: Double? = null,
    val hips: Double? = null,
    val notes: String? = null,
    @Json(name = "blood_pressure") val bloodPressure: String? = null,
    @Json(name = "heart_rate") val heartRate: Int? = null,
)

@JsonClass(generateAdapter = true)
data class PatientTrainingPlansData(
    val plans: List<TrainingPlanSummaryDto>,
)

@JsonClass(generateAdapter = true)
data class MedicalDocumentsData(
    val reports: List<MedicalDocumentDto> = emptyList(),
    val prescriptions: List<MedicalDocumentDto> = emptyList(),
    val certificates: List<MedicalDocumentDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class MedicalDocumentDto(
    val id: Int,
    val title: String,
    val date: String?,
    val description: String? = null,
    @Json(name = "professional_name") val professionalName: String? = null,
)

@JsonClass(generateAdapter = true)
data class CurrentSubscriptionData(
    val subscription: SubscriptionDto? = null,
    @Json(name = "is_premium") val isPremium: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class CommunityPostsData(
    val posts: List<CommunityPostDto>,
)

@JsonClass(generateAdapter = true)
data class CommunityPostDto(
    val id: Int,
    @Json(name = "author_name") val authorName: String? = null,
    val content: String,
    val visibility: String? = null,
    val status: String? = null,
    @Json(name = "created_at") val createdAt: String? = null,
    @Json(name = "reactions_count") val reactionsCount: Int = 0,
    @Json(name = "comments_count") val commentsCount: Int = 0,
    val comments: List<CommunityCommentDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class CommunityCommentDto(
    val id: Int,
    @Json(name = "author_name") val authorName: String? = null,
    val content: String,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class CreateCommunityPostRequest(
    val content: String,
    val visibility: String = "public",
)

@JsonClass(generateAdapter = true)
data class CreateCommunityCommentRequest(
    val content: String,
)

@JsonClass(generateAdapter = true)
data class CommunityPostCreatedData(
    val post: CommunityPostDto,
)

@JsonClass(generateAdapter = true)
data class CommunityCommentCreatedData(
    val comment: CommunityCommentDto,
)

@JsonClass(generateAdapter = true)
data class ConversationsData(
    val conversations: List<ConversationDto>,
)

@JsonClass(generateAdapter = true)
data class ConversationDto(
    val id: Int,
    val type: String? = null,
    val status: String? = null,
    @Json(name = "other_user_name") val otherUserName: String? = null,
    @Json(name = "last_message") val lastMessage: String? = null,
    @Json(name = "last_message_at") val lastMessageAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class ConversationMessagesData(
    val conversation: ConversationDto,
    val messages: List<InternalMessageDto>,
)

@JsonClass(generateAdapter = true)
data class InternalMessageDto(
    val id: Int,
    @Json(name = "sender_name") val senderName: String? = null,
    val content: String,
    @Json(name = "is_mine") val isMine: Boolean = false,
    @Json(name = "is_read") val isRead: Boolean = false,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class SendInternalMessageRequest(
    val content: String,
)

@JsonClass(generateAdapter = true)
data class InternalMessageCreatedData(
    val message: InternalMessageDto,
)

@JsonClass(generateAdapter = true)
data class SupportConversationData(
    val conversation: ConversationDto,
)

@JsonClass(generateAdapter = true)
data class SubscriptionDto(
    val id: Int,
    val status: String,
    @Json(name = "financial_status") val financialStatus: String? = null,
    @Json(name = "payment_method") val paymentMethod: String? = null,
    @Json(name = "gateway_type") val gatewayType: String? = null,
    @Json(name = "start_date") val startDate: String? = null,
    @Json(name = "end_date") val endDate: String? = null,
    @Json(name = "next_billing_date") val nextBillingDate: String? = null,
    @Json(name = "cancelled_at") val cancelledAt: String? = null,
    @Json(name = "days_overdue") val daysOverdue: Int? = null,
    @Json(name = "retry_count") val retryCount: Int? = null,
    val plan: PlanDto? = null,
    @Json(name = "pending_plan") val pendingPlan: PlanDto? = null,
)

@JsonClass(generateAdapter = true)
data class PlanDto(
    val id: Int,
    val name: String,
    val price: Double,
    @Json(name = "billing_cycle") val billingCycle: String? = null,
    val description: String? = null,
)

@JsonClass(generateAdapter = true)
data class ProfessionalSearchResultDto(
    val id: Int,
    val name: String,
    val email: String,
    val specialty: String? = null,
    @Json(name = "service_types") val serviceTypes: List<String>? = null,
    val profession: String? = null,
)

@JsonClass(generateAdapter = true)
data class SearchProfessionalsData(
    val professionals: List<ProfessionalSearchResultDto>,
    val meta: PaginationMetaDto? = null,
)

@JsonClass(generateAdapter = true)
data class PaginationMetaDto(
    @Json(name = "current_page") val currentPage: Int,
    @Json(name = "last_page") val lastPage: Int,
    val total: Int,
)

@JsonClass(generateAdapter = true)
data class ProfessionalPatientRequestDto(
    val id: Int,
    @Json(name = "professional_id") val professionalId: Int? = null,
    @Json(name = "professional_name") val professionalName: String? = null,
    @Json(name = "patient_id") val patientId: Int? = null,
    @Json(name = "patient_name") val patientName: String? = null,
    @Json(name = "patient_email") val patientEmail: String? = null,
    val message: String? = null,
    val status: String,
    @Json(name = "created_at") val createdAt: String? = null,
)

@JsonClass(generateAdapter = true)
data class StudentRequestsData(
    val requests: List<ProfessionalPatientRequestDto>,
)

@JsonClass(generateAdapter = true)
data class ConnectionRequestSummaryDto(
    val id: Int,
    val status: String,
)

@JsonClass(generateAdapter = true)
data class RequestConnectionResponse(
    val message: String,
    val request: ConnectionRequestSummaryDto,
)

@JsonClass(generateAdapter = true)
data class ProfessionalRequestsData(
    val requests: List<ProfessionalPatientRequestDto>,
)

@JsonClass(generateAdapter = true)
data class RequestConnectionRequest(
    @Json(name = "professional_id") val professionalId: Int,
    val message: String? = null,
)
