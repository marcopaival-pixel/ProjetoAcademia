package br.com.nexshape.academia.ui.chat

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.getValue
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ChatMessageDto
import br.com.nexshape.academia.data.repository.ChatRepository
import kotlinx.coroutines.launch

@Composable
fun ChatScreen(modifier: Modifier = Modifier) {
    val repository = remember { ChatRepository() }
    val scope = rememberCoroutineScope()
    var messages by remember { mutableStateOf<List<ChatMessageDto>>(emptyList()) }
    var input by remember { mutableStateOf("") }
    var sending by remember { mutableStateOf(false) }

    LaunchedEffect(Unit) {
        repository.history().onSuccess { messages = it }
    }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("NexBot")
        LazyColumn(modifier = Modifier.weight(1f).padding(vertical = 8.dp)) {
            items(messages) { msg ->
                val prefix = if (msg.role == "user") "Você" else "NexBot"
                Text("$prefix: ${msg.message}", modifier = Modifier.padding(vertical = 4.dp))
            }
        }
        Row(modifier = Modifier.fillMaxWidth()) {
            OutlinedTextField(
                value = input,
                onValueChange = { input = it },
                modifier = Modifier.weight(1f),
                placeholder = { Text("Pergunte sobre treino ou nutrição") },
                singleLine = true,
            )
            Button(
                onClick = {
                    if (input.isBlank()) return@Button
                    sending = true
                    val text = input
                    input = ""
                    scope.launch {
                        messages = messages + ChatMessageDto(role = "user", message = text)
                        repository.send(text)
                            .onSuccess { reply ->
                                messages = messages + ChatMessageDto(role = "assistant", message = reply)
                            }
                        sending = false
                    }
                },
                enabled = !sending,
                modifier = Modifier.padding(start = 8.dp),
            ) {
                Text("Enviar")
            }
        }
    }
}
