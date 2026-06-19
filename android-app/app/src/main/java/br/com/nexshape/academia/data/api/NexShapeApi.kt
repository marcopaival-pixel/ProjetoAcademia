package br.com.nexshape.academia.data.api

import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.PATCH
import retrofit2.http.POST
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query

interface NexShapeApi {
    @GET("health")
    suspend fun health(): Map<String, Any?>

    @POST("auth/token")
    suspend fun login(@Body body: LoginRequest): AuthTokenResponse

    @POST("auth/refresh")
    suspend fun refresh(@Body body: RefreshRequest = RefreshRequest()): AuthTokenResponse

    @DELETE("auth/token")
    suspend fun logout()

    @GET("me")
    suspend fun profile(): ApiSuccessResponse<ProfileDto>

    @PATCH("me")
    suspend fun updateProfile(@Body body: Map<String, String>): ApiSuccessResponse<Map<String, Any?>>

    @GET("training-plans")
    suspend fun trainingPlans(): TrainingPlansResponse

    @GET("training-plans/{id}")
    suspend fun trainingPlan(@Path("id") id: Int): ApiSuccessResponse<TrainingPlanDetailDto>

    @GET("nutrition/diary")
    suspend fun nutritionDiary(@Query("date") date: String? = null): ApiSuccessResponse<NutritionDiaryData>

    @POST("nutrition/diary")
    suspend fun createFoodEntry(@Body body: CreateFoodEntryRequest): ApiSuccessResponse<FoodEntryDto>

    @GET("chat/history")
    suspend fun chatHistory(@Query("limit") limit: Int = 50): ApiSuccessResponse<ChatHistoryData>

    @POST("chat/send")
    suspend fun chatSend(@Body body: ChatSendRequest): ApiSuccessResponse<ChatSendData>

    @POST("exercise-logs/sync")
    suspend fun syncExercise(@Body body: ExerciseSyncRequest): ApiSuccessResponse<ExerciseSyncData>

    @POST("devices")
    suspend fun registerDevice(@Body body: DeviceRegisterRequest): ApiSuccessResponse<Map<String, Any?>>

    @GET("assessments")
    suspend fun assessments(): ApiSuccessResponse<AssessmentsData>

    @POST("assessments")
    suspend fun createAssessment(@Body body: CreateAssessmentRequest): ApiSuccessResponse<BodyAssessmentDto>

    @GET("evolution-photos")
    suspend fun evolutionPhotos(): ApiSuccessResponse<EvolutionPhotosData>

    @DELETE("evolution-photos/{id}")
    suspend fun deleteEvolutionPhoto(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @Multipart
    @POST("evolution-photos")
    suspend fun uploadEvolutionPhoto(
        @Part photo: MultipartBody.Part,
        @Part("type") type: RequestBody,
        @Part("registered_date") registeredDate: RequestBody,
        @Part("weight_kg") weightKg: RequestBody? = null,
    ): ApiSuccessResponse<EvolutionPhotoDto>

    @GET("payments/status")
    suspend fun paymentStatus(): PaymentStatusDto

    @GET("subscriptions/plans")
    suspend fun subscriptionPlans(): ApiSuccessResponse<SubscriptionPlansData>

    @POST("subscriptions/checkout")
    suspend fun subscriptionCheckout(@Body body: CheckoutRequest): ApiSuccessResponse<CheckoutData>

    @GET("student/professionals")
    suspend fun linkedProfessionals(): ApiSuccessResponse<LinkedProfessionalsData>

    @GET("student/appointments")
    suspend fun appointments(): ApiSuccessResponse<AppointmentsData>

    @GET("student/appointments/slots")
    suspend fun appointmentSlots(
        @Query("professional_id") professionalId: Int,
        @Query("date") date: String,
    ): ApiSuccessResponse<AppointmentSlotsData>

    @POST("student/appointments")
    suspend fun createAppointment(@Body body: CreateAppointmentRequest): ApiSuccessResponse<AppointmentDto>

    @GET("professional/dashboard")
    suspend fun professionalDashboard(): ApiSuccessResponse<ProfessionalDashboardData>

    @GET("professional/patients")
    suspend fun professionalPatients(@Query("search") search: String? = null): ApiSuccessResponse<ProfessionalPatientsData>

    @GET("professional/patients/{id}")
    suspend fun professionalPatient(@Path("id") id: Int): ApiSuccessResponse<ProfessionalPatientDetailData>

    @GET("professional/appointments")
    suspend fun professionalAppointments(
        @Query("date") date: String? = null,
        @Query("status") status: String? = null,
    ): ApiSuccessResponse<ProfessionalAppointmentsData>

    @PATCH("professional/appointments/{id}/status")
    suspend fun updateProfessionalAppointmentStatus(
        @Path("id") id: Int,
        @Body body: UpdateAppointmentStatusRequest,
    ): ApiSuccessResponse<ProfessionalAppointmentDto>

    @GET("professional/alerts")
    suspend fun professionalAlerts(
        @Query("unread_only") unreadOnly: Boolean? = null,
        @Query("limit") limit: Int? = null,
    ): ApiSuccessResponse<ProfessionalAlertsData>

    @PATCH("professional/alerts/{id}/read")
    suspend fun markProfessionalAlertRead(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @GET("professional/protocols")
    suspend fun professionalProtocols(@Query("type") type: String? = "training"): ApiSuccessResponse<ClinicProtocolsData>

    @GET("professional/patients/{patientId}/training-plans")
    suspend fun patientTrainingPlans(@Path("patientId") patientId: Int): ApiSuccessResponse<PatientTrainingPlansData>

    @GET("professional/patients/{patientId}/training-plans/{planId}")
    suspend fun patientTrainingPlanDetail(
        @Path("patientId") patientId: Int,
        @Path("planId") planId: Int,
    ): ApiSuccessResponse<TrainingPlanDetailDto>

    @POST("professional/patients/{patientId}/training-plans")
    suspend fun createPatientTrainingPlan(
        @Path("patientId") patientId: Int,
        @Body body: CreatePatientTrainingPlanRequest,
    ): ApiSuccessResponse<TrainingPlanSummaryDto>

    @GET("professional/patients/{patientId}/assessments")
    suspend fun patientAssessments(@Path("patientId") patientId: Int): ApiSuccessResponse<AssessmentsData>

    @POST("professional/patients/{patientId}/assessments")
    suspend fun createPatientAssessment(
        @Path("patientId") patientId: Int,
        @Body body: CreatePatientAssessmentRequest,
    ): ApiSuccessResponse<BodyAssessmentDto>

    @GET("professional/patients/{patientId}/evolution-photos")
    suspend fun patientEvolutionPhotos(@Path("patientId") patientId: Int): ApiSuccessResponse<EvolutionPhotosData>

    @Multipart
    @POST("professional/patients/{patientId}/evolution-photos")
    suspend fun uploadPatientEvolutionPhoto(
        @Path("patientId") patientId: Int,
        @Part photo: MultipartBody.Part,
        @Part("type") type: RequestBody,
        @Part("registered_date") registeredDate: RequestBody,
        @Part("weight_kg") weightKg: RequestBody? = null,
    ): ApiSuccessResponse<EvolutionPhotoDto>

    @POST("client-errors")
    suspend fun reportClientError(@Body body: ClientErrorRequest): Map<String, Boolean>

    @Multipart
    @POST("uploads/nutrition-photo")
    suspend fun uploadNutritionPhoto(
        @Part photo: MultipartBody.Part,
    ): ApiSuccessResponse<Map<String, Any?>>
}
