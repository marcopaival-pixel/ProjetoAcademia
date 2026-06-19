package br.com.nexshape.academia.ui.professional

import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Tab
import androidx.compose.material3.TabRow
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
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.BodyAssessmentDto
import br.com.nexshape.academia.data.api.ClinicProtocolDto
import br.com.nexshape.academia.data.api.CreatePatientAssessmentRequest
import br.com.nexshape.academia.data.api.CreatePatientTrainingPlanRequest
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Column
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File
import br.com.nexshape.academia.data.api.EvolutionPhotoDto
import br.com.nexshape.academia.data.media.AuthenticatedImageLoader
import coil.compose.AsyncImage
import coil.request.ImageRequest
import br.com.nexshape.academia.data.api.TrainingPlanDetailDto
import br.com.nexshape.academia.data.api.TrainingPlanSummaryDto
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

@Composable
fun ProPatientCareScreen(modifier: Modifier = Modifier) {
    val session = remember { ApiClient.sessionPreferences() }
    val patientId = session.getActivePatientId()
    val patientName = session.getActivePatientName() ?: "Aluno #$patientId"
    var tab by remember { mutableIntStateOf(0) }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Acompanhamento", style = MaterialTheme.typography.headlineSmall)
        Text(patientName, style = MaterialTheme.typography.titleMedium, modifier = Modifier.padding(vertical = 8.dp))

        if (patientId == null) {
            Text("Selecione um aluno ativo na aba Alunos.")
            return@Column
        }

        TabRow(selectedTabIndex = tab) {
            Tab(selected = tab == 0, onClick = { tab = 0 }, text = { Text("Treinos") })
            Tab(selected = tab == 1, onClick = { tab = 1 }, text = { Text("Avaliações") })
            Tab(selected = tab == 2, onClick = { tab = 2 }, text = { Text("Fotos") })
        }

        when (tab) {
            0 -> ProPatientTrainingTab(patientId = patientId)
            1 -> ProPatientAssessmentTab(patientId = patientId)
            2 -> ProPatientPhotosTab(patientId = patientId)
        }
    }
}

@Composable
private fun ProPatientTrainingTab(patientId: Int) {
    val repository = remember { ProfessionalRepository() }
    val scope = rememberCoroutineScope()
    var plans by remember { mutableStateOf<List<TrainingPlanSummaryDto>>(emptyList()) }
    var protocols by remember { mutableStateOf<List<ClinicProtocolDto>>(emptyList()) }
    var selected by remember { mutableStateOf<TrainingPlanDetailDto?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showCreate by remember { mutableStateOf(false) }

    fun reload() {
        scope.launch {
            loading = true
            repository.patientTrainingPlans(patientId)
                .onSuccess { plans = it; error = null }
                .onFailure { error = it.message }
            repository.protocols()
                .onSuccess { protocols = it }
            loading = false
        }
    }

    LaunchedEffect(patientId) { reload() }

    Column(modifier = Modifier.padding(top = 12.dp)) {
        Button(onClick = { showCreate = true }, modifier = Modifier.padding(bottom = 8.dp)) {
            Text("Prescrever treino")
        }

        when {
            loading -> CircularProgressIndicator()
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            selected != null -> {
                Text("← Voltar", modifier = Modifier.clickable { selected = null }.padding(bottom = 8.dp))
                Text(selected!!.name, style = MaterialTheme.typography.titleLarge)
                selected!!.description?.let { Text(it) }
                selected!!.exercises.orEmpty().forEach { ex ->
                    Text("• ${ex.name ?: "Exercício"}", style = MaterialTheme.typography.bodySmall)
                }
            }
            else -> LazyColumn {
                items(plans) { plan ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 4.dp)
                            .clickable {
                                scope.launch {
                                    repository.patientTrainingPlanDetail(patientId, plan.id)
                                        .onSuccess { selected = it }
                                }
                            },
                    ) {
                        Column(Modifier.padding(12.dp)) {
                            Text(plan.name, style = MaterialTheme.typography.titleSmall)
                            Text("${plan.exercisesCount} exercícios", style = MaterialTheme.typography.bodySmall)
                        }
                    }
                }
            }
        }
    }

    if (showCreate) {
        var name by remember { mutableStateOf("") }
        var goal by remember { mutableStateOf("") }
        var selectedProtocol by remember { mutableStateOf<ClinicProtocolDto?>(null) }

        AlertDialog(
            onDismissRequest = { showCreate = false },
            title = { Text("Prescrição rápida") },
            text = {
                Column {
                    OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("Nome do plano") })
                    OutlinedTextField(
                        value = goal,
                        onValueChange = { goal = it },
                        label = { Text("Objetivo") },
                        modifier = Modifier.padding(top = 8.dp),
                    )
                    if (protocols.isNotEmpty()) {
                        Text("Ou aplicar protocolo:", modifier = Modifier.padding(top = 12.dp))
                        protocols.forEach { protocol ->
                            Text(
                                protocol.name,
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .clickable { selectedProtocol = protocol }
                                    .padding(vertical = 4.dp),
                                color = if (selectedProtocol?.id == protocol.id) {
                                    MaterialTheme.colorScheme.primary
                                } else {
                                    MaterialTheme.colorScheme.onSurface
                                },
                            )
                        }
                    }
                }
            },
            confirmButton = {
                TextButton(
                    onClick = {
                        scope.launch {
                            val request = if (selectedProtocol != null) {
                                CreatePatientTrainingPlanRequest(
                                    name = selectedProtocol!!.name,
                                    protocolId = selectedProtocol!!.id,
                                )
                            } else {
                                CreatePatientTrainingPlanRequest(
                                    name = name,
                                    goal = goal.ifBlank { null },
                                )
                            }
                            repository.createPatientTrainingPlan(patientId, request)
                                .onSuccess {
                                    showCreate = false
                                    reload()
                                }
                                .onFailure { error = it.message }
                        }
                    },
                ) { Text("Salvar") }
            },
            dismissButton = {
                TextButton(onClick = { showCreate = false }) { Text("Cancelar") }
            },
        )
    }
}

