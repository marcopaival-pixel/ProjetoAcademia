package br.com.nexshape.academia.ui.messages

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
import androidx.compose.material.icons.filled.ArrowBack
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
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
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ConversationDto
import br.com.nexshape.academia.data.api.InternalMessageDto
import br.com.nexshape.academia.data.repository.InternalMessagesRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun MessagesScreen(modifier: Modifier = Modifier) {
    val repository = remember { InternalMessagesRepository() }
    val scope = rememberCoroutineScope()
    var conversations by remember { mutableStateOf<List<ConversationDto>>(emptyList()) }
    var selected by remember { mutableStateOf<ConversationDto?>(null) }
    var messages by remember { mutableStateOf<List<InternalMessageDto>>(emptyList()) }
    var input by remember { mutableStateOf("") }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }

    fun loadConversations() {
        loading = true
        error = null
        scope.launch {
            repository.conversations()
                .onSuccess { conversations = it }
                .onFailure { error = friendlyError(it) }
            loading = false
        }
    }

    fun openConversation(conversation: ConversationDto) {
        selected = conversation
        loading = true
        error = null
        scope.launch {
            repository.messages(conversation.id)
                .onSuccess {
                    selected = it.conversation
                    messages = it.messages
                }
                .onFailure { error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { loadConversations() }

    NexShapeScreen(
        title = if (selected == null) "Chat" else selected?.otherUserName ?: "Conversa",
        subtitle = if (selected == null) "Conversas internas da plataforma." else "Chat com suporte ou profissional.",
        modifier = modifier,
        action = {
            Row {
                if (selected != null) {
                    IconButton(onClick = { selected = null; messages = emptyList(); loadConversations() }) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Voltar", tint = NexNeon)
                    }
                }
                IconButton(
                    onClick = {
                        if (selected == null) loadConversations() else selected?.let(::openConversation)
                    },
                ) {
                    Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
                }
                if (selected == null) {
                    IconButton(onClick = {
                        scope.launch {
                            repository.startSupport()
                                .onSuccess { openConversation(it) }
                                .onFailure { error = friendlyError(it) }
                        }
                    }) {
                        Icon(Icons.Default.Add, contentDescription = "Nova conversa", tint = NexNeon)
                    }
                }
            }
        },
    ) {
        when {
            loading -> NexLoadingState("Carregando mensagens...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = {
                if (selected == null) loadConversations() else selected?.let(::openConversation)
            })
            selected == null -> ConversationList(conversations, onOpen = ::openConversation)
            else -> MessageThread(
                messages = messages,
                input = input,
                onInputChange = { input = it.take(2000) },
                onSend = {
                    val conversationId = selected?.id ?: return@MessageThread
                    val text = input.trim()
                    if (text.isBlank()) return@MessageThread
                    input = ""
                    scope.launch {
                        repository.send(conversationId, text)
                            .onSuccess { messages = messages + it }
                            .onFailure { error = friendlyError(it) }
                    }
                },
            )
        }
    }
}

@Composable
private fun ConversationList(
    conversations: List<ConversationDto>,
    onOpen: (ConversationDto) -> Unit,
) {
    if (conversations.isEmpty()) {
        NexEmptyState("Sem mensagens", "Toque no botao + para abrir uma conversa com o suporte.")
        return
    }
    LazyColumn(verticalArrangement = Arrangement.spacedBy(10.dp)) {
        items(conversations, key = { it.id }) { conversation ->
            NexCard(modifier = Modifier.clickable { onOpen(conversation) }) {
                Text(conversation.otherUserName ?: "Conversa", color = Color.White, fontWeight = FontWeight.Black)
                Text(conversation.lastMessage ?: "Sem mensagens ainda", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                conversation.lastMessageAt?.let { Text(it, color = NexMuted, modifier = Modifier.padding(top = 2.dp)) }
            }
        }
    }
}

@Composable
private fun MessageThread(
    messages: List<InternalMessageDto>,
    input: String,
    onInputChange: (String) -> Unit,
    onSend: () -> Unit,
) {
    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
        LazyColumn(verticalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.weight(1f)) {
            items(messages, key = { it.id }) { message ->
                NexCard {
                    Text(
                        text = if (message.isMine) "Voce" else message.senderName ?: "Contato",
                        color = if (message.isMine) NexNeon else Color.White,
                        fontWeight = FontWeight.Bold,
                        textAlign = if (message.isMine) TextAlign.End else TextAlign.Start,
                        modifier = Modifier.fillMaxWidth(),
                    )
                    Text(message.content, color = Color.White, modifier = Modifier.padding(top = 4.dp))
                }
            }
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.fillMaxWidth()) {
            OutlinedTextField(
                value = input,
                onValueChange = onInputChange,
                label = { Text("Mensagem") },
                modifier = Modifier.weight(1f),
                singleLine = true,
            )
            TextButton(onClick = onSend, enabled = input.isNotBlank()) {
                Text("Enviar")
            }
        }
    }
}
