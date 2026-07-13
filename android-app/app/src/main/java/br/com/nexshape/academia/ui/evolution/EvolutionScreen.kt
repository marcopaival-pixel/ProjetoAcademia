package br.com.nexshape.academia.ui.evolution

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
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
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Scale
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.AssistChip
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.StrokeCap
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.BodyAssessmentDto
import br.com.nexshape.academia.data.api.AssessmentSummaryDto
import br.com.nexshape.academia.data.api.CreateAssessmentRequest
import br.com.nexshape.academia.data.api.EvolutionPhotoDto
import br.com.nexshape.academia.data.media.AuthenticatedImageLoader
import br.com.nexshape.academia.data.repository.EvolutionRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexPanel
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMetricCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
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

@Composable
fun EvolutionScreen(modifier: Modifier = Modifier) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val repository = remember { EvolutionRepository() }
    var selectedTab by remember { mutableIntStateOf(0) }
    var assessments by remember { mutableStateOf<List<BodyAssessmentDto>>(emptyList()) }
    var summary by remember { mutableStateOf<AssessmentSummaryDto?>(null) }
    var photos by remember { mutableStateOf<List<EvolutionPhotoDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showMeasureDialog by remember { mutableStateOf(false) }
    var showPhotoTypeDialog by remember { mutableStateOf(false) }
    var selectedPhotoType by remember { mutableStateOf("front") }

    fun reload() {
        scope.launch {
            loading = true
            error = null
            repository.assessments()
                .onSuccess { assessments = it }
                .onFailure { error = friendlyError(it) }
            repository.assessmentSummary()
                .onSuccess { summary = it }
                .onFailure { if (error == null) error = friendlyError(it) }
            repository.photos()
                .onSuccess { photos = it }
                .onFailure { if (error == null) error = friendlyError(it) }
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
                    type = selectedPhotoType.toRequestBody("text/plain".toMediaTypeOrNull()),
                    date = LocalDate.now().toString().toRequestBody("text/plain".toMediaTypeOrNull()),
                    weight = assessments.firstOrNull()?.weightKg?.toString()?.toRequestBody("text/plain".toMediaTypeOrNull()),
                ).getOrThrow()
            }.onSuccess { reload() }
                .onFailure {
                    error = friendlyError(it)
                    loading = false
                }
        }
    }

    NexShapeScreen(
        title = "Evolucao",
        subtitle = "Medidas e fotos protegidas, carregadas direto da API.",
        modifier = modifier,
        action = {
            FloatingActionButton(
                onClick = {
                    when (EvolutionTab.entries[selectedTab]) {
                        EvolutionTab.Measures -> showMeasureDialog = true
                        EvolutionTab.Photos -> showPhotoTypeDialog = true
                    }
                },
                containerColor = NexNeon,
                contentColor = Color(0xFF04110D),
            ) {
                Icon(
                    imageVector = if (EvolutionTab.entries[selectedTab] == EvolutionTab.Photos) Icons.Default.CameraAlt else Icons.Default.Add,
                    contentDescription = "Adicionar",
                )
            }
        },
    ) {
        TabRow(selectedTabIndex = selectedTab, containerColor = Color.Transparent, contentColor = NexNeon) {
            EvolutionTab.entries.forEachIndexed { index, tab ->
                Tab(selected = selectedTab == index, onClick = { selectedTab = index }, text = { Text(tab.label) })
            }
        }

        when {
            loading -> NexLoadingState("Carregando sua evolucao...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = { reload() })
            EvolutionTab.entries[selectedTab] == EvolutionTab.Measures -> MeasuresContent(assessments, summary)
            else -> PhotosContent(
                photos = photos,
                onDelete = { photo ->
                    scope.launch {
                        repository.deletePhoto(photo.id)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
            )
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
                        .onFailure { error = friendlyError(it) }
                }
            },
        )
    }

    if (showPhotoTypeDialog) {
        PhotoTypeDialog(
            onDismiss = { showPhotoTypeDialog = false },
            onSelected = { type ->
                selectedPhotoType = type
                showPhotoTypeDialog = false
                photoPicker.launch("image/*")
            },
        )
    }
}

