package br.com.nexshape.academia.ui.evolution

import android.net.Uri
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.clickable
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
import androidx.compose.material3.ExtendedFloatingActionButton
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
import br.com.nexshape.academia.data.api.BodyAnalysisCompareData
import br.com.nexshape.academia.data.api.BodyAnalysisDto
import br.com.nexshape.academia.data.api.CreateAssessmentRequest
import br.com.nexshape.academia.data.api.EvolutionPhotoDto
import br.com.nexshape.academia.data.api.EvolutionReportConsentDto
import br.com.nexshape.academia.data.api.EvolutionReportDto
import br.com.nexshape.academia.data.api.EvolutionSessionAnalysisDto
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
    BodyAnalysis("Analise IA"),
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
    var bodyAnalyses by remember { mutableStateOf<List<BodyAnalysisDto>>(emptyList()) }
    var selectedBodyAnalysis by remember { mutableStateOf<BodyAnalysisDto?>(null) }
    var selectedBodyViewType by remember { mutableStateOf("front") }
    var bodyCompare by remember { mutableStateOf<BodyAnalysisCompareData?>(null) }
    var firstCompareId by remember { mutableStateOf<Int?>(null) }
    var secondCompareId by remember { mutableStateOf<Int?>(null) }
    var bodyAnalysisLoading by remember { mutableStateOf(false) }
    var bodyAnalysisError by remember { mutableStateOf<String?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showMeasureDialog by remember { mutableStateOf(false) }
    var showPhotoTypeDialog by remember { mutableStateOf(false) }
    var selectedPhotoType by remember { mutableStateOf("front") }
    var photoValidationTitle by remember { mutableStateOf("Foto nao aprovada") }
    var photoValidationMessage by remember { mutableStateOf<String?>(null) }
    var reportLoading by remember { mutableStateOf(false) }
    var reportStatusMessage by remember { mutableStateOf("Gerando relatorio...") }
    var reportData by remember { mutableStateOf<EvolutionReportDto?>(null) }
    var reportError by remember { mutableStateOf<String?>(null) }
    var pendingConsent by remember { mutableStateOf<EvolutionReportConsentDto?>(null) }

    fun startReport(acceptConsent: Boolean = false) {
        scope.launch {
            reportLoading = true
            reportStatusMessage = "Preparando relatorio..."
            reportError = null
            reportData = null
            repository.report(acceptConsent = acceptConsent) { status ->
                reportStatusMessage = status
            }
                .onSuccess { reportData = it }
                .onFailure { reportError = friendlyError(it) }
            reportLoading = false
        }
    }

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
            repository.bodyAnalyses()
                .onSuccess {
                    bodyAnalyses = it
                    selectedBodyAnalysis = selectedBodyAnalysis ?: it.firstOrNull()
                }
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
                val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                val extension = extensionForMimeType(mimeType)
                    ?: throw IllegalArgumentException("Envie uma foto em JPG, PNG ou WebP. Nenhum credito foi consumido.")

                val temp = File.createTempFile("evo_", ".$extension", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody(mimeType.toMediaTypeOrNull()),
                )
                val validation = repository.validatePhoto(part).getOrThrow()
                if (!validation.approved) {
                    throw IllegalArgumentException(validation.messages.firstOrNull() ?: "Foto reprovada para evolução.")
                }
                repository.uploadPhoto(
                    photoPart = part,
                    type = selectedPhotoType.toRequestBody("text/plain".toMediaTypeOrNull()),
                    date = LocalDate.now().toString().toRequestBody("text/plain".toMediaTypeOrNull()),
                    weight = assessments.firstOrNull()?.weightKg?.toString()?.toRequestBody("text/plain".toMediaTypeOrNull()),
                ).getOrThrow()
            }.onSuccess { reload() }
                .onFailure {
                    val message = friendlyError(it)
                    photoValidationTitle = if (message.contains("credito", ignoreCase = true)) {
                        "Credito insuficiente"
                    } else {
                        "Foto nao aprovada"
                    }
                    photoValidationMessage = if (message.contains("credito", ignoreCase = true)) {
                        message
                    } else {
                        "$message\n\nNenhum credito foi consumido."
                    }
                    loading = false
                }
        }
    }

    val bodyAnalysisPhotoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri ?: return@rememberLauncherForActivityResult
        scope.launch {
            bodyAnalysisLoading = true
            bodyAnalysisError = null
            runCatching {
                val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                val extension = extensionForMimeType(mimeType)
                    ?: throw IllegalArgumentException("Envie uma foto em JPG, PNG ou WebP. Nenhum credito foi consumido.")

                val temp = File.createTempFile("body_analysis_", ".$extension", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                }
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody(mimeType.toMediaTypeOrNull()),
                )
                repository.uploadBodyAnalysisPhoto(
                    photoPart = part,
                    viewType = selectedBodyViewType.toRequestBody("text/plain".toMediaTypeOrNull()),
                ).getOrThrow()
            }.onSuccess { analysis ->
                selectedBodyAnalysis = analysis
                repository.bodyAnalyses()
                    .onSuccess { bodyAnalyses = it }
                bodyCompare = null
            }.onFailure {
                bodyAnalysisError = friendlyError(it)
            }
            bodyAnalysisLoading = false
        }
    }

    NexShapeScreen(
        title = "Evolucao",
        subtitle = "Novo registro, fotos de hoje, relatorio IA e historico de evolucao.",
        modifier = modifier,
        action = {
            ExtendedFloatingActionButton(
                onClick = { showPhotoTypeDialog = true },
                containerColor = NexNeon,
                contentColor = Color(0xFF04110D),
            ) {
                Icon(Icons.Default.CameraAlt, contentDescription = null)
                Text("Novo registro", fontWeight = FontWeight.Black, modifier = Modifier.padding(start = 8.dp))
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
            EvolutionTab.entries[selectedTab] == EvolutionTab.Photos -> PhotosContent(
                photos = photos,
                repository = repository,
                onNewRecord = { showPhotoTypeDialog = true },
                onCaptureAngle = { type ->
                    selectedPhotoType = type
                    photoPicker.launch("image/*")
                },
                onReport = {
                    scope.launch {
                        reportError = null
                        repository.reportConsent()
                            .onSuccess { consent ->
                                if (consent.accepted) {
                                    startReport()
                                } else {
                                    pendingConsent = consent
                                }
                            }
                            .onFailure { reportError = friendlyError(it) }
                    }
                },
                onDelete = { photo ->
                    scope.launch {
                        repository.deletePhoto(photo.id)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
            )
            else -> BodyAnalysisContent(
                analyses = bodyAnalyses,
                selected = selectedBodyAnalysis,
                selectedViewType = selectedBodyViewType,
                loading = bodyAnalysisLoading,
                error = bodyAnalysisError,
                compare = bodyCompare,
                firstCompareId = firstCompareId,
                secondCompareId = secondCompareId,
                onViewTypeChange = { selectedBodyViewType = it },
                onUpload = { bodyAnalysisPhotoPicker.launch("image/*") },
                onSelect = { analysis ->
                    selectedBodyAnalysis = analysis
                    bodyCompare = null
                },
                onFirstCompareChange = { id ->
                    firstCompareId = id
                    bodyCompare = null
                },
                onSecondCompareChange = { id ->
                    secondCompareId = id
                    bodyCompare = null
                },
                onCompare = {
                    val firstId = firstCompareId
                    val secondId = secondCompareId
                    if (firstId != null && secondId != null && firstId != secondId) {
                        scope.launch {
                            bodyAnalysisLoading = true
                            bodyAnalysisError = null
                            repository.compareBodyAnalysis(firstId, secondId)
                                .onSuccess { bodyCompare = it }
                                .onFailure { bodyAnalysisError = friendlyError(it) }
                            bodyAnalysisLoading = false
                        }
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

    if (photoValidationMessage != null) {
        AlertDialog(
            onDismissRequest = { photoValidationMessage = null },
            containerColor = Color(0xFF0B1117),
            titleContentColor = Color.White,
            textContentColor = NexMuted,
            title = { Text("Foto não aprovada", fontWeight = FontWeight.Black) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(photoValidationTitle, color = Color.White, fontWeight = FontWeight.Bold)
                    Text(photoValidationMessage.orEmpty())
                }
            },
            confirmButton = {
                TextButton(onClick = { photoValidationMessage = null }, colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)) {
                    Text("OK")
                }
            },
        )
    }

    if (reportData != null || reportError != null || reportLoading) {
        EvolutionReportDialog(
            loading = reportLoading,
            loadingMessage = reportStatusMessage,
            report = reportData,
            error = reportError,
            onDismiss = {
                if (!reportLoading) {
                    reportData = null
                    reportError = null
                }
            },
        )
    }

    pendingConsent?.let { consent ->
        EvolutionConsentDialog(
            consent = consent,
            onDismiss = { pendingConsent = null },
            onAccept = {
                pendingConsent = null
                startReport(acceptConsent = true)
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
private fun BodyAnalysisContent(
    analyses: List<BodyAnalysisDto>,
    selected: BodyAnalysisDto?,
    selectedViewType: String,
    loading: Boolean,
    error: String?,
    compare: BodyAnalysisCompareData?,
    firstCompareId: Int?,
    secondCompareId: Int?,
    onViewTypeChange: (String) -> Unit,
    onUpload: () -> Unit,
    onSelect: (BodyAnalysisDto) -> Unit,
    onFirstCompareChange: (Int?) -> Unit,
    onSecondCompareChange: (Int?) -> Unit,
    onCompare: () -> Unit,
) {
    LazyColumn(verticalArrangement = Arrangement.spacedBy(14.dp), modifier = Modifier.padding(top = 14.dp)) {
        item {
            NexCard {
                Text("Cyber-Fit Body Analysis", color = Color.White, fontWeight = FontWeight.Black)
                Text(
                    "Analise postural por foto com o mesmo motor usado na web.",
                    color = NexMuted,
                    modifier = Modifier.padding(top = 4.dp),
                )
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
                    listOf("front" to "Frontal", "back" to "Posterior", "side" to "Lateral").forEach { (value, label) ->
                        AssistChip(
                            onClick = { onViewTypeChange(value) },
                            label = { Text(label) },
                            leadingIcon = if (selectedViewType == value) {
                                { Icon(Icons.Default.MonitorHeart, contentDescription = null) }
                            } else {
                                null
                            },
                        )
                    }
                }
                TextButton(
                    enabled = !loading,
                    onClick = onUpload,
                    colors = ButtonDefaults.textButtonColors(contentColor = NexNeon, disabledContentColor = NexMuted),
                    modifier = Modifier.padding(top = 8.dp),
                ) {
                    Icon(Icons.Default.CameraAlt, contentDescription = null)
                    Text(if (loading) "Analisando..." else "Enviar foto para analise", fontWeight = FontWeight.Black, modifier = Modifier.padding(start = 8.dp))
                }
                Text(
                    "A foto passa por validacao, credito IA e auditoria de limitacoes antes de salvar no historico.",
                    color = NexMuted,
                    modifier = Modifier.padding(top = 4.dp),
                )
            }
        }

        if (error != null) {
            item {
                NexCard {
                    Text("Nao foi possivel concluir", color = Color(0xFFF59E0B), fontWeight = FontWeight.Black)
                    Text(error, color = NexMuted, modifier = Modifier.padding(top = 6.dp))
                }
            }
        }

        if (selected != null) {
            item { BodyAnalysisResultCard(selected) }
        } else if (analyses.isEmpty()) {
            item {
                NexEmptyState("Nenhuma analise ainda", "Envie uma foto frontal, posterior ou lateral para iniciar o historico.")
            }
        }

        if (analyses.size >= 2) {
            item {
                BodyAnalysisCompareCard(
                    analyses = analyses,
                    firstCompareId = firstCompareId,
                    secondCompareId = secondCompareId,
                    compare = compare,
                    loading = loading,
                    onFirstCompareChange = onFirstCompareChange,
                    onSecondCompareChange = onSecondCompareChange,
                    onCompare = onCompare,
                )
            }
        }

        if (analyses.isNotEmpty()) {
            item {
                Text("Ultimas analises", color = Color.White, fontWeight = FontWeight.Black)
                Text("Toque em um registro para ver os detalhes.", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
            }
            items(analyses, key = { it.id }) { analysis ->
                BodyAnalysisHistoryCard(analysis = analysis, selected = selected?.id == analysis.id, onSelect = { onSelect(analysis) })
            }
        }
    }
}

@Composable
private fun BodyAnalysisResultCard(analysis: BodyAnalysisDto) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Resultado ${bodyViewLabel(analysis.viewType)}", color = Color.White, fontWeight = FontWeight.Black)
                Text(analysis.createdAt?.take(10) ?: "Analise salva", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
            }
            analysis.visionConfidence?.let {
                AssistChip(onClick = {}, label = { Text("IA ${(it * 100).toInt()}%") })
            }
        }

        analysis.photoUrl?.let { url ->
            val context = LocalContext.current
            AsyncImage(
                model = ImageRequest.Builder(context).data(url).crossfade(true).build(),
                imageLoader = AuthenticatedImageLoader.get(context),
                contentDescription = "Foto analisada",
                modifier = Modifier
                    .fillMaxWidth()
                    .height(220.dp)
                    .padding(top = 12.dp),
                contentScale = ContentScale.Crop,
            )
        }

        Text(analysis.summary ?: "Analise concluida.", color = Color.White, modifier = Modifier.padding(top = 12.dp))
        analysis.visionSummary?.takeIf { it.isNotBlank() }?.let {
            Text(it, color = NexMuted, modifier = Modifier.padding(top = 6.dp))
        }

        BodyMetricRow(analysis.metrics)
        BodyAnalysisListSection("Pontos de atencao", analysis.attentionPoints)
        BodyAnalysisListSection("Limitacoes", analysis.limitations)
        BodyAnalysisListSection("Exercicios sugeridos", analysis.exercises)

        analysis.workout?.takeIf { it.isNotBlank() }?.let {
            Text("Treino e recuperacao", color = Color.White, fontWeight = FontWeight.Black, modifier = Modifier.padding(top = 12.dp))
            Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        }
        analysis.diet?.takeIf { it.isNotBlank() }?.let {
            Text("Habitos de suporte", color = Color.White, fontWeight = FontWeight.Black, modifier = Modifier.padding(top = 12.dp))
            Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        }
    }
}

@Composable
private fun BodyMetricRow(metrics: Map<String, Double?>) {
    val chips = listOf(
        "posture_score" to "Postura",
        "asymmetry_shoulders" to "Ombros",
        "asymmetry_hips" to "Quadril",
        "head_forward_score" to "Cabeca",
        "landmark_confidence" to "Confianca",
    ).mapNotNull { (key, label) -> metrics[key]?.let { label to it } }

    if (chips.isEmpty()) return

    Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
        chips.take(3).forEach { (label, value) ->
            AssistChip(onClick = {}, label = { Text("$label ${formatMetric(value)}") })
        }
    }
    if (chips.size > 3) {
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 6.dp)) {
            chips.drop(3).forEach { (label, value) ->
                AssistChip(onClick = {}, label = { Text("$label ${formatMetric(value)}") })
            }
        }
    }
}

@Composable
private fun BodyAnalysisListSection(title: String, values: List<String>) {
    if (values.isEmpty()) return

    Text(title, color = Color.White, fontWeight = FontWeight.Black, modifier = Modifier.padding(top = 12.dp))
    values.forEach { value ->
        Text("- $value", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
    }
}

@Composable
private fun BodyAnalysisHistoryCard(
    analysis: BodyAnalysisDto,
    selected: Boolean,
    onSelect: () -> Unit,
) {
    NexCard(modifier = Modifier.clickable(onClick = onSelect)) {
        Row(horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text("${bodyViewLabel(analysis.viewType)} - ${analysis.createdAt?.take(10) ?: "-"}", color = Color.White, fontWeight = FontWeight.Black)
                Text(analysis.summary?.take(90) ?: "Analise salva.", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
            }
            Text(if (selected) "Aberta" else "Ver", color = if (selected) NexNeon else NexMuted, fontWeight = FontWeight.Bold)
        }
    }
}

@Composable
private fun BodyAnalysisCompareCard(
    analyses: List<BodyAnalysisDto>,
    firstCompareId: Int?,
    secondCompareId: Int?,
    compare: BodyAnalysisCompareData?,
    loading: Boolean,
    onFirstCompareChange: (Int?) -> Unit,
    onSecondCompareChange: (Int?) -> Unit,
    onCompare: () -> Unit,
) {
    NexCard {
        Text("Comparar fotos", color = Color.White, fontWeight = FontWeight.Black)
        Text("Selecione duas analises para ver deltas objetivos.", color = NexMuted, modifier = Modifier.padding(top = 4.dp))

        Text("Primeira", color = Color.White, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 12.dp))
        BodyAnalysisSelectionRow(analyses, firstCompareId, onFirstCompareChange)
        Text("Mais recente", color = Color.White, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 10.dp))
        BodyAnalysisSelectionRow(analyses, secondCompareId, onSecondCompareChange)

        TextButton(
            enabled = !loading && firstCompareId != null && secondCompareId != null && firstCompareId != secondCompareId,
            onClick = onCompare,
            colors = ButtonDefaults.textButtonColors(contentColor = NexNeon, disabledContentColor = NexMuted),
            modifier = Modifier.padding(top = 8.dp),
        ) {
            Text(if (loading) "Comparando..." else "Comparar", fontWeight = FontWeight.Black)
        }

        compare?.metrics?.forEach { metric ->
            val statusColor = when (metric.status) {
                "improved" -> NexNeon
                "worsened" -> Color(0xFFF59E0B)
                else -> NexMuted
            }
            Text(
                "${metric.label}: ${formatMetric(metric.first)} -> ${formatMetric(metric.second)} (${metric.diff?.let { formatMetric(it) } ?: "-"})",
                color = statusColor,
                modifier = Modifier.padding(top = 8.dp),
            )
        }
    }
}

@Composable
private fun BodyAnalysisSelectionRow(
    analyses: List<BodyAnalysisDto>,
    selectedId: Int?,
    onSelected: (Int?) -> Unit,
) {
    Column(verticalArrangement = Arrangement.spacedBy(6.dp), modifier = Modifier.padding(top = 6.dp)) {
        analyses.take(5).forEach { analysis ->
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .clickable { onSelected(analysis.id) }
                    .padding(vertical = 6.dp),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically,
            ) {
                Text("${bodyViewLabel(analysis.viewType)} - ${analysis.createdAt?.take(10) ?: analysis.id}", color = Color.White)
                Text(if (selectedId == analysis.id) "selecionada" else "usar", color = if (selectedId == analysis.id) NexNeon else NexMuted)
            }
        }
    }
}

@Composable
private fun PhotosContent(
    photos: List<EvolutionPhotoDto>,
    repository: EvolutionRepository,
    onNewRecord: () -> Unit,
    onCaptureAngle: (String) -> Unit,
    onReport: () -> Unit,
    onDelete: (EvolutionPhotoDto) -> Unit,
) {
    val sessions = remember(photos) { photos.groupBy { it.registeredDate }.toSortedMap(compareByDescending { it }) }
    val today = LocalDate.now().toString()
    val todayPhotos = photos.filter { it.registeredDate == today }
    val hasCompleteRecord = remember(photos) {
        photos.groupBy { it.registeredDate }.values.any { session ->
            val types = session.map { it.type }.toSet()
            listOf("front", "back", "right_side", "left_side").all { type ->
                type in types || (type.endsWith("side") && "side" in types)
            }
        }
    }

    LazyColumn(verticalArrangement = Arrangement.spacedBy(14.dp), modifier = Modifier.padding(top = 14.dp)) {
        item {
            TodayPhotoProgressCard(todayPhotos = todayPhotos, onCaptureAngle = onCaptureAngle)
        }
        item {
            NexCard {
                Row(horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically, modifier = Modifier.fillMaxWidth()) {
                    Column(modifier = Modifier.weight(1f)) {
                        Text("Relatorio IA", color = Color.White, fontWeight = FontWeight.Black)
                        Text(
                            if (hasCompleteRecord) {
                                "Relatorio inteligente de evolucao visual com auditoria anti-alucinacao."
                            } else {
                                "Complete um registro para gerar o relatorio."
                            },
                            color = NexMuted,
                            modifier = Modifier.padding(top = 4.dp),
                        )
                    }
                    TextButton(
                        enabled = hasCompleteRecord,
                        onClick = onReport,
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon, disabledContentColor = NexMuted),
                    ) {
                        Text("Gerar", fontWeight = FontWeight.Black)
                    }
                }
            }
        }
        if (photos.isEmpty()) {
            item {
                NexCard {
                    Text("Sem registros", color = Color.White, fontWeight = FontWeight.Black)
                    Text(
                        "Voce ainda nao criou um registro de evolucao. Comece com fotos de frente, costas e laterais.",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 6.dp),
                    )
                    TextButton(
                        onClick = onNewRecord,
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                        modifier = Modifier.padding(top = 8.dp),
                    ) {
                        Text("Criar primeiro registro", fontWeight = FontWeight.Black)
                    }
                }
            }
            return@LazyColumn
        }
        item {
            Text("Historico de evolucao", color = Color.White, fontWeight = FontWeight.Black)
            Text("Registros anteriores agrupados por data.", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
        }
        items(sessions.entries.toList(), key = { it.key }) { entry ->
            EvolutionSessionCard(date = entry.key, photos = entry.value, repository = repository)
        }
    }
}

