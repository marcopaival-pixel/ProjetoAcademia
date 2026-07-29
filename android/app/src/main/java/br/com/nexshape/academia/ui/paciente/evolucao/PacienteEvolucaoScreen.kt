package br.com.nexshape.academia.ui.paciente.evolucao

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
import br.com.nexshape.academia.data.api.EvolutionAssessmentDto
import br.com.nexshape.academia.ui.components.NexNeon

@Composable
fun PacienteEvolucaoScreen(
    modifier: Modifier = Modifier,
    viewModel: PacienteEvolucaoViewModel = viewModel()
) {
    val state by viewModel.uiState.collectAsState()

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10))
            .padding(16.dp)
    ) {
        Text(text = "Evolução Corporativa", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(16.dp))

        if (state.isLoading) {
            CircularProgressIndicator(color = NexNeon, modifier = Modifier.align(Alignment.CenterHorizontally))
        } else {
            if (state.latest != null) {
                Card(
                    modifier = Modifier.fillMaxWidth(),
                    colors = CardDefaults.cardColors(containerColor = Color(0xFF131A24)),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Column(modifier = Modifier.padding(16.dp)) {
                        Text("Última Avaliação (${state.latest?.assessmentDate ?: "N/A"})", color = NexNeon, fontWeight = FontWeight.Bold)
                        Spacer(modifier = Modifier.height(8.dp))
                        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                            Text("Peso: ${state.latest?.weightKg ?: "--"} kg", color = Color.White)
                            Text("Gordura: ${state.latest?.bfPercent ?: "--"} %", color = Color.White)
                        }
                    }
                }
                Spacer(modifier = Modifier.height(24.dp))
            }
            
            Text(text = "Histórico de Medidas", color = Color.White, fontSize = 18.sp, fontWeight = FontWeight.SemiBold)
            Spacer(modifier = Modifier.height(8.dp))

            if (state.assessments.isEmpty()) {
                Text(text = "Nenhum dado de evolução encontrado.", color = Color.Gray)
            } else {
                LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    items(state.assessments) { item ->
                        AssessmentItemRow(item)
                    }
                }
            }
        }
    }
}

@Composable
fun AssessmentItemRow(item: EvolutionAssessmentDto) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .background(Color(0xFF131A24), shape = RoundedCornerShape(8.dp))
            .padding(12.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(text = item.assessmentDate ?: "Data", color = Color.White, fontSize = 14.sp)
        Text(text = "${item.weightKg} kg", color = Color.White, fontSize = 14.sp)
        Text(text = "${item.bfPercent}% BF", color = Color.White, fontSize = 14.sp)
    }
}
