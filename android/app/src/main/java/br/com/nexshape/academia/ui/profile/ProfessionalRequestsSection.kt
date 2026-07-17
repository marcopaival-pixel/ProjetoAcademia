package br.com.nexshape.academia.ui.profile

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.api.ProfessionalSearchResultDto
import br.com.nexshape.academia.data.api.ProfessionalPatientRequestDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.data.repository.ProfessionalRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexGreen
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPanel
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProfessionalRequestsSection(
    modifier: Modifier = Modifier,
    profile: ProfileDto
) {
    val scope = rememberCoroutineScope()
    val agendaRepository = remember { AgendaRepository() }
    val professionalRepository = remember { ProfessionalRepository() }

    var searchQuery by remember { mutableStateOf("") }
    var searchResults by remember { mutableStateOf<List<ProfessionalSearchResultDto>>(emptyList()) }
    var sentRequests by remember { mutableStateOf<List<ProfessionalPatientRequestDto>>(emptyList()) }
    var receivedRequests by remember { mutableStateOf<List<ProfessionalPatientRequestDto>>(emptyList()) }

    var loading by remember { mutableStateOf(false) }
    var actionLoadingId by remember { mutableStateOf<Int?>(null) }
    var message by remember { mutableStateOf<String?>(null) }
    var error by remember { mutableStateOf<String?>(null) }

    val isStudent = profile.isStudent
    val isProfessional = profile.isProfessional

    fun loadData() {
        scope.launch {
            loading = true
            error = null
            if (isStudent) {
                agendaRepository.studentRequests()
                    .onSuccess { sentRequests = it }
                    .onFailure { error = it.message }
            }
            if (isProfessional) {
                professionalRepository.requests()
                    .onSuccess { receivedRequests = it }
                    .onFailure { if (error == null) error = it.message }
            }
            loading = false
        }
    }

    LaunchedEffect(profile.id) {
        loadData()
    }

    Column(modifier = modifier.fillMaxWidth()) {
        Text(
            text = "Vínculos e Solicitações",
            color = Color.White,
            fontWeight = FontWeight.Black,
            modifier = Modifier.padding(bottom = 8.dp)
        )

        error?.let {
            Text(it, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(bottom = 8.dp))
        }
        message?.let {
            Text(it, color = NexNeon, modifier = Modifier.padding(bottom = 8.dp))
        }

        if (isStudent) {
            NexCard(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)) {
                Text("Buscar Profissional", color = Color.White, fontWeight = FontWeight.Bold)
                Spacer(modifier = Modifier.height(6.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    OutlinedTextField(
                        value = searchQuery,
                        onValueChange = { searchQuery = it },
                        label = { Text("Nome ou especialidade") },
                        modifier = Modifier.weight(1f),
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
                        )
                    )
                    Button(
                        onClick = {
                            scope.launch {
                                loading = true
                                agendaRepository.searchProfessionals(query = searchQuery.trim())
                                    .onSuccess { searchResults = it }
                                    .onFailure { error = it.message }
                                loading = false
                            }
                        },
                        enabled = !loading,
                        colors = ButtonDefaults.buttonColors(
                            containerColor = NexGreen,
                            contentColor = Color(0xFF04110D)
                        )
                    ) {
                        Text("Buscar", fontWeight = FontWeight.Bold)
                    }
                }

                if (searchResults.isNotEmpty()) {
                    Spacer(modifier = Modifier.height(10.dp))
                    Text("Resultados:", color = Color.White, fontWeight = FontWeight.Bold)
                    searchResults.forEach { pro ->
                        Row(
                            modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column(modifier = Modifier.weight(1f)) {
                                Text(pro.name, color = Color.White, fontWeight = FontWeight.Bold)
                                Text(pro.specialty ?: pro.profession ?: "Profissional", color = NexMuted)
                            }
                            Button(
                                onClick = {
                                    actionLoadingId = pro.id
                                    scope.launch {
                                        agendaRepository.createStudentRequest(pro.id, "Solicitação de vínculo via mobile")
                                            .onSuccess {
                                                message = "Solicitação enviada!"
                                                searchResults = searchResults.filter { it.id != pro.id }
                                                loadData()
                                            }
                                            .onFailure { error = it.message }
                                        actionLoadingId = null
                                    }
                                },
                                enabled = actionLoadingId != pro.id,
                                colors = ButtonDefaults.buttonColors(
                                    containerColor = NexNeon,
                                    contentColor = Color.Black
                                )
                            ) {
                                Text("Vincular", fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                }
            }

            if (sentRequests.isNotEmpty()) {
                NexCard(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)) {
                    Text("Solicitações Enviadas", color = Color.White, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.height(6.dp))
                    sentRequests.forEach { req ->
                        Row(
                            modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Column {
                                Text(req.professionalName ?: "Profissional", color = Color.White, fontWeight = FontWeight.Bold)
                                Text("Status: ${req.status.uppercase()}", color = if (req.status == "pending") NexNeon else NexMuted)
                            }
                        }
                    }
                }
            }
        }

        if (isProfessional) {
            if (receivedRequests.isNotEmpty()) {
                NexCard(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)) {
                    Text("Solicitações Pendentes", color = Color.White, fontWeight = FontWeight.Bold)
                    Spacer(modifier = Modifier.height(6.dp))
                    receivedRequests.forEach { req ->
                        Column(modifier = Modifier.fillMaxWidth().padding(vertical = 6.dp)) {
                            Text(req.patientName ?: "Aluno", color = Color.White, fontWeight = FontWeight.Bold)
                            req.message?.let { Text("\"$it\"", color = NexMuted) }
                            Row(
                                modifier = Modifier.fillMaxWidth().padding(top = 6.dp),
                                horizontalArrangement = Arrangement.spacedBy(10.dp)
                            ) {
                                Button(
                                    onClick = {
                                        actionLoadingId = req.id
                                        scope.launch {
                                            professionalRepository.approveRequest(req.id)
                                                .onSuccess {
                                                    message = "Solicitação aprovada!"
                                                    loadData()
                                                }
                                                .onFailure { error = it.message }
                                            actionLoadingId = null
                                        }
                                    },
                                    enabled = actionLoadingId != req.id,
                                    colors = ButtonDefaults.buttonColors(
                                        containerColor = NexGreen,
                                        contentColor = Color.Black
                                    )
                                ) {
                                    Text("Aprovar")
                                }
                                OutlinedButton(
                                    onClick = {
                                        actionLoadingId = req.id
                                        scope.launch {
                                            professionalRepository.rejectRequest(req.id)
                                                .onSuccess {
                                                    message = "Solicitação rejeitada."
                                                    loadData()
                                                }
                                                .onFailure { error = it.message }
                                            actionLoadingId = null
                                        }
                                    },
                                    enabled = actionLoadingId != req.id,
                                    colors = ButtonDefaults.outlinedButtonColors(contentColor = Color.Red)
                                ) {
                                    Text("Rejeitar")
                                }
                            }
                        }
                    }
                }
            } else {
                Text(
                    text = "Nenhuma solicitação de paciente pendente.",
                    color = NexMuted,
                    modifier = Modifier.padding(vertical = 8.dp)
                )
            }
        }
    }
}
