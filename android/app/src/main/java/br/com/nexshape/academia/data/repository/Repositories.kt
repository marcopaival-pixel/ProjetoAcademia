package br.com.nexshape.academia.data.repository

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.AnalyzeMealRequest
import br.com.nexshape.academia.data.api.CreatePatientAssessmentRequest
import br.com.nexshape.academia.data.api.CreatePatientTrainingPlanRequest
import br.com.nexshape.academia.data.api.CreateTrainingPlanRequest
import br.com.nexshape.academia.data.api.ExerciseCatalogDto
import br.com.nexshape.academia.data.api.ExerciseSyncRequest
import br.com.nexshape.academia.data.api.EvolutionReportRequest
import br.com.nexshape.academia.data.api.CreateLoadLogRequest
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.api.TrainingPlansResponse
import br.com.nexshape.academia.data.api.StartWorkoutSessionRequest
import br.com.nexshape.academia.data.api.SaveWorkoutImportRequest
import br.com.nexshape.academia.data.api.UpdateWorkoutSessionRequest
import br.com.nexshape.academia.data.api.UpdateAppointmentStatusRequest
import br.com.nexshape.academia.data.api.WorkoutSessionRequest
import br.com.nexshape.academia.data.api.WorkoutImportData
import br.com.nexshape.academia.data.api.WorkoutValidationData
import br.com.nexshape.academia.data.local.AppDatabase
import br.com.nexshape.academia.data.local.PendingSyncEntity
import com.squareup.moshi.Types
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.delay
import kotlinx.coroutines.withContext
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.RequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.time.LocalDate

