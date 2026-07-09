package br.com.nexshape.academia.ui.evolution

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
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
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.BodyAssessmentDto
import br.com.nexshape.academia.data.api.CreateAssessmentRequest
import br.com.nexshape.academia.data.api.EvolutionPhotoDto
import br.com.nexshape.academia.data.media.AuthenticatedImageLoader
import br.com.nexshape.academia.data.repository.EvolutionRepository
import coil.compose.AsyncImage
import coil.request.ImageRequest
import kotlinx.coroutines.launch
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.File
import java.time.LocalDate

private enum class EvolutionTab(val label: String) {
    Measures("Medidas"),
    Photos("Fotos"),
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun EvolutionScreen(modifier: Modifier = Modifier) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val repository = remember { EvolutionRepository() }
    var selectedTab by remember { mutableIntStateOf(0) }
    var assessments by remember { mutableStateOf<List<BodyAssessmentDto>>(emptyList()) }
    var photos by remember { mutableStateOf<List<EvolutionPhotoDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showMeasureDialog by remember { mutableStateOf(false) }

    fun reload() {
        scope.launch {
            loading = true
            error = null
            repository.assessments()
                .onSuccess { assessments = it }
                .onFailure { error = it.message }
            repository.photos()
                .onSuccess { photos = it }
                .onFailure { if (error == null) error = it.message }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    val photoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri ?: return@rememberLauncherForActivityResult
        scope.launch {
            loading = true
            runCatching {
                val temp = File.createTempFile("evo_", ".jpg", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody("image/jpeg".toMediaTypeOrNull()),
                )
                repository.uploadPhoto(
                    photoPart = part,
                    type = "front".toRequestBody("text/plain".toMediaTypeOrNull()),
                    date = LocalDate.now().toString().toRequestBody("text/plain".toMediaTypeOrNull()),
                    weight = assessments.firstOrNull()?.weightKg?.toString()?.toRequestBody("text/plain".toMediaTypeOrNull()),
                )
            }.onSuccess { reload() }
                .onFailure {
                    error = it.message
                    loading = false
                }
        }
    }

    Scaffold(
        modifier = modifier,
        floatingActionButton = {
            FloatingActionButton(
                onClick = {
                    when (EvolutionTab.entries[selectedTab]) {
                        EvolutionTab.Measures -> showMeasureDialog = true
                        EvolutionTab.Photos -> photoPicker.launch("image/*")
                    }
                },
            ) {
                Icon(
                    imageVector = if (EvolutionTab.entries[selectedTab] == EvolutionTab.Photos) {
                        Icons.Default.CameraAlt
                    } else {
                        Icons.Default.Add
                    },
                    contentDescription = "Adicionar",
                )
            }
        },
    ) { padding ->
        Column(modifier = Modifier.padding(padding)) {
            TabRow(selectedTabIndex = selectedTab) {
                EvolutionTab.entries.forEachIndexed { index, tab ->
                    Tab(
                        selected = selectedTab == index,
                        onClick = { selectedTab = index },
                        text = { Text(tab.label) },
                    )
                }
            }

            PlanHint(
                assessmentCount = assessments.size,
                photoCount = photos.size,
                modifier = Modifier.padding(16.dp),
            )

            when {
                loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
                error != null -> Box(Modifier.fillMaxSize().padding(20.dp), contentAlignment = Alignment.Center) {
                    Text(error!!, color = MaterialTheme.colorScheme.error)
                }
                EvolutionTab.entries[selectedTab] == EvolutionTab.Measures -> MeasuresList(assessments)
                else -> PhotosGrid(photos)
            }
        }
    }

    if (showMeasureDialog) {
        MeasureDialog(
            onDismiss = { showMeasureDialog = false },
            onSave = { request ->
                scope.launch {
                    repository.createAssessment(request)
                        .onSuccess {
                            showMeasureDialog = false
                            reload()
                        }
                        .onFailure { error = it.message }
                }
            },
        )
    }
}

@Composable
private fun MeasuresList(assessments: List<BodyAssessmentDto>) {
    if (assessments.isEmpty()) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("Nenhuma avaliacao registrada.")
        }
        return
    }

    LazyColumn(
        contentPadding = PaddingValues(16.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp),
    ) {
        items(assessments, key = { it.id }) { item ->
            AssessmentCard(item)
        }
    }
}

@Composable
private fun PhotosGrid(photos: List<EvolutionPhotoDto>) {
    if (photos.isEmpty()) {
        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text("Nenhuma foto de evolucao.")
        }
        return
    }

    LazyVerticalGrid(
        columns = GridCells.Fixed(2),
        contentPadding = PaddingValues(12.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        verticalArrangement = Arrangement.spacedBy(8.dp),
    ) {
        items(photos, key = { it.id }) { photo ->
            EvolutionPhotoCard(photo)
        }
    }
}

