package br.com.nexshape.academia.ui.training

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
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
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var pendingSync by remember { mutableIntStateOf(0) }

    fun refreshPending() {
        scope.launch {
            pendingSync = AppDatabase.get(context).pendingSyncDao().pendingCount()
        }
    }

    LaunchedEffect(Unit) {
        repository.listPlans()
            .onSuccess { plans = it; loading = false }
            .onFailure { error = it.message; loading = false }
        refreshPending()
    }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
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
                onBack = { selected = null },
                onLogSession = {
                    scope.launch {
                        offlineRepository.queueExerciseSync(
                            context,
                            ExerciseSyncRequest(
                                entryDate = LocalDate.now().toString(),
                                activityType = selected!!.name,
                                durationMin = 45,
                                notes = "Treino registrado pelo app",
                            ),
                        )
                        refreshPending()
                    }
                },
            )
            else -> LazyColumn(modifier = Modifier.padding(top = 12.dp)) {
                items(plans) { plan ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 6.dp)
                            .clickable {
                                scope.launch {
                                    repository.planDetail(plan.id)
                                        .onSuccess { selected = it }
                                        .onFailure { error = it.message }
                                }
                            },
                    ) {
                        Column(Modifier.padding(16.dp)) {
                            Text(plan.name, style = MaterialTheme.typography.titleMedium)
                            Text("${plan.exercisesCount} exercícios")
                            plan.goal?.let { Text("Objetivo: $it", style = MaterialTheme.typography.bodySmall) }
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun TrainingDetail(
    plan: TrainingPlanDetailDto,
    onBack: () -> Unit,
    onLogSession: () -> Unit,
) {
    Column {
        Text("← Voltar", modifier = Modifier.clickable { onBack() }.padding(bottom = 8.dp))
        Text(plan.name, style = MaterialTheme.typography.titleLarge)
        plan.description?.let { Text(it, modifier = Modifier.padding(vertical = 8.dp)) }
        plan.frequency?.let { Text("Frequência: $it") }
        plan.difficulty?.let { Text("Dificuldade: $it") }

        Button(onClick = onLogSession, modifier = Modifier.padding(vertical = 12.dp)) {
            Text("Registrar treino de hoje")
        }

        plan.exercises.orEmpty().forEach { exercise ->
            Card(modifier = Modifier.fillMaxWidth().padding(vertical = 6.dp)) {
                Column(Modifier.padding(12.dp)) {
                    Text(exercise.name ?: "Exercício", style = MaterialTheme.typography.titleSmall)
                    exercise.muscleGroup?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                    exercise.sets.orEmpty().forEach { set ->
                        val reps = set.repsTarget?.let { "$it reps" } ?: "—"
                        val rest = set.restSeconds?.let { " · ${it}s descanso" } ?: ""
                        Text("Série ${set.setNumber ?: "?"}: $reps$rest", style = MaterialTheme.typography.bodySmall)
                    }
                }
            }
        }
    }
}