class TrainingRepository {
    suspend fun listPlans(): Result<List<TrainingPlanSummaryDto>> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().trainingPlans().data }
    }

    suspend fun getPlansResponse(): Result<TrainingPlansResponse> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().trainingPlans() }
    }

    suspend fun planDetail(id: Int): Result<TrainingPlanDetailDto> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().trainingPlan(id).data }
    }

    suspend fun createPlan(request: CreateTrainingPlanRequest): Result<TrainingPlanSummaryDto> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().createTrainingPlan(request).data }
    }

    suspend fun createPlanWithTargetPhotos(
        request: CreateTrainingPlanRequest,
        targetPhotos: List<okhttp3.MultipartBody.Part>,
    ): Result<TrainingPlanSummaryDto> = withContext(Dispatchers.IO) {
        runCatching {
            val moshi = Moshi.Builder().add(KotlinJsonAdapterFactory()).build()
            val stringListType = Types.newParameterizedType(List::class.java, String::class.java)
            val targetAreaType = Types.newParameterizedType(
                List::class.java,
                br.com.nexshape.academia.data.api.TrainingTargetAreaRequest::class.java,
            )
            val exerciseType = Types.newParameterizedType(
                List::class.java,
                br.com.nexshape.academia.data.api.CreateTrainingExerciseRequest::class.java,
            )

            ApiClient.api().createTrainingPlanMultipart(
                name = request.name.formPart(),
                planLabel = request.planLabel?.formPart(),
                goal = request.goal?.formPart(),
                description = request.description?.formPart(),
                frequency = request.frequency?.toString()?.formPart(),
                difficulty = request.difficulty?.formPart(),
                estimatedDuration = request.estimatedDuration?.toString()?.formPart(),
                studentProfile = request.studentProfile?.formPart(),
                splitType = request.splitType?.formPart(),
                status = request.status?.formPart(),
                daysOfWeek = request.daysOfWeek?.let { moshi.adapter<List<String>>(stringListType).toJson(it).formPart() },
                totalVolume = request.totalVolume?.toString()?.formPart(),
                musclesWorked = request.musclesWorked?.let { moshi.adapter<List<String>>(stringListType).toJson(it).formPart() },
                targetAreas = request.targetAreas?.let {
                    moshi.adapter<List<br.com.nexshape.academia.data.api.TrainingTargetAreaRequest>>(targetAreaType).toJson(it).formPart()
                },
                isTemplate = request.isTemplate.toString().formPart(),
                exercises = moshi.adapter<List<br.com.nexshape.academia.data.api.CreateTrainingExerciseRequest>>(exerciseType)
                    .toJson(request.exercises.orEmpty())
                    .formPart(),
                targetPhotos = targetPhotos,
            ).data
        }
    }

    suspend fun updatePlan(id: Int, request: CreateTrainingPlanRequest): Result<TrainingPlanSummaryDto> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().updateTrainingPlan(id, request).data }
    }

    suspend fun deletePlan(id: Int): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().deleteTrainingPlan(id)
            Unit
        }
    }

    suspend fun exerciseCatalog(search: String? = null): Result<List<ExerciseCatalogDto>> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().exerciseCatalog(search).data.exercises }
    }

    suspend fun validateWorkoutImport(photoPart: okhttp3.MultipartBody.Part): Result<WorkoutValidationData> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().validateWorkoutImport(photoPart).data }
    }

    suspend fun processWorkoutImport(photoPart: okhttp3.MultipartBody.Part): Result<WorkoutImportData> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().processWorkoutImport(photoPart).data }
    }

    suspend fun initializeWorkoutImportOrchestrated(photoParts: List<okhttp3.MultipartBody.Part>) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().initializeWorkoutImportOrchestrated(photoParts).data }
    }

    suspend fun validateWorkoutImportOrchestrated(uuid: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().validateWorkoutImportOrchestrated(uuid).data }
    }

    suspend fun processWorkoutImportOrchestrated(uuid: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().processWorkoutImportOrchestrated(uuid).data }
    }

    suspend fun workoutImportOrchestratedStatus(uuid: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().workoutImportOrchestratedStatus(uuid).data }
    }

    suspend fun substituteWorkoutImportOrchestrated(uuid: String, imageId: Int, photoPart: okhttp3.MultipartBody.Part) = withContext(Dispatchers.IO) {
        val body = imageId.toString().toRequestBody("text/plain".toMediaTypeOrNull())
        runCatching { ApiClient.api().substituteWorkoutImportOrchestrated(uuid, body, photoPart).data }
    }

    suspend fun saveWorkoutImport(request: SaveWorkoutImportRequest) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().saveWorkoutImport(request).data }
    }

    suspend fun sessions(limit: Int = 10) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().workoutSessions(limit).data }
    }

    suspend fun activeSession() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().activeWorkoutSession().data }
    }

    suspend fun startSession(planId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().startWorkoutSession(StartWorkoutSessionRequest(planId)).data }
    }

    suspend fun updateSession(id: Int, request: UpdateWorkoutSessionRequest) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().updateWorkoutSession(id, request).data }
    }

    suspend fun saveSession(request: WorkoutSessionRequest) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().createWorkoutSession(request).data }
    }

    suspend fun saveLoadLog(request: CreateLoadLogRequest) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().createLoadLog(request).data }
    }
}

private fun String.formPart(): RequestBody = toRequestBody("text/plain".toMediaTypeOrNull())