@Composable
private fun MeasuresContent(assessments: List<BodyAssessmentDto>, summary: AssessmentSummaryDto?) {
    if (assessments.isEmpty()) {
        NexEmptyState("Sem avaliacao ainda", "Registre sua primeira medida ou aguarde a avaliacao do profissional.")
        return
    }
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.padding(top = 14.dp)) {
        item {
            Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.fillMaxWidth()) {
                NexMetricCard(
                    title = "Peso atual",
                    value = summary?.currentWeightKg?.let { "${it} kg" } ?: "-",
                    icon = Icons.Default.Scale,
                    modifier = Modifier.weight(1f),
                )
                NexMetricCard(
                    title = "Gordura",
                    value = summary?.currentBfPercent?.let { "${it}%" } ?: "-",
                    icon = Icons.Default.MonitorHeart,
                    modifier = Modifier.weight(1f),
                )
            }
        }
        item {
            SummaryCard(summary)
        }
        item {
            EvolutionChartCard(assessments)
        }
        items(assessments, key = { it.id }) { item -> AssessmentCard(item) }
    }
}

@Composable
private fun SummaryCard(summary: AssessmentSummaryDto?) {
    NexCard {
        Text("Resumo corporal", color = Color.White, fontWeight = FontWeight.Black)
        Text("IMC: ${summary?.bmi ?: "-"}", color = NexMuted, modifier = Modifier.padding(top = 8.dp))
        Text("Peso inicial: ${summary?.initialWeightKg?.let { "$it kg" } ?: "-"}", color = NexMuted)
        Text("Meta: ${summary?.targetWeightKg?.let { "$it kg" } ?: "-"}", color = NexMuted)
        Text("Progresso da meta: ${summary?.goalProgressPercent?.let { "$it%" } ?: "-"}", color = NexNeon, modifier = Modifier.padding(top = 4.dp))
        Text("Delta peso: ${summary?.deltas?.weightKg?.let { "$it kg" } ?: "-"}", color = NexMuted, modifier = Modifier.padding(top = 6.dp))
        Text("Delta gordura: ${summary?.deltas?.bfPercent?.let { "$it%" } ?: "-"}", color = NexMuted)
        Text("Ultima avaliacao: ${summary?.lastAssessmentDate ?: "-"}", color = NexMuted, modifier = Modifier.padding(top = 6.dp))
        Text("Proxima prevista: ${summary?.nextAssessmentDate ?: "-"}", color = NexMuted)
    }
}

@Composable
private fun EvolutionChartCard(assessments: List<BodyAssessmentDto>) {
    val ordered = assessments.sortedBy { it.assessmentDate }
    val weights = ordered.mapNotNull { it.weightKg }
    val fat = ordered.mapNotNull { it.bfPercent }

    NexCard {
        Text("Grafico de evolucao", color = Color.White, fontWeight = FontWeight.Black)
        Text("Peso e gordura conforme avaliacoes da API", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Canvas(
            modifier = Modifier
                .fillMaxWidth()
                .height(170.dp)
                .padding(top = 16.dp),
        ) {
            fun drawSeries(values: List<Double>, color: Color) {
                if (values.size < 2) return

                val min = values.minOrNull() ?: return
                val max = values.maxOrNull() ?: return
                val range = (max - min).takeIf { it > 0.0 } ?: 1.0
                val stepX = size.width / (values.size - 1).coerceAtLeast(1)
                val path = Path()

                values.forEachIndexed { index, value ->
                    val x = stepX * index
                    val y = size.height - (((value - min) / range).toFloat() * size.height)
                    if (index == 0) path.moveTo(x, y) else path.lineTo(x, y)
                }

                drawPath(
                    path = path,
                    color = color,
                    style = Stroke(width = 4.dp.toPx(), cap = StrokeCap.Round),
                )
            }

            drawLine(
                color = Color.White.copy(alpha = 0.12f),
                start = androidx.compose.ui.geometry.Offset(0f, size.height),
                end = androidx.compose.ui.geometry.Offset(size.width, size.height),
            )
            drawSeries(weights, NexNeon)
            drawSeries(fat, Color(0xFFF59E0B))
        }
        Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.padding(top = 8.dp)) {
            Text("Peso", color = NexNeon)
            Text("Gordura", color = Color(0xFFF59E0B))
        }
    }
}

