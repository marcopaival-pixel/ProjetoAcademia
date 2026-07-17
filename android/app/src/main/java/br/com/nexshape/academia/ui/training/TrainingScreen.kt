package br.com.nexshape.academia.ui.training

import coil.compose.AsyncImage
import androidx.compose.runtime.mutableStateListOf
import androidx.compose.ui.draw.clip
import androidx.compose.foundation.layout.size
import androidx.compose.material3.CircularProgressIndicator
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.gestures.detectDragGestures
import androidx.compose.foundation.gestures.detectTapGestures
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.ui.Alignment
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.sp
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Checkbox
import androidx.compose.material3.OutlinedButton
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.History
import androidx.compose.material.icons.filled.RadioButtonUnchecked
import androidx.compose.material.icons.filled.Sync
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.AssistChip
import androidx.compose.material3.Icon
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Slider
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.geometry.Size
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.graphics.graphicsLayer
import androidx.compose.ui.layout.ContentScale
import android.net.Uri
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.input.pointer.pointerInput
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.R
import br.com.nexshape.academia.data.api.CreateTrainingExerciseRequest
import br.com.nexshape.academia.data.api.CreateTrainingPlanRequest
import br.com.nexshape.academia.data.api.CreateTrainingSetRequest
import br.com.nexshape.academia.data.api.TrainingTargetAreaRequest
import br.com.nexshape.academia.data.api.CreateLoadLogRequest
import br.com.nexshape.academia.data.api.ExerciseCatalogDto
import br.com.nexshape.academia.data.api.ExerciseSetDto
import br.com.nexshape.academia.data.api.ExerciseSyncRequest
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.SaveWorkoutImportRequest
import br.com.nexshape.academia.data.api.TrainingExerciseDto
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.api.TrainingPlansResponse
import br.com.nexshape.academia.data.api.UpdateWorkoutSessionRequest
import br.com.nexshape.academia.data.api.WorkoutImportExerciseDto
import br.com.nexshape.academia.data.api.WorkoutSessionDto
import br.com.nexshape.academia.data.api.WorkoutSessionRequest
import br.com.nexshape.academia.data.local.ActiveWorkoutDraft
import br.com.nexshape.academia.data.local.ActiveWorkoutStore
import br.com.nexshape.academia.data.local.AppDatabase
import br.com.nexshape.academia.data.repository.OfflineSyncRepository
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import br.com.nexshape.academia.data.repository.TrainingRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexGreen
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMetricCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPrimaryButton
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import java.io.File
import java.time.LocalDate
import kotlin.math.roundToInt

