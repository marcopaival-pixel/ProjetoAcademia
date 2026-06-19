package br.com.nexshape.academia.ui.agenda

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.AppointmentDto
import br.com.nexshape.academia.data.api.AppointmentSlotDto
import br.com.nexshape.academia.data.api.CreateAppointmentRequest
import br.com.nexshape.academia.data.api.LinkedProfessionalDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AgendaScreen(modifier: Modifier = Modifier) {
    val repository = remember { AgendaRepository() }
    val scope = rememberCoroutineScope()
    var appointments by remember { mutableStateOf<List<AppointmentDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showScheduleDialog by remember { mutableStateOf(false) }

    fun reload() {
        scope.launch {
            loading = true
            repository.appointments()
                .onSuccess { appointments = it; error = null }
                .onFailure { error = it.message }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    Scaffold(
        modifier = modifier,
        floatingActionButton = {
            FloatingActionButton(onClick = { showScheduleDialog = true }) {
                Icon(Icons.Default.Add, contentDescription = "Agendar")
            }
        },
    ) { padding ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(padding)
                .padding(16.dp),
        ) {
            Text("Agenda", style = MaterialTheme.typography.headlineSmall)

            when {
                loading -> CircularProgressIndicator(modifier = Modifier.padding(top = 24.dp))
                error != null -> Text(error!!, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(top = 12.dp))
                appointments.isEmpty() -> Text(
                    "Nenhuma consulta agendada. Toque em + para marcar.",
                    modifier = Modifier.padding(top = 12.dp),
                )
                else -> LazyColumn(
                    modifier = Modifier.padding(top = 12.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                ) {
                    items(appointments) { item ->
                        AppointmentCard(item)
                    }
                }
            }
        }
    }

    if (showScheduleDialog) {
        ScheduleDialog(
            repository = repository,
            onDismiss = { showScheduleDialog = false },
            onScheduled = {
                showScheduleDialog = false
                reload()
            },
        )
    }
}

@Composable
private fun AppointmentCard(appointment: AppointmentDto) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(
                appointment.professionalName ?: "Profissional #${appointment.professionalId}",
                style = MaterialTheme.typography.titleSmall,
            )
            Text(appointment.appointmentAt, modifier = Modifier.padding(top = 4.dp))
            Text(
                appointment.statusLabel ?: appointment.status,
                style = MaterialTheme.typography.bodySmall,
                modifier = Modifier.padding(top = 4.dp),
            )
            appointment.serviceType?.let {
                Text("Tipo: $it", style = MaterialTheme.typography.bodySmall)
            }
        }
    }
}

@Composable
private fun ScheduleDialog(
    repository: AgendaRepository,
    onDismiss: () -> Unit,
    onScheduled: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var step by remember { mutableStateOf(ScheduleStep.Professional) }
    var professionals by remember { mutableStateOf<List<LinkedProfessionalDto>>(emptyList()) }
    var selectedProfessional by remember { mutableStateOf<LinkedProfessionalDto?>(null) }
    var selectedDate by remember { mutableStateOf(LocalDate.now().plusDays(1).toString()) }
    var slots by remember { mutableStateOf<List<AppointmentSlotDto>>(emptyList()) }
    var selectedSlot by remember { mutableStateOf<AppointmentSlotDto?>(null) }
    var notes by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        loading = true
        repository.professionals()
            .onSuccess { professionals = it }
            .onFailure { error = it.message }
        loading = false
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Agendar consulta") },
        text = {
            Column {
                error?.let {
                    Text(it, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(bottom = 8.dp))
                }
                when (step) {
                    ScheduleStep.Professional -> {
                        if (loading) {
                            CircularProgressIndicator()
                        } else if (professionals.isEmpty()) {
                            Text("Nenhum profissional vinculado. Peça ao seu profissional para vincular sua conta.")
                        } else {
                            professionals.forEach { pro ->
                                Text(
                                    "${pro.name}${pro.specialty?.let { " · $it" } ?: ""}",
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable {
                                            selectedProfessional = pro
                                            step = ScheduleStep.Date
                                            error = null
                                        }
                                        .padding(vertical = 8.dp),
                                )
                            }
                        }
                    }
                    ScheduleStep.Date -> {
                        Text("Profissional: ${selectedProfessional?.name}")
                        OutlinedTextField(
                            value = selectedDate,
                            onValueChange = { selectedDate = it },
                            label = { Text("Data (AAAA-MM-DD)") },
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(vertical = 8.dp),
                        )
                        TextButton(
                            onClick = {
                                val pro = selectedProfessional ?: return@TextButton
                                loading = true
                                scope.launch {
                                    repository.slots(pro.id, selectedDate)
                                        .onSuccess {
                                            slots = it.filter { slot -> slot.available }
                                            step = ScheduleStep.Slot
                                            error = null
                                        }
                                        .onFailure { error = it.message }
                                    loading = false
                                }
                            },
                        ) {
                            Text("Ver horários")
                        }
                    }
                    ScheduleStep.Slot -> {
                        Text("Data: $selectedDate")
                        if (slots.isEmpty()) {
                            Text("Nenhum horário disponível nesta data.")
                        } else {
                            slots.forEach { slot ->
                                Text(
                                    slot.time,
                                    modifier = Modifier
                                        .fillMaxWidth()
                                        .clickable {
                                            selectedSlot = slot
                                            step = ScheduleStep.Confirm
                                        }
                                        .padding(vertical = 6.dp),
                                )
                            }
                        }
                    }
                    ScheduleStep.Confirm -> {
                        Text("Profissional: ${selectedProfessional?.name}")
                        Text("Data/hora: $selectedDate ${selectedSlot?.time ?: ""}")
                        OutlinedTextField(
                            value = notes,
                            onValueChange = { notes = it },
                            label = { Text("Observações (opcional)") },
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(top = 8.dp),
                        )
                    }
                }
            }
        },
        confirmButton = {
            when (step) {
                ScheduleStep.Confirm -> TextButton(
                    onClick = {
                        val pro = selectedProfessional ?: return@TextButton
                        val slot = selectedSlot ?: return@TextButton
                        loading = true
                        scope.launch {
                            val appointmentAt = "$selectedDate ${slot.time}:00"
                            repository.schedule(
                                CreateAppointmentRequest(
                                    professionalId = pro.id,
                                    appointmentAt = appointmentAt,
                                    notes = notes.ifBlank { null },
                                ),
                            )
                                .onSuccess { onScheduled() }
                                .onFailure { error = it.message; loading = false }
                        }
                    },
                    enabled = !loading,
                ) {
                    Text("Confirmar")
                }
                else -> {}
            }
        },
        dismissButton = {
            Row {
                if (step != ScheduleStep.Professional) {
                    TextButton(onClick = {
                        step = when (step) {
                            ScheduleStep.Date -> ScheduleStep.Professional
                            ScheduleStep.Slot -> ScheduleStep.Date
                            ScheduleStep.Confirm -> ScheduleStep.Slot
                            else -> ScheduleStep.Professional
                        }
                    }) {
                        Text("Voltar")
                    }
                }
                TextButton(onClick = onDismiss) { Text("Cancelar") }
            }
        },
    )
}

private enum class ScheduleStep {
    Professional,
    Date,
    Slot,
    Confirm,
}
