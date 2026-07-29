package br.com.nexshape.academia.ui.paciente.mensagens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Send
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import br.com.nexshape.academia.ui.components.NexNeon

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PacienteMensagensScreen(
    modifier: Modifier = Modifier,
    viewModel: PacienteMensagensViewModel = viewModel()
) {
    val state by viewModel.uiState.collectAsState()
    var messageText by remember { mutableStateOf("") }

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10))
    ) {
        // Header
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .background(Color(0xFF131A24))
                .padding(16.dp),
            contentAlignment = Alignment.Center
        ) {
            Text(text = "Chat com Profissional", color = Color.White, fontSize = 18.sp)
            if (state.isConnected) {
                Box(
                    modifier = Modifier
                        .size(8.dp)
                        .background(Color.Green, shape = RoundedCornerShape(4.dp))
                        .align(Alignment.CenterEnd)
                )
            }
        }

        // Lista de mensagens
        LazyColumn(
            modifier = Modifier
                .weight(1f)
                .padding(16.dp),
            contentPadding = PaddingValues(vertical = 8.dp),
            verticalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            items(state.messages) { msg ->
                val alignment = if (msg.isMine) Alignment.CenterEnd else Alignment.CenterStart
                val bgColor = if (msg.isMine) NexNeon else Color(0xFF1F2937)
                val textColor = if (msg.isMine) Color.Black else Color.White
                val shape = if (msg.isMine) {
                    RoundedCornerShape(12.dp, 12.dp, 0.dp, 12.dp)
                } else {
                    RoundedCornerShape(12.dp, 12.dp, 12.dp, 0.dp)
                }

                Box(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 8.dp),
                    contentAlignment = alignment
                ) {
                    Card(
                        shape = shape,
                        colors = CardDefaults.cardColors(containerColor = bgColor)
                    ) {
                        Text(
                            text = msg.content,
                            color = textColor,
                            modifier = Modifier.padding(12.dp),
                            fontSize = 15.sp
                        )
                    }
                }
            }
        }

        // Input de envio
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            OutlinedTextField(
                value = messageText,
                onValueChange = { messageText = it },
                modifier = Modifier.weight(1f),
                placeholder = { Text("Digite sua mensagem...") },
                colors = OutlinedTextFieldDefaults.colors(
                    focusedContainerColor = Color(0xFF131A24),
                    unfocusedContainerColor = Color(0xFF131A24),
                    focusedBorderColor = NexNeon,
                    unfocusedBorderColor = Color.Transparent,
                    focusedTextColor = Color.White,
                    unfocusedTextColor = Color.White
                ),
                shape = RoundedCornerShape(24.dp)
            )
            Spacer(modifier = Modifier.width(8.dp))
            IconButton(
                onClick = {
                    viewModel.sendMessage(messageText)
                    messageText = ""
                },
                modifier = Modifier
                    .background(NexNeon, shape = RoundedCornerShape(50))
            ) {
                Icon(Icons.AutoMirrored.Filled.Send, contentDescription = "Enviar", tint = Color.Black)
            }
        }
    }
}