@Composable
private fun ProPatientAssessmentTab(patientId: Int) {
    val repository = remember { ProfessionalRepository() }
    val scope = rememberCoroutineScope()
    var assessments by remember { mutableStateOf<List<BodyAssessmentDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showCreate by remember { mutableStateOf(false) }
    var weight by remember { mutableStateOf("") }
    var bf by remember { mutableStateOf("") }
    var notes by remember { mutableStateOf("") }

    fun reload() {
        scope.launch {
            loading = true
            repository.patientAssessments(patientId)
                .onSuccess { assessments = it; error = null; loading = false }
                .onFailure { error = it.message; loading = false }
        }
    }

    LaunchedEffect(patientId) { reload() }

    Column(modifier = Modifier.padding(top = 12.dp)) {
        Button(onClick = { showCreate = true }, modifier = Modifier.padding(bottom = 8.dp)) {
            Text("Nova avaliação")
        }

        when {
            loading -> CircularProgressIndicator()
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            assessments.isEmpty() -> Text("Nenhuma avaliação registrada.")
            else -> LazyColumn {
                items(assessments) { item ->
                    Card(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
                        Column(Modifier.padding(12.dp)) {
                            Text(item.assessmentDate ?: "—", style = MaterialTheme.typography.titleSmall)
                            item.weightKg?.let { Text("Peso: $it kg") }
                            item.bfPercent?.let { Text("BF: $it%") }
                            item.notes?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                        }
                    }
                }
            }
        }
    }

    if (showCreate) {
        AlertDialog(
            onDismissRequest = { showCreate = false },
            title = { Text("Registrar avaliação") },
            text = {
                Column {
                    OutlinedTextField(value = weight, onValueChange = { weight = it }, label = { Text("Peso (kg)") })
                    OutlinedTextField(
                        value = bf,
                        onValueChange = { bf = it },
                        label = { Text("Gordura (%)") },
                        modifier = Modifier.padding(top = 8.dp),
                    )
                    OutlinedTextField(
                        value = notes,
                        onValueChange = { notes = it },
                        label = { Text("Observações") },
                        modifier = Modifier.padding(top = 8.dp),
                    )
                }
            },
            confirmButton = {
                TextButton(
                    onClick = {
                        scope.launch {
                            repository.createPatientAssessment(
                                patientId,
                                CreatePatientAssessmentRequest(
                                    assessmentDate = LocalDate.now().toString(),
                                    weightKg = weight.toDoubleOrNull(),
                                    bfPercent = bf.toDoubleOrNull(),
                                    notes = notes.ifBlank { null },
                                ),
                            )
                                .onSuccess {
                                    showCreate = false
                                    reload()
                                }
                                .onFailure { error = it.message }
                        }
                    },
                ) { Text("Salvar") }
            },
            dismissButton = {
                TextButton(onClick = { showCreate = false }) { Text("Cancelar") }
            },
        )
    }
}

@Composable
private fun ProPatientPhotosTab(patientId: Int) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val repository = remember { ProfessionalRepository() }
    var photos by remember { mutableStateOf<List<EvolutionPhotoDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun reload() {
        scope.launch {
            loading = true
            repository.patientEvolutionPhotos(patientId)
                .onSuccess { photos = it; error = null; loading = false }
                .onFailure { error = it.message; loading = false }
        }
    }

    LaunchedEffect(patientId) { reload() }

    val photoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri ?: return@rememberLauncherForActivityResult
        scope.launch {
            loading = true
            runCatching {
                val temp = File.createTempFile("pro_evo_", ".jpg", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody("image/jpeg".toMediaTypeOrNull()),
                )
                repository.uploadPatientEvolutionPhoto(
                    patientId = patientId,
                    photoPart = part,
                    type = "front".toRequestBody("text/plain".toMediaTypeOrNull()),
                    date = LocalDate.now().toString().toRequestBody("text/plain".toMediaTypeOrNull()),
                )
            }.onSuccess { reload() }
                .onFailure { err -> error = err.message; loading = false }
        }
    }

    Box(modifier = Modifier.fillMaxSize().padding(top = 12.dp)) {
        Column {
            when {
                loading -> CircularProgressIndicator()
                error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
                photos.isEmpty() -> Text("Nenhuma foto de evolução.")
                else -> LazyVerticalGrid(
                    columns = GridCells.Fixed(2),
                    modifier = Modifier.fillMaxSize(),
                ) {
                    items(photos) { photo ->
                        Card(modifier = Modifier.padding(4.dp)) {
                            AsyncImage(
                                model = ImageRequest.Builder(context)
                                    .data(photo.mediaUrl)
                                    .crossfade(true)
                                    .build(),
                                imageLoader = AuthenticatedImageLoader.get(context),
                                contentDescription = photo.type,
                                modifier = Modifier
                                    .fillMaxWidth()
                                    .aspectRatio(0.75f),
                                contentScale = ContentScale.Crop,
                            )
                            Text(
                                "${photo.type} · ${photo.registeredDate}",
                                style = MaterialTheme.typography.bodySmall,
                                modifier = Modifier.padding(8.dp),
                            )
                        }
                    }
                }
            }
        }

        FloatingActionButton(
            onClick = { photoPicker.launch("image/*") },
            modifier = Modifier
                .align(Alignment.BottomEnd)
                .padding(16.dp),
        ) {
            Icon(Icons.Default.Add, contentDescription = "Adicionar foto")
        }
    }
}
