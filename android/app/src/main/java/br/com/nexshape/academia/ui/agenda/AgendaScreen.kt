package br.com.nexshape.academia.ui.agenda

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.FloatingActionButton
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.AppointmentDto
import br.com.nexshape.academia.data.api.AppointmentSlotDto
import br.com.nexshape.academia.data.api.CreateAppointmentRequest
import br.com.nexshape.academia.data.api.LinkedProfessionalDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPanel
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch
import java.time.LocalDate

@Composable
fun AgendaScreen(modifier: Modifier = Modifier) {
    val repository = remember { AgendaRepository() }
    val scope = rememberCoroutineScope()
    var appointments by remember { mutableStateOf<List<AppointmentDto>>(emptyList()) }
    var professionals by remember { mutableStateOf<List<LinkedProfessionalDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var showScheduleDialog by remember { mutableStateOf(false) }

    fun reload() {
        scope.launch {
            loading = true
            repository.appointments()
                .onSuccess { appointments = it; error = null }
                .onFailure { error = friendlyError(it) }
            repository.professionals()
                .onSuccess { professionals = it }
                .onFailure { if (error == null) error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    NexShapeScreen(
        title = "Agenda",
        subtitle = "Consultas e horarios vinculados aos seus mentores.",
        modifier = modifier,
        action = if (professionals.isNotEmpty()) {
            {
                FloatingActionButton(
                    onClick = { showScheduleDialog = true },
                    containerColor = NexNeon,
                    contentColor = Color(0xFF04110D),
                ) {
                    Icon(Icons.Default.Add, contentDescription = "Agendar")
                }
            }
        } else null,
    ) {
        when {
            loading -> NexLoadingState("Carregando agenda...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = { reload() })
            appointments.isEmpty() -> NexEmptyState(
                title = "Nenhuma consulta",
                message = if (professionals.isEmpty()) {
                    "Você está sem profissional vinculado. A agenda fica disponível quando houver um vínculo ativo."
                } else {
                    "Toque em + para marcar com um profissional vinculado."
                },
            )
            else -> LazyColumn(
                modifier = Modifier.padding(top = 14.dp),
                verticalArrangement = Arrangement.spacedBy(10.dp),
            ) {
                items(appointments, key = { it.id }) { item ->
                    AppointmentCard(item)
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
    NexCard(modifier = Modifier.fillMaxWidth()) {
        Text(
            appointment.professionalName ?: "Profissional #${appointment.professionalId}",
            color = Color.White,
            fontWeight = FontWeight.Black,
        )
        Text(appointment.appointmentAt, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        Text(appointment.statusLabel ?: appointment.status, color = NexNeon, modifier = Modifier.padding(top = 4.dp))
        appointment.serviceType?.let {
            Text("Tipo: $it", color = NexMuted)
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
    var notice by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        loading = true
        repository.professionals()
            .onSuccess { professionals = it }
            .onFailure { error = friendlyError(it) }
        loading = false
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0B1117),
        titleContentColor = Color.White,
        textContentColor = NexMuted,
        title = { Text("Agendar consulta", fontWeight = FontWeight.Black) },
        text = {
            Column {
                error?.let {
                    Text(it, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(bottom = 8.dp))
                }
                notice?.let {
                    Text(it, color = NexNeon, modifier = Modifier.padding(bottom = 8.dp))
                }
                when (step) {
                    ScheduleStep.Professional -> {
                        if (loading) {
                            CircularProgressIndicator(color = NexNeon)
                        } else if (professionals.isEmpty()) {
                            Text("Nenhum profissional vinculado.")
                        } else {
                            professionals.forEach { pro ->
                                Text(
                                    "${pro.name}${pro.specialty?.let { " - $it" } ?: ""}",
                                    color = Color.White,
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
                        Text("Profissional: ${selectedProfessional?.name}", color = Color.White)
                        ThemedTextField(
                            value = selectedDate,
                            onValueChange = { selectedDate = it },
                            label = "Data (AAAA-MM-DD)",
                            modifier = Modifier.padding(vertical = 8.dp),
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
                                        .onFailure { error = friendlyError(it) }
                                    loading = false
                                }
                            },
                            colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                        ) {
                            Text("Ver horarios")
                        }
                    }
                    ScheduleStep.Slot -> {
                        Text("Data: $selectedDate", color = Color.White)
                        if (slots.isEmpty()) {
                            Text("Nenhum horario disponivel nesta data.")
                            TextButton(
                                onClick = {
                                    val pro = selectedProfessional ?: return@TextButton
                                    loading = true
                                    scope.launch {
                                        repository.joinWaitlist(pro.id, selectedDate)
                                            .onSuccess {
                                                notice = "Voce entrou na lista de espera para esta data."
                                                error = null
                                                loading = false
                                            }
                                            .onFailure {
                                                error = friendlyError(it)
                                                loading = false
                                            }
                                    }
                                },
                                enabled = !loading,
                                colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                            ) {
                                Text("Entrar na lista de espera")
                            }
                        } else {
                            slots.forEach { slot ->
                                Text(
                                    slot.time,
                                    color = Color.White,
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
                        Text("Profissional: ${selectedProfessional?.name}", color = Color.White)
                        Text("Data/hora: $selectedDate ${selectedSlot?.time ?: ""}", color = NexMuted)
                        ThemedTextField(
                            value = notes,
                            onValueChange = { notes = it },
                            label = "Observacoes (opcional)",
                            modifier = Modifier.padding(top = 8.dp),
                        )
                    }
                }
            }
        },
        confirmButton = {
            if (step == ScheduleStep.Confirm) {
                TextButton(
                    onClick = {
                        val pro = selectedProfessional ?: return@TextButton
                        val slot = selectedSlot ?: return@TextButton
                        loading = true
                        scope.launch {
                            repository.schedule(
                                CreateAppointmentRequest(
                                    professionalId = pro.id,
                                    appointmentAt = "$selectedDate ${slot.time}:00",
                                    notes = notes.ifBlank { null },
                                ),
                            )
                                .onSuccess { onScheduled() }
                                .onFailure { error = friendlyError(it); loading = false }
                        }
                    },
                    enabled = !loading,
                    colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                ) {
                    Text("Confirmar", fontWeight = FontWeight.Black)
                }
            }
        },
        dismissButton = {
            Row {
                if (step != ScheduleStep.Professional) {
                    TextButton(
                        onClick = {
                            step = when (step) {
                                ScheduleStep.Date -> ScheduleStep.Professional
                                ScheduleStep.Slot -> ScheduleStep.Date
                                ScheduleStep.Confirm -> ScheduleStep.Slot
                                else -> ScheduleStep.Professional
                            }
                        },
                        colors = ButtonDefaults.textButtonColors(contentColor = NexMuted),
                    ) {
                        Text("Voltar")
                    }
                }
                TextButton(onClick = onDismiss, colors = ButtonDefaults.textButtonColors(contentColor = NexMuted)) {
                    Text("Cancelar")
                }
            }
        },
    )
}

@Composable
private fun ThemedTextField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    modifier: Modifier = Modifier,
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        singleLine = true,
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

private enum class ScheduleStep {
    Professional,
    Date,
    Slot,
    Confirm,
}