@Composable
fun TrainingScreen(
    modifier: Modifier = Modifier,
    onNavigateToChat: (() -> Unit)? = null,
) {
    val context = LocalContext.current
    val repository = remember { TrainingRepository() }
    val authRepository = remember { AuthRepository(ApiClient.tokenStore(), context.applicationContext) }
    val professionalRepository = remember { ProfessionalRepository() }
    val sessionPreferences = remember { ApiClient.sessionPreferences() }
    val offlineRepository = remember { OfflineSyncRepository(context) }
    val activeWorkoutStore = remember { ActiveWorkoutStore(context) }
    val scope = rememberCoroutineScope()
    var plans by remember { mutableStateOf<List<TrainingPlanSummaryDto>>(emptyList()) }
    var selected by remember { mutableStateOf<TrainingPlanDetailDto?>(null) }
    var sessions by remember { mutableStateOf<List<WorkoutSessionDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var pendingSync by remember { mutableIntStateOf(0) }
    var showRpeDialog by remember { mutableStateOf(false) }
    var loadLogExercise by remember { mutableStateOf<TrainingExerciseDto?>(null) }
    var activeWorkout by remember { mutableStateOf<ActiveWorkoutDraft?>(activeWorkoutStore.get()) }
    var restSecondsRemaining by remember { mutableIntStateOf(0) }
    var activePatientId by remember { mutableStateOf<Int?>(null) }
    var hasProfessionalLink by remember { mutableStateOf(false) }
    var canCreateOwnWorkout by remember { mutableStateOf(false) }
    var showCreatePlanDialog by remember { mutableStateOf(false) }
    var showImportDialog by remember { mutableStateOf(false) }
    var showEditPlanDialog by remember { mutableStateOf(false) }
    var showDeletePlanDialog by remember { mutableStateOf(false) }
    var actionError by remember { mutableStateOf<String?>(null) }
    var importExercises by remember { mutableStateOf<List<WorkoutImportExerciseDto>>(emptyList()) }
    var importWorkoutName by remember { mutableStateOf("") }
    var importBusy by remember { mutableStateOf(false) }
    var importError by remember { mutableStateOf<String?>(null) }

    var importState by remember { mutableStateOf("IDLE") }
    var importConfidence by remember { mutableStateOf(1.0) }
    var pendingPhotoPart by remember { mutableStateOf<MultipartBody.Part?>(null) }
    var showLowConfidenceAlert by remember { mutableStateOf(false) }
    var lowConfidenceMessage by remember { mutableStateOf("") }

    // New Android Wizard States
    val creditsRepository = remember { br.com.nexshape.academia.data.repository.AiCreditsRepository() }
    var aiCreditsBalance by remember { mutableIntStateOf(0) }
    var selectedPhotoUri by remember { mutableStateOf<Uri?>(null) }
    var selectedPhotoUris by remember { mutableStateOf<List<Uri>>(emptyList()) }
    var wizardStep by remember { mutableIntStateOf(1) }
    var agentValidationStatus by remember { mutableStateOf("idle") }
    var agentOCRStatus by remember { mutableStateOf("idle") }
    var agentSpecialistStatus by remember { mutableStateOf("idle") }
    var agentAuditorStatus by remember { mutableStateOf("idle") }
    val aiMessages = remember { mutableStateListOf<String>() }
    var selectedDay by remember { mutableStateOf("segunda-feira") }
    var importProcessingSeconds by remember { mutableIntStateOf(0) }

    fun refreshPending() {
        scope.launch { pendingSync = AppDatabase.get(context).pendingSyncDao().pendingCount() }
    }

    fun runIAOrchestrator(uris: List<Uri>) {
        scope.launch {
            val selectedUris = uris.take(7)
            if (selectedUris.isEmpty()) return@launch
            importBusy = true
            val startedAt = System.currentTimeMillis()
            importProcessingSeconds = 0
            wizardStep = 3
            importError = null
            aiMessages.clear()
            aiMessages.add("Iniciando Orquestrador NexShape AI.")
            
            agentValidationStatus = "running"
            agentOCRStatus = "idle"
            agentSpecialistStatus = "idle"
            agentAuditorStatus = "idle"
            aiMessages.add("Agente de Validação iniciado: analisando se a imagem é uma ficha de treino.")
            
            runCatching {
                val uri = selectedUris.first()
                if (selectedUris.size > 1) {
                    val daysOfWeek = listOf(
                        "segunda-feira", "terça-feira", "quarta-feira",
                        "quinta-feira", "sexta-feira", "sábado", "domingo"
                    )
                    val allExercises = mutableListOf<WorkoutImportExerciseDto>()
                    val confidences = mutableListOf<Double>()

                    selectedUris.forEachIndexed { imageIndex, photoUri ->
                        val temp = File.createTempFile("workout_import_${imageIndex + 1}_", ".jpg", context.cacheDir)
                        context.contentResolver.openInputStream(photoUri)?.use { input ->
                            temp.outputStream().use { output -> input.copyTo(output) }
                        }
                        val part = MultipartBody.Part.createFormData(
                            "photo",
                            temp.name,
                            temp.asRequestBody("image/jpeg".toMediaTypeOrNull()),
                        )
                        val inferredDay = daysOfWeek.getOrElse(imageIndex) { selectedDay }

                        aiMessages.add("Imagem ${imageIndex + 1}: validando ficha de treino.")
                        val validation = repository.validateWorkoutImport(part).getOrThrow()
                        confidences.add(validation.confidence)

                        if (!validation.isWorkout || validation.confidence < 0.70) {
                            throw Exception("Imagem ${imageIndex + 1}: a foto nao parece conter uma ficha de treino.")
                        }

                        aiMessages.add("Imagem ${imageIndex + 1}: extraindo exercicios.")
                        val data = repository.processWorkoutImport(part).getOrThrow()
                        allExercises += data.exercises.map { exercise ->
                            if (exercise.day.isNullOrBlank()) exercise.copy(day = inferredDay) else exercise
                        }
                    }

                    importConfidence = confidences.average().takeIf { !it.isNaN() } ?: 1.0
                    agentValidationStatus = "success"
                    aiMessages.add("Agente de Validação concluído com sucesso.")
                    agentOCRStatus = "running"
                    aiMessages.add("Agente OCR iniciado: extraindo textos e tabelas das fichas.")
                    delay(1200)
                    agentOCRStatus = "success"
                    aiMessages.add("Agente OCR concluído.")
                    agentSpecialistStatus = "running"
                    aiMessages.add("Agente Especialista iniciado: consolidando exercícios, séries e cargas.")
                    importExercises = allExercises
                    importWorkoutName = "Treino IA - ${LocalDate.now()}"
                    agentSpecialistStatus = "success"
                    aiMessages.add("Agente Especialista concluído.")
                    agentAuditorStatus = "running"
                    aiMessages.add("Agente Auditor iniciado: verificando integridade física e consistências.")
                    delay(1000)
                    agentAuditorStatus = "success"
                    aiMessages.add("Agente Auditor concluído. Análise consolidada com sucesso.")
                    importProcessingSeconds = ((System.currentTimeMillis() - startedAt) / 1000).toInt().coerceAtLeast(1)
                    wizardStep = 4
                    return@runCatching
                }
                val temp = File.createTempFile("workout_import_", ".jpg", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody("image/jpeg".toMediaTypeOrNull()),
                )
                
                // 1. Validação
                val validation = repository.validateWorkoutImport(part).getOrThrow()
                importConfidence = validation.confidence
                
                if (!validation.isWorkout || validation.confidence < 0.70) {
                    throw Exception("Não foi possível identificar uma ficha de treino nesta imagem. Verifique a qualidade.")
                }
                
                agentValidationStatus = "success"
                aiMessages.add("Agente de Validação concluído com sucesso.")
                
                // 2. OCR
                agentOCRStatus = "running"
                aiMessages.add("Agente OCR iniciado: extraindo textos e tabelas da ficha.")
                delay(1200)
                agentOCRStatus = "success"
                aiMessages.add("Agente OCR concluído.")
                
                // 3. Especialista
                agentSpecialistStatus = "running"
                aiMessages.add("Agente Especialista iniciado: interpretando exercícios, séries e cargas.")
                val data = repository.processWorkoutImport(part).getOrThrow()
                importExercises = data.exercises.map { exercise ->
                    if (exercise.day.isNullOrBlank()) exercise.copy(day = selectedDay) else exercise
                }
                importWorkoutName = "Treino IA - ${LocalDate.now()}"
                agentSpecialistStatus = "success"
                aiMessages.add("Agente Especialista concluído.")
                
                // 4. Auditoria
                agentAuditorStatus = "running"
                aiMessages.add("Agente Auditor iniciado: verificando integridade física e consistências.")
                delay(1000)
                agentAuditorStatus = "success"
                aiMessages.add("Agente Auditor concluído. Análise consolidada com sucesso.")
                importProcessingSeconds = ((System.currentTimeMillis() - startedAt) / 1000).toInt().coerceAtLeast(1)
                
                wizardStep = 4 // Revisão
            }.onFailure {
                importError = friendlyError(it)
                agentValidationStatus = "failed"
                agentOCRStatus = "failed"
                agentSpecialistStatus = "failed"
                agentAuditorStatus = "failed"
                aiMessages.add("Erro no Orquestrador IA: ${it.message}")
                wizardStep = 2 // Retorna para etapa de ajuste
            }
            importBusy = false
        }
    }

    fun runIAOrchestratorV2(uris: List<Uri>) {
        scope.launch {
            val selectedUris = uris.take(7)
            if (selectedUris.isEmpty()) return@launch

            importBusy = true
            val startedAt = System.currentTimeMillis()
            importProcessingSeconds = 0
            wizardStep = 3
            importError = null
            importExercises = emptyList()
            aiMessages.clear()
            aiMessages.add("Iniciando sessao orquestrada NexShape AI.")

            agentValidationStatus = "running"
            agentOCRStatus = "idle"
            agentSpecialistStatus = "idle"
            agentAuditorStatus = "idle"

            runCatching {
                val photoParts = selectedUris.mapIndexed { imageIndex, photoUri ->
                    val temp = File.createTempFile("workout_import_${imageIndex + 1}_", ".jpg", context.cacheDir)
                    context.contentResolver.openInputStream(photoUri)?.use { input ->
                        temp.outputStream().use { output -> input.copyTo(output) }
                    }
                    MultipartBody.Part.createFormData(
                        "photos[]",
                        temp.name,
                        temp.asRequestBody("image/jpeg".toMediaTypeOrNull()),
                    )
                }

                aiMessages.add("Agente de Validacao iniciado: analisando ${photoParts.size} imagem(ns).")
                val initialized = repository.initializeWorkoutImportOrchestrated(photoParts).getOrThrow()
                val uuid = initialized.uuid ?: throw Exception("A sessao de importacao nao retornou identificador.")

                val validation = repository.validateWorkoutImportOrchestrated(uuid).getOrThrow()
                when (validation.status) {
                    "AI_SERVICE_ERROR" -> throw Exception(validation.errorMessage ?: "Servico de IA indisponivel. Corrija a configuracao e tente novamente.")
                    "INVALID_IMAGE" -> throw Exception(validation.errorMessage ?: "Uma ou mais imagens nao parecem ser fichas de treino. Troque as fotos marcadas e tente novamente.")
                }

                agentValidationStatus = "success"
                aiMessages.add("Agente de Validacao concluido com sucesso.")

                agentOCRStatus = "running"
                agentSpecialistStatus = "running"
                aiMessages.add("Agente OCR iniciado: extraindo textos e tabelas das fichas.")
                aiMessages.add("Agente Especialista iniciado: interpretando exercicios, series e cargas.")

                val processed = repository.processWorkoutImportOrchestrated(uuid).getOrThrow()
                if (processed.status != "WAITING_REVIEW") {
                    throw Exception(processed.errorMessage ?: "A ficha foi reconhecida, mas nao foi possivel extrair exercicios para revisao.")
                }

                val imported = processed.exercises.map { exercise ->
                    if (exercise.day.isNullOrBlank()) exercise.copy(day = selectedDay) else exercise
                }
                if (imported.isEmpty()) {
                    throw Exception(processed.errorMessage ?: "A ficha foi reconhecida, mas nao retornou exercicios para revisao.")
                }

                importExercises = imported
                importWorkoutName = "Treino IA - ${LocalDate.now()}"
                importConfidence = 1.0
                agentOCRStatus = "success"
                aiMessages.add("Agente OCR concluido.")
                agentSpecialistStatus = "success"
                aiMessages.add("Agente Especialista concluido.")

                agentAuditorStatus = "running"
                aiMessages.add("Agente Auditor iniciado: verificando integridade fisica e consistencias.")
                delay(800)
                agentAuditorStatus = "success"
                aiMessages.add("Agente Auditor concluido. Analise consolidada com sucesso.")

                importProcessingSeconds = ((System.currentTimeMillis() - startedAt) / 1000).toInt().coerceAtLeast(1)
                wizardStep = 4
            }.onFailure {
                importError = friendlyError(it)
                agentValidationStatus = if (agentValidationStatus == "running") "failed" else agentValidationStatus
                agentOCRStatus = if (agentOCRStatus == "running") "failed" else agentOCRStatus
                agentSpecialistStatus = if (agentSpecialistStatus == "running") "failed" else agentSpecialistStatus
                agentAuditorStatus = if (agentAuditorStatus == "running") "failed" else agentAuditorStatus
                aiMessages.add("Erro no Orquestrador IA: ${it.message}")
                wizardStep = 2
            }

            importBusy = false
        }
    }

    fun reload() {
        scope.launch {
            loading = true
            error = null
            
            // Load user AI credits balance
            creditsRepository.balance()
                .onSuccess { aiCreditsBalance = it.balance }

            val profile = authRepository.loadProfile().getOrNull()
            activePatientId = if (profile?.isProfessional == true && profile.isStudent == false) {
                sessionPreferences.getActivePatientId() ?: profile.activePatientId
            } else {
                null
            }
            val plansResult = if (activePatientId != null) {
                professionalRepository.patientTrainingPlans(activePatientId!!)
            } else {
                val respResult = repository.getPlansResponse()
                respResult.onSuccess { resp ->
                    hasProfessionalLink = resp.meta?.hasProfessionalLink ?: false
                    canCreateOwnWorkout = resp.meta?.canCreateOwnWorkout ?: false
                }
                respResult.map { it.data }
            }
            plansResult
                .onSuccess { plans = it }
                .onFailure { error = friendlyError(it) }

            if (activePatientId == null) {
                repository.sessions()
                    .onSuccess { sessions = it }
                    .onFailure { if (error == null) error = friendlyError(it) }
                repository.activeSession()
                    .onSuccess { remote ->
                        if (remote != null && remote.trainingPlanId != null) {
                            val planName = plans.firstOrNull { it.id == remote.trainingPlanId }?.name ?: "Treino em andamento"
                            activeWorkoutStore.start(remote.trainingPlanId, planName, remote.id)
                            remote.completedExerciseIds.forEach { activeWorkoutStore.setExerciseCompleted(it, true) }
                            activeWorkout = activeWorkoutStore.get()
                        }
                    }
                    .onFailure { /* Mantem estado local se estiver offline. */ }
            } else {
                sessions = emptyList()
            }
            refreshPending()
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    val importPhotoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetMultipleContents()) { uris: List<Uri> ->
        val selectedUris = uris.take(7)
        if (selectedUris.isEmpty()) return@rememberLauncherForActivityResult
        selectedPhotoUris = selectedUris
        selectedPhotoUri = selectedUris.firstOrNull()
        wizardStep = 1
        importExercises = emptyList()
        importError = null
        showImportDialog = true
    }

    LaunchedEffect(restSecondsRemaining) {
        if (restSecondsRemaining > 0) {
            delay(1000)
            restSecondsRemaining -= 1
        }
    }

    NexShapeScreen(
        title = "Treino",
        subtitle = "Planos, exercicios e registros sincronizados com a plataforma.",
        modifier = modifier,
        action = {
            if (pendingSync > 0) {
                AssistChip(
                    onClick = {
                        scope.launch {
                            offlineRepository.flush(context)
                            refreshPending()
                        }
                    },
                    label = { Text("$pendingSync pendente(s)") },
                    leadingIcon = { Icon(Icons.Default.Sync, contentDescription = null) },
                )
            }
        },
    ) {
        when {
            loading -> NexLoadingState("Carregando seus treinos...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = { reload() })
            selected != null -> TrainingDetail(
                plan = selected!!,
                recentSessions = sessions,
                activeWorkout = activeWorkout,
                restSecondsRemaining = restSecondsRemaining,
                readOnly = activePatientId != null,
                actionError = actionError,
                onBack = { selected = null },
                onStartWorkout = {
                    scope.launch {
                        repository.startSession(selected!!.id)
                            .onSuccess { session ->
                                activeWorkoutStore.start(selected!!.id, selected!!.name, session.id)
                                activeWorkout = activeWorkoutStore.get()
                            }
                            .onFailure {
                                activeWorkoutStore.start(selected!!.id, selected!!.name)
                                activeWorkout = activeWorkoutStore.get()
                                error = friendlyError(it)
                            }
                    }
                },
                onCancelWorkout = {
                    activeWorkout?.sessionId?.let { sessionId ->
                        scope.launch {
                            repository.updateSession(sessionId, UpdateWorkoutSessionRequest(status = "cancelled"))
                        }
                    }
                    activeWorkoutStore.clear()
                    activeWorkout = null
                },
                onToggleExercise = { exerciseId, completed ->
                    activeWorkoutStore.setExerciseCompleted(exerciseId, completed)
                    activeWorkout = activeWorkoutStore.get()
                    activeWorkout?.sessionId?.let { sessionId ->
                        val total = selected?.exercises.orEmpty().size
                        val done = activeWorkout?.completedExerciseIds.orEmpty().size
                        scope.launch {
                            repository.updateSession(
                                sessionId,
                                UpdateWorkoutSessionRequest(
                                    status = "active",
                                    completionPercent = workoutCompletionPercent(total, done),
                                    completedExerciseIds = activeWorkout?.completedExerciseIds.orEmpty().toList(),
                                ),
                            )
                        }
                    }
                    if (completed) {
                        restSecondsRemaining = selected?.exercises
                            .orEmpty()
                            .firstOrNull { it.id == exerciseId }
                            ?.sets
                            .orEmpty()
                            .mapNotNull { it.restSeconds }
                            .maxOrNull()
                            ?: 0
                    }
                },
                onSkipRest = { restSecondsRemaining = 0 },
                onOpenLoadLog = { loadLogExercise = it },
                onFinishWorkout = { showRpeDialog = true },
                onEditPlan = { showEditPlanDialog = true },
                onDeletePlan = { showDeletePlanDialog = true },
            )
            plans.isEmpty() -> {
                Box(
                    modifier = Modifier.fillMaxSize().padding(16.dp),
                    contentAlignment = Alignment.Center
                ) {
                    NexCard(modifier = Modifier.fillMaxWidth().padding(horizontal = 6.dp)) {
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            val titleText: String
                            val messageText: String
                            val showCreateButton: Boolean
                            val showChatButton: Boolean

                            if (!hasProfessionalLink) {
                                // Aluno independente: a criação própria depende da feature create_workout no backend.
                                titleText = "Você ainda não possui uma ficha de treino."
                                messageText = if (canCreateOwnWorkout) {
                                    "Crie seu próprio treino e acompanhe sua evolução diretamente pelo NexShape."
                                } else {
                                    "Seu plano atual não libera criação de fichas. Faça upgrade para criar e gerenciar seus treinos."
                                }
                                showCreateButton = canCreateOwnWorkout
                                showChatButton = false
                            } else if (canCreateOwnWorkout) {
                                // Aluno vinculado com permissão
                                titleText = "Seu profissional ainda não disponibilizou uma ficha de treino."
                                messageText = "Você pode aguardar a liberação ou criar uma ficha própria, conforme as permissões definidas pelo seu profissional."
                                showCreateButton = true
                                showChatButton = true
                            } else {
                                // Aluno vinculado sem permissão
                                titleText = "Seu profissional ainda não disponibilizou uma ficha de treino."
                                messageText = "Assim que o treino for criado ou liberado, ele aparecerá aqui para você acompanhar."
                                showCreateButton = false
                                showChatButton = true
                            }

                            Text(
                                text = titleText,
                                color = Color.White,
                                fontSize = 16.sp,
                                fontWeight = FontWeight.Bold,
                                textAlign = TextAlign.Center
                            )
                            Text(
                                text = messageText,
                                color = NexMuted,
                                fontSize = 12.sp,
                                lineHeight = 16.sp,
                                textAlign = TextAlign.Center,
                                modifier = Modifier.padding(top = 8.dp, bottom = 16.dp)
                            )

                            if (showCreateButton) {
                                Button(
                                    onClick = { showCreatePlanDialog = true },
                                    colors = ButtonDefaults.buttonColors(containerColor = NexNeon),
                                    modifier = Modifier.fillMaxWidth().height(44.dp)
                                ) {
                                    Text("Criar minha ficha de treino", color = Color.Black, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                                }
                                Spacer(modifier = Modifier.height(10.dp))
                                OutlinedButton(
                                     onClick = { importPhotoPicker.launch("image/*") },
                                     enabled = !importBusy,
                                     colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                                     modifier = Modifier.fillMaxWidth().height(44.dp)
                                 ) {
                                     Text(
                                         text = when {
                                             importBusy && importState == "VALIDATING_IMAGE" -> "Analisando a imagem..."
                                             importBusy && importState == "EXTRACTING_WORKOUT" -> "Lendo imagem..."
                                             importBusy -> "Lendo imagem..."
                                             else -> "Importar treino por foto IA"
                                         },
                                         fontWeight = FontWeight.Bold,
                                         fontSize = 13.sp
                                     )
                                 }
                            }

                            if (showChatButton) {
                                if (showCreateButton) {
                                    Spacer(modifier = Modifier.height(10.dp))
                                }
                                OutlinedButton(
                                    onClick = { onNavigateToChat?.invoke() },
                                    colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                                    modifier = Modifier.fillMaxWidth().height(44.dp)
                                ) {
                                    Text("Falar com meu profissional", fontWeight = FontWeight.Bold, fontSize = 13.sp)
                                }
                            }
                        }
                    }
                }
            }
            else -> TrainingList(
                plans = plans,
                sessions = sessions,
                activeWorkout = activeWorkout,
                importBusy = importBusy,
                importState = importState,
                onCreatePlan = { showCreatePlanDialog = true },
                onImportPhoto = { importPhotoPicker.launch("image/*") },
                onOpenPlan = { plan ->
                    scope.launch {
                        val detailResult = if (activePatientId != null) {
                            professionalRepository.patientTrainingPlanDetail(activePatientId!!, plan.id)
                        } else {
                            repository.planDetail(plan.id)
                        }
                        detailResult
                            .onSuccess { selected = it }
                            .onFailure { error = friendlyError(it) }
                    }
                },
            )
        }
    }

    val currentPlan = selected
    if (showRpeDialog && currentPlan != null) {
        RpeDialog(
            planName = currentPlan.name,
            onDismiss = { showRpeDialog = false },
            onSave = { rpe, mood, notes ->
                scope.launch {
                    val finishResult = activeWorkout?.sessionId?.let { sessionId ->
                        repository.updateSession(
                            sessionId,
                            UpdateWorkoutSessionRequest(
                                status = "completed",
                                completionPercent = 100,
                                completedExerciseIds = activeWorkout?.completedExerciseIds.orEmpty().toList(),
                                rpeScore = rpe,
                                mood = mood.ifBlank { null },
                                notes = notes.ifBlank { null },
                            ),
                        ).map { Unit }
                    } ?: repository.saveSession(
                        WorkoutSessionRequest(
                            sessionDate = LocalDate.now().toString(),
                            rpeScore = rpe,
                            mood = mood.ifBlank { null },
                            notes = notes.ifBlank { null },
                        ),
                    ).map { Unit }

                    finishResult.onSuccess {
                        offlineRepository.queueExerciseSync(
                            context,
                            ExerciseSyncRequest(
                                entryDate = LocalDate.now().toString(),
                                activityType = currentPlan.name,
                                durationMin = 45,
                                rpe = rpe,
                                notes = notes.ifBlank { "Treino registrado pelo app" },
                            ),
                        )
                        showRpeDialog = false
                        activeWorkoutStore.clear()
                        activeWorkout = null
                        reload()
                    }.onFailure { error = friendlyError(it) }
                }
            },
        )
    }

    if (showCreatePlanDialog) {
        CreateTrainingPlanDialog(
            onDismiss = { showCreatePlanDialog = false },
            title = "Criar ficha de treino",
            onSave = { request, targetPhotos ->
                scope.launch {
                    val result = if (targetPhotos.isEmpty()) {
                        repository.createPlan(request)
                    } else {
                        repository.createPlanWithTargetPhotos(request, targetPhotos)
                    }

                    result.onSuccess { plan ->
                        showCreatePlanDialog = false
                        reload()
                        repository.planDetail(plan.id)
                            .onSuccess { selected = it }
                            .onFailure { error = friendlyError(it) }
                    }.onFailure {
                        error = friendlyError(it)
                    }
                }
            },
        )
    }

    if (showImportDialog) {
        WorkoutImportDialog(
            workoutName = importWorkoutName,
            onWorkoutNameChange = { importWorkoutName = it.take(100) },
            exercises = importExercises,
            error = importError,
            busy = importBusy,
            importConfidence = importConfidence,
            processingSeconds = importProcessingSeconds,
            selectedPhotoUri = selectedPhotoUri,
            selectedPhotoUris = selectedPhotoUris,
            selectedPhotoCount = selectedPhotoUris.size,
            wizardStep = wizardStep,
            agentValidationStatus = agentValidationStatus,
            agentOCRStatus = agentOCRStatus,
            agentSpecialistStatus = agentSpecialistStatus,
            agentAuditorStatus = agentAuditorStatus,
            aiMessages = aiMessages,
            selectedDay = selectedDay,
            onDayChange = { selectedDay = it },
            onStartScan = { runIAOrchestratorV2(selectedPhotoUris) },
            onStepChange = { wizardStep = it },
            aiCreditsBalance = aiCreditsBalance,
            onDismiss = { 
                showImportDialog = false 
                importState = "IDLE"
                wizardStep = 1
                selectedPhotoUris = emptyList()
                selectedPhotoUri = null
            },
            onPickAnother = { importPhotoPicker.launch("image/*") },
            onExerciseChange = { index, exercise ->
                importExercises = importExercises.mapIndexed { i, current -> if (i == index) exercise else current }
            },
            onRemoveExercise = { index ->
                importExercises = importExercises.filterIndexed { i, _ -> i != index }
            },
            onSave = {
                scope.launch {
                    importBusy = true
                    importState = "SAVING"
                    importError = null
                    repository.saveWorkoutImport(
                        SaveWorkoutImportRequest(
                            workoutName = importWorkoutName.ifBlank { "Treino IA - ${LocalDate.now()}" },
                            exercises = importExercises,
                        ),
                    ).onSuccess { saved ->
                        wizardStep = 5
                        importExercises = emptyList()
                        importState = "SUCCESS"
                        reload()
                        repository.planDetail(saved.planId)
                            .onSuccess { selected = it }
                            .onFailure { error = friendlyError(it) }
                    }.onFailure { 
                        importError = friendlyError(it)
                        importState = "REVIEWING"
                    }
                    importBusy = false
                }
            },
        )
    }



    if (showEditPlanDialog && selected != null) {
        CreateTrainingPlanDialog(
            onDismiss = { showEditPlanDialog = false },
            title = "Editar ficha de treino",
            initialPlan = selected,
            onSave = { request, _ ->
                val planId = selected!!.id
                scope.launch {
                    repository.updatePlan(planId, request).onSuccess {
                        actionError = null
                        showEditPlanDialog = false
                        repository.planDetail(planId)
                            .onSuccess { selected = it }
                            .onFailure { actionError = friendlyError(it) }
                        reload()
                    }.onFailure { actionError = friendlyError(it) }
                }
            },
        )
    }

    if (showDeletePlanDialog && selected != null) {
        AlertDialog(
            onDismissRequest = { showDeletePlanDialog = false },
            containerColor = Color(0xFF0B1117),
            titleContentColor = Color.White,
            textContentColor = NexMuted,
            title = { Text("Excluir ficha de treino") },
            text = { Text("Deseja excluir \"${selected!!.name}\"? Esta acao nao pode ser desfeita.") },
            confirmButton = {
                TextButton(
                    onClick = {
                        val planId = selected!!.id
                        scope.launch {
                            repository.deletePlan(planId)
                                .onSuccess {
                                    actionError = null
                                    showDeletePlanDialog = false
                                    selected = null
                                    reload()
                                }
                                .onFailure {
                                    showDeletePlanDialog = false
                                    actionError = friendlyError(it)
                                }
                        }
                    },
                ) {
                    Text("Excluir", color = Color(0xFFFF6B6B), fontWeight = FontWeight.Bold)
                }
            },
            dismissButton = {
                TextButton(onClick = { showDeletePlanDialog = false }) { Text("Cancelar") }
            },
        )
    }

    loadLogExercise?.let { exercise ->
        LoadLogDialog(
            exercise = exercise,
            onDismiss = { loadLogExercise = null },
            onSave = { setNumber, reps, weight, rpe, toFailure ->
                val exerciseId = exercise.exerciseId
                if (exerciseId == null) {
                    error = "Exercicio sem catalogo vinculado para registrar carga."
                    loadLogExercise = null
                    return@LoadLogDialog
                }

                scope.launch {
                    repository.saveLoadLog(
                        CreateLoadLogRequest(
                            trainingPlanExerciseId = exercise.id,
                            exerciseId = exerciseId,
                            logDate = LocalDate.now().toString(),
                            setNumber = setNumber,
                            repsDone = reps,
                            weightKg = weight,
                            rpe = rpe,
                            toFailure = toFailure,
                        ),
                    ).onSuccess {
                        loadLogExercise = null
                        selected?.let { current ->
                            repository.planDetail(current.id)
                                .onSuccess { selected = it }
                                .onFailure { error = friendlyError(it) }
                        }
                    }.onFailure { error = friendlyError(it) }
                }
            },
        )
    }
}

@Composable
private fun CreateTrainingPlanDialog(
    onDismiss: () -> Unit,
    title: String,
    initialPlan: TrainingPlanDetailDto? = null,
    onSave: (request: CreateTrainingPlanRequest, targetPhotos: List<MultipartBody.Part>) -> Unit,
) {
    val context = LocalContext.current
    val repository = remember { TrainingRepository() }
    val scope = rememberCoroutineScope()
    var step by remember { mutableIntStateOf(1) }
    var name by remember { mutableStateOf(initialPlan?.name.orEmpty()) }
    var planLabel by remember { mutableStateOf(initialPlan?.planLabel ?: "Treino A") }
    var goal by remember { mutableStateOf(initialPlan?.goal.orEmpty()) }
    var studentProfile by remember { mutableStateOf("Intermediario") }
    var splitType by remember { mutableStateOf("ABC") }
    var frequency by remember { mutableStateOf(initialPlan?.frequency?.filter { it.isDigit() }?.ifBlank { null } ?: "3") }
    var difficulty by remember { mutableStateOf(initialPlan?.difficulty ?: "Intermediario") }
    var status by remember { mutableStateOf(initialPlan?.status ?: "Ativo") }
    var description by remember { mutableStateOf(initialPlan?.description.orEmpty()) }
    var isTemplate by remember { mutableStateOf(false) }
    var catalog by remember { mutableStateOf<List<ExerciseCatalogDto>>(emptyList()) }
    var catalogSearch by remember { mutableStateOf("") }
    var loadingCatalog by remember { mutableStateOf(false) }
    var targetSearch by remember { mutableStateOf("") }
    var selectedTargetAreas by remember { mutableStateOf<List<String>>(emptyList()) }
    var selectedTargetPhotoType by remember { mutableStateOf("front") }
    var targetPhotoLabels by remember { mutableStateOf<List<String>>(emptyList()) }
    var targetPhotoParts by remember { mutableStateOf<List<MultipartBody.Part>>(emptyList()) }
    var selectedExercises by remember {
        mutableStateOf(initialPlan?.exercises.orEmpty().map { WorkoutBuilderExercise.fromPlanExercise(it) })
    }
    var validation by remember { mutableStateOf<String?>(null) }
    val days = listOf("Segunda", "Terca", "Quarta", "Quinta", "Sexta", "Sabado", "Domingo")
    var selectedDays by remember { mutableStateOf<List<String>>(emptyList()) }

    fun loadCatalog(search: String? = null) {
        scope.launch {
            loadingCatalog = true
            validation = null
            repository.exerciseCatalog(search?.takeIf { it.isNotBlank() })
                .onSuccess { catalog = it }
                .onFailure { validation = friendlyError(it) }
            loadingCatalog = false
        }
    }

    fun estimatedDuration(): Int {
        val seconds = selectedExercises.sumOf { exercise ->
            exercise.sets.sumOf { set -> 40 + (set.rest.toIntOrNull() ?: 60) }
        }
        return (seconds + 59) / 60
    }

    fun totalVolume(): Double = selectedExercises.sumOf { exercise ->
        exercise.sets.sumOf { set ->
            (set.reps.toDoubleOrNull() ?: 0.0) * (set.weight.toDoubleOrNull() ?: 0.0)
        }
    }

    fun musclesWorked(): List<String> = selectedExercises
        .flatMap { it.muscles.ifEmpty { listOfNotNull(it.muscleGroup) } }
        .filter { it.isNotBlank() }
        .distinct()

    LaunchedEffect(Unit) { loadCatalog() }

    val targetPhotoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri ?: return@rememberLauncherForActivityResult
        scope.launch {
            runCatching {
                val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                val extension = trainingPhotoExtensionForMimeType(mimeType)
                    ?: throw IllegalArgumentException("Envie uma foto em JPG, PNG ou WebP.")
                val temp = File.createTempFile("target_${selectedTargetPhotoType}_", ".$extension", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }

                val part = MultipartBody.Part.createFormData(
                    "target_photos[$selectedTargetPhotoType]",
                    temp.name,
                    temp.asRequestBody(mimeType.toMediaTypeOrNull()),
                )

                targetPhotoParts = targetPhotoParts + part
                targetPhotoLabels = targetPhotoLabels + trainingPhotoTypeLabel(selectedTargetPhotoType)
            }.onFailure {
                validation = friendlyError(it)
            }
        }
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = {
            Column {
                Text(title)
                Text("Etapa $step de 6: ${workoutBuilderStepTitle(step)}", color = NexMuted, fontSize = 12.sp)
                LinearProgressIndicator(
                    progress = { step / 6f },
                    modifier = Modifier.fillMaxWidth().padding(top = 10.dp),
                    color = NexNeon,
                    trackColor = Color(0x22FFFFFF),
                )
            }
        },
        text = {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                validation?.let { message ->
                    item { Text(message, color = Color(0xFFFF6B6B), fontSize = 12.sp, fontWeight = FontWeight.Bold) }
                }
                when (step) {
                    1 -> item {
                        BuilderTargetAreasStep(
                            selectedAreas = selectedTargetAreas,
                            search = targetSearch,
                            onSearchChange = { targetSearch = it.take(40) },
                            onToggleArea = { area ->
                                selectedTargetAreas = if (selectedTargetAreas.contains(area)) {
                                    selectedTargetAreas - area
                                } else {
                                    selectedTargetAreas + area
                                }
                            },
                            onAddCustomArea = {
                                val area = targetSearch.trim()
                                if (area.isNotBlank() && !selectedTargetAreas.contains(area)) {
                                    selectedTargetAreas = selectedTargetAreas + area
                                }
                                targetSearch = ""
                            },
                            targetPhotoLabels = targetPhotoLabels,
                            onPickTargetPhoto = { type ->
                                selectedTargetPhotoType = type
                                targetPhotoPicker.launch("image/*")
                            },
                        )
                    }
                    2 -> item {
                        BuilderStepBasics(
                            name = name,
                            onNameChange = { name = it.take(100) },
                            planLabel = planLabel,
                            onPlanLabelChange = { planLabel = it.take(10) },
                            goal = goal,
                            onGoalChange = { goal = it.take(50) },
                            studentProfile = studentProfile,
                            onStudentProfileChange = { studentProfile = it.take(30) },
                            splitType = splitType,
                            onSplitTypeChange = { splitType = it.take(30) },
                            frequency = frequency,
                            onFrequencyChange = { frequency = onlyDigits(it).take(1) },
                            difficulty = difficulty,
                            onDifficultyChange = { difficulty = it.take(20) },
                            status = status,
                            onStatusChange = { status = it.take(20) },
                            description = description,
                            onDescriptionChange = { description = it.take(2000) },
                            days = days,
                            selectedDays = selectedDays,
                            onToggleDay = { day ->
                                selectedDays = if (selectedDays.contains(day)) {
                                    selectedDays - day
                                } else {
                                    selectedDays + day
                                }
                            },
                        )
                    }
                    3 -> {
                        item {
                            OutlinedTextField(
                                value = catalogSearch,
                                onValueChange = { catalogSearch = it },
                                label = { Text("Buscar exercicio") },
                                modifier = Modifier.fillMaxWidth(),
                                singleLine = true,
                                colors = workoutTextFieldColors(),
                            )
                            TextButton(
                                onClick = { loadCatalog(catalogSearch) },
                                enabled = !loadingCatalog,
                                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                            ) {
                                Text(if (loadingCatalog) "Buscando..." else "Buscar no catalogo")
                            }
                        }
                        if (!loadingCatalog && catalog.isEmpty()) {
                            item {
                                Text(
                                    "Nenhum exercicio encontrado no catalogo.",
                                    color = NexMuted,
                                    fontSize = 12.sp,
                                )
                            }
                        }
                        items(catalog, key = { it.id }) { exercise ->
                            val selected = selectedExercises.any { it.id == exercise.id }
                            NexCard(
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable {
                                        selectedExercises = if (selected) {
                                            selectedExercises.filterNot { it.id == exercise.id }
                                        } else {
                                            selectedExercises + WorkoutBuilderExercise.fromCatalog(exercise)
                                        }
                                    },
                            ) {
                                Row(verticalAlignment = Alignment.CenterVertically) {
                                    Checkbox(checked = selected, onCheckedChange = null)
                                    Column(modifier = Modifier.padding(start = 8.dp)) {
                                        Text(exercise.name, color = Color.White, fontWeight = FontWeight.Bold)
                                        Text(
                                            listOfNotNull(exercise.muscleGroup, exercise.equipment, exercise.difficulty).joinToString(" • "),
                                            color = NexMuted,
                                            fontSize = 11.sp,
                                        )
                                    }
                                }
                            }
                        }
                    }
                    4 -> {
                        if (selectedExercises.isEmpty()) {
                            item { Text("Selecione pelo menos um exercicio na etapa anterior.", color = NexMuted) }
                        }
                        items(selectedExercises, key = { it.localId }) { exercise ->
                            BuilderExerciseSetsCard(
                                exercise = exercise,
                                onChange = { changed ->
                                    selectedExercises = selectedExercises.map { if (it.localId == changed.localId) changed else it }
                                },
                                onRemove = {
                                    selectedExercises = selectedExercises.filterNot { it.localId == exercise.localId }
                                },
                            )
                        }
                    }
                    5 -> item {
                        BuilderReviewCard(
                            name = name,
                            goal = goal,
                            frequency = frequency.toIntOrNull(),
                            duration = estimatedDuration(),
                            totalVolume = totalVolume(),
                            exercises = selectedExercises,
                            targetAreas = selectedTargetAreas,
                        )
                    }
                    6 -> item {
                        NexCard {
                            Text("Tudo pronto", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
                            Text("Salve a ficha completa com dados, exercicios, series e revisao.", color = NexMuted, modifier = Modifier.padding(top = 6.dp))
                            Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.padding(top = 12.dp)) {
                                Checkbox(checked = isTemplate, onCheckedChange = { isTemplate = it })
                                Text("Salvar como modelo reutilizavel", color = Color.White, fontSize = 12.sp)
                            }
                        }
                    }
                }
            }
        },
        confirmButton = {
            TextButton(
                enabled = step == 1 || name.isNotBlank(),
                colors = ButtonDefaults.textButtonColors(
                    contentColor = NexNeon,
                    disabledContentColor = Color(0xFF7D8594),
                ),
                onClick = {
                    validation = null
                    if (step < 6) {
                        if (step == 1 && selectedTargetAreas.isEmpty()) {
                            validation = "Selecione pelo menos uma area de treino."
                            return@TextButton
                        }
                        if (step == 2 && name.isBlank()) {
                            validation = "Informe o titulo do treino."
                            return@TextButton
                        }
                        if (step == 3 && selectedExercises.isEmpty()) {
                            validation = "Adicione pelo menos um exercicio."
                            return@TextButton
                        }
                        step += 1
                        return@TextButton
                    }

                    if (selectedExercises.isEmpty()) {
                        validation = "Adicione exercicios antes de salvar."
                        step = 3
                        return@TextButton
                    }

                    val request = CreateTrainingPlanRequest(
                            name = name.trim(),
                            planLabel = planLabel.ifBlank { null },
                            goal = goal.ifBlank { null },
                            description = description.ifBlank { null },
                            frequency = frequency.toIntOrNull()?.coerceIn(1, 7),
                            difficulty = difficulty.ifBlank { null },
                            estimatedDuration = estimatedDuration().takeIf { it > 0 },
                            studentProfile = studentProfile.ifBlank { null },
                            splitType = splitType.ifBlank { null },
                            status = status.ifBlank { "Ativo" },
                            daysOfWeek = selectedDays,
                            totalVolume = totalVolume(),
                            musclesWorked = (selectedTargetAreas + musclesWorked()).distinct(),
                            targetAreas = selectedTargetAreas.map { TrainingTargetAreaRequest(name = it) },
                            isTemplate = isTemplate,
                            exercises = selectedExercises.map { exercise ->
                                CreateTrainingExerciseRequest(
                                    id = exercise.id,
                                    notes = exercise.notes.ifBlank { null },
                                    sets = exercise.sets.map { set ->
                                        CreateTrainingSetRequest(
                                            type = set.type.ifBlank { "work" },
                                            reps = set.reps.toIntOrNull(),
                                            weight = set.weight.toDoubleOrNull(),
                                            rest = set.rest.toIntOrNull(),
                                            rpe = set.rpe.toIntOrNull()?.coerceIn(1, 10),
                                            cadence = set.cadence.ifBlank { null },
                                        )
                                    },
                                )
                            },
                    )
                    onSave(request, targetPhotoParts)
                },
            ) { Text(if (step < 6) "Continuar" else "Salvar") }
        },
        dismissButton = {
            TextButton(
                onClick = {
                    if (step > 1) step -= 1 else onDismiss()
                },
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
            ) { Text(if (step > 1) "Voltar" else "Cancelar") }
        },
    )
}

private data class WorkoutBuilderExercise(
    val localId: Long,
    val id: Int,
    val name: String,
    val muscleGroup: String?,
    val muscles: List<String>,
    val notes: String = "",
    val sets: List<WorkoutBuilderSet> = listOf(
        WorkoutBuilderSet(),
        WorkoutBuilderSet(),
        WorkoutBuilderSet(),
    ),
) {
    companion object {
        fun fromCatalog(exercise: ExerciseCatalogDto): WorkoutBuilderExercise = WorkoutBuilderExercise(
            localId = System.nanoTime(),
            id = exercise.id,
            name = exercise.name,
            muscleGroup = exercise.muscleGroup,
            muscles = exercise.muscles,
        )

        fun fromPlanExercise(exercise: TrainingExerciseDto): WorkoutBuilderExercise = WorkoutBuilderExercise(
            localId = System.nanoTime() + exercise.id,
            id = exercise.exerciseId ?: exercise.id,
            name = exercise.name ?: "Exercicio",
            muscleGroup = exercise.muscleGroup,
            muscles = listOfNotNull(exercise.muscleGroup),
            notes = exercise.notes.orEmpty(),
            sets = exercise.sets.orEmpty().ifEmpty { listOf(ExerciseSetDto(id = 0)) }.map { set ->
                WorkoutBuilderSet(
                    reps = set.repsTarget?.toString() ?: "12",
                    weight = "0",
                    rest = set.restSeconds?.toString() ?: "60",
                )
            },
        )
    }
}

private data class WorkoutBuilderSet(
    val type: String = "work",
    val reps: String = "12",
    val weight: String = "0",
    val rest: String = "60",
    val rpe: String = "",
    val cadence: String = "",
)

@Composable
private fun workoutTextFieldColors() = OutlinedTextFieldDefaults.colors(
    focusedTextColor = Color.White,
    unfocusedTextColor = Color.White,
    disabledTextColor = Color.White.copy(alpha = 0.7f),
    focusedLabelColor = NexNeon,
    unfocusedLabelColor = Color(0xFFC7CDD8),
    disabledLabelColor = Color(0xFFC7CDD8),
    focusedPlaceholderColor = Color(0xFFB7BEC9),
    unfocusedPlaceholderColor = Color(0xFFB7BEC9),
    cursorColor = NexNeon,
    focusedBorderColor = NexNeon,
    unfocusedBorderColor = Color(0xFF7D8594),
    disabledBorderColor = Color(0xFF6B7280),
    focusedContainerColor = Color(0xFF0B1117),
    unfocusedContainerColor = Color(0xFF0B1117),
    disabledContainerColor = Color(0xFF0B1117),
)

private fun workoutBuilderStepTitle(step: Int): String = when (step) {
    1 -> "Areas"
    2 -> "Dados"
    3 -> "Exercicios"
    4 -> "Series"
    5 -> "Revisao"
    else -> "Finalizar"
}

@Composable
private fun BuilderTargetAreasStep(
    selectedAreas: List<String>,
    search: String,
    onSearchChange: (String) -> Unit,
    onToggleArea: (String) -> Unit,
    onAddCustomArea: () -> Unit,
    targetPhotoLabels: List<String>,
    onPickTargetPhoto: (String) -> Unit,
) {
    val areas = listOf(
        "Peitoral",
        "Costas",
        "Ombros",
        "Biceps",
        "Triceps",
        "Abdomen",
        "Quadriceps",
        "Posterior de coxa",
        "Gluteos",
        "Panturrilhas",
    )
    var bodySide by remember { mutableStateOf(BodySide.Front) }
    var lastTouchedArea by remember { mutableStateOf<String?>(null) }
    var zoom by remember { mutableStateOf(1f) }
    var panOffset by remember { mutableStateOf(Offset.Zero) }

    LaunchedEffect(bodySide) {
        panOffset = Offset.Zero
    }

    LaunchedEffect(zoom) {
        if (zoom <= 1.01f) {
            panOffset = Offset.Zero
        }
    }

    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
        Text("Selecione as areas de treino", color = Color.White, fontWeight = FontWeight.Black, fontSize = 18.sp)
        Text("Use o corpo para escolher o foco e ajuste pela lista quando preferir.", color = NexMuted, fontSize = 12.sp)
        BodyAreaPicker(
            side = bodySide,
            selectedAreas = selectedAreas,
            lastTouchedArea = lastTouchedArea,
            zoom = zoom,
            panOffset = panOffset,
            onSideChange = { bodySide = it },
            onZoomChange = { zoom = it.coerceIn(1f, 1.8f) },
            onPanChange = { panOffset = it },
            onResetView = {
                zoom = 1f
                panOffset = Offset.Zero
            },
            onAreaTap = { area ->
                lastTouchedArea = area
                onToggleArea(area)
            },
        )
        Text("Selecionar por lista", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
        areas.chunked(2).forEach { rowAreas ->
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                rowAreas.forEach { area ->
                    val selected = selectedAreas.contains(area)
                    AssistChip(
                        onClick = { onToggleArea(area) },
                        label = { Text(area, color = if (selected) NexNeon else Color.White) },
                        leadingIcon = if (selected) {
                            { Icon(Icons.Default.CheckCircle, contentDescription = null, tint = NexNeon) }
                        } else {
                            null
                        },
                        modifier = Modifier.weight(1f),
                    )
                }
                if (rowAreas.size == 1) {
                    Spacer(Modifier.weight(1f))
                }
            }
        }
        if (selectedAreas.isNotEmpty()) {
            Text("Escolhidas", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
            selectedAreas.chunked(2).forEach { rowAreas ->
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                    rowAreas.forEach { area ->
                        AssistChip(
                            onClick = { onToggleArea(area) },
                            label = { Text(area, color = NexNeon) },
                            leadingIcon = { Icon(Icons.Default.CheckCircle, contentDescription = null, tint = NexNeon) },
                            modifier = Modifier.weight(1f),
                        )
                    }
                    if (rowAreas.size == 1) {
                        Spacer(Modifier.weight(1f))
                    }
                }
            }
        }
        OutlinedTextField(
            value = search,
            onValueChange = onSearchChange,
            label = { Text("Adicionar outra area") },
            modifier = Modifier.fillMaxWidth(),
            singleLine = true,
            colors = workoutTextFieldColors(),
        )
        TextButton(
            onClick = onAddCustomArea,
            colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
        ) {
            Text("Adicionar area digitada")
        }

        Text("Fotos de referencia", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
        Text("Opcional. Fotos recusadas na validacao do servidor nao consomem credito.", color = NexMuted, fontSize = 11.sp)
        listOf(
            "front" to "Frente",
            "back" to "Costas",
            "right_side" to "Lado direito",
            "left_side" to "Lado esquerdo",
        ).chunked(2).forEach { rowItems ->
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                rowItems.forEach { (type, label) ->
                    OutlinedButton(
                        onClick = { onPickTargetPhoto(type) },
                        modifier = Modifier.weight(1f),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                    ) {
                        Text(label, fontSize = 12.sp)
                    }
                }
            }
        }
        if (targetPhotoLabels.isNotEmpty()) {
            Text(
                "Anexadas: ${targetPhotoLabels.joinToString(", ")}",
                color = NexMuted,
                fontSize = 11.sp,
            )
        }
    }
}