class NutritionRepository {
    suspend fun diary(date: LocalDate = LocalDate.now()): Result<NutritionDiaryData> =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().nutritionDiary(date.toString()).data }
        }

    suspend fun addEntry(request: br.com.nexshape.academia.data.api.CreateFoodEntryRequest): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching { 
            ApiClient.api().createFoodEntry(request)
            Unit
        }
    }

    suspend fun analyzeMeal(description: String, mealType: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().analyzeMeal(AnalyzeMealRequest(description, mealType)).data }
    }

    suspend fun analyzeMealPhoto(photoPart: okhttp3.MultipartBody.Part, mealType: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().analyzeNutritionPhoto(photoPart, mealType.formPart()).data }
    }

    suspend fun updateEntry(id: Int, request: br.com.nexshape.academia.data.api.CreateFoodEntryRequest): Result<Unit> =
        withContext(Dispatchers.IO) {
            runCatching {
                ApiClient.api().updateFoodEntry(id, request)
                Unit
            }
        }

    suspend fun deleteEntry(id: Int): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().deleteFoodEntry(id)
            Unit
        }
    }

    suspend fun updateGoal(goal: String, split: String) = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().updateNutritionGoal(
                br.com.nexshape.academia.data.api.UpdateNutritionGoalRequest(goal, split),
            ).data
        }
    }

    suspend fun mealTemplates() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().mealTemplates().data.templates }
    }

    suspend fun applyMealTemplate(id: Int, date: LocalDate = LocalDate.now()): Result<Unit> =
        withContext(Dispatchers.IO) {
            runCatching {
                ApiClient.api().applyMealTemplate(
                    id,
                    br.com.nexshape.academia.data.api.ApplyMealTemplateRequest(date.toString()),
                )
                Unit
            }
        }

    suspend fun hydrationStatus(date: LocalDate = LocalDate.now()) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().hydrationStatus(date.toString()).data }
    }

    suspend fun addWater(amountMl: Int, date: LocalDate = LocalDate.now()): Result<Unit> =
        withContext(Dispatchers.IO) {
            runCatching {
                ApiClient.api().createHydrationEntry(
                    br.com.nexshape.academia.data.api.CreateHydrationEntryRequest(
                        amountMl = amountMl,
                        entryDate = date.toString(),
                    ),
                )
                Unit
            }
        }

    suspend fun deleteWaterEntry(id: Int): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().deleteHydrationEntry(id)
            Unit
        }
    }
}

class ChatRepository {
    suspend fun history() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().chatHistory().data.messages }
    }

    suspend fun send(message: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().chatSend(br.com.nexshape.academia.data.api.ChatSendRequest(message)).data.message }
    }
}

class AiCreditsRepository {
    suspend fun balance() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().aiCredits().data }
    }
}

class OfflineSyncRepository(context: Context) {
    private val dao = AppDatabase.get(context).pendingSyncDao()
    private val moshi = Moshi.Builder().add(KotlinJsonAdapterFactory()).build()

    suspend fun queueExerciseSync(context: Context, request: ExerciseSyncRequest) {
        val json = moshi.adapter(ExerciseSyncRequest::class.java).toJson(request)
        dao.insert(PendingSyncEntity(endpoint = "exercise-logs/sync", payloadJson = json))
        flush(context)
    }

    suspend fun flush(context: Context) = withContext(Dispatchers.IO) {
        val pending = dao.pending()
        pending.forEach { item ->
            if (item.endpoint == "exercise-logs/sync") {
                val adapter = moshi.adapter(ExerciseSyncRequest::class.java)
                val body = adapter.fromJson(item.payloadJson) ?: return@forEach
                runCatching {
                    ApiClient.api().syncExercise(body)
                    dao.delete(item.id)
                }
            }
        }
    }
}