@Composable
private fun TodayPhotoProgressCard(
    todayPhotos: List<EvolutionPhotoDto>,
    onCaptureAngle: (String) -> Unit,
) {
    val angles = listOf(
        "front" to "Frente",
        "back" to "Costas",
        "right_side" to "Lado direito",
        "left_side" to "Lado esquerdo",
    )
    val byType = todayPhotos.groupBy { it.type }

    NexCard {
        Text("Fotos de hoje", color = Color.White, fontWeight = FontWeight.Black)
        Text("Complete os angulos para formar um registro completo.", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Column(verticalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
            angles.forEach { (type, label) ->
                val done = byType[type]?.isNotEmpty() == true || (type.endsWith("side") && byType["side"]?.isNotEmpty() == true)
                Row(
                    modifier = Modifier
                        .fillMaxWidth()
                        .clickable { onCaptureAngle(type) }
                        .padding(vertical = 8.dp),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically,
                ) {
                    Text(label, color = Color.White, fontWeight = FontWeight.Bold)
                    Text(if (done) "concluido" else "pendente", color = if (done) NexNeon else NexMuted)
                }
            }
        }
    }
}

@Composable
private fun EvolutionSessionCard(
    date: String,
    photos: List<EvolutionPhotoDto>,
    repository: EvolutionRepository,
) {
    val scope = rememberCoroutineScope()
    var loading by remember { mutableStateOf(false) }
    var analysis by remember { mutableStateOf<EvolutionSessionAnalysisDto?>(null) }
    var error by remember { mutableStateOf<String?>(null) }
    val types = mapOf(
        "front" to "Frente",
        "back" to "Costas",
        "right_side" to "Lado direito",
        "left_side" to "Lado esquerdo",
    )
    val byType = photos.groupBy { it.type }
    val completion = listOf("front", "back", "right_side", "left_side").count { byType[it]?.isNotEmpty() == true || (it.endsWith("side") && byType["side"]?.isNotEmpty() == true) }

    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically, modifier = Modifier.fillMaxWidth()) {
            Column {
                Text(date, color = Color.White, fontWeight = FontWeight.Black)
                Text("$completion/4 angulos capturados", color = NexMuted)
            }
            TextButton(
                enabled = completion >= 4 && !loading,
                onClick = {
                    scope.launch {
                        loading = true
                        error = null
                        repository.analyzeSession(date)
                            .onSuccess { analysis = it.analysis }
                            .onFailure { error = friendlyError(it) }
                        loading = false
                    }
                },
                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon, disabledContentColor = NexMuted),
            ) {
                Text(if (loading) "Analisando..." else "Relatorio IA")
            }
        }
        if (completion < 4) {
            Text(
                "Complete um registro para gerar o relatorio.",
                color = NexMuted,
                modifier = Modifier.padding(top = 8.dp),
            )
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 10.dp)) {
            types.forEach { (type, label) ->
                val sent = byType[type]?.isNotEmpty() == true || (type.endsWith("side") && byType["side"]?.isNotEmpty() == true)
                AssistChip(onClick = {}, label = { Text(if (sent) "$label ✓" else label) })
            }
        }
        analysis?.let {
            Text(it.summary ?: it.message ?: "Analise concluida.", color = Color.White, modifier = Modifier.padding(top = 10.dp))
            it.nextRecommendations.takeIf { list -> list.isNotEmpty() }?.let { recs ->
                Text(recs.joinToString(prefix = "Recomendacoes: ", separator = " • "), color = NexMuted, modifier = Modifier.padding(top = 6.dp))
            }
        }
        error?.let { Text(it, color = Color(0xFFF59E0B), modifier = Modifier.padding(top = 10.dp)) }
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