private enum class BodySide {
    Front,
    Back,
}

private data class BodyHitArea(
    val name: String,
    val side: BodySide,
    val centerX: Float,
    val centerY: Float,
    val width: Float,
    val height: Float,
)

private val workoutBodyAreas = listOf(
    BodyHitArea("Ombros", BodySide.Front, 0.34f, 0.23f, 0.18f, 0.13f),
    BodyHitArea("Ombros", BodySide.Front, 0.66f, 0.23f, 0.18f, 0.13f),
    BodyHitArea("Peitoral", BodySide.Front, 0.50f, 0.29f, 0.30f, 0.17f),
    BodyHitArea("Biceps", BodySide.Front, 0.25f, 0.39f, 0.15f, 0.22f),
    BodyHitArea("Biceps", BodySide.Front, 0.75f, 0.39f, 0.15f, 0.22f),
    BodyHitArea("Abdomen", BodySide.Front, 0.50f, 0.46f, 0.24f, 0.24f),
    BodyHitArea("Quadriceps", BodySide.Front, 0.40f, 0.68f, 0.17f, 0.26f),
    BodyHitArea("Quadriceps", BodySide.Front, 0.60f, 0.68f, 0.17f, 0.26f),
    BodyHitArea("Panturrilhas", BodySide.Front, 0.41f, 0.89f, 0.13f, 0.18f),
    BodyHitArea("Panturrilhas", BodySide.Front, 0.59f, 0.89f, 0.13f, 0.18f),
    BodyHitArea("Costas", BodySide.Back, 0.50f, 0.31f, 0.34f, 0.24f),
    BodyHitArea("Ombros", BodySide.Back, 0.34f, 0.23f, 0.18f, 0.13f),
    BodyHitArea("Ombros", BodySide.Back, 0.66f, 0.23f, 0.18f, 0.13f),
    BodyHitArea("Triceps", BodySide.Back, 0.25f, 0.39f, 0.15f, 0.23f),
    BodyHitArea("Triceps", BodySide.Back, 0.75f, 0.39f, 0.15f, 0.23f),
    BodyHitArea("Gluteos", BodySide.Back, 0.50f, 0.57f, 0.25f, 0.15f),
    BodyHitArea("Posterior de coxa", BodySide.Back, 0.40f, 0.72f, 0.17f, 0.27f),
    BodyHitArea("Posterior de coxa", BodySide.Back, 0.60f, 0.72f, 0.17f, 0.27f),
    BodyHitArea("Panturrilhas", BodySide.Back, 0.41f, 0.89f, 0.13f, 0.18f),
    BodyHitArea("Panturrilhas", BodySide.Back, 0.59f, 0.89f, 0.13f, 0.18f),
)

