package br.com.nexshape.academia.ui.training

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
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
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ExerciseSyncRequest
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.api.WorkoutSessionDto
import br.com.nexshape.academia.data.api.WorkoutSessionRequest
import br.com.nexshape.academia.data.local.AppDatabase
import br.com.nexshape.academia.data.repository.OfflineSyncRepository
import br.com.nexshape.academia.data.repository.TrainingRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

@Composable
fun TrainingScreen(modifier: Modifier = Modifier) {
    val context = LocalContext.current
    val repository = remember { TrainingRepository() }
    val offlineRepository = remember { OfflineSyncRepository(context) }
    val scope = rememberCoroutineScope()
    var plans by remember { mutableStateOf<List<TrainingPlanSummaryDto>>(emptyList()) }
    var selected by remember { mutableStateOf<TrainingPlanDetailDto?>(null) }
    var sessions by remember { mutableStateOf<List<WorkoutSessionDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var pendingSync by remember { mutableIntStateOf(0) }
    var showRpeDialog by remember { mutableStateOf(false) }

    fun refreshPending() {
        scope.launch {
            pendingSync = AppDatabase.get(context).pendingSyncDao().pendingCount()
        }
    }

    fun refreshSessions() {
        scope.launch {
            repository.sessions()
                .onSuccess { sessions = it }
                .onFailure { if (error == null) error = it.message }
        }
    }

    LaunchedEffect(Unit) {
        repository.listPlans()
            .onSuccess {
                plans = it
                loading = false
            }
            .onFailure {
                error = it.message
                loading = false
            }
        refreshPending()
        refreshSessions()
    }

    Column(
        modifier = modifier
            .fillMaxSize()
            .padding(16.dp),
    ) {
        Row(modifier = Modifier.fillMaxWidth()) {
            Text("Planos de treino", style = MaterialTheme.typography.headlineSmall, modifier = Modifier.weight(1f))
            if (pendingSync > 0) {
                Text("$pendingSync pendente(s)", color = MaterialTheme.colorScheme.error)
            }
        }

        if (pendingSync > 0) {
            Button(
                onClick = {
                    scope.launch {
                        offlineRepository.flush(context)
                        refreshPending()
                    }
                },
                modifier = Modifier.padding(top = 8.dp),
            ) {
                Text("Sincronizar offline")
            }
        }

        when {
            loading -> CircularProgressIndicator(modifier = Modifier.padding(top = 24.dp))
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            selected != null -> TrainingDetail(
                plan = selected!!,
                recentSessions = sessions,
                onBack = { selected = null },
                onLogSession = { showRpeDialog = true },
            )
            else -> TrainingList(
                plans = plans,
                sessions = sessions,
                onOpenPlan = { plan ->
                    scope.launch {
                        repository.planDetail(plan.id)
                            .onSuccess { selected = it }
                            .onFailure { error = it.message }
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
                    repository.saveSession(
                        WorkoutSessionRequest(
                            sessionDate = LocalDate.now().toString(),
                            rpeScore = rpe,
                            mood = mood.ifBlank { null },
                            notes = notes.ifBlank { null },
                        ),
                    ).onSuccess {
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
                        refreshPending()
                        refreshSessions()
                    }.onFailure { error = it.message }
                }
            },
        )
    }
}

@Composable
private fun TrainingList(
    plans: List<TrainingPlanSummaryDto>,
    sessions: List<WorkoutSessionDto>,
    onOpenPlan: (TrainingPlanSummaryDto) -> Unit,
) {
    LazyColumn(
        modifier = Modifier.padding(top = 12.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp),
    ) {
        if (sessions.isNotEmpty()) {
            item {
                RecentSessionsCard(sessions = sessions.take(3))
            }
        }

        items(plans, key = { it.id }) { plan ->
            Card(
                modifier = Modifier
                    .fillMaxWidth()
                    .clickable { onOpenPlan(plan) },
            ) {
                Column(Modifier.padding(16.dp)) {
                    Text(plan.name, style = MaterialTheme.typography.titleMedium)
                    Text("${plan.exercisesCount} exercicios")
                    plan.goal?.let { Text("Objetivo: $it", style = MaterialTheme.typography.bodySmall) }
                }
            }
        }
    }
}

@Composable
private fun TrainingDetail(
    plan: TrainingPlanDetailDto,
    recentSessions: List<WorkoutSessionDto>,
    onBack: () -> Unit,
    onLogSession: () -> Unit,
) {
    LazyColumn(
        verticalArrangement = Arrangement.spacedBy(8.dp),
    ) {
        item {
            Text("<- Voltar", modifier = Modifier.clickable { onBack() }.padding(bottom = 8.dp))
            Text(plan.name, style = MaterialTheme.typography.titleLarge)
            plan.description?.let { Text(it, modifier = Modifier.padding(vertical = 8.dp)) }
            plan.frequency?.let { Text("Frequencia: $it") }
            plan.difficulty?.let { Text("Dificuldade: $it") }

            Button(onClick = onLogSession, modifier = Modifier.padding(vertical = 12.dp)) {
                Text("Registrar treino e RPE")
            }

            if (recentSessions.isNotEmpty()) {
                RecentSessionsCard(sessions = recentSessions.take(3))
            }
        }

        items(plan.exercises.orEmpty(), key = { it.id }) { exercise ->
            Card(modifier = Modifier.fillMaxWidth()) {
                Column(Modifier.padding(12.dp)) {
                    Text(exercise.name ?: "Exercicio", style = MaterialTheme.typography.titleSmall)
                    exercise.muscleGroup?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                    exercise.sets.orEmpty().forEach { set ->
                        val reps = set.repsTarget?.let { "$it reps" } ?: "-"
                        val rest = set.restSeconds?.let { " - ${it}s descanso" } ?: ""
                        Text("Serie ${set.setNumber ?: "?"}: $reps$rest", style = MaterialTheme.typography.bodySmall)
                    }
                }
            }
        }
    }
}

@Composable
private fun RecentSessionsCard(sessions: List<WorkoutSessionDto>) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text("RPE recente", style = MaterialTheme.typography.titleMedium)
            sessions.forEach { session ->
                Text(
                    "${session.sessionDate}: RPE ${session.rpeScore}${session.mood?.let { " - $it" } ?: ""}",
                    style = MaterialTheme.typography.bodySmall,
                    modifier = Modifier.padding(top = 4.dp),
                )
            }
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
        title = { Text("Registrar treino") },
        text = {
            Column {
                Text(planName, style = MaterialTheme.typography.titleSmall)
                Text("Esforco percebido: ${rpe.toInt()}/10", modifier = Modifier.padding(top = 12.dp))
                Slider(
                    value = rpe,
                    onValueChange = { rpe = it },
                    valueRange = 1f..10f,
                    steps = 8,
                )
                OutlinedTextField(
                    value = mood,
                    onValueChange = { mood = it },
                    label = { Text("Humor/energia") },
                    modifier = Modifier.fillMaxWidth(),
                    singleLine = true,
                )
                OutlinedTextField(
                    value = notes,
                    onValueChange = { notes = it },
                    label = { Text("Observacoes") },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 8.dp),
                )
            }
        },
        confirmButton = {
            TextButton(onClick = { onSave(rpe.toInt(), mood, notes) }) {
                Text("Salvar")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar") }
        },
    )
}
