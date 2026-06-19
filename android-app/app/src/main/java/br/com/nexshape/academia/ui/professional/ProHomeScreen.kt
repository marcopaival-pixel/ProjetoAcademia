package br.com.nexshape.academia.ui.professional

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ProfessionalDashboardStatsDto
import br.com.nexshape.academia.data.repository.ProfessionalRepository

@Composable
fun ProHomeScreen(modifier: Modifier = Modifier) {
    val repository = remember { ProfessionalRepository() }
    var stats by remember { mutableStateOf<ProfessionalDashboardStatsDto?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        repository.dashboard()
            .onSuccess { stats = it; loading = false }
            .onFailure { error = it.message; loading = false }
    }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Painel Profissional", style = MaterialTheme.typography.headlineSmall)

        when {
            loading -> CircularProgressIndicator(modifier = Modifier.padding(top = 24.dp))
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(top = 12.dp))
            stats != null -> {
                val cards = listOf(
                    "Alunos" to stats!!.totalPatients.toString(),
                    "Ativos (30d)" to stats!!.activePatients30d.toString(),
                    "Consultas hoje" to stats!!.todayAppointments.toString(),
                    "Pendentes" to stats!!.pendingAppointments.toString(),
                    "Avaliações/mês" to stats!!.assessmentsThisMonth.toString(),
                    "Planos ativos" to stats!!.activeTrainingPlans.toString(),
                    "Alertas" to stats!!.unreadAlerts.toString(),
                )
                LazyVerticalGrid(
                    columns = GridCells.Fixed(2),
                    modifier = Modifier.padding(top = 16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                ) {
                    items(cards) { (label, value) ->
                        StatCard(label = label, value = value)
                    }
                }
            }
        }
    }
}

@Composable
private fun StatCard(label: String, value: String) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(value, style = MaterialTheme.typography.headlineMedium)
            Text(label, style = MaterialTheme.typography.bodySmall)
        }
    }
}