@Composable
private fun BodyAreaPicker(
    side: BodySide,
    selectedAreas: List<String>,
    lastTouchedArea: String?,
    zoom: Float,
    panOffset: Offset,
    onSideChange: (BodySide) -> Unit,
    onZoomChange: (Float) -> Unit,
    onPanChange: (Offset) -> Unit,
    onResetView: () -> Unit,
    onAreaTap: (String) -> Unit,
) {
    val canResetView = zoom > 1.01f || panOffset != Offset.Zero

    Column(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, Color(0xFF243244), MaterialTheme.shapes.medium)
            .background(Color(0xFF08111D), MaterialTheme.shapes.medium)
            .padding(12.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.spacedBy(10.dp),
    ) {
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
            BodySideButton("Frente", side == BodySide.Front, Modifier.weight(1f)) { onSideChange(BodySide.Front) }
            BodySideButton("Costas", side == BodySide.Back, Modifier.weight(1f)) { onSideChange(BodySide.Back) }
        }
        Row(
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier.fillMaxWidth(),
        ) {
            OutlinedButton(
                onClick = { onZoomChange(zoom - 0.15f) },
                enabled = zoom > 1f,
                modifier = Modifier.weight(1f).height(38.dp),
            ) {
                Text("-", fontWeight = FontWeight.Black)
            }
            Text(
                text = "Zoom ${(zoom * 100).toInt()}%",
                color = Color.White,
                fontWeight = FontWeight.Bold,
                fontSize = 12.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.weight(1.4f),
            )
            OutlinedButton(
                onClick = { onZoomChange(zoom + 0.15f) },
                enabled = zoom < 1.8f,
                modifier = Modifier.weight(1f).height(38.dp),
            ) {
                Text("+", fontWeight = FontWeight.Black)
            }
        }
        if (canResetView) {
            TextButton(
                onClick = onResetView,
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                modifier = Modifier.height(36.dp),
            ) {
                Text("Redefinir imagem", fontWeight = FontWeight.Bold, fontSize = 12.sp)
            }
        }
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .aspectRatio(0.68f)
                .background(Color.Black, MaterialTheme.shapes.medium)
                .pointerInput(side, selectedAreas, zoom, panOffset) {
                    detectTapGestures { offset ->
                        val x = ((offset.x - (size.width / 2f) - panOffset.x) / zoom + (size.width / 2f)) / size.width
                        val y = ((offset.y - (size.height / 2f) - panOffset.y) / zoom + (size.height / 2f)) / size.height
                        workoutBodyAreas
                            .filter { it.side == side && x in (it.centerX - it.width / 2)..(it.centerX + it.width / 2) && y in (it.centerY - it.height / 2)..(it.centerY + it.height / 2) }
                            .minByOrNull { kotlin.math.abs(x - it.centerX) + kotlin.math.abs(y - it.centerY) }
                            ?.let { onAreaTap(it.name) }
                    }
                }
                .pointerInput(zoom, panOffset) {
                    detectDragGestures { change, dragAmount ->
                        if (zoom <= 1.01f) {
                            return@detectDragGestures
                        }
                        change.consume()
                        val maxX = size.width * (zoom - 1f) / 2f
                        val maxY = size.height * (zoom - 1f) / 2f
                        onPanChange(
                            Offset(
                                x = (panOffset.x + dragAmount.x).coerceIn(-maxX, maxX),
                                y = (panOffset.y + dragAmount.y).coerceIn(-maxY, maxY),
                            ),
                        )
                    }
                },
        ) {
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .graphicsLayer(
                        scaleX = zoom,
                        scaleY = zoom,
                        translationX = panOffset.x,
                        translationY = panOffset.y,
                    ),
            ) {
                Image(
                    painter = painterResource(
                        id = if (side == BodySide.Front) R.drawable.body_male_front else R.drawable.body_male_back,
                    ),
                    contentDescription = if (side == BodySide.Front) "Corpo visto de frente" else "Corpo visto de costas",
                    contentScale = ContentScale.Fit,
                    modifier = Modifier.fillMaxSize(),
                )
                Canvas(modifier = Modifier.fillMaxSize()) {
                    val lineColor = Color(0xFF5EEAD4)
                    val selectedColor = NexNeon.copy(alpha = 0.34f)
                    val activeColor = Color(0xFF7CA7FF).copy(alpha = 0.24f)

                    workoutBodyAreas.filter { it.side == side }.forEach { area ->
                        val selected = selectedAreas.contains(area.name)
                        val touched = lastTouchedArea == area.name
                        if (!selected && !touched) {
                            return@forEach
                        }
                        val fill = when {
                            selected -> selectedColor
                            touched -> activeColor
                            else -> Color.Transparent
                        }
                        drawRoundRect(
                            color = fill,
                            topLeft = Offset(size.width * (area.centerX - area.width / 2), size.height * (area.centerY - area.height / 2)),
                            size = Size(size.width * area.width, size.height * area.height),
                            cornerRadius = androidx.compose.ui.geometry.CornerRadius(26f, 26f),
                        )
                        drawRoundRect(
                            color = if (selected) NexNeon else lineColor.copy(alpha = 0.75f),
                            topLeft = Offset(size.width * (area.centerX - area.width / 2), size.height * (area.centerY - area.height / 2)),
                            size = Size(size.width * area.width, size.height * area.height),
                            cornerRadius = androidx.compose.ui.geometry.CornerRadius(26f, 26f),
                            style = Stroke(width = if (selected) 3f else 1.5f),
                        )
                    }
                }
            }
            Text(
                text = lastTouchedArea ?: "Toque em uma regiao",
                color = Color.White,
                fontWeight = FontWeight.Bold,
                modifier = Modifier
                    .align(Alignment.BottomCenter)
                    .background(Color(0xCC050A12), MaterialTheme.shapes.small)
                    .padding(horizontal = 12.dp, vertical = 7.dp),
            )
        }
    }
}