class EvolutionRepository {
    suspend fun assessments() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().assessments().data.assessments }
    }

    suspend fun assessmentSummary() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().assessmentSummary().data }
    }

    suspend fun createAssessment(request: br.com.nexshape.academia.data.api.CreateAssessmentRequest) =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().createAssessment(request) }
        }

    suspend fun photos() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().evolutionPhotos().data.photos }
    }

    suspend fun reportConsent() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().evolutionReportConsent().data }
    }

    suspend fun report(
        acceptConsent: Boolean = false,
        onStatus: (suspend (String) -> Unit)? = null,
    ) = withContext(Dispatchers.IO) {
        runCatching {
            val api = ApiClient.api()
            val consent = runCatching { api.evolutionReportConsent().data }.getOrNull()
            if (consent?.accepted == false && !acceptConsent) {
                throw IllegalStateException(consent.copy?.summary ?: "Aceite a analise de fotos corporais por IA para gerar o relatorio.")
            }

            onStatus?.invoke("Solicitando relatorio...")
            val request = api.requestEvolutionReport(
                EvolutionReportRequest(acceptAiBodyPhotoAnalysis = consent?.accepted != true && acceptConsent),
            ).data

            repeat(30) {
                val status = api.evolutionReportStatus(request.reportId).data
                onStatus?.invoke(reportStatusLabel(status.status))
                when (status.status) {
                    "completed", "completed_with_limitations" -> {
                        return@runCatching status.finalReport ?: api.evolutionReport().data
                    }
                    "failed" -> {
                        throw IllegalStateException(status.failureReason ?: "Falha ao gerar relatorio de evolucao.")
                    }
                }

                delay(2_000)
            }

            throw IllegalStateException("O relatorio ainda esta sendo preparado. Tente novamente em instantes.")
        }.recoverCatching {
            onStatus?.invoke("Carregando relatorio anterior...")
            ApiClient.api().evolutionReport().data
        }
    }

    private fun reportStatusLabel(status: String): String = when (status) {
        "pending" -> "Relatorio na fila..."
        "processing" -> "Analisando fotos e metricas..."
        "completed", "completed_with_limitations" -> "Relatorio concluido."
        "failed" -> "Falha ao gerar relatorio."
        else -> "Preparando relatorio..."
    }

    suspend fun uploadPhoto(
        photoPart: okhttp3.MultipartBody.Part,
        type: okhttp3.RequestBody,
        date: okhttp3.RequestBody,
        weight: okhttp3.RequestBody?,
    ) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().uploadEvolutionPhoto(photoPart, type, date, weight) }
    }

    suspend fun validatePhoto(photoPart: okhttp3.MultipartBody.Part) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().validateEvolutionPhoto(photoPart).data }
    }

    suspend fun analyzeSession(date: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().analyzeEvolutionSession(br.com.nexshape.academia.data.api.EvolutionSessionAnalyzeRequest(date)).data }
    }

    suspend fun deletePhoto(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().deleteEvolutionPhoto(id) }
    }

    suspend fun bodyAnalyses() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().bodyAnalyses().data.analyses }
    }

    suspend fun bodyAnalysis(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().bodyAnalysis(id).data.analysis }
    }

    suspend fun uploadBodyAnalysisPhoto(
        photoPart: okhttp3.MultipartBody.Part,
        viewType: okhttp3.RequestBody,
    ) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().uploadBodyAnalysisPhoto(photoPart, viewType).data.analysis }
    }

    suspend fun compareBodyAnalysis(id1: Int, id2: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().compareBodyAnalysis(id1, id2).data }
    }
}

class SubscriptionRepository {
    suspend fun paymentStatus() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().paymentStatus() }
    }

    suspend fun plans() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().subscriptionPlans().data.plans }
    }

    suspend fun checkout(planId: Int, paymentMethod: String = "pix") = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().subscriptionCheckout(
                br.com.nexshape.academia.data.api.CheckoutRequest(planId, paymentMethod),
            ).data
        }
    }

    suspend fun current() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().currentSubscription().data }
    }

    suspend fun cancel() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().cancelSubscription() }
    }
}

class NotificationsRepository {
    suspend fun unreadCounts() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().notificationCounts().data }
    }
}

class AgendaRepository {
    suspend fun professionals() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().linkedProfessionals().data.professionals }
    }

    suspend fun appointments() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().appointments().data.appointments }
    }

    suspend fun slots(professionalId: Int, date: String) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().appointmentSlots(professionalId, date).data.slots }
    }

    suspend fun schedule(request: br.com.nexshape.academia.data.api.CreateAppointmentRequest) =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().createAppointment(request).data }
        }

    suspend fun joinWaitlist(professionalId: Int, date: String) = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().joinAppointmentWaitlist(
                br.com.nexshape.academia.data.api.AppointmentWaitlistRequest(professionalId, date),
            ).data
        }
    }

    suspend fun updateLinkPermissions(linkId: Int, permissions: Map<String, Boolean>) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().updateLinkPermissions(linkId, br.com.nexshape.academia.data.api.UpdatePermissionsRequest(permissions)) }
    }

    suspend fun revokeLink(linkId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().revokeLink(linkId) }
    }

    suspend fun searchProfessionals(query: String? = null, specialty: String? = null, serviceType: String? = null) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().searchProfessionals(query, specialty, serviceType).data.professionals }
    }

    suspend fun studentRequests() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().studentRequests().data.requests }
    }

    suspend fun createStudentRequest(professionalId: Int, message: String? = null) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().createStudentRequest(br.com.nexshape.academia.data.api.RequestConnectionRequest(professionalId, message)).data }
    }
}

