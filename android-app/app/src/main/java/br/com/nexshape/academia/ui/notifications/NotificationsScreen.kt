package br.com.nexshape.academia.ui.notifications

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.MarkEmailUnread
import androidx.compose.material.icons.filled.Refresh
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
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.NotificationCountsDto
import br.com.nexshape.academia.data.repository.NotificationsRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMetricCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun NotificationsScreen(modifier: Modifier = Modifier) {
    val repository = remember { NotificationsRepository() }
    val scope = rememberCoroutineScope()
    var counts by remember { mutableStateOf<NotificationCountsDto?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.unreadCounts()
                .onSuccess {
                    counts = it
                    loading = false
                }
                .onFailure {
                    error = friendlyError(it)
                    loading = false
                }
        }
    }

    LaunchedEffect(Unit) { load() }

    NexShapeScreen(
        title = "Notificacoes",
        subtitle = "Mensagens e avisos nao lidos da plataforma web.",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load, enabled = !loading) {
                Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
            }
        },
    ) {
        when {
            loading -> NexLoadingState("Carregando notificacoes...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            counts == null -> NexEmptyState("Sem dados", "Nao foi possivel carregar as notificacoes.")
            counts?.total == 0 -> NexEmptyState("Tudo em dia", "Voce nao possui mensagens ou avisos pendentes.")
            else -> NotificationContent(counts!!)
        }
    }
}

@Composable
private fun NotificationContent(counts: NotificationCountsDto) {
    Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        NexMetricCard(
            title = "Pendentes",
            value = counts.total.toString(),
            icon = Icons.Default.MarkEmailUnread,
            modifier = Modifier.fillMaxWidth(),
        )
        NexCard {
            Text("Mensagens diretas", color = Color.White, fontWeight = FontWeight.Black)
            Text("${counts.messages} conversa(s) com mensagens nao lidas", color = NexMuted)
        }
        NexCard {
            Text("E-mails internos", color = Color.White, fontWeight = FontWeight.Black)
            Text("${counts.emails} item(ns) nao lidos", color = NexMuted)
        }
    }
}