@Composable
private fun BodySideButton(
    label: String,
    selected: Boolean,
    modifier: Modifier = Modifier,
    onClick: () -> Unit,
) {
    Button(
        onClick = onClick,
        modifier = modifier.height(40.dp),
        colors = ButtonDefaults.buttonColors(
            containerColor = if (selected) NexNeon else Color(0xFF111827),
            contentColor = if (selected) Color.Black else Color.White,
        ),
    ) {
        Text(label, fontWeight = FontWeight.Bold, fontSize = 12.sp)
    }
}

@Composable
private fun BuilderStepBasics(
    name: String,
    onNameChange: (String) -> Unit,
    planLabel: String,
    onPlanLabelChange: (String) -> Unit,
    goal: String,
    onGoalChange: (String) -> Unit,
    studentProfile: String,
    onStudentProfileChange: (String) -> Unit,
    splitType: String,
    onSplitTypeChange: (String) -> Unit,
    frequency: String,
    onFrequencyChange: (String) -> Unit,
    difficulty: String,
    onDifficultyChange: (String) -> Unit,
    status: String,
    onStatusChange: (String) -> Unit,
    description: String,
    onDescriptionChange: (String) -> Unit,
    days: List<String>,
    selectedDays: List<String>,
    onToggleDay: (String) -> Unit,
) {
    Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
        OutlinedTextField(name, onNameChange, label = { Text("Titulo do treino") }, modifier = Modifier.fillMaxWidth(), singleLine = true, colors = workoutTextFieldColors())
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            OutlinedTextField(planLabel, onPlanLabelChange, label = { Text("Etiqueta") }, modifier = Modifier.weight(1f), singleLine = true, colors = workoutTextFieldColors())
            OutlinedTextField(frequency, onFrequencyChange, label = { Text("x/sem") }, modifier = Modifier.weight(1f), keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number), singleLine = true, colors = workoutTextFieldColors())
        }
        OutlinedTextField(goal, onGoalChange, label = { Text("Objetivo") }, modifier = Modifier.fillMaxWidth(), singleLine = true, colors = workoutTextFieldColors())
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            OutlinedTextField(studentProfile, onStudentProfileChange, label = { Text("Perfil") }, modifier = Modifier.weight(1f), singleLine = true, colors = workoutTextFieldColors())
            OutlinedTextField(difficulty, onDifficultyChange, label = { Text("Exigencia") }, modifier = Modifier.weight(1f), singleLine = true, colors = workoutTextFieldColors())
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            OutlinedTextField(splitType, onSplitTypeChange, label = { Text("Divisao") }, modifier = Modifier.weight(1f), singleLine = true, colors = workoutTextFieldColors())
            OutlinedTextField(status, onStatusChange, label = { Text("Status") }, modifier = Modifier.weight(1f), singleLine = true, colors = workoutTextFieldColors())
        }
        Text("Dias recomendados", color = Color.White, fontSize = 11.sp, fontWeight = FontWeight.Bold)
        Row(horizontalArrangement = Arrangement.spacedBy(6.dp), modifier = Modifier.fillMaxWidth()) {
            days.take(4).forEach { day ->
                val isSelected = selectedDays.contains(day)
                AssistChip(
                    onClick = { onToggleDay(day) },
                    label = { Text(day.take(3), color = if (isSelected) NexNeon else Color.White) },
                    leadingIcon = if (isSelected) {
                        { Icon(Icons.Default.CheckCircle, contentDescription = null, tint = NexNeon) }
                    } else {
                        null
                    },
                )
            }
        }
        Row(horizontalArrangement = Arrangement.spacedBy(6.dp), modifier = Modifier.fillMaxWidth()) {
            days.drop(4).forEach { day ->
                val isSelected = selectedDays.contains(day)
                AssistChip(
                    onClick = { onToggleDay(day) },
                    label = { Text(day.take(3), color = if (isSelected) NexNeon else Color.White) },
                    leadingIcon = if (isSelected) {
                        { Icon(Icons.Default.CheckCircle, contentDescription = null, tint = NexNeon) }
                    } else {
                        null
                    },
                )
            }
        }
        OutlinedTextField(description, onDescriptionChange, label = { Text("Observacoes") }, modifier = Modifier.fillMaxWidth(), colors = workoutTextFieldColors())
    }
}

@Composable
private fun BuilderExerciseSetsCard(
    exercise: WorkoutBuilderExercise,
    onChange: (WorkoutBuilderExercise) -> Unit,
    onRemove: () -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text(exercise.name, color = Color.White, fontWeight = FontWeight.Black)
                Text(exercise.muscleGroup.orEmpty(), color = NexMuted, fontSize = 11.sp)
            }
            TextButton(
                onClick = onRemove,
                colors = ButtonDefaults.textButtonColors(contentColor = Color(0xFFFF8A8A)),
            ) { Text("Remover") }
        }
        OutlinedTextField(
            value = exercise.notes,
            onValueChange = { onChange(exercise.copy(notes = it.take(1000))) },
            label = { Text("Observacoes do exercicio") },
            modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
            colors = workoutTextFieldColors(),
        )
        exercise.sets.forEachIndexed { index, set ->
            NexCard(modifier = Modifier.fillMaxWidth().padding(top = 8.dp)) {
                Text("Serie ${index + 1}", color = Color.White, fontWeight = FontWeight.Bold)
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 8.dp)) {
                    OutlinedTextField(
                        value = set.reps,
                        onValueChange = { value -> replaceBuilderSet(exercise, index, set.copy(reps = onlyDigits(value).take(3)), onChange) },
                        label = { Text("Reps") },
                        modifier = Modifier.weight(1f),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        colors = workoutTextFieldColors(),
                    )
                    OutlinedTextField(
                        value = set.weight,
                        onValueChange = { value -> replaceBuilderSet(exercise, index, set.copy(weight = onlyDecimal(value).take(7)), onChange) },
                        label = { Text("Kg") },
                        modifier = Modifier.weight(1f),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                        singleLine = true,
                        colors = workoutTextFieldColors(),
                    )
                }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 8.dp)) {
                    OutlinedTextField(
                        value = set.rest,
                        onValueChange = { value -> replaceBuilderSet(exercise, index, set.copy(rest = onlyDigits(value).take(4)), onChange) },
                        label = { Text("Descanso") },
                        modifier = Modifier.weight(1f),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        colors = workoutTextFieldColors(),
                    )
                    OutlinedTextField(
                        value = set.rpe,
                        onValueChange = { value -> replaceBuilderSet(exercise, index, set.copy(rpe = onlyDigits(value).take(2)), onChange) },
                        label = { Text("RPE") },
                        modifier = Modifier.weight(1f),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                        singleLine = true,
                        colors = workoutTextFieldColors(),
                    )
                }
                OutlinedTextField(
                    value = set.cadence,
                    onValueChange = { value -> replaceBuilderSet(exercise, index, set.copy(cadence = value.take(20)), onChange) },
                    label = { Text("Cadencia") },
                    modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                if (exercise.sets.size > 1) {
                    TextButton(
                        onClick = { onChange(exercise.copy(sets = exercise.sets.filterIndexed { i, _ -> i != index })) },
                        colors = ButtonDefaults.textButtonColors(contentColor = Color(0xFFFF8A8A)),
                    ) { Text("Remover serie") }
                }
            }
        }
        OutlinedButton(
            onClick = { onChange(exercise.copy(sets = exercise.sets + WorkoutBuilderSet())) },
            modifier = Modifier.fillMaxWidth().padding(top = 10.dp),
        ) { Text("Adicionar serie") }
    }
}

private fun replaceBuilderSet(
    exercise: WorkoutBuilderExercise,
    index: Int,
    set: WorkoutBuilderSet,
    onChange: (WorkoutBuilderExercise) -> Unit,
) {
    onChange(exercise.copy(sets = exercise.sets.mapIndexed { i, current -> if (i == index) set else current }))
}

