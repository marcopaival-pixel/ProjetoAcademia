package br.com.nexshape.academia.ui.clinical

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CalendarMonth
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
import br.com.nexshape.academia.data.api.AppointmentDto
import br.com.nexshape.academia.data.api.AssessmentSummaryDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.data.repository.EvolutionRepository
import br.com.nexshape.academia.data.repository.MedicalDocumentsRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun ClinicalConductScreen(modifier: androidx.compose.ui.Modifier = androidx.compose.ui.Modifier) {
    val evolutionRepository = remember { EvolutionRepository() }
    val agendaRepository = remember { AgendaRepository() }
    val documentsRepository = remember { MedicalDocumentsRepository() }
    val scope = rememberCoroutineScope()

    var summary by remember { mutableStateOf<AssessmentSummaryDto?>(null) }
    var appointments by remember { mutableStateOf<List<AppointmentDto>>(emptyList()) }
    var documentsCount by remember { mutableStateOf(0) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            evolutionRepository.assessmentSummary()
                .onSuccess { summary = it }
                .onFailure { error = friendlyError(it) }
            agendaRepository.appointments()
                .onSuccess { appointments = it.take(3) }
                .onFailure { if (error == null) error = friendlyError(it) }
            documentsRepository.getDocuments()
                .onSuccess { documentsCount = it.reports.size + it.prescriptions.size + it.certificates.size }
                .onFailure { if (error == null) error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { load() }

    NexShapeScreen(
        title = "Conduta Clinica",
        subtitle = "Resumo clinico para acompanhamento, orientacoes e proximas acoes.",
        modifier = modifier,
    ) {
        when {
            loading -> NexLoadingState("Carregando conduta clinica...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            else -> LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                item {
                    NexCard {
                        Icon(Icons.Default.MonitorHeart, contentDescription = null, tint = NexNeon)
                        Text("Resumo corporal", color = Color.White, fontWeight = FontWeight.Black)
                        Text("Ultima avaliacao: ${summary?.lastAssessmentDate ?: "-"}", color = NexMuted)
                        Text("Proxima prevista: ${summary?.nextAssessmentDate ?: "-"}", color = NexMuted)
                        Text("Peso atual: ${summary?.currentWeightKg?.let { "$it kg" } ?: "-"}", color = NexMuted)
                        Text("Gordura atual: ${summary?.currentBfPercent?.let { "$it%" } ?: "-"}", color = NexMuted)
                    }
                }
                item {
                    NexCard {
                        Icon(Icons.Default.CalendarMonth, contentDescription = null, tint = NexNeon)
                        Text("Agenda clinica", color = Color.White, fontWeight = FontWeight.Black)
                        if (appointments.isEmpty()) {
                            Text("Nenhuma consulta futura encontrada.", color = NexMuted)
                        } else {
                            appointments.forEach {
                                Text("${it.appointmentAt} - ${it.serviceType ?: "Consulta"}", color = NexMuted)
                            }
                        }
                    }
                }
                item {
                    NexCard {
                        Icon(Icons.Default.Description, contentDescription = null, tint = NexNeon)
                        Text("Documentos clinicos", color = Color.White, fontWeight = FontWeight.Black)
                        Text("$documentsCount documento(s) disponiveis entre laudos, receitas e atestados.", color = NexMuted)
                    }
                }
            }
        }
    }
}
