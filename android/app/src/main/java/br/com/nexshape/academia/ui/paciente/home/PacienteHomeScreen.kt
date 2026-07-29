package br.com.nexshape.academia.ui.paciente.home

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Assignment
import androidx.compose.material.icons.filled.CalendarMonth

import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import br.com.nexshape.academia.data.api.PatientDashboardSummaryDto
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.paciente.agenda.formatIsoDate

@Composable
fun PacienteHomeScreen(
    modifier: Modifier = Modifier,
    authRepository: AuthRepository,
    onOpenEvolution: () -> Unit,
    onOpenAgenda: () -> Unit,
    onOpenDocuments: () -> Unit,
    viewModel: PacienteDashboardViewModel = viewModel()
) {
    val state by viewModel.uiState.collectAsState()

    if (state.isLoading) {
        Box(modifier = modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator(color = NexNeon)
        }
        return
    }

    LazyColumn(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10))
            .padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(20.dp)
    ) {
        item {
            HeaderSection()
        }



        item {
            UpcomingAppointmentsSection(state.summary, onClick = onOpenAgenda)
        }

        item {
            RecentOrientationsSection(onClick = onOpenDocuments)
        }
    }
}

@Composable
private fun HeaderSection() {
    Column {
        Text(
            text = "Resumo do Acompanhamento",
            color = Color.White,
            fontSize = 24.sp,
            fontWeight = FontWeight.Bold
        )
        Text(
            text = "Visão Geral",
            color = NexNeon,
            fontSize = 14.sp
        )
    }
}



@Composable
private fun UpcomingAppointmentsSection(summary: PatientDashboardSummaryDto?, onClick: () -> Unit) {
    Column {
        Text(
            text = "Próximos Compromissos",
            color = Color.White,
            fontWeight = FontWeight.Bold,
            fontSize = 18.sp,
            modifier = Modifier.padding(bottom = 12.dp)
        )
        
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color(0xFF151A24)),
            shape = RoundedCornerShape(12.dp)
        ) {
            Row(
                modifier = Modifier.padding(16.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier
                        .size(48.dp)
                        .clip(RoundedCornerShape(12.dp))
                        .background(Brush.linearGradient(listOf(Color(0xFF0288D1), Color(0xFF01579B)))),
                    contentAlignment = Alignment.Center
                ) {
                    Icon(Icons.Default.CalendarMonth, contentDescription = "Agenda", tint = Color.White)
                }
                Spacer(modifier = Modifier.width(16.dp))
                Column(modifier = Modifier.weight(1f)) {
                    if (summary?.nextAppointment != null) {
                        Text(
                            text = "Consulta Agendada",
                            color = Color.White,
                            fontWeight = FontWeight.SemiBold
                        )
                        Text(
                            text = formatIsoDate(summary.nextAppointment),
                            color = NexMuted,
                            fontSize = 13.sp
                        )
                    } else {
                        Text(
                            text = "Nenhuma consulta marcada",
                            color = Color.White,
                            fontWeight = FontWeight.SemiBold
                        )
                    }
                }
                TextButton(onClick = onClick) {
                    Text("Ver", color = NexNeon)
                }
            }
        }
    }
}

@Composable
private fun RecentOrientationsSection(onClick: () -> Unit) {
    Column {
        Text(
            text = "Orientações e Arquivos",
            color = Color.White,
            fontWeight = FontWeight.Bold,
            fontSize = 18.sp,
            modifier = Modifier.padding(bottom = 12.dp)
        )
        
        Card(
            modifier = Modifier.fillMaxWidth(),
            colors = CardDefaults.cardColors(containerColor = Color(0xFF151A24)),
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(modifier = Modifier.padding(16.dp)) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Assignment, contentDescription = "Documento", tint = Color(0xFF4ADE80))
                    Spacer(modifier = Modifier.width(8.dp))
                    Text("Acesso a Laudos e Receitas", color = Color.White, fontWeight = FontWeight.Medium)
                }
                Text(
                    text = "Consulte os documentos emitidos pelo profissional.",
                    color = NexMuted,
                    fontSize = 13.sp,
                    modifier = Modifier.padding(start = 32.dp, top = 4.dp)
                )
                Spacer(modifier = Modifier.height(8.dp))
                Button(
                    onClick = onClick,
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF0F172A)),
                    modifier = Modifier.align(Alignment.End)
                ) {
                    Text("Ver todos", color = NexNeon)
                }
            }
        }
    }
}
