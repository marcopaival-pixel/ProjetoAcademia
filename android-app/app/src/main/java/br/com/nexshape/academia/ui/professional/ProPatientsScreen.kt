package br.com.nexshape.academia.ui.professional

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
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
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ProfessionalPatientDto
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import kotlinx.coroutines.launch

@Composable
fun ProPatientsScreen(
    modifier: Modifier = Modifier,
    onPatientSelected: () -> Unit = {},
) {
    val context = LocalContext.current
    val session = remember { ApiClient.sessionPreferences() }
    val repository = remember { ProfessionalRepository() }
    var patients by remember { mutableStateOf<List<ProfessionalPatientDto>>(emptyList()) }
    var selected by remember { mutableStateOf<ProfessionalPatientDto?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var search by remember { mutableStateOf("") }

    fun reload() {
        loading = true
        repository.patients(search.ifBlank { null })
            .onSuccess { patients = it; error = null; loading = false }
            .onFailure { error = it.message; loading = false }
    }

    LaunchedEffect(Unit) { reload() }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Meus alunos", style = MaterialTheme.typography.headlineSmall)
        OutlinedTextField(
            value = search,
            onValueChange = { search = it },
            label = { Text("Buscar") },
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 8.dp),
        )
        TextButton(onClick = { reload() }) { Text("Atualizar") }

        when {
            loading -> CircularProgressIndicator(modifier = Modifier.padding(top = 12.dp))
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            patients.isEmpty() -> Text("Nenhum aluno vinculado.", modifier = Modifier.padding(top = 12.dp))
            else -> LazyColumn(modifier = Modifier.padding(top = 8.dp)) {
                items(patients) { patient ->
                    Card(
                        modifier = Modifier
                            .fillMaxWidth()
                            .padding(vertical = 4.dp)
                            .clickable { selected = patient },
                    ) {
                        Column(Modifier.padding(12.dp)) {
                            Text(patient.name, style = MaterialTheme.typography.titleSmall)
                            patient.email?.let { Text(it, style = MaterialTheme.typography.bodySmall) }
                            Text("Status: ${patient.status ?: "—"}", style = MaterialTheme.typography.bodySmall)
                        }
                    }
                }
            }
        }
    }

    selected?.let { patient ->
        PatientDetailDialog(
            patient = patient,
            repository = repository,
            onDismiss = { selected = null },
            onSelectActive = {
                session.setActivePatient(patient.id, patient.name)
                onPatientSelected()
                selected = null
            },
        )
    }
}

@Composable
private fun PatientDetailDialog(
    patient: ProfessionalPatientDto,
    repository: ProfessionalRepository,
    onDismiss: () -> Unit,
    onSelectActive: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var detail by remember { mutableStateOf<ProfessionalPatientDto?>(null) }
    var loading by remember { mutableStateOf(true) }

    LaunchedEffect(patient.id) {
        repository.patientDetail(patient.id)
            .onSuccess { detail = it; loading = false }
            .onFailure { loading = false }
    }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text(patient.name) },
        text = {
            if (loading) {
                CircularProgressIndicator()
            } else {
                Column {
                    Text("Status: ${detail?.status ?: patient.status ?: "—"}")
                    detail?.goal?.let { Text("Objetivo: $it") }
                    detail?.lastWeightKg?.let { Text("Último peso: ${it} kg") }
                    detail?.lastBfPercent?.let { Text("Último BF: ${it}%") }
                    detail?.lastAssessmentDate?.let { Text("Última avaliação: $it") }
                }
            }
        },
        confirmButton = {
            TextButton(onClick = onSelectActive) { Text("Selecionar ativo") }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Fechar") }
        },
    )
}
