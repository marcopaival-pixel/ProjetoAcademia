package br.com.nexshape.academia.ui.professional

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.DropdownMenu
import androidx.compose.material3.DropdownMenuItem
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
import br.com.nexshape.academia.data.api.ProfessionalAppointmentDto
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

private val STATUS_OPTIONS = listOf(
    "scheduled" to "Agendado",
    "confirmed" to "Confirmado",
    "in_progress" to "Em atendimento",
    "finished" to "Finalizado",
    "cancelled" to "Cancelado",
    "no_show" to "Faltou",
)

@Composable
fun ProAgendaScreen(modifier: Modifier = Modifier) {
    val repository = remember { ProfessionalRepository() }
    val scope = rememberCoroutineScope()
    var appointments by remember { mutableStateOf<List<ProfessionalAppointmentDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var statusMenuFor by remember { mutableStateOf<Int?>(null) }

    fun reload() {
        scope.launch {
            loading = true
            repository.appointments(date = LocalDate.now().toString())
                .onSuccess { appointments = it; error = null }
                .onFailure { error = it.message }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Agenda de hoje", style = MaterialTheme.typography.headlineSmall)
        Button(onClick = { reload() }, modifier = Modifier.padding(vertical = 8.dp)) {
            Text("Atualizar")
        }

        when {
            loading -> CircularProgressIndicator()
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            appointments.isEmpty() -> Text("Sem consultas para hoje.")
            else -> LazyColumn {
                items(appointments) { item ->
                    Card(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
                        Column(Modifier.padding(12.dp)) {
                            Text(item.patientName ?: "Aluno #${item.patientId}", style = MaterialTheme.typography.titleSmall)
                            Text(item.appointmentAt)
                            Text(item.statusLabel ?: item.status, style = MaterialTheme.typography.bodySmall)
                            item.serviceType?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                            Button(
                                onClick = { statusMenuFor = item.id },
                                modifier = Modifier.padding(top = 8.dp),
                            ) {
                                Text("Alterar status")
                            }
                            DropdownMenu(
                                expanded = statusMenuFor == item.id,
                                onDismissRequest = { statusMenuFor = null },
                            ) {
                                STATUS_OPTIONS.forEach { (code, label) ->
                                    DropdownMenuItem(
                                        text = { Text(label) },
                                        onClick = {
                                            statusMenuFor = null
                                            scope.launch {
                                                repository.updateAppointmentStatus(item.id, code)
                                                    .onSuccess { reload() }
                                                    .onFailure { error = it.message }
                                            }
                                        },
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
