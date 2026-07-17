package br.com.nexshape.academia.ui.health

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.BodyAssessmentDto
import br.com.nexshape.academia.data.api.MedicalDocumentsData
import br.com.nexshape.academia.data.repository.EvolutionRepository
import br.com.nexshape.academia.data.repository.MedicalDocumentsRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun ExamsMeasuresScreen(modifier: androidx.compose.ui.Modifier = androidx.compose.ui.Modifier) {
    val evolutionRepository = remember { EvolutionRepository() }
    val documentsRepository = remember { MedicalDocumentsRepository() }
    val scope = rememberCoroutineScope()

    var assessments by remember { mutableStateOf<List<BodyAssessmentDto>>(emptyList()) }
    var documents by remember { mutableStateOf<MedicalDocumentsData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            evolutionRepository.assessments()
                .onSuccess { assessments = it }
                .onFailure { error = friendlyError(it) }
            documentsRepository.getDocuments()
                .onSuccess { documents = it }
                .onFailure { if (error == null) error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { load() }

    NexShapeScreen(
        title = "Avaliacoes e Exames",
        subtitle = "Medidas, exames, documentos clinicos e evolucao em um so lugar.",
        modifier = modifier,
    ) {
        when {
            loading -> NexLoadingState("Carregando avaliacoes e exames...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            assessments.isEmpty() && documents == null -> NexEmptyState("Sem registros", "Nenhum exame ou medida encontrado.")
            else -> LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                item {
                    NexCard {
                        Icon(Icons.Default.MonitorHeart, contentDescription = null, tint = NexNeon)
                        Text("Medidas recentes", color = Color.White, fontWeight = FontWeight.Black)
                    }
                }
                if (assessments.isEmpty()) {
                    item { NexEmptyState("Sem avaliacoes", "Registre medidas em Minha Evolucao ou aguarde seu profissional.") }
                } else {
                    items(assessments.take(5), key = { it.id }) { item ->
                        NexCard {
                            Text(item.assessmentDate, color = Color.White, fontWeight = FontWeight.Black)
                            item.weightKg?.let { Text("Peso: $it kg", color = NexMuted) }
                            item.bfPercent?.let { Text("Gordura: $it%", color = NexMuted) }
                            item.musclePercent?.let { Text("Musculo: $it%", color = NexMuted) }
                            item.notes?.let { Text(it, color = NexMuted) }
                        }
                    }
                }
                item {
                    NexCard {
                        Icon(Icons.Default.Description, contentDescription = null, tint = NexNeon)
                        Text("Documentos e exames", color = Color.White, fontWeight = FontWeight.Black)
                        val count = documents?.let { it.reports.size + it.prescriptions.size + it.certificates.size } ?: 0
                        Text("$count documento(s) disponiveis.", color = NexMuted)
                        documents?.reports.orEmpty().take(3).forEach {
                            Text("Laudo: ${it.title}", color = NexMuted)
                        }
                        documents?.prescriptions.orEmpty().take(2).forEach {
                            Text("Receita: ${it.title}", color = NexMuted)
                        }
                        documents?.certificates.orEmpty().take(2).forEach {
                            Text("Atestado: ${it.title}", color = NexMuted)
                        }
                    }
                }
            }
        }
    }
}
