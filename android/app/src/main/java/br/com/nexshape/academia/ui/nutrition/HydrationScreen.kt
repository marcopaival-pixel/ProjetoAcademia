package br.com.nexshape.academia.ui.nutrition

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FilterChip
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.HydrationStatusData
import br.com.nexshape.academia.data.repository.NutritionRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HydrationScreen(modifier: Modifier = Modifier) {
    val repository = remember { NutritionRepository() }
    val scope = rememberCoroutineScope()
    var hydration by remember { mutableStateOf<HydrationStatusData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun reload() {
        scope.launch {
            loading = true
            repository.hydrationStatus()
                .onSuccess { 
                    hydration = it 
                    error = null
                }
                .onFailure { error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    NexShapeScreen(
        title = "Hidratação",
        subtitle = "Acompanhe seu consumo diário de água",
        modifier = modifier,
    ) {
        when {
            loading -> {
                Column(modifier = Modifier.fillMaxSize(), verticalArrangement = Arrangement.Center, horizontalAlignment = Alignment.CenterHorizontally) {
                    CircularProgressIndicator(color = NexNeon)
                }
            }
            error != null -> NexErrorState(error.orEmpty(), onRetry = { reload() })
            else -> {
                NexCard {
                    Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                        Column(modifier = Modifier.weight(1f)) {
                            Text("Nex Hydra", color = Color.White, fontWeight = FontWeight.Black)
                            val consumed = hydration?.consumedMl ?: 0
                            val target = hydration?.targetMl ?: 0
                            Text("$consumed ml / $target ml", color = NexNeon, modifier = Modifier.padding(top = 4.dp))
                            Text("${hydration?.percentage ?: 0}% da meta", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                            hydration?.let {
                                val statusLabel = when (it.status) {
                                    "excellent" -> "Excelente"
                                    "good" -> "Bom"
                                    "behind" -> "Atrasado"
                                    else -> it.status
                                }
                                Text("Status: $statusLabel (Esperado agora: ${it.expectedNowMl} ml)", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                                Text(
                                    if (it.isAuto) "Meta calculada automaticamente" else "Meta definida manualmente",
                                    color = NexMuted,
                                    modifier = Modifier.padding(top = 2.dp),
                                )
                            }
                        }
                        Icon(Icons.Default.WaterDrop, contentDescription = null, tint = NexNeon)
                    }
                    Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
                        listOf(250, 350, 500).forEach { amount ->
                            FilterChip(
                                selected = false,
                                onClick = { 
                                    scope.launch {
                                        repository.addWater(amount)
                                            .onSuccess { reload() }
                                            .onFailure { error = friendlyError(it) }
                                    }
                                },
                                label = { Text("+${amount}ml") },
                            )
                        }
                    }
                    hydration?.entries?.forEach { entry ->
                        Row(
                            horizontalArrangement = Arrangement.SpaceBetween,
                            verticalAlignment = Alignment.CenterVertically,
                            modifier = Modifier
                                .fillMaxWidth()
                                .padding(top = 8.dp),
                        ) {
                            Text("${entry.amountMl} ml adicionado", color = NexMuted)
                            IconButton(onClick = { 
                                scope.launch {
                                    repository.deleteWaterEntry(entry.id)
                                        .onSuccess { reload() }
                                        .onFailure { error = friendlyError(it) }
                                }
                             }) {
                                Icon(Icons.Default.Delete, contentDescription = "Excluir agua", tint = Color.White)
                            }
                        }
                    }
                }
            }
        }
    }
}