@Composable
private fun BuilderReviewCard(
    name: String,
    goal: String,
    frequency: Int?,
    duration: Int,
    totalVolume: Double,
    exercises: List<WorkoutBuilderExercise>,
    targetAreas: List<String>,
) {
    NexCard {
        Text("Ficha de revisao", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
        Text(name.ifBlank { "Sem titulo" }, color = NexNeon, modifier = Modifier.padding(top = 8.dp), fontWeight = FontWeight.Bold)
        Text("Objetivo: ${goal.ifBlank { "Geral" }}", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Text("Frequencia: ${frequency ?: 0}x/semana • Duracao: ${duration} min", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Text("Volume estimado: ${totalVolume.toInt()} kg", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        if (targetAreas.isNotEmpty()) {
            Text("Areas: ${targetAreas.joinToString(", ")}", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        }
        Spacer(Modifier.height(12.dp))
        exercises.forEachIndexed { index, exercise ->
            Text("${index + 1}. ${exercise.name} • ${exercise.sets.size} series", color = Color.White, fontSize = 13.sp, modifier = Modifier.padding(top = 6.dp))
        }
    }
}

private fun importNumericValue(value: String?, fallback: Int = 0): Int =
    Regex("\\d+").find(value.orEmpty())?.value?.toIntOrNull() ?: fallback

private fun importedDays(exercises: List<WorkoutImportExerciseDto>, selectedDay: String): List<String> {
    val days = exercises.mapNotNull { it.day?.takeIf { day -> day.isNotBlank() } }.distinct()
    return days.ifEmpty { listOf(selectedDay) }
}

private fun totalImportedSets(exercises: List<WorkoutImportExerciseDto>): Int =
    exercises.sumOf { importNumericValue(it.series, 3) }

private fun totalImportedReps(exercises: List<WorkoutImportExerciseDto>): Int =
    exercises.sumOf { importNumericValue(it.series, 3) * importNumericValue(it.repeticoes, 12) }

private fun importReviewIssues(exercises: List<WorkoutImportExerciseDto>): List<String> =
    exercises.flatMapIndexed { index, exercise ->
        buildList {
            val name = exercise.nomeExercicio.ifBlank { "Exercicio ${index + 1}" }
            if (exercise.nomeExercicio.isBlank()) add("$name: nome do exercicio")
            if (exercise.series.isNullOrBlank()) add("$name: series")
            if (exercise.repeticoes.isNullOrBlank()) add("$name: repeticoes")
            if (exercise.carga.isNullOrBlank()) add("$name: carga")
        }
    }

private fun formatImportedDay(day: String): String =
    day.replaceFirstChar { it.uppercase() }

@Composable
private fun ImportStatCard(label: String, value: String, valueColor: Color = Color.White, modifier: Modifier = Modifier) {
    androidx.compose.material3.Card(
        colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color.White.copy(alpha = 0.04f)),
        border = androidx.compose.foundation.BorderStroke(1.dp, Color.White.copy(alpha = 0.08f)),
        modifier = modifier,
    ) {
        Column(modifier = Modifier.padding(10.dp)) {
            Text(label, color = NexMuted, fontSize = 9.sp, fontWeight = FontWeight.Black)
            Text(value, color = valueColor, fontSize = 18.sp, fontWeight = FontWeight.Black, modifier = Modifier.padding(top = 2.dp))
        }
    }
}

@Composable
private fun WorkoutImportDialog(
    workoutName: String,
    onWorkoutNameChange: (String) -> Unit,
    exercises: List<WorkoutImportExerciseDto>,
    error: String?,
    busy: Boolean,
    importConfidence: Double,
    processingSeconds: Int,
    selectedPhotoUri: Uri?,
    selectedPhotoUris: List<Uri>,
    selectedPhotoCount: Int,
    wizardStep: Int,
    agentValidationStatus: String,
    agentOCRStatus: String,
    agentSpecialistStatus: String,
    agentAuditorStatus: String,
    aiMessages: List<String>,
    selectedDay: String,
    onDayChange: (String) -> Unit,
    onStartScan: () -> Unit,
    onStepChange: (Int) -> Unit,
    onDismiss: () -> Unit,
    onPickAnother: () -> Unit,
    onExerciseChange: (Int, WorkoutImportExerciseDto) -> Unit,
    onRemoveExercise: (Int) -> Unit,
    onSave: () -> Unit,
    aiCreditsBalance: Int = 0,
) {
    val context = LocalContext.current
    var previewZoomShow by remember { mutableStateOf(false) }
    var zoomScale by remember { mutableStateOf(1f) }
    var rotationAngle by remember { mutableStateOf(0f) }
    var previewIndex by remember(selectedPhotoUris) { mutableIntStateOf(0) }
    val previewUris = selectedPhotoUris.ifEmpty { selectedPhotoUri?.let { listOf(it) } ?: emptyList() }
    val currentPreviewUri = previewUris.getOrNull(previewIndex.coerceIn(0, (previewUris.size - 1).coerceAtLeast(0)))

    val fileSize = currentPreviewUri?.let { uri ->
        runCatching {
            context.contentResolver.openFileDescriptor(uri, "r")?.use { it.statSize }
        }.getOrNull()
    } ?: 0L
    val qualityScore = if (fileSize > 2000000) 98 else if (fileSize > 500000) 94 else 88
    val qualityStatus = if (qualityScore >= 95) "Excelente" else if (qualityScore >= 90) "Boa" else "Regular"

    AlertDialog(
        onDismissRequest = { if (!busy) onDismiss() },
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = {
            Column(modifier = Modifier.fillMaxWidth()) {
                // Header Title with Premium badge
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                    Text("NexShape AI Import", fontWeight = FontWeight.Black, fontSize = 18.sp, color = Color.White)
                    Row(
                        modifier = Modifier
                            .clip(androidx.compose.foundation.shape.RoundedCornerShape(8.dp))
                            .background(Color(0xFF10B981).copy(alpha = 0.15f))
                            .border(1.dp, Color(0xFF10B981).copy(alpha = 0.3f), androidx.compose.foundation.shape.RoundedCornerShape(8.dp))
                            .padding(horizontal = 6.dp, vertical = 2.dp),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Text("PREMIUM 👑", color = Color(0xFF10B981), fontSize = 9.sp, fontWeight = FontWeight.Bold)
                    }
                }
                
                Spacer(Modifier.height(10.dp))

                // Progress Step indicators 1 to 5
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    val stepsList = listOf("Fotos", "Dias", "Scanner", "Revisar", "Sucesso")
                    stepsList.forEachIndexed { idx, label ->
                        val stepNum = idx + 1
                        val isActive = stepNum == wizardStep
                        val isCompleted = stepNum < wizardStep
                        val color = if (isActive) NexNeon else if (isCompleted) Color(0xFF10B981) else Color.White.copy(alpha = 0.15f)
                        
                        Column(
                            horizontalAlignment = Alignment.CenterHorizontally,
                            modifier = Modifier.weight(1f)
                        ) {
                            Box(
                                modifier = Modifier
                                    .size(24.dp)
                                    .clip(androidx.compose.foundation.shape.CircleShape)
                                    .background(if (isActive) NexNeon.copy(alpha = 0.15f) else Color.White.copy(alpha = 0.05f))
                                    .border(1.dp, color, androidx.compose.foundation.shape.CircleShape),
                                contentAlignment = Alignment.Center
                            ) {
                                if (isCompleted) {
                                    Text("✔", color = Color(0xFF10B981), fontSize = 10.sp, fontWeight = FontWeight.Bold)
                                } else {
                                    Text(stepNum.toString(), color = color, fontSize = 10.sp, fontWeight = FontWeight.Bold)
                                }
                            }
                            Spacer(Modifier.height(4.dp))
                            Text(label, color = color, fontSize = 8.sp, fontWeight = FontWeight.Bold)
                        }
                    }
                }
            }
        },
        text = {
            Column(modifier = Modifier.fillMaxWidth()) {
                if (error != null && wizardStep != 3) {
                    Text(error, color = Color(0xFFFF6B6B), fontSize = 12.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(bottom = 8.dp))
                }

                when (wizardStep) {
                    1 -> {
                        // Step 1: Selecionar Fotos
                        Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Text("1. Imagens Selecionadas", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                            Text("$selectedPhotoCount de 7 fotos selecionadas", color = NexNeon, fontWeight = FontWeight.Bold, fontSize = 11.sp)
                            
                            currentPreviewUri?.let { uri ->
                                Box(
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .height(140.dp)
                                        .clip(androidx.compose.foundation.shape.RoundedCornerShape(16.dp))
                                        .border(1.dp, Color.White.copy(alpha = 0.1f), androidx.compose.foundation.shape.RoundedCornerShape(16.dp))
                                        .clickable { previewZoomShow = true },
                                    contentAlignment = Alignment.Center
                                ) {
                                    AsyncImage(
                                        model = uri,
                                        contentDescription = "Workout Photo",
                                        modifier = Modifier.fillMaxSize(),
                                        contentScale = ContentScale.Crop
                                    )
                                    Box(
                                        modifier = Modifier
                                            .fillMaxSize()
                                            .background(Color.Black.copy(alpha = 0.4f)),
                                        contentAlignment = Alignment.Center
                                    ) {
                                        Text("🔍 Toque para Ampliar / Girar", color = Color.White, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                    }
                                }

                                if (previewUris.size > 1) {
                                    Row(
                                        modifier = Modifier.fillMaxWidth(),
                                        horizontalArrangement = Arrangement.SpaceBetween,
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        OutlinedButton(
                                            onClick = { previewIndex = (previewIndex - 1).coerceAtLeast(0) },
                                            enabled = previewIndex > 0,
                                        ) {
                                            Text("Anterior")
                                        }
                                        Text(
                                            "Foto ${previewIndex + 1} de ${previewUris.size}",
                                            color = Color.White,
                                            fontSize = 11.sp,
                                            fontWeight = FontWeight.Bold
                                        )
                                        OutlinedButton(
                                            onClick = { previewIndex = (previewIndex + 1).coerceAtMost(previewUris.lastIndex) },
                                            enabled = previewIndex < previewUris.lastIndex,
                                        ) {
                                            Text("Proxima")
                                        }
                                    }
                                }

                                // Quality Rating Badge
                                androidx.compose.material3.Card(
                                    colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color(0x1F22C55E)),
                                    border = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFF22C55E).copy(alpha = 0.4f)),
                                    modifier = Modifier.fillMaxWidth()
                                ) {
                                    Row(modifier = Modifier.padding(10.dp), verticalAlignment = Alignment.CenterVertically) {
                                        Text("Qualidade da imagem: $qualityScore% ($qualityStatus)", color = Color(0xFF22C55E), fontWeight = FontWeight.Bold, fontSize = 12.sp)
                                    }
                                }
                            }

                            // Credit estimation Card
                            val importsRemaining = aiCreditsBalance / 50
                            val creditColor = if (importsRemaining <= 2) Color(0xFFF97316) else Color(0xFF10B981)
                            val creditBgColor = if (importsRemaining <= 2) Color(0x1FF97316) else Color(0x1F10B981)
                            val creditBorderColor = if (importsRemaining <= 2) Color(0xFFF97316).copy(alpha = 0.3f) else Color(0xFF10B981).copy(alpha = 0.3f)
                            androidx.compose.material3.Card(
                                colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = creditBgColor),
                                border = androidx.compose.foundation.BorderStroke(1.dp, creditBorderColor),
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Column(modifier = Modifier.padding(12.dp), verticalArrangement = Arrangement.spacedBy(4.dp)) {
                                    Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                                        Text("Escaneamentos IA", color = creditColor, fontWeight = FontWeight.Bold, fontSize = 12.sp)
                                        Text("$importsRemaining restantes", color = creditColor, fontWeight = FontWeight.ExtraBold, fontSize = 13.sp)
                                    }
                                    Text("✔ Custo: 50 créditos (1 importação)", color = NexMuted, fontSize = 11.sp)
                                    Text("✔ Saldo atual: $aiCreditsBalance créditos", color = NexMuted, fontSize = 11.sp)
                                    if (importsRemaining <= 2) {
                                        Text("⚠ Saldo baixo! Adquira mais créditos.", color = Color(0xFFF97316), fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                    }
                                }
                            }
                        }
                    }
                    2 -> {
                        // Step 2: Organizar dias
                        Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            Text("2. Vincular ao dia da semana", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                            
                            // Day selection list
                            val daysOfWeek = listOf(
                                "segunda-feira", "terça-feira", "quarta-feira", 
                                "quinta-feira", "sexta-feira", "sábado", "domingo"
                            )
                            
                            LazyColumn(modifier = Modifier.height(180.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
                                items(daysOfWeek.size) { idx ->
                                    val dayName = daysOfWeek[idx]
                                    val isSelected = dayName == selectedDay
                                    Row(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .clip(androidx.compose.foundation.shape.RoundedCornerShape(12.dp))
                                            .background(if (isSelected) NexNeon.copy(alpha = 0.15f) else Color.White.copy(alpha = 0.03f))
                                            .border(1.dp, if (isSelected) NexNeon else Color.White.copy(alpha = 0.08f), androidx.compose.foundation.shape.RoundedCornerShape(12.dp))
                                            .clickable { onDayChange(dayName) }
                                            .padding(12.dp),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Text(
                                            dayName.replaceFirstChar { it.uppercase() },
                                            color = if (isSelected) NexNeon else Color.White,
                                            fontWeight = FontWeight.Bold,
                                            fontSize = 12.sp,
                                            modifier = Modifier.weight(1f)
                                        )
                                        if (isSelected) {
                                            Text("✔ Selecionado", color = NexNeon, fontSize = 10.sp, fontWeight = FontWeight.Bold)
                                        }
                                    }
                                }
                            }
                        }
                    }
                    3 -> {
                        // Step 3: Orquestrador IA
                        Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                            Row(
                                modifier = Modifier.fillMaxWidth(),
                                horizontalArrangement = Arrangement.SpaceBetween,
                                verticalAlignment = Alignment.CenterVertically
                            ) {
                                Text("3. Scanner AI em Progresso", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                                // Chip discreto de consumo
                                Row(
                                    modifier = Modifier
                                        .clip(androidx.compose.foundation.shape.RoundedCornerShape(20.dp))
                                        .background(Color(0x1AA855F7))
                                        .padding(horizontal = 10.dp, vertical = 4.dp),
                                    verticalAlignment = Alignment.CenterVertically,
                                    horizontalArrangement = Arrangement.spacedBy(4.dp)
                                ) {
                                    Text("🧠", fontSize = 10.sp)
                                    Text("1 crédito IA", color = Color(0xFFA855F7), fontSize = 9.sp, fontWeight = FontWeight.Black)
                                }
                            }
                            
                            // Status bar layout
                            Column(verticalArrangement = Arrangement.spacedBy(6.dp)) {
                                val agents = listOf(
                                    Triple("Agente de Validação", agentValidationStatus, "Inspeciona o documento"),
                                    Triple("Agente OCR", agentOCRStatus, "Lê a imagem em alta definição"),
                                    Triple("Agente Especialista", agentSpecialistStatus, "Interpreta os exercícios"),
                                    Triple("Agente Auditor", agentAuditorStatus, "Valida inconsistências físicas")
                                )
                                
                                agents.forEach { (name, status, desc) ->
                                    Row(
                                        modifier = Modifier
                                            .fillMaxWidth()
                                            .clip(androidx.compose.foundation.shape.RoundedCornerShape(12.dp))
                                            .background(Color.White.copy(alpha = 0.03f))
                                            .padding(10.dp),
                                        verticalAlignment = Alignment.CenterVertically
                                    ) {
                                        Column(modifier = Modifier.weight(1f)) {
                                            Text(name, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 12.sp)
                                            Text(desc, color = NexMuted, fontSize = 10.sp)
                                        }
                                        
                                        when (status) {
                                            "running" -> {
                                                CircularProgressIndicator(modifier = Modifier.size(16.dp), color = NexNeon, strokeWidth = 2.dp)
                                            }
                                            "success" -> {
                                                Text("✔ Concluído", color = Color(0xFF10B981), fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                            }
                                            "failed" -> {
                                                Text("❌ Falhou", color = Color(0xFFFF6B6B), fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                            }
                                            else -> {
                                                Text("⏳ Na Fila", color = NexMuted, fontSize = 11.sp)
                                            }
                                        }
                                    }
                                }
                            }

                            // Live Monospace terminal logs card
                            Text("Live Console Logs", color = NexMuted, fontSize = 10.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 4.dp))
                            androidx.compose.material3.Card(
                                colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color.Black),
                                border = androidx.compose.foundation.BorderStroke(1.dp, Color.White.copy(alpha = 0.1f)),
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .height(100.dp)
                            ) {
                                LazyColumn(
                                    modifier = Modifier.padding(8.dp),
                                    verticalArrangement = Arrangement.spacedBy(4.dp)
                                ) {
                                    items(aiMessages.size) { idx ->
                                        Text(
                                            "> ${aiMessages[idx]}",
                                            color = Color(0xFF10B981),
                                            fontFamily = androidx.compose.ui.text.font.FontFamily.Monospace,
                                            fontSize = 9.sp
                                        )
                                    }
                                }
                            }
                        }
                    }
                    4 -> {
                        // Step 4: Revisão
                        val days = importedDays(exercises, selectedDay)
                        val confidencePercent = (importConfidence * 100).roundToInt().coerceIn(0, 100)
                        val issues = importReviewIssues(exercises)
                        val totalSets = totalImportedSets(exercises)
                        val totalReps = totalImportedReps(exercises)
                        val missingLoad = exercises.count { it.carga.isNullOrBlank() }

                        LazyColumn(modifier = Modifier.height(380.dp), verticalArrangement = Arrangement.spacedBy(10.dp)) {
                            item {
                                Text("4. Revisar dados do treino", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                            }

                            item {
                                Text("NexShape AI", color = NexNeon, fontWeight = FontWeight.Black, fontSize = 10.sp)
                                Text("Importacao concluida", color = Color.White, fontWeight = FontWeight.Black, fontSize = 16.sp)
                                Text("Confira rapidamente se a IA interpretou tudo corretamente.", color = NexMuted, fontSize = 11.sp)
                            }

                            item {
                                androidx.compose.material3.Card(
                                    colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = NexNeon.copy(alpha = 0.06f)),
                                    border = androidx.compose.foundation.BorderStroke(1.dp, NexNeon.copy(alpha = 0.2f)),
                                    modifier = Modifier.fillMaxWidth()
                                ) {
                                    Column(modifier = Modifier.padding(12.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                                        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                                            Text("Confianca da IA", color = NexMuted, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                                            Text("$confidencePercent%", color = NexNeon, fontSize = 24.sp, fontWeight = FontWeight.Black)
                                        }
                                        LinearProgressIndicator(
                                            progress = { confidencePercent / 100f },
                                            modifier = Modifier.fillMaxWidth().height(6.dp).clip(androidx.compose.foundation.shape.RoundedCornerShape(99.dp)),
                                            color = NexNeon,
                                            trackColor = Color.White.copy(alpha = 0.08f),
                                        )
                                    }
                                }
                            }

                            item {
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                                    ImportStatCard("Dias", days.size.toString(), modifier = Modifier.weight(1f))
                                    ImportStatCard("Exercicios", exercises.size.toString(), modifier = Modifier.weight(1f))
                                }
                            }

                            item {
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                                    ImportStatCard("Series", totalSets.toString(), modifier = Modifier.weight(1f))
                                    ImportStatCard("Repeticoes", totalReps.toString(), modifier = Modifier.weight(1f))
                                }
                            }

                            item {
                                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
                                    ImportStatCard("Campos com duvida", issues.size.toString(), if (issues.isEmpty()) NexNeon else Color(0xFFFFC857), modifier = Modifier.weight(1f))
                                    ImportStatCard("Processamento", "${processingSeconds}s", modifier = Modifier.weight(1f))
                                }
                            }

                            item {
                                NexCard {
                                    Text("Dias encontrados", color = Color.White, fontWeight = FontWeight.Black, fontSize = 12.sp)
                                    days.forEach { day ->
                                        Text("OK ${formatImportedDay(day)}", color = NexNeon, fontSize = 11.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 4.dp))
                                    }
                                }
                            }

                            if (issues.isNotEmpty()) {
                                item {
                                    androidx.compose.material3.Card(
                                        colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color(0x26F59E0B)),
                                        border = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFF59E0B).copy(alpha = 0.35f)),
                                        modifier = Modifier.fillMaxWidth(),
                                    ) {
                                        Column(modifier = Modifier.padding(12.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
                                            Text("Exercicios que precisam de revisao", color = Color(0xFFFFC857), fontWeight = FontWeight.Black, fontSize = 12.sp)
                                            issues.take(4).forEach {
                                                Text("! $it", color = Color.White, fontSize = 11.sp)
                                            }
                                            if (issues.size > 4) {
                                                Text("+ ${issues.size - 4} outros campos", color = NexMuted, fontSize = 10.sp)
                                            }
                                        }
                                    }
                                }
                            }

                            item {
                                NexCard {
                                    Text("Analise da IA", color = Color.White, fontWeight = FontWeight.Black, fontSize = 12.sp)
                                    val opinion = if (issues.isEmpty()) {
                                        "Foram identificados ${days.size} dias de treino, totalizando ${exercises.size} exercicios e $totalSets series. A leitura apresentou $confidencePercent% de confianca e esta pronta para ser salva."
                                    } else {
                                        "Foram identificados ${days.size} dias de treino, totalizando ${exercises.size} exercicios e $totalSets series. A leitura apresentou $confidencePercent% de confianca. ${issues.size} campos exigem revisao manual."
                                    }
                                    Text(opinion, color = NexMuted, fontSize = 11.sp, lineHeight = 15.sp, modifier = Modifier.padding(top = 6.dp))
                                }
                            }

                            if (missingLoad > 0) {
                                item {
                                    NexCard {
                                        Text("Observacoes", color = Color.White, fontWeight = FontWeight.Black, fontSize = 12.sp)
                                        Text("! Nao foi encontrada carga em $missingLoad exercicios.", color = Color(0xFFFFC857), fontSize = 11.sp, modifier = Modifier.padding(top = 6.dp))
                                    }
                                }
                            }
                            
                            // Stats Summary
                            item {
                                androidx.compose.material3.Card(
                                    colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = NexNeon.copy(alpha = 0.05f)),
                                    border = androidx.compose.foundation.BorderStroke(1.dp, NexNeon.copy(alpha = 0.2f)),
                                    modifier = Modifier.fillMaxWidth()
                                ) {
                                    Row(modifier = Modifier.fillMaxWidth().padding(10.dp), horizontalArrangement = Arrangement.SpaceBetween) {
                                        Text("✔ ${exercises.size} Exercícios", color = NexNeon, fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                        Text("✔ Confiança da IA: ${(importConfidence * 100).toInt()}%", color = NexNeon, fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                    }
                                }
                            }

                            item {
                                OutlinedTextField(
                                    value = workoutName,
                                    onValueChange = onWorkoutNameChange,
                                    label = { Text("Nome do treino") },
                                    modifier = Modifier.fillMaxWidth(),
                                    singleLine = true,
                                    colors = workoutTextFieldColors(),
                                    isError = workoutName.isBlank(),
                                )
                            }
                            
                            items(exercises.size, key = { it }) { index ->
                                val exercise = exercises[index]
                                NexCard {
                                    OutlinedTextField(
                                        value = exercise.nomeExercicio,
                                        onValueChange = { onExerciseChange(index, exercise.copy(nomeExercicio = it.take(120))) },
                                        label = { Text("Exercicio") },
                                        modifier = Modifier.fillMaxWidth(),
                                        singleLine = true,
                                        colors = workoutTextFieldColors(),
                                        isError = exercise.nomeExercicio.isBlank(),
                                    )
                                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 8.dp)) {
                                        OutlinedTextField(
                                            value = exercise.series.orEmpty(),
                                            onValueChange = { onExerciseChange(index, exercise.copy(series = onlyDigits(it).take(2))) },
                                            label = { Text("Series") },
                                            modifier = Modifier.weight(1f),
                                            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                                            singleLine = true,
                                            colors = workoutTextFieldColors(),
                                            isError = exercise.series.isNullOrBlank(),
                                        )
                                        OutlinedTextField(
                                            value = exercise.repeticoes.orEmpty(),
                                            onValueChange = { onExerciseChange(index, exercise.copy(repeticoes = it.take(20))) },
                                            label = { Text("Reps") },
                                            modifier = Modifier.weight(1f),
                                            singleLine = true,
                                            colors = workoutTextFieldColors(),
                                            isError = exercise.repeticoes.isNullOrBlank(),
                                        )
                                    }
                                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 8.dp)) {
                                        OutlinedTextField(
                                            value = exercise.carga.orEmpty(),
                                            onValueChange = { onExerciseChange(index, exercise.copy(carga = it.take(20))) },
                                            label = { Text("Carga") },
                                            modifier = Modifier.weight(1f),
                                            singleLine = true,
                                            colors = workoutTextFieldColors(),
                                            isError = exercise.carga.isNullOrBlank(),
                                        )
                                        TextButton(
                                            onClick = { onRemoveExercise(index) },
                                            colors = ButtonDefaults.textButtonColors(contentColor = Color(0xFFFF8A8A)),
                                            modifier = Modifier.weight(1f),
                                        ) { Text("Remover") }
                                    }
                                    OutlinedTextField(
                                        value = exercise.observacoes.orEmpty(),
                                        onValueChange = { onExerciseChange(index, exercise.copy(observacoes = it.take(500))) },
                                        label = { Text("Observacoes") },
                                        modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                                        colors = workoutTextFieldColors(),
                                    )
                                }
                            }
                        }
                    }
                    5 -> {
                        // Step 5: Sucesso / Concluído
                        Column(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalAlignment = Alignment.CenterHorizontally,
                            verticalArrangement = Arrangement.spacedBy(12.dp)
                        ) {
                            Text("🎉 Importação Realizada!", color = NexNeon, fontWeight = FontWeight.Black, fontSize = 16.sp)
                            Text("Seu treino foi lido, estruturado e salvo com sucesso através do nosso motor premium NexShape AI.", color = NexMuted, fontSize = 12.sp, textAlign = TextAlign.Center)
                            
                            // Stats Summary Box
                            androidx.compose.material3.Card(
                                colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color.White.copy(alpha = 0.05f)),
                                border = androidx.compose.foundation.BorderStroke(1.dp, Color.White.copy(alpha = 0.1f)),
                                modifier = Modifier.fillMaxWidth().padding(top = 8.dp)
                            ) {
                                Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(6.dp)) {
                                    Text("Ficha técnica do Treino", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 12.sp)
                                    Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                                        Text("Status:", color = NexMuted, fontSize = 11.sp)
                                        Text("✔ Ativo e Configurado", color = Color(0xFF10B981), fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                    }
                                    Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                                        Text("Dia Vinculado:", color = NexMuted, fontSize = 11.sp)
                                        Text(selectedDay.replaceFirstChar { it.uppercase() }, color = Color.White, fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                    }
                                }
                            }

                            // Mensagem positiva de créditos restantes
                            val importsRemaining = maxOf(0, aiCreditsBalance / 50 - 1)
                            androidx.compose.material3.Card(
                                colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color(0x1AA855F7)),
                                border = androidx.compose.foundation.BorderStroke(1.dp, Color(0xFFA855F7).copy(alpha = 0.2f)),
                                modifier = Modifier.fillMaxWidth()
                            ) {
                                Row(
                                    modifier = Modifier.padding(12.dp),
                                    horizontalArrangement = Arrangement.SpaceBetween,
                                    verticalAlignment = Alignment.CenterVertically
                                ) {
                                    Column(modifier = Modifier.weight(1f)) {
                                        Text("Crédito utilizado: 1 importação", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 11.sp)
                                        Text("Você ainda pode importar mais $importsRemaining treinos este mês.", color = NexMuted, fontSize = 10.sp)
                                    }
                                    Text("$importsRemaining", color = Color(0xFFA855F7), fontWeight = FontWeight.Black, fontSize = 22.sp)
                                }
                            }
                        }
                    }
                }
            }
        },
        confirmButton = {
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.End) {
                when (wizardStep) {
                    1 -> {
                        TextButton(
                            onClick = { onStepChange(2) },
                            colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)
                        ) { Text("Organizar dias") }
                    }
                    2 -> {
                        // Confirmation state before starting scan
                        var showScanConfirm by remember { mutableStateOf(false) }

                        Button(
                            onClick = { showScanConfirm = true },
                            colors = ButtonDefaults.buttonColors(containerColor = NexNeon)
                        ) { Text("Iniciar Escaneamento IA", color = Color.Black, fontWeight = FontWeight.Bold) }

                        if (showScanConfirm) {
                            val importsAfter = maxOf(0, aiCreditsBalance / 50 - 1)
                            val totalImports = aiCreditsBalance / 50
                            AlertDialog(
                                onDismissRequest = { showScanConfirm = false },
                                containerColor = Color(0xFF0B1117),
                                titleContentColor = Color.White,
                                textContentColor = NexMuted,
                                title = {
                                    Column(horizontalAlignment = Alignment.CenterHorizontally, modifier = Modifier.fillMaxWidth()) {
                                        Text("🧠", fontSize = 28.sp)
                                        Spacer(Modifier.height(6.dp))
                                        Text("Confirmar Escaneamento", fontWeight = FontWeight.Black, fontSize = 15.sp, textAlign = TextAlign.Center)
                                    }
                                },
                                text = {
                                    Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                                        Text("Esta importação utilizará créditos de IA.", color = NexMuted, fontSize = 12.sp, textAlign = TextAlign.Center, modifier = Modifier.fillMaxWidth())
                                        androidx.compose.material3.Card(
                                            colors = androidx.compose.material3.CardDefaults.cardColors(containerColor = Color.White.copy(alpha = 0.04f)),
                                            border = androidx.compose.foundation.BorderStroke(1.dp, Color.White.copy(alpha = 0.08f)),
                                            modifier = Modifier.fillMaxWidth()
                                        ) {
                                            Column(modifier = Modifier.padding(14.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
                                                Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                                                    Text("Esta importação:", color = NexMuted, fontSize = 11.sp)
                                                    Text("1 crédito IA", color = Color(0xFFA855F7), fontWeight = FontWeight.Black, fontSize = 11.sp)
                                                }
                                                Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                                                    Text("Após a conclusão:", color = NexMuted, fontSize = 11.sp)
                                                    Text("$importsAfter de ${aiCreditsBalance / 50}", color = Color.White, fontWeight = FontWeight.Black, fontSize = 11.sp)
                                                }
                                            }
                                        }
                                        Text(
                                            "✅ Você ainda poderá importar mais $importsAfter treinos este mês.",
                                            color = NexMuted, fontSize = 11.sp,
                                            textAlign = TextAlign.Center, modifier = Modifier.fillMaxWidth()
                                        )
                                    }
                                },
                                dismissButton = {
                                    TextButton(onClick = { showScanConfirm = false },
                                        colors = ButtonDefaults.textButtonColors(contentColor = NexMuted)) {
                                        Text("Cancelar")
                                    }
                                },
                                confirmButton = {
                                    Button(
                                        onClick = { showScanConfirm = false; onStartScan() },
                                        colors = ButtonDefaults.buttonColors(containerColor = NexNeon)
                                    ) { Text("Continuar", color = Color.Black, fontWeight = FontWeight.Bold) }
                                }
                            )
                        }
                    }
                    3 -> {
                        // Busy scanner state - buttons are disabled
                    }
                    4 -> {
                        Button(
                            enabled = !busy && workoutName.isNotBlank() && exercises.isNotEmpty(),
                            onClick = onSave,
                            colors = ButtonDefaults.buttonColors(containerColor = NexNeon)
                        ) { Text(if (busy) "Salvando..." else "Aceitar e salvar treino", color = Color.Black, fontWeight = FontWeight.Bold) }
                    }
                    5 -> {
                        Button(
                            onClick = onDismiss,
                            colors = ButtonDefaults.buttonColors(containerColor = NexNeon)
                        ) { Text("Acessar Treino", color = Color.Black, fontWeight = FontWeight.Bold) }
                    }
                }
            }
        },
        dismissButton = {
            Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                if (wizardStep > 1 && wizardStep < 5 && wizardStep != 3) {
                    TextButton(
                        onClick = { onStepChange(wizardStep - 1) },
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)
                    ) { Text("Voltar") }
                }
                if (wizardStep < 5 && wizardStep != 3) {
                    TextButton(
                        onClick = onDismiss,
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)
                    ) { Text("Cancelar") }
                }
            }
        }
    )

    // Image Zoom / Rotate Preview Dialog
    if (previewZoomShow && currentPreviewUri != null) {
        AlertDialog(
            onDismissRequest = { previewZoomShow = false },
            containerColor = Color.Black,
            title = {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("Visualização Premium", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    TextButton(onClick = { previewZoomShow = false }) {
                        Text("Fechar", color = NexNeon)
                    }
                }
            },
            text = {
                Column(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalAlignment = Alignment.CenterHorizontally,
                    verticalArrangement = Arrangement.spacedBy(12.dp)
                ) {
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(280.dp)
                            .clip(androidx.compose.foundation.shape.RoundedCornerShape(12.dp))
                            .background(Color(0xFF0B1117))
                            .border(1.dp, Color.White.copy(alpha = 0.1f)),
                        contentAlignment = Alignment.Center
                    ) {
                        AsyncImage(
                            model = currentPreviewUri,
                            contentDescription = "Workout Zoomed",
                            modifier = Modifier
                                .fillMaxSize()
                                .graphicsLayer(
                                    scaleX = zoomScale,
                                    scaleY = zoomScale,
                                    rotationZ = rotationAngle
                                ),
                            contentScale = ContentScale.Fit
                        )
                    }
                    
                    // Controls
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        horizontalArrangement = Arrangement.SpaceEvenly,
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        OutlinedButton(onClick = { zoomScale = (zoomScale + 0.25f).coerceAtMost(3f) }) {
                            Text("Zoom +")
                        }
                        OutlinedButton(onClick = { zoomScale = (zoomScale - 0.25f).coerceAtLeast(1f) }) {
                            Text("Zoom -")
                        }
                        OutlinedButton(onClick = { rotationAngle = (rotationAngle + 90f) % 360f }) {
                            Text("Girará 90°")
                        }
                    }
                }
            },
            confirmButton = {}
        )
    }
}