@Composable
private fun EvolutionReportDialog(
    loading: Boolean,
    loadingMessage: String,
    report: EvolutionReportDto?,
    error: String?,
    onDismiss: () -> Unit,
) {
    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Relatorio inteligente de evolucao visual", fontWeight = FontWeight.Black) },
        text = {
            when {
                loading -> NexLoadingState(loadingMessage)
                error != null -> Text(error, color = Color(0xFFF59E0B))
                report != null -> LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.height(420.dp)) {
                    item {
                        val confidence = (report.generalConfidence * 100).toInt()
                        Text(report.status.replace('_', ' '), color = NexNeon, fontWeight = FontWeight.Black)
                        Text("Confianca geral: $confidence%", color = Color.White, modifier = Modifier.padding(top = 4.dp))
                        Text(
                            "Atual: ${report.comparedPeriod?.currentRecord ?: "-"} | Anterior: ${report.comparedPeriod?.previousRecord ?: "nao disponivel"}",
                            color = NexMuted,
                            modifier = Modifier.padding(top = 4.dp),
                        )
                    }
                    if (report.alertas.isNotEmpty()) {
                        item { ReportSection("Alertas", report.alertas) }
                    }
                    item {
                        Text("Dados confirmados", color = Color.White, fontWeight = FontWeight.Black)
                    }
                    if (report.confirmedData.isEmpty()) {
                        item { Text("Nenhum dado objetivo disponivel para calculo.", color = NexMuted) }
                    } else {
                        items(report.confirmedData) { item ->
                            Text(
                                "${item.metrica}: atual ${item.atual ?: "-"}" +
                                    (item.absoluteVariation?.let { " | variacao $it" } ?: "") +
                                    (item.percentVariation?.let { " ($it%)" } ?: ""),
                                color = NexMuted,
                            )
                        }
                    }
                    item {
                        Text("Comparacao visual", color = Color.White, fontWeight = FontWeight.Black)
                    }
                    if (report.visualObservations.isEmpty()) {
                        item { Text("Nao foi possivel confirmar mudanca visual com as evidencias atuais.", color = NexMuted) }
                    } else {
                        items(report.visualObservations) { item ->
                            Column {
                                Text(item.texto, color = NexMuted)
                                Text(
                                    "Fonte: ${item.fonte ?: "-"} | confianca ${((item.confianca ?: 0.0) * 100).toInt()}%",
                                    color = Color(0xFF6F7782),
                                    modifier = Modifier.padding(top = 2.dp),
                                )
                                item.limitacao?.let { Text(it, color = Color(0xFFF59E0B), modifier = Modifier.padding(top = 2.dp)) }
                            }
                        }
                    }
                    item { ReportSection("Limitacoes", report.limitacoes) }
                    item { ReportSection("Orientacoes", report.recomendacoes) }
                    item {
                        Text("Auditoria anti-alucinacao", color = Color.White, fontWeight = FontWeight.Black)
                        Text(
                            if (report.auditApproved) "Auditoria aprovada" else "Auditoria reprovada ou limitada",
                            color = if (report.auditApproved) NexNeon else Color(0xFFF59E0B),
                            modifier = Modifier.padding(top = 4.dp),
                        )
                        Text("Modelo: ${report.auditoria?.modelo ?: "-"}", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                    }
                }
            }
        },
        confirmButton = {
            TextButton(onClick = onDismiss, enabled = !loading, colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)) {
                Text("Fechar")
            }
        },
    )
}

