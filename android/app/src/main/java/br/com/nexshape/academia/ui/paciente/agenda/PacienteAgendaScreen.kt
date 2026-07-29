package br.com.nexshape.academia.ui.paciente.agenda

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import br.com.nexshape.academia.data.api.AppointmentDto
import br.com.nexshape.academia.ui.components.NexNeon
import java.text.SimpleDateFormat
import java.util.Locale

@Composable
fun PacienteAgendaScreen(
    modifier: Modifier = Modifier,
    viewModel: PacienteAgendaViewModel = viewModel()
) {
    val state by viewModel.uiState.collectAsState()

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10))
            .padding(16.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(text = "Meus Agendamentos", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Bold)
            Button(
                onClick = { /* TODO: Navegar para solicitação de slot */ },
                colors = ButtonDefaults.buttonColors(containerColor = NexNeon)
            ) {
                Text("Novo", color = Color.Black)
            }
        }
        Spacer(modifier = Modifier.height(16.dp))

        if (state.isLoading) {
            CircularProgressIndicator(color = NexNeon, modifier = Modifier.align(Alignment.CenterHorizontally))
        } else if (state.appointments.isEmpty()) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                Text("Nenhum agendamento encontrado.", color = Color.Gray)
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                items(state.appointments) { apt ->
                    AppointmentCard(apt)
                }
            }
        }
    }
}

@Composable
fun AppointmentCard(apt: AppointmentDto) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF131A24)),
        shape = RoundedCornerShape(12.dp)
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                Text(
                    text = apt.appointmentAt?.let { formatIsoDate(it) } ?: "Data Indisponível",
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 16.sp
                )
                Text(
                    text = apt.statusLabel ?: apt.status,
                    color = if (apt.status == "scheduled") NexNeon else Color.Gray,
                    fontWeight = FontWeight.Medium,
                    fontSize = 14.sp
                )
            }
            Spacer(modifier = Modifier.height(8.dp))
            Text(text = "Profissional: ${apt.professionalName ?: "N/A"}", color = Color.LightGray, fontSize = 14.sp)
            if (!apt.serviceType.isNullOrEmpty()) {
                Text(text = "Serviço: ${apt.serviceType}", color = Color.Gray, fontSize = 14.sp)
            }
        }
    }
}

fun formatIsoDate(isoString: String): String {
    return try {
        val parser = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.getDefault())
        val date = parser.parse(isoString)
        val formatter = SimpleDateFormat("dd/MM/yyyy 'às' HH:mm", Locale.getDefault())
        date?.let { formatter.format(it) } ?: isoString
    } catch (e: Exception) {
        isoString
    }
}
