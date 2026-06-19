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
data class ProfileDto(
    val id: Int,
    val name: String,
    val email: String,
    val roles: List<String>? = null,
    @Json(name = "is_premium") val isPremium: Boolean = false,
    @Json(name = "is_student") val isStudent: Boolean = false,
    @Json(name = "is_professional") val isProfessional: Boolean = false,
    val panels: List<String>? = null,
    @Json(name = "active_patient_id") val activePatientId: Int? = null,
    @Json(name = "clinic_id") val clinicId: Int? = null,
    @Json(name = "academy_company_id") val academyCompanyId: Int? = null,
    val status: String? = null,
    val branding: BrandingDto? = null,
)

@JsonClass(generateAdapter = true)
data class BrandingDto(
    @Json(name = "primary_color") val primaryColor: String? = null,
    @Json(name = "accent_color") val accentColor: String? = null,
    @Json(name = "clinic_name") val clinicName: String? = null,
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
)

@JsonClass(generateAdapter = true)
data class TrainingPlanDetailDto(
    val id: Int,
    val name: String,
    val description: String? = null,
    val frequency: String? = null,
    val difficulty: String? = null,
    val exercises: List<TrainingExerciseDto>? = null,
)

@JsonClass(generateAdapter = true)
data class TrainingExerciseDto(
    val id: Int,
    val position: Int? = null,
    val name: String? = null,
    @Json(name = "muscle_group") val muscleGroup: String? = null,
    val notes: String? = null,
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
    val entries: List<FoodEntryDto>,
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
data class ExerciseSyncRequest(
    val id: Int? = null,
    @Json(name = "entry_date") val entryDate: String,
    @Json(name = "activity_type") val activityType: String? = null,
    @Json(name = "duration_min") val durationMin: Int? = null,
    @Json(name = "calories_burned") val caloriesBurned: Int? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class ExerciseSyncData(
    val id: Int,
    val synced: Boolean,
)

@JsonClass(generateAdapter = true)
data class BodyAssessmentDto(
    val id: Int,
    @Json(name = "assessment_date") val assessmentDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
    val waist: Double? = null,
    val notes: String? = null,
    val status: String? = null,
)

@JsonClass(generateAdapter = true)
data class AssessmentsData(
    val assessments: List<BodyAssessmentDto>,
)

@JsonClass(generateAdapter = true)
data class CreateAssessmentRequest(
    @Json(name = "assessment_date") val assessmentDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "bf_percent") val bfPercent: Double? = null,
    @Json(name = "muscle_percent") val musclePercent: Double? = null,
    val waist: Double? = null,
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class EvolutionPhotoDto(
    val id: Int,
    val type: String,
    @Json(name = "registered_date") val registeredDate: String,
    @Json(name = "weight_kg") val weightKg: Double? = null,
    @Json(name = "media_url") val mediaUrl: String,
)

@JsonClass(generateAdapter = true)
data class EvolutionPhotosData(
    val photos: List<EvolutionPhotoDto>,
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
data class LinkedProfessionalDto(
    val id: Int,
    val name: String,
    val email: String? = null,
    val specialty: String? = null,
    @Json(name = "service_types") val serviceTypes: List<String>? = null,
    val branding: BrandingDto? = null,
)

@JsonClass(generateAdapter = true)
data class LinkedProfessionalsData(
    val professionals: List<LinkedProfessionalDto>,
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
    val notes: String? = null,
)

@JsonClass(generateAdapter = true)
data class PatientTrainingPlansData(
    val plans: List<TrainingPlanSummaryDto>,
)