class ProfessionalRepository {
    suspend fun dashboard() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalDashboard().data.stats }
    }

    suspend fun requests() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalRequests().data.requests }
    }

    suspend fun approveRequest(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().approveProfessionalRequest(id) }
    }

    suspend fun rejectRequest(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().rejectProfessionalRequest(id) }
    }

    suspend fun patients(search: String? = null) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalPatients(search).data.patients }
    }

    suspend fun patientDetail(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalPatient(id).data.patient }
    }

    suspend fun appointments(date: String? = null, status: String? = null) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalAppointments(date, status).data.appointments }
    }

    suspend fun updateAppointmentStatus(id: Int, status: String) = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().updateProfessionalAppointmentStatus(id, UpdateAppointmentStatusRequest(status)).data
        }
    }

    suspend fun alerts(unreadOnly: Boolean = true) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalAlerts(unreadOnly, 30).data.alerts }
    }

    suspend fun markAlertRead(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().markProfessionalAlertRead(id) }
    }

    suspend fun protocols(type: String = "training") = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalProtocols(type).data.protocols }
    }

    suspend fun patientTrainingPlans(patientId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().patientTrainingPlans(patientId).data.plans }
    }

    suspend fun patientTrainingPlanDetail(patientId: Int, planId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().patientTrainingPlanDetail(patientId, planId).data }
    }

    suspend fun createPatientTrainingPlan(patientId: Int, request: CreatePatientTrainingPlanRequest) =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().createPatientTrainingPlan(patientId, request).data }
        }

    suspend fun patientAssessments(patientId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().patientAssessments(patientId).data.assessments }
    }

    suspend fun createPatientAssessment(patientId: Int, request: CreatePatientAssessmentRequest) =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().createPatientAssessment(patientId, request).data }
        }

    suspend fun patientEvolutionPhotos(patientId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().patientEvolutionPhotos(patientId).data.photos }
    }

    suspend fun uploadPatientEvolutionPhoto(
        patientId: Int,
        photoPart: okhttp3.MultipartBody.Part,
        type: okhttp3.RequestBody,
        date: okhttp3.RequestBody,
        weight: okhttp3.RequestBody? = null,
    ) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().uploadPatientEvolutionPhoto(patientId, photoPart, type, date, weight).data }
    }
}

class MedicalDocumentsRepository {
    suspend fun getDocuments() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().studentMedicalDocuments().data }
    }

    suspend fun downloadReport(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().downloadReportPdf(id) }
    }

    suspend fun downloadPrescription(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().downloadPrescriptionPdf(id) }
    }

    suspend fun downloadCertificate(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().downloadCertificatePdf(id) }
    }
}

class CommunityRepository {
    suspend fun posts() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().communityPosts().data.posts }
    }

    suspend fun createPost(content: String, visibility: String = "public") = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().createCommunityPost(
                br.com.nexshape.academia.data.api.CreateCommunityPostRequest(content, visibility),
            ).data.post
        }
    }

    suspend fun createComment(postId: Int, content: String) = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().createCommunityComment(
                postId,
                br.com.nexshape.academia.data.api.CreateCommunityCommentRequest(content),
            ).data.comment
        }
    }
}

class InternalMessagesRepository {
    suspend fun conversations() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().conversations().data.conversations }
    }

    suspend fun startSupport() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().startSupportConversation().data.conversation }
    }

    suspend fun messages(conversationId: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().conversationMessages(conversationId).data }
    }

    suspend fun send(conversationId: Int, content: String) = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().sendInternalMessage(
                conversationId,
                br.com.nexshape.academia.data.api.SendInternalMessageRequest(content),
            ).data.message
        }
    }
}
