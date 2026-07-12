package br.com.nexshape.academia.ui.training

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.CreateTrainingExerciseRequest
import br.com.nexshape.academia.data.api.CreateTrainingPlanRequest
import br.com.nexshape.academia.data.api.CreateTrainingSetRequest
import br.com.nexshape.academia.data.api.CreateLoadLogRequest
import br.com.nexshape.academia.data.api.ExerciseCatalogDto
import br.com.nexshape.academia.data.api.ExerciseSetDto
import br.com.nexshape.academia.data.api.ExerciseSyncRequest
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.TrainingExerciseDto
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.api.TrainingPlansResponse
import br.com.nexshape.academia.data.api.UpdateWorkoutSessionRequest
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
import java.time.LocalDate

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
    var showEditPlanDialog by remember { mutableStateOf(false) }
    var showDeletePlanDialog by remember { mutableStateOf(false) }
    var actionError by remember { mutableStateOf<String?>(null) }

    fun refreshPending() {
        scope.launch { pendingSync = AppDatabase.get(context).pendingSyncDao().pendingCount() }
    }

    fun reload() {
        scope.launch {
            loading = true
            error = null
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
            onSave = { request ->
                scope.launch {
                    repository.createPlan(request).onSuccess { plan ->
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

    if (showEditPlanDialog && selected != null) {
        CreateTrainingPlanDialog(
            onDismiss = { showEditPlanDialog = false },
            title = "Editar ficha de treino",
            initialPlan = selected,
            onSave = { request ->
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
    onSave: (request: CreateTrainingPlanRequest) -> Unit,
) {
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
    var selectedExercises by remember {
        mutableStateOf(initialPlan?.exercises.orEmpty().map { WorkoutBuilderExercise.fromPlanExercise(it) })
    }
    var validation by remember { mutableStateOf<String?>(null) }
    val days = listOf("Segunda", "Terca", "Quarta", "Quinta", "Sexta", "Sabado", "Domingo")
    var selectedDays by remember { mutableStateOf<List<String>>(emptyList()) }

    fun loadCatalog(search: String? = null) {
        scope.launch {
            loadingCatalog = true
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

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = {
            Column {
                Text(title)
                Text("Etapa $step de 5: ${workoutBuilderStepTitle(step)}", color = NexMuted, fontSize = 12.sp)
                LinearProgressIndicator(
                    progress = { step / 5f },
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
                    2 -> {
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
                                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                            ) {
                                Text(if (loadingCatalog) "Buscando..." else "Buscar no catalogo")
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
                    3 -> {
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
                    4 -> item {
                        BuilderReviewCard(
                            name = name,
                            goal = goal,
                            frequency = frequency.toIntOrNull(),
                            duration = estimatedDuration(),
                            totalVolume = totalVolume(),
                            exercises = selectedExercises,
                        )
                    }
                    5 -> item {
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
                enabled = name.isNotBlank(),
                colors = ButtonDefaults.textButtonColors(
                    contentColor = NexNeon,
                    disabledContentColor = Color(0xFF7D8594),
                ),
                onClick = {
                    validation = null
                    if (step < 5) {
                        if (step == 1 && name.isBlank()) {
                            validation = "Informe o titulo do treino."
                            return@TextButton
                        }
                        if (step == 2 && selectedExercises.isEmpty()) {
                            validation = "Adicione pelo menos um exercicio."
                            return@TextButton
                        }
                        step += 1
                        return@TextButton
                    }

                    if (selectedExercises.isEmpty()) {
                        validation = "Adicione exercicios antes de salvar."
                        step = 2
                        return@TextButton
                    }

                    onSave(
                        CreateTrainingPlanRequest(
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
                            musclesWorked = musclesWorked(),
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
                        ),
                    )
                },
            ) { Text(if (step < 5) "Continuar" else "Salvar") }
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
    1 -> "Dados"
    2 -> "Exercicios"
    3 -> "Series"
    4 -> "Revisao"
    else -> "Finalizar"
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
) {
    NexCard {
        Text("Ficha de revisao", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
        Text(name.ifBlank { "Sem titulo" }, color = NexNeon, modifier = Modifier.padding(top = 8.dp), fontWeight = FontWeight.Bold)
        Text("Objetivo: ${goal.ifBlank { "Geral" }}", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Text("Frequencia: ${frequency ?: 0}x/semana • Duracao: ${duration} min", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Text("Volume estimado: ${totalVolume.toInt()} kg", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Spacer(Modifier.height(12.dp))
        exercises.forEachIndexed { index, exercise ->
            Text("${index + 1}. ${exercise.name} • ${exercise.sets.size} series", color = Color.White, fontSize = 13.sp, modifier = Modifier.padding(top = 6.dp))
        }
    }
}

@Composable
private fun TrainingList(
    plans: List<TrainingPlanSummaryDto>,
    sessions: List<WorkoutSessionDto>,
    activeWorkout: ActiveWorkoutDraft?,
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
