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
                    weight = null,
                )
            }.onSuccess { reload() }
                .onFailure { error = it.message; loading = false }
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

            when {
                loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator()
                }
                error != null -> Box(Modifier.fillMaxSize().padding(20.dp), contentAlignment = Alignment.Center) {
                    Text(error!!, color = MaterialTheme.colorScheme.error)
                }
                EvolutionTab.entries[selectedTab] == EvolutionTab.Measures -> {
                    if (assessments.isEmpty()) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Nenhuma avaliação registrada.")
                        }
                    } else {
                        LazyColumn(
                            contentPadding = PaddingValues(16.dp),
                            verticalArrangement = Arrangement.spacedBy(8.dp),
                        ) {
                            items(assessments, key = { it.id }) { item ->
                                AssessmentCard(item)
                            }
                        }
                    }
                }
                else -> {
                    if (photos.isEmpty()) {
                        Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                            Text("Nenhuma foto de evolução.")
                        }
                    } else {
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
                }
            }
        }
    }

    if (showMeasureDialog) {
        MeasureDialog(
            onDismiss = { showMeasureDialog = false },
            onSave = { weight, bf ->
                scope.launch {
                    repository.createAssessment(
                        CreateAssessmentRequest(
                            assessmentDate = LocalDate.now().toString(),
                            weightKg = weight,
                            bfPercent = bf,
                        ),
                    ).onSuccess {
                        showMeasureDialog = false
                        reload()
                    }.onFailure { error = it.message }
                }
            },
        )
    }
}

@Composable
private fun AssessmentCard(item: BodyAssessmentDto) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(item.assessmentDate, style = MaterialTheme.typography.titleMedium)
            item.weightKg?.let { Text("Peso: ${it} kg") }
            item.bfPercent?.let { Text("Gordura: ${it}%") }
            item.musclePercent?.let { Text("Músculo: ${it}%") }
            item.waist?.let { Text("Cintura: ${it} cm") }
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
    onSave: (weight: Double?, bf: Double?) -> Unit,
) {
    var weight by remember { mutableStateOf("") }
    var bf by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Nova avaliação") },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                OutlinedTextField(
                    value = weight,
                    onValueChange = { weight = it },
                    label = { Text("Peso (kg)") },
                    singleLine = true,
                )
                OutlinedTextField(
                    value = bf,
                    onValueChange = { bf = it },
                    label = { Text("Gordura (%)") },
                    singleLine = true,
                )
            }
        },
        confirmButton = {
            TextButton(onClick = {
                onSave(
                    weight.toDoubleOrNull(),
                    bf.toDoubleOrNull(),
                )
            }) {
                Text("Salvar")
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar") }
        },
    )
}