@Composable
private fun PhotosContent(
    photos: List<EvolutionPhotoDto>,
    onDelete: (EvolutionPhotoDto) -> Unit,
) {
    if (photos.isEmpty()) {
        NexEmptyState("Sem fotos de evolucao", "Envie fotos frontal, lateral ou posterior para acompanhar sua mudanca.")
        return
    }
    LazyVerticalGrid(
        columns = GridCells.Fixed(2),
        horizontalArrangement = Arrangement.spacedBy(10.dp),
        verticalArrangement = Arrangement.spacedBy(10.dp),
        modifier = Modifier.padding(top = 14.dp),
    ) {
        items(photos, key = { it.id }) { photo -> EvolutionPhotoCard(photo, onDelete) }
    }
}

@Composable
private fun AssessmentCard(item: BodyAssessmentDto) {
    NexCard {
        Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Text(item.assessmentDate, color = Color.White, fontWeight = FontWeight.Black)
            item.status?.let { AssistChip(onClick = {}, label = { Text(it) }) }
        }
        item.weightKg?.let { Text("Peso: ${it} kg", color = NexMuted, modifier = Modifier.padding(top = 8.dp)) }
        item.bfPercent?.let { Text("Gordura: ${it}%", color = NexMuted) }
        item.musclePercent?.let { Text("Musculo: ${it}%", color = NexMuted) }
        item.neck?.let { Text("Pescoco: ${it} cm", color = NexMuted) }
        item.chest?.let { Text("Torax: ${it} cm", color = NexMuted) }
        item.waist?.let { Text("Cintura: ${it} cm", color = NexMuted) }
        item.abdomen?.let { Text("Abdomen: ${it} cm", color = NexMuted) }
        item.hips?.let { Text("Quadril: ${it} cm", color = NexMuted) }
        item.bicepL?.let { Text("Braco esquerdo: ${it} cm", color = NexMuted) }
        item.bicepR?.let { Text("Braco direito: ${it} cm", color = NexMuted) }
        item.forearmL?.let { Text("Antebraco esquerdo: ${it} cm", color = NexMuted) }
        item.forearmR?.let { Text("Antebraco direito: ${it} cm", color = NexMuted) }
        item.thighL?.let { Text("Coxa esquerda: ${it} cm", color = NexMuted) }
        item.thighR?.let { Text("Coxa direita: ${it} cm", color = NexMuted) }
        item.calfL?.let { Text("Panturrilha esquerda: ${it} cm", color = NexMuted) }
        item.calfR?.let { Text("Panturrilha direita: ${it} cm", color = NexMuted) }
        item.bloodPressure?.takeIf { it.isNotBlank() }?.let { Text("PA: $it", color = NexMuted) }
        item.heartRate?.let { Text("FC: $it bpm", color = NexMuted) }
        item.notes?.takeIf { it.isNotBlank() }?.let { Text("Obs.: $it", color = NexMuted, modifier = Modifier.padding(top = 8.dp)) }
    }
}

@Composable
private fun EvolutionPhotoCard(photo: EvolutionPhotoDto, onDelete: (EvolutionPhotoDto) -> Unit) {
    val context = LocalContext.current
    Box(modifier = Modifier.aspectRatio(0.75f)) {
        NexCard(modifier = Modifier.fillMaxSize()) {
            Column {
                AsyncImage(
                    model = ImageRequest.Builder(context).data(photo.mediaUrl).crossfade(true).build(),
                    imageLoader = AuthenticatedImageLoader.get(context),
                    contentDescription = photo.type,
                    modifier = Modifier
                        .fillMaxWidth()
                        .weight(1f),
                    contentScale = ContentScale.Crop,
                )
                Text(photoTypeLabel(photo.type), color = Color.White, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 8.dp))
                Text(photo.registeredDate, color = NexMuted)
                photo.weightKg?.let { Text("${it} kg", color = NexMuted) }
            }
        }
        IconButton(onClick = { onDelete(photo) }, modifier = Modifier.align(Alignment.TopEnd)) {
            Icon(Icons.Default.Delete, contentDescription = "Excluir", tint = Color.White)
        }
    }
}

private fun photoTypeLabel(type: String): String = when (type) {
    "front" -> "Frontal"
    "side" -> "Lateral"
    "back" -> "Posterior"
    else -> "Outro"
}

