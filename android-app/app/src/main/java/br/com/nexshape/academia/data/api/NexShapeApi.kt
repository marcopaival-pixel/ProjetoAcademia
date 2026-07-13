package br.com.nexshape.academia.data.api

import okhttp3.MultipartBody
import okhttp3.RequestBody
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.Multipart
import retrofit2.http.PATCH
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Part
import retrofit2.http.Path
import retrofit2.http.Query

interface NexShapeApi {
    @GET("health")
    suspend fun health(): Map<String, Any?>

    @POST("auth/token")
    suspend fun login(@Body body: LoginRequest): AuthTokenResponse

    @POST("auth/google")
    suspend fun googleLogin(@Body body: GoogleLoginRequest): AuthTokenResponse

    @POST("auth/register")
    suspend fun register(@Body body: RegisterRequest): RegisterResponse

    @POST("auth/forgot-password")
    suspend fun forgotPassword(@Body body: ForgotPasswordRequest): MessageResponse

    @POST("onboarding/profile")
    suspend fun completeOnboarding(@Body body: OnboardingProfileRequest): ApiSuccessResponse<OnboardingProfileResponse>

    @POST("auth/refresh")
    suspend fun refresh(@Body body: RefreshRequest = RefreshRequest()): AuthTokenResponse

    @DELETE("auth/token")
    suspend fun logout()

    @GET("me")
    suspend fun profile(): ApiSuccessResponse<ProfileDto>

    @PATCH("me")
    suspend fun updateProfile(@Body body: UpdateProfileRequest): ApiSuccessResponse<ProfileDto>

    @GET("training-plans")
    suspend fun trainingPlans(): TrainingPlansResponse

    @POST("training-plans")
    suspend fun createTrainingPlan(@Body body: CreateTrainingPlanRequest): ApiSuccessResponse<TrainingPlanSummaryDto>

    @GET("training-plans/{id}")
    suspend fun trainingPlan(@Path("id") id: Int): ApiSuccessResponse<TrainingPlanDetailDto>

    @PUT("training-plans/{id}")
    suspend fun updateTrainingPlan(
        @Path("id") id: Int,
        @Body body: CreateTrainingPlanRequest,
    ): ApiSuccessResponse<TrainingPlanSummaryDto>

