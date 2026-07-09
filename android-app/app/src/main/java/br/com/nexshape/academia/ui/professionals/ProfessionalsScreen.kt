package br.com.nexshape.academia.ui.professionals

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
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
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.LinkedProfessionalDto
import br.com.nexshape.academia.data.repository.AgendaRepository

@Composable
fun ProfessionalsScreen(modifier: Modifier = Modifier) {
    val repository = remember { AgendaRepository() }
    var professionals by remember { mutableStateOf<List<LinkedProfessionalDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        repository.professionals()
            .onSuccess {
                professionals = it
                error = null
            }
            .onFailure { error = it.message }
        loading = false
    }

    Column(
        modifier = modifier
            .fillMaxSize()
            .padding(16.dp),
    ) {
        Text("Profissionais", style = MaterialTheme.typography.headlineSmall)
        Text(
            "Mentores vinculados ao seu acompanhamento.",
            style = MaterialTheme.typography.bodySmall,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            modifier = Modifier.padding(top = 4.dp),
        )

        when {
            loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator()
            }
            error != null -> Text(
                error!!,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(top = 16.dp),
            )
            professionals.isEmpty() -> EmptyProfessionals()
            else -> LazyColumn(
                contentPadding = PaddingValues(vertical = 16.dp),
                verticalArrangement = Arrangement.spacedBy(10.dp),
            ) {
                items(professionals, key = { it.id }) { professional ->
                    ProfessionalCard(professional)
                }
            }
        }
    }
}

@Composable
private fun EmptyProfessionals() {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .padding(top = 16.dp),
    ) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text("Nenhum profissional vinculado", style = MaterialTheme.typography.titleMedium)
            Text(
                "A busca de novos profissionais existe no painel web, mas ainda nao ha endpoint API v1 mobile para criar essa tela com seguranca.",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurfaceVariant,
                modifier = Modifier.padding(top = 6.dp),
            )
        }
    }
}

@Composable
private fun ProfessionalCard(professional: LinkedProfessionalDto) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(professional.name, style = MaterialTheme.typography.titleMedium)
            professional.specialty?.takeIf { it.isNotBlank() }?.let {
                Text(it, style = MaterialTheme.typography.bodyMedium)
            }
            professional.email?.takeIf { it.isNotBlank() }?.let {
                Text(it, style = MaterialTheme.typography.bodySmall)
            }
            professional.branding?.clinicName?.takeIf { it.isNotBlank() }?.let {
                Text(
                    it,
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant,
                    modifier = Modifier.padding(top = 4.dp),
                )
            }
            val serviceTypes = professional.serviceTypes.orEmpty().filter { it.isNotBlank() }
            if (serviceTypes.isNotEmpty()) {
                Text(
                    serviceTypes.joinToString(prefix = "Servicos: "),
                    style = MaterialTheme.typography.bodySmall,
                    modifier = Modifier.padding(top = 6.dp),
                )
            }
        }
    }
}
