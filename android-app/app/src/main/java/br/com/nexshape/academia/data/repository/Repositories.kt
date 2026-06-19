package br.com.nexshape.academia.data.repository

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.CreatePatientAssessmentRequest
import br.com.nexshape.academia.data.api.CreatePatientTrainingPlanRequest
import br.com.nexshape.academia.data.api.ExerciseSyncRequest
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.api.UpdateAppointmentStatusRequest
import br.com.nexshape.academia.data.local.AppDatabase
import br.com.nexshape.academia.data.local.PendingSyncEntity
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.time.LocalDate

class TrainingRepository {
    suspend fun listPlans(): Result<List<TrainingPlanSummaryDto>> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().trainingPlans().data }
    }

    suspend fun planDetail(id: Int): Result<TrainingPlanDetailDto> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().trainingPlan(id).data }
    }
}

class NutritionRepository {
    suspend fun diary(date: LocalDate = LocalDate.now()): Result<NutritionDiaryData> =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().nutritionDiary(date.toString()).data }
        }

    suspend fun addEntry(request: CreateFoodEntryRequest): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().createFoodEntry(request) }
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

    suspend fun createAssessment(request: br.com.nexshape.academia.data.api.CreateAssessmentRequest) =
        withContext(Dispatchers.IO) {
            runCatching { ApiClient.api().createAssessment(request) }
        }

    suspend fun photos() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().evolutionPhotos().data.photos }
    }

    suspend fun uploadPhoto(
        photoPart: okhttp3.MultipartBody.Part,
        type: okhttp3.RequestBody,
        date: okhttp3.RequestBody,
        weight: okhttp3.RequestBody?,
    ) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().uploadEvolutionPhoto(photoPart, type, date, weight) }
    }

    suspend fun deletePhoto(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().deleteEvolutionPhoto(id) }
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
}

class ProfessionalRepository {
    suspend fun dashboard() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().professionalDashboard().data.stats }
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