    @DELETE("training-plans/{id}")
    suspend fun deleteTrainingPlan(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @GET("exercise-catalog")
    suspend fun exerciseCatalog(@Query("search") search: String? = null): ApiSuccessResponse<ExerciseCatalogData>

    @Multipart
    @POST("workout-import/process")
    suspend fun processWorkoutImport(
        @Part photo: MultipartBody.Part,
    ): ApiSuccessResponse<WorkoutImportData>

    @POST("workout-import/save")
    suspend fun saveWorkoutImport(@Body body: SaveWorkoutImportRequest): ApiSuccessResponse<SaveWorkoutImportData>

    @GET("nutrition/diary")
    suspend fun nutritionDiary(@Query("date") date: String? = null): ApiSuccessResponse<NutritionDiaryData>

    @POST("nutrition/diary")
    suspend fun createFoodEntry(@Body body: CreateFoodEntryRequest): ApiSuccessResponse<FoodEntryDto>

    @PUT("nutrition/diary/{id}")
    suspend fun updateFoodEntry(
        @Path("id") id: Int,
        @Body body: CreateFoodEntryRequest,
    ): ApiSuccessResponse<FoodEntryDto>

    @DELETE("nutrition/diary/{id}")
    suspend fun deleteFoodEntry(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @POST("nutrition/goal")
    suspend fun updateNutritionGoal(@Body body: UpdateNutritionGoalRequest): ApiSuccessResponse<UpdateNutritionGoalData>

    @GET("nutrition/meal-templates")
    suspend fun mealTemplates(): ApiSuccessResponse<MealTemplatesData>

    @POST("nutrition/meal-templates/{id}/apply")
    suspend fun applyMealTemplate(
        @Path("id") id: Int,
        @Body body: ApplyMealTemplateRequest,
    ): ApiSuccessResponse<ApplyMealTemplateData>

    @GET("hydration/status")
    suspend fun hydrationStatus(@Query("date") date: String? = null): ApiSuccessResponse<HydrationStatusData>

    @POST("hydration/entries")
    suspend fun createHydrationEntry(@Body body: CreateHydrationEntryRequest): ApiSuccessResponse<HydrationEntryDto>

    @DELETE("hydration/entries/{id}")
    suspend fun deleteHydrationEntry(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @GET("chat/history")
    suspend fun chatHistory(@Query("limit") limit: Int = 50): ApiSuccessResponse<ChatHistoryData>

    @POST("chat/send")
    suspend fun chatSend(@Body body: ChatSendRequest): ApiSuccessResponse<ChatSendData>

    @GET("ai/credits")
    suspend fun aiCredits(): ApiSuccessResponse<AiCreditBalanceDto>

    @POST("exercise-logs/sync")
    suspend fun syncExercise(@Body body: ExerciseSyncRequest): ApiSuccessResponse<ExerciseSyncData>

    @POST("load-logs")
    suspend fun createLoadLog(@Body body: CreateLoadLogRequest): ApiSuccessResponse<LoadLogDto>

    @GET("workout-sessions")
    suspend fun workoutSessions(@Query("limit") limit: Int = 30): ApiSuccessResponse<List<WorkoutSessionDto>>

    @GET("workout-sessions/active")
    suspend fun activeWorkoutSession(): ApiSuccessResponse<WorkoutSessionDto?>

    @POST("workout-sessions")
    suspend fun createWorkoutSession(@Body body: WorkoutSessionRequest): ApiSuccessResponse<WorkoutSessionDto>

    @POST("workout-sessions/start")
    suspend fun startWorkoutSession(@Body body: StartWorkoutSessionRequest): ApiSuccessResponse<WorkoutSessionDto>

    @PATCH("workout-sessions/{id}")
    suspend fun updateWorkoutSession(
        @Path("id") id: Int,
        @Body body: UpdateWorkoutSessionRequest,
    ): ApiSuccessResponse<WorkoutSessionDto>

    @POST("devices")
    suspend fun registerDevice(@Body body: DeviceRegisterRequest): ApiSuccessResponse<Map<String, Any?>>

    @GET("notifications/unread-counts")
    suspend fun notificationCounts(): ApiSuccessResponse<NotificationCountsDto>

    @GET("assessments")
    suspend fun assessments(): ApiSuccessResponse<AssessmentsData>

    @GET("assessments/summary")
    suspend fun assessmentSummary(): ApiSuccessResponse<AssessmentSummaryDto>

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

    @GET("subscriptions/current")
    suspend fun currentSubscription(): ApiSuccessResponse<CurrentSubscriptionData>

    @POST("subscriptions/cancel")
    suspend fun cancelSubscription(): ApiSuccessResponse<Map<String, Any?>>

    @GET("student/professionals/search")
    suspend fun searchProfessionals(
        @Query("q") query: String? = null,
        @Query("specialty") specialty: String? = null,
        @Query("service_type") serviceType: String? = null,
    ): ApiSuccessResponse<SearchProfessionalsData>

    @GET("student/professionals/requests")
    suspend fun studentRequests(): ApiSuccessResponse<StudentRequestsData>

    @POST("student/professionals/requests")
    suspend fun createStudentRequest(
        @Body body: RequestConnectionRequest,
    ): ApiSuccessResponse<RequestConnectionResponse>

    @GET("professional/patients/requests")
    suspend fun professionalRequests(): ApiSuccessResponse<ProfessionalRequestsData>

    @POST("professional/patients/requests/{id}/approve")
    suspend fun approveProfessionalRequest(
        @Path("id") id: Int,
    ): ApiSuccessResponse<Map<String, Any?>>

    @POST("professional/patients/requests/{id}/reject")
    suspend fun rejectProfessionalRequest(
        @Path("id") id: Int,
    ): ApiSuccessResponse<Map<String, Any?>>

    @GET("student/professionals")
    suspend fun linkedProfessionals(): ApiSuccessResponse<LinkedProfessionalsData>

    @POST("student/professionals/links/{linkId}/permissions")
    suspend fun updateLinkPermissions(
        @Path("linkId") linkId: Int,
        @Body body: UpdatePermissionsRequest,
    ): ApiSuccessResponse<Map<String, Any?>>

    @POST("student/professionals/links/{linkId}/revoke")
    suspend fun revokeLink(
        @Path("linkId") linkId: Int,
    ): ApiSuccessResponse<Map<String, Any?>>

    @GET("student/appointments")
    suspend fun appointments(): ApiSuccessResponse<AppointmentsData>

    @GET("student/appointments/slots")
    suspend fun appointmentSlots(
        @Query("professional_id") professionalId: Int,
        @Query("date") date: String,
    ): ApiSuccessResponse<AppointmentSlotsData>

    @POST("student/appointments")
    suspend fun createAppointment(@Body body: CreateAppointmentRequest): ApiSuccessResponse<AppointmentDto>

    @POST("student/appointments/waitlist")
    suspend fun joinAppointmentWaitlist(@Body body: AppointmentWaitlistRequest): ApiSuccessResponse<AppointmentWaitlistDto>

    @GET("dashboard")
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

    @GET("student/medical-documents")
    suspend fun studentMedicalDocuments(): ApiSuccessResponse<MedicalDocumentsData>

    @GET("student/medical-documents/reports/{id}/download")
    suspend fun downloadReportPdf(@Path("id") id: Int): okhttp3.ResponseBody

    @GET("student/medical-documents/prescriptions/{id}/download")
    suspend fun downloadPrescriptionPdf(@Path("id") id: Int): okhttp3.ResponseBody

    @GET("student/medical-documents/certificates/{id}/download")
    suspend fun downloadCertificatePdf(@Path("id") id: Int): okhttp3.ResponseBody

    @GET("student/gamification")
    suspend fun studentGamification(): ApiSuccessResponse<GamificationData>

    @GET("student/active-rest")
    suspend fun getActiveRest(): ApiSuccessResponse<ActiveRestData>

    @POST("student/active-rest/{id}/favorite")
    suspend fun toggleActiveRestFavorite(@Path("id") id: Int): ApiSuccessResponse<Map<String, Any?>>

    @POST("student/active-rest/{id}/log")
    suspend fun storeActiveRestLog(@Path("id") id: Int, @Body body: ActiveRestLogRequest): ApiSuccessResponse<Map<String, Any?>>

    @GET("community/posts")
    suspend fun communityPosts(): ApiSuccessResponse<CommunityPostsData>

    @POST("community/posts")
    suspend fun createCommunityPost(@Body body: CreateCommunityPostRequest): ApiSuccessResponse<CommunityPostCreatedData>

    @POST("community/posts/{id}/comments")
    suspend fun createCommunityComment(
        @Path("id") id: Int,
        @Body body: CreateCommunityCommentRequest,
    ): ApiSuccessResponse<CommunityCommentCreatedData>

    @GET("messages/conversations")
    suspend fun conversations(): ApiSuccessResponse<ConversationsData>

    @POST("messages/conversations/support")
    suspend fun startSupportConversation(): ApiSuccessResponse<SupportConversationData>

    @GET("messages/conversations/{id}")
    suspend fun conversationMessages(@Path("id") id: Int): ApiSuccessResponse<ConversationMessagesData>

    @POST("messages/conversations/{id}")
    suspend fun sendInternalMessage(
        @Path("id") id: Int,
        @Body body: SendInternalMessageRequest,
    ): ApiSuccessResponse<InternalMessageCreatedData>
}