@Composable
private fun PlanHint(
    assessmentCount: Int,
    photoCount: Int,
    modifier: Modifier = Modifier,
) {
    Card(modifier = modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp)) {
            Text("Limites do plano aplicados pela API", style = MaterialTheme.typography.titleSmall)
            Text(
                "Avaliacoes carregadas: $assessmentCount. Fotos carregadas: $photoCount.",
                style = MaterialTheme.typography.bodySmall,
                modifier = Modifier.padding(top = 4.dp),
            )
            Text(
                "No Free, o historico pode ser reduzido e uploads podem retornar plan_limit_reached.",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                modifier = Modifier.padding(top = 2.dp),
            )
        }
    }
}

@Composable
private fun AssessmentCard(item: BodyAssessmentDto) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(item.assessmentDate, style = MaterialTheme.typography.titleMedium)
            item.weightKg?.let { Text("Peso: ${it} kg") }
            item.bfPercent?.let { Text("Gordura: ${it}%") }
            item.musclePercent?.let { Text("Musculo: ${it}%") }
            item.neck?.let { Text("Pescoco: ${it} cm") }
            item.chest?.let { Text("Torax: ${it} cm") }
            item.waist?.let { Text("Cintura: ${it} cm") }
            item.abdomen?.let { Text("Abdomen: ${it} cm") }
            item.hips?.let { Text("Quadril: ${it} cm") }
            item.notes?.takeIf { it.isNotBlank() }?.let { Text("Obs.: $it") }
        }
    }
}

@Composable
private fun EvolutionPhotoCard(photo: EvolutionPhotoDto) {
    val context = LocalContext.current
    Card(modifier = Modifier.aspectRatio(0.75f)) {
        AsyncImage(
            model = ImageRequest.Builder(context)
                .data(photo.mediaUrl)
                .crossfade(true)
                .build(),
            imageLoader = AuthenticatedImageLoader.get(context),
            contentDescription = photo.type,
            modifier = Modifier.fillMaxSize(),
            contentScale = ContentScale.Crop,
        )
    }
}

@Composable
private fun MeasureDialog(
    onDismiss: () -> Unit,
    onSave: (CreateAssessmentRequest) -> Unit,
) {
    var weight by remember { mutableStateOf("") }
    var bf by remember { mutableStateOf("") }
    var muscle by remember { mutableStateOf("") }
    var neck by remember { mutableStateOf("") }
    var chest by remember { mutableStateOf("") }
    var waist by remember { mutableStateOf("") }
    var abdomen by remember { mutableStateOf("") }
    var hips by remember { mutableStateOf("") }
    var notes by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Nova avaliacao") },
        text = {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                item { NumberField("Peso (kg)", weight) { weight = onlyDecimal(it) } }
                item { NumberField("Gordura (%)", bf) { bf = onlyDecimal(it) } }
                item { NumberField("Musculo (%)", muscle) { muscle = onlyDecimal(it) } }
                item { NumberField("Pescoco (cm)", neck) { neck = onlyDecimal(it) } }
                item { NumberField("Torax (cm)", chest) { chest = onlyDecimal(it) } }
                item { NumberField("Cintura (cm)", waist) { waist = onlyDecimal(it) } }
                item { NumberField("Abdomen (cm)", abdomen) { abdomen = onlyDecimal(it) } }
                item { NumberField("Quadril (cm)", hips) { hips = onlyDecimal(it) } }
                item {
                    OutlinedTextField(
                        value = notes,
                        onValueChange = { notes = it },
                        label = { Text("Observacoes") },
                        modifier = Modifier.fillMaxWidth(),
                    )
                }
            }
        },
        confirmButton = {
            TextButton(
                onClick = {
                    onSave(
                        CreateAssessmentRequest(
                            assessmentDate = LocalDate.now().toString(),
                            weightKg = weight.toDoubleOrNull(),
                            bfPercent = bf.toDoubleOrNull(),
                            musclePercent = muscle.toDoubleOrNull(),
                            neck = neck.toDoubleOrNull(),
                            chest = chest.toDoubleOrNull(),
                            waist = waist.toDoubleOrNull(),
                            abdomen = abdomen.toDoubleOrNull(),
                            hips = hips.toDoubleOrNull(),
                            notes = notes.ifBlank { null },
                        ),
                    )
                },
            ) {
                Text("Salvar")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar") }
        },
    )
}

@Composable
private fun NumberField(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = Modifier.fillMaxWidth(),
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
    )
}

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char ->
        char.isDigit() || (char == '.' && value.indexOf('.') == index)
    }