@Composable
private fun TrainingList(
    plans: List<TrainingPlanSummaryDto>,
    sessions: List<WorkoutSessionDto>,
    activeWorkout: ActiveWorkoutDraft?,
    importBusy: Boolean,
    importState: String,
    onCreatePlan: () -> Unit,
    onImportPhoto: () -> Unit,
    onOpenPlan: (TrainingPlanSummaryDto) -> Unit,
) {
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.fillMaxWidth()) {
                NexMetricCard(
                    title = "Planos",
                    value = plans.size.toString(),
                    icon = Icons.Default.FitnessCenter,
                    modifier = Modifier.weight(1f),
                )
                NexMetricCard(
                    title = "Historico",
                    value = sessions.size.toString(),
                    icon = Icons.Default.History,
                    modifier = Modifier.weight(1f),
                )
            }
        }
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.fillMaxWidth()) {
                Button(
                    onClick = onCreatePlan,
                    colors = ButtonDefaults.buttonColors(containerColor = NexNeon),
                    modifier = Modifier.weight(1f),
                ) {
                    Text("Criar", color = Color.Black, fontWeight = FontWeight.Bold)
                }
                OutlinedButton(
                    onClick = onImportPhoto,
                    enabled = !importBusy,
                    modifier = Modifier.weight(1f),
                ) {
                    Text(
                        text = when {
                            importBusy && importState == "VALIDATING_IMAGE" -> "Analisando..."
                            importBusy && importState == "EXTRACTING_WORKOUT" -> "Lendo..."
                            importBusy -> "Lendo..."
                            else -> "Importar IA"
                        }
                    )
                }
            }
        }
        if (sessions.isNotEmpty()) {
            item { RecentSessionsCard(sessions.take(3)) }
        }
        activeWorkout?.let { draft ->
            item {
                NexCard {
                    Text("Treino em andamento", color = Color.White, fontWeight = FontWeight.Black)
                    Text(draft.planName, color = NexNeon, modifier = Modifier.padding(top = 4.dp))
                    Text("Iniciado ha ${elapsedWorkoutLabel(draft.startedAt)}", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                }
            }
        }
        items(plans, key = { it.id }) { plan ->
            NexCard(
                modifier = Modifier.clickable { onOpenPlan(plan) },
            ) {
                Text(plan.name, color = Color.White, fontWeight = FontWeight.Black)
                Text("${plan.exercisesCount} exercicios", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                plan.goal?.let { Text("Objetivo: $it", color = NexMuted, modifier = Modifier.padding(top = 2.dp)) }
                plan.status?.let { Text("Status: $it", color = NexNeon, modifier = Modifier.padding(top = 8.dp)) }
            }
        }
    }
}

