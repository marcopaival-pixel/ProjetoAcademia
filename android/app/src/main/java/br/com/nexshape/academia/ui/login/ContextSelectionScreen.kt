package br.com.nexshape.academia.ui.login

import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.MedicalServices
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import br.com.nexshape.academia.data.api.ApiClient
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

data class ContextOption(
    val id: Int,
    val name: String,
    val type: String,
)

@Composable
fun ContextSelectionScreen(
    userName: String,
    onContextSelected: (contextId: Int) -> Unit,
) {
    var loading by remember { mutableStateOf(true) }
    var options by remember { mutableStateOf<List<ContextOption>>(emptyList()) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        withContext(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientLinks()
                options = response.data.map { link ->
                        ContextOption(
                            id = link.id,
                            name = link.label ?: link.professionalName ?: "Acompanhamento",
                            type = link.type ?: "clinic",
                        )
                    }
                loading = false
            } catch (e: Exception) {
                error = e.localizedMessage ?: "Erro ao carregar vínculos."
                loading = false
            }
        }
    }

    if (loading) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            CircularProgressIndicator()
        }
        return
    }

    if (error != null || options.isEmpty()) {
        Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
            Text(
                text = error ?: "Nenhum vínculo clínico ativo encontrado.",
                color = Color.Red,
            )
        }
        return
    }

    ProfileSelectorScreen(
        userName = userName,
        titleText = "Como você deseja acessar?",
        cards = options.map { opt ->
            ProfileCardData(
                id = opt.id.toString(),
                title = opt.name,
                subtitle = if (opt.type == "personal") "Meu perfil pessoal" else "Meu acompanhamento clínico",
                features = emptyList(),
                icon = Icons.Default.MedicalServices,
                gradient = listOf(Color(0xFF0288D1), Color(0xFF01579B)),
            )
        },
        onCardSelected = { card ->
            onContextSelected(card.id.toInt())
        },
    )
}