@Composable
private fun PhotoTypeDialog(
    onDismiss: () -> Unit,
    onSelected: (String) -> Unit,
) {
    val options = listOf(
        "front" to "Frontal",
        "side" to "Lateral",
        "back" to "Posterior",
        "custom" to "Outro",
    )

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Tipo da foto", fontWeight = FontWeight.Black) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                options.forEach { (value, label) ->
                    TextButton(
                        onClick = { onSelected(value) },
                        modifier = Modifier.fillMaxWidth(),
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                    ) {
                        Text(label)
                    }
                }
            }
        },
        confirmButton = {},
        dismissButton = {
            TextButton(onClick = onDismiss, colors = ButtonDefaults.textButtonColors(contentColor = NexMuted)) {
                Text("Cancelar")
            }
        },
    )
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
    var bicepL by remember { mutableStateOf("") }
    var bicepR by remember { mutableStateOf("") }
    var forearmL by remember { mutableStateOf("") }
    var forearmR by remember { mutableStateOf("") }
    var thighL by remember { mutableStateOf("") }
    var thighR by remember { mutableStateOf("") }
    var calfL by remember { mutableStateOf("") }
    var calfR by remember { mutableStateOf("") }
    var bloodPressure by remember { mutableStateOf("") }
    var heartRate by remember { mutableStateOf("") }
    var notes by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Nova avaliacao", fontWeight = FontWeight.Black) },
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
                item { NumberField("Braco esquerdo (cm)", bicepL) { bicepL = onlyDecimal(it) } }
                item { NumberField("Braco direito (cm)", bicepR) { bicepR = onlyDecimal(it) } }
                item { NumberField("Antebraco esquerdo (cm)", forearmL) { forearmL = onlyDecimal(it) } }
                item { NumberField("Antebraco direito (cm)", forearmR) { forearmR = onlyDecimal(it) } }
                item { NumberField("Coxa esquerda (cm)", thighL) { thighL = onlyDecimal(it) } }
                item { NumberField("Coxa direita (cm)", thighR) { thighR = onlyDecimal(it) } }
                item { NumberField("Panturrilha esquerda (cm)", calfL) { calfL = onlyDecimal(it) } }
                item { NumberField("Panturrilha direita (cm)", calfR) { calfR = onlyDecimal(it) } }
                item {
                    ThemedTextField(value = bloodPressure, onValueChange = { bloodPressure = it.take(20) }, label = "Pressao arterial")
                }
                item { NumberField("Frequencia cardiaca (bpm)", heartRate) { heartRate = onlyDigits(it).take(3) } }
                item {
                    ThemedTextField(value = notes, onValueChange = { notes = it }, label = "Observacoes")
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
                            bicepL = bicepL.toDoubleOrNull(),
                            bicepR = bicepR.toDoubleOrNull(),
                            forearmL = forearmL.toDoubleOrNull(),
                            forearmR = forearmR.toDoubleOrNull(),
                            thighL = thighL.toDoubleOrNull(),
                            thighR = thighR.toDoubleOrNull(),
                            calfL = calfL.toDoubleOrNull(),
                            calfR = calfR.toDoubleOrNull(),
                            bloodPressure = bloodPressure.ifBlank { null },
                            heartRate = heartRate.toIntOrNull(),
                            notes = notes.ifBlank { null },
                        ),
                    )
                },
            ) { Text("Salvar", color = NexNeon, fontWeight = FontWeight.Black) }
        },
        dismissButton = {
            TextButton(onClick = onDismiss, colors = ButtonDefaults.textButtonColors(contentColor = NexMuted)) {
                Text("Cancelar")
            }
        },
    )
}

@Composable
private fun NumberField(label: String, value: String, onValueChange: (String) -> Unit) {
    ThemedTextField(
        value = value,
        onValueChange = onValueChange,
        label = label,
        keyboardType = KeyboardType.Decimal,
    )
}

@Composable
private fun ThemedTextField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    keyboardType: KeyboardType = KeyboardType.Text,
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = Modifier.fillMaxWidth(),
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        colors = OutlinedTextFieldDefaults.colors(
            focusedTextColor = Color.White,
            unfocusedTextColor = Color.White,
            focusedBorderColor = NexNeon,
            unfocusedBorderColor = Color.White.copy(alpha = 0.28f),
            focusedLabelColor = NexNeon,
            unfocusedLabelColor = NexMuted,
            cursorColor = NexNeon,
            focusedContainerColor = NexPanel,
            unfocusedContainerColor = NexPanel,
        ),
    )
}

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char -> char.isDigit() || (char == '.' && value.indexOf('.') == index) }

private fun onlyDigits(value: String): String = value.filter { it.isDigit() }