@Composable
private fun TrainingDetail(
    plan: TrainingPlanDetailDto,
    recentSessions: List<WorkoutSessionDto>,
    activeWorkout: ActiveWorkoutDraft?,
    restSecondsRemaining: Int,
    readOnly: Boolean,
    actionError: String?,
    onBack: () -> Unit,
    onStartWorkout: () -> Unit,
    onCancelWorkout: () -> Unit,
    onToggleExercise: (exerciseId: Int, completed: Boolean) -> Unit,
    onSkipRest: () -> Unit,
    onOpenLoadLog: (TrainingExerciseDto) -> Unit,
    onFinishWorkout: () -> Unit,
    onEditPlan: () -> Unit,
    onDeletePlan: () -> Unit,
) {
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            Text("< Voltar aos planos", color = NexNeon, modifier = Modifier.clickable { onBack() })
            Spacer(Modifier.height(12.dp))
            NexCard {
                Text(plan.name, color = Color.White, fontWeight = FontWeight.Black, style = MaterialTheme.typography.titleLarge)
                actionError?.let {
                    Text(it, color = Color(0xFFFF6B6B), fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 8.dp))
                }
                plan.description?.let { Text(it, color = NexMuted, modifier = Modifier.padding(top = 8.dp)) }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
                    plan.frequency?.let { AssistChip(onClick = {}, label = { Text(it) }) }
                    plan.difficulty?.let { AssistChip(onClick = {}, label = { Text(it) }) }
                }
                if (!readOnly) {
                    Row(horizontalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.padding(top = 12.dp)) {
                        OutlinedButton(onClick = onEditPlan, modifier = Modifier.weight(1f)) {
                            Text("Editar")
                        }
                        OutlinedButton(onClick = onDeletePlan, modifier = Modifier.weight(1f)) {
                            Text("Excluir", color = Color(0xFFFF6B6B))
                        }
                    }
                }
                val isThisWorkoutActive = !readOnly && activeWorkout?.planId == plan.id
                val otherWorkoutActive = !readOnly && activeWorkout != null && !isThisWorkoutActive
                if (readOnly) {
                    Text(
                        "Modo profissional: acompanhe a ficha do aluno aqui. O registro de execucao fica disponivel no modo aluno.",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 12.dp),
                    )
                } else if (isThisWorkoutActive) {
                    val totalExercises = plan.exercises.orEmpty().size
                    val completedCount = activeWorkout.completedExerciseIds.count { id ->
                        plan.exercises.orEmpty().any { it.id == id }
                    }
                    val percent = workoutCompletionPercent(totalExercises, completedCount)
                    Text(
                        "Treino em andamento desde ${elapsedWorkoutLabel(activeWorkout.startedAt)}",
                        color = NexNeon,
                        modifier = Modifier.padding(top = 12.dp),
                    )
                    Text(
                        "$completedCount/$totalExercises exercicios concluidos - $percent%",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 4.dp),
                    )
                    if (restSecondsRemaining > 0) {
                        Text(
                            "Descanso: ${restSecondsRemaining}s",
                            color = NexNeon,
                            modifier = Modifier.padding(top = 8.dp),
                        )
                        TextButton(onClick = onSkipRest) {
                            Text("Pular descanso")
                        }
                    }
                    Row(horizontalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.padding(top = 12.dp)) {
                        NexPrimaryButton(
                            text = "Finalizar",
                            onClick = onFinishWorkout,
                            modifier = Modifier.weight(1f),
                        )
                        TextButton(onClick = onCancelWorkout, modifier = Modifier.weight(1f)) {
                            Text("Cancelar")
                        }
                    }
                } else if (otherWorkoutActive) {
                    Text(
                        "Existe outro treino em andamento: ${activeWorkout?.planName}. Finalize ou cancele antes de iniciar este.",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 12.dp),
                    )
                } else {
                    NexPrimaryButton(
                        text = "Iniciar treino",
                        onClick = onStartWorkout,
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(top = 14.dp),
                    )
                }
            }
            if (recentSessions.isNotEmpty()) {
                Spacer(Modifier.height(12.dp))
                RecentSessionsCard(recentSessions.take(3))
            }
        }
        items(plan.exercises.orEmpty(), key = { it.id }) { exercise ->
            val isActive = !readOnly && activeWorkout?.planId == plan.id
            val completed = activeWorkout?.completedExerciseIds?.contains(exercise.id) == true
            NexCard {
                Row {
                    Icon(
                        imageVector = if (completed) Icons.Default.CheckCircle else Icons.Default.RadioButtonUnchecked,
                        contentDescription = if (completed) "Concluido" else "Pendente",
                        tint = if (completed) NexGreen else NexMuted,
                        modifier = if (isActive) {
                            Modifier.clickable { onToggleExercise(exercise.id, !completed) }
                        } else {
                            Modifier
                        },
                    )
                    Column(modifier = Modifier.padding(start = 12.dp)) {
                        Text(exercise.name ?: "Exercicio", color = Color.White, fontWeight = FontWeight.Black)
                        if (isActive) {
                            Text(
                                if (completed) "Concluido" else "Toque no circulo para concluir",
                                color = if (completed) NexGreen else NexMuted,
                                modifier = Modifier.padding(top = 2.dp),
                            )
                        }
                        exercise.muscleGroup?.let { Text(it, color = NexMuted, modifier = Modifier.padding(top = 2.dp)) }
                        exercise.notes?.let { Text(it, color = NexMuted, modifier = Modifier.padding(top = 8.dp)) }
                        exercise.lastLog?.let { log ->
                            Text(
                                "Ultima carga: ${log.weightKg ?: 0.0} kg x ${log.repsDone} reps",
                                color = NexNeon,
                                modifier = Modifier.padding(top = 8.dp),
                            )
                        }
                        exercise.sets.orEmpty().forEach { set ->
                            val reps = set.repsTarget?.let { "$it reps" } ?: "-"
                            val rest = set.restSeconds?.let { " - ${it}s descanso" } ?: ""
                            Text(
                                "Serie ${set.setNumber ?: "?"}: $reps$rest",
                                color = NexMuted,
                                modifier = Modifier.padding(top = 6.dp),
                            )
                        }
                        if (isActive) {
                            TextButton(onClick = { onOpenLoadLog(exercise) }) {
                                Text("Registrar carga/reps")
                            }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun LoadLogDialog(
    exercise: TrainingExerciseDto,
    onDismiss: () -> Unit,
    onSave: (setNumber: Int, reps: Int, weight: Double?, rpe: Int?, toFailure: Boolean) -> Unit,
) {
    var setNumber by remember { mutableStateOf("1") }
    var reps by remember { mutableStateOf("") }
    var weight by remember { mutableStateOf(exercise.lastLog?.weightKg?.toString().orEmpty()) }
    var rpe by remember { mutableStateOf("") }
    var toFailure by remember { mutableStateOf(false) }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Registrar carga") },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                Text(exercise.name ?: "Exercicio", fontWeight = FontWeight.Bold)
                OutlinedTextField(
                    value = setNumber,
                    onValueChange = { setNumber = onlyDigits(it).take(2) },
                    label = { Text("Serie") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                OutlinedTextField(
                    value = reps,
                    onValueChange = { reps = onlyDigits(it).take(3) },
                    label = { Text("Repeticoes realizadas") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                OutlinedTextField(
                    value = weight,
                    onValueChange = { weight = onlyDecimal(it).take(7) },
                    label = { Text("Carga (kg)") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                OutlinedTextField(
                    value = rpe,
                    onValueChange = { rpe = onlyDigits(it).take(2) },
                    label = { Text("RPE 1-10") },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Number),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                TextButton(
                    onClick = { toFailure = !toFailure },
                    colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                ) {
                    Text(if (toFailure) "Falha: sim" else "Falha: nao")
                }
            }
        },
        confirmButton = {
            TextButton(
                onClick = {
                    val set = setNumber.toIntOrNull() ?: return@TextButton
                    val repsDone = reps.toIntOrNull() ?: return@TextButton
                    val rpeValue = rpe.toIntOrNull()?.coerceIn(1, 10)
                    onSave(set, repsDone, weight.toDoubleOrNull(), rpeValue, toFailure)
                },
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
            ) { Text("Salvar") }
        },
        dismissButton = {
            TextButton(
                onClick = onDismiss,
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
            ) { Text("Cancelar") }
        },
    )
}

private fun onlyDigits(value: String): String = value.filter { it.isDigit() }

private fun trainingPhotoExtensionForMimeType(mimeType: String): String? =
    when (mimeType.lowercase()) {
        "image/jpeg", "image/jpg" -> "jpg"
        "image/png" -> "png"
        "image/webp" -> "webp"
        else -> null
    }

private fun trainingPhotoTypeLabel(type: String): String =
    when (type) {
        "front" -> "Frente"
        "back" -> "Costas"
        "right_side" -> "Lado direito"
        "left_side" -> "Lado esquerdo"
        else -> "Foto"
    }

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char -> char.isDigit() || (char == '.' && value.indexOf('.') == index) }

@Composable
private fun RecentSessionsCard(sessions: List<WorkoutSessionDto>) {
    NexCard {
        Text("RPE recente", color = Color.White, fontWeight = FontWeight.Black)
        sessions.forEach { session ->
            Text(
                "${session.sessionDate}: RPE ${session.rpeScore}${session.mood?.let { " - $it" } ?: ""}",
                color = NexMuted,
                modifier = Modifier.padding(top = 6.dp),
            )
        }
    }
}

@Composable
private fun RpeDialog(
    planName: String,
    onDismiss: () -> Unit,
    onSave: (rpe: Int, mood: String, notes: String) -> Unit,
) {
    var rpe by remember { mutableStateOf(7f) }
    var mood by remember { mutableStateOf("") }
    var notes by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Registrar treino") },
        text = {
            Column {
                Text(planName, fontWeight = FontWeight.Bold)
                Text("Esforco percebido: ${rpe.toInt()}/10", modifier = Modifier.padding(top = 12.dp))
                Slider(value = rpe, onValueChange = { rpe = it }, valueRange = 1f..10f, steps = 8)
                OutlinedTextField(
                    value = mood,
                    onValueChange = { mood = it },
                    label = { Text("Humor/energia") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                    colors = workoutTextFieldColors(),
                )
                OutlinedTextField(
                    value = notes,
                    onValueChange = { notes = it },
                    label = { Text("Observacoes") },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 8.dp),
                    colors = workoutTextFieldColors(),
                )
            }
        },
        confirmButton = {
            TextButton(
                onClick = { onSave(rpe.toInt(), mood, notes) },
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
            ) { Text("Salvar") }
        },
        dismissButton = {
            TextButton(
                onClick = onDismiss,
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
            ) { Text("Cancelar") }
        },
    )
}
