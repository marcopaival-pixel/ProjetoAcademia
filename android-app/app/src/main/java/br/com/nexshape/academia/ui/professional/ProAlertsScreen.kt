package br.com.nexshape.academia.ui.professional

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ProfessionalAlertDto
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import kotlinx.coroutines.launch

@Composable
fun ProAlertsScreen(modifier: Modifier = Modifier) {
    val repository = remember { ProfessionalRepository() }
    val scope = rememberCoroutineScope()
    var alerts by remember { mutableStateOf<List<ProfessionalAlertDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun reload() {
        scope.launch {
            loading = true
            repository.alerts(unreadOnly = true)
                .onSuccess { alerts = it; error = null }
                .onFailure { error = it.message }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Alertas", style = MaterialTheme.typography.headlineSmall)
        Text("Toque para marcar como lido", style = MaterialTheme.typography.bodySmall)

        when {
            loading -> CircularProgressIndicator(modifier = Modifier.padding(top = 12.dp))
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            alerts.isEmpty() -> Text("Nenhum alerta pendente.", modifier = Modifier.padding(top = 12.dp))
            else -> LazyColumn(modifier = Modifier.padding(top = 8.dp)) {
                items(alerts) { alert ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 4.dp)
                            .clickable {
                                scope.launch {
                                    repository.markAlertRead(alert.id)
                                        .onSuccess { reload() }
                                        .onFailure { error = it.message }
                                }
                            },
                    ) {
                        Column(Modifier.padding(12.dp)) {
                            Text(alert.patientName ?: "Aluno #${alert.patientId}", style = MaterialTheme.typography.titleSmall)
                            Text(alert.message)
                            alert.severity?.let {
                                Text("Severidade: $it", style = MaterialTheme.typography.bodySmall)
                            }
                        }
                    }
                }
            }
        }
    }
}