@Composable
private fun EvolutionConsentDialog(
    consent: EvolutionReportConsentDto,
    onDismiss: () -> Unit,
    onAccept: () -> Unit,
) {
    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text(consent.copy?.title ?: "Analise de fotos corporais por IA", fontWeight = FontWeight.Black) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                Text(
                    consent.copy?.summary
                        ?: "Usaremos suas fotos apenas para gerar comparacoes visuais conservadoras. Nao fazemos diagnostico, reconhecimento biometrico ou estimativa de percentual de gordura por imagem.",
                )
                Text("Voce pode solicitar exclusao dos seus dados conforme a politica de privacidade.", color = Color(0xFF6F7782))
            }
        },
        confirmButton = {
            TextButton(onClick = onAccept, colors = ButtonDefaults.textButtonColors(contentColor = NexNeon)) {
                Text("Aceitar e gerar", fontWeight = FontWeight.Black)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss, colors = ButtonDefaults.textButtonColors(contentColor = NexMuted)) {
                Text("Cancelar")
            }
        },
    )
}

@Composable
private fun ReportSection(title: String, items: List<String>) {
    Column {
        Text(title, color = Color.White, fontWeight = FontWeight.Black)
        if (items.isEmpty()) {
            Text("-", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        } else {
            items.forEach {
                Text("- $it", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
            }
        }
    }
}

private fun photoTypeLabel(type: String): String = when (type) {
    "front" -> "Frontal"
    "side" -> "Lateral"
    "right_side" -> "Lateral direita"
    "left_side" -> "Lateral esquerda"
    "back" -> "Posterior"
    else -> "Outro"
}

private fun bodyViewLabel(type: String): String = when (type) {
    "front" -> "Frontal"
    "back" -> "Posterior"
    "side" -> "Lateral"
    else -> photoTypeLabel(type)
}

private fun formatMetric(value: Double?): String =
    value?.let {
        if (kotlin.math.abs(it) >= 10.0) {
            "%.0f".format(it)
        } else {
            "%.2f".format(it)
        }
    } ?: "-"

@Composable
private fun PhotoTypeDialog(
    onDismiss: () -> Unit,
    onSelected: (String) -> Unit,
) {
    val options = listOf(
        "front" to "Frontal",
        "right_side" to "Lateral direita",
        "left_side" to "Lateral esquerda",
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

private fun extensionForMimeType(mimeType: String): String? =
    when (mimeType.lowercase()) {
        "image/jpeg", "image/jpg" -> "jpg"
        "image/png" -> "png"
        "image/webp" -> "webp"
        else -> null
    }
