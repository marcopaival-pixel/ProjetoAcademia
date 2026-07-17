package br.com.nexshape.academia.ui.community

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
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
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.CommunityPostDto
import br.com.nexshape.academia.data.repository.CommunityRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPrimaryButton
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun CommunityScreen(modifier: Modifier = Modifier) {
    val repository = remember { CommunityRepository() }
    val scope = rememberCoroutineScope()
    var posts by remember { mutableStateOf<List<CommunityPostDto>>(emptyList()) }
    var content by remember { mutableStateOf("") }
    var commentByPost by remember { mutableStateOf<Map<Int, String>>(emptyMap()) }
    var loading by remember { mutableStateOf(true) }
    var saving by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.posts()
                .onSuccess { posts = it }
                .onFailure { error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { load() }

    NexShapeScreen(
        title = "Comunidade",
        subtitle = "Feed social, comentarios e conquistas compartilhadas.",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load, enabled = !loading) {
                Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
            }
        },
    ) {
        when {
            loading -> NexLoadingState("Carregando comunidade...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            else -> LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                item {
                    NexCard {
                        Text("Nova publicacao", color = Color.White, fontWeight = FontWeight.Black)
                        OutlinedTextField(
                            value = content,
                            onValueChange = { content = it.take(1000) },
                            label = { Text("Compartilhe sua evolucao") },
                            modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
                            minLines = 3,
                        )
                        NexPrimaryButton(
                            text = if (saving) "Publicando..." else "Publicar",
                            enabled = !saving && content.isNotBlank(),
                            onClick = {
                                val text = content.trim()
                                if (text.isBlank()) return@NexPrimaryButton
                                saving = true
                                scope.launch {
                                    repository.createPost(text)
                                        .onSuccess {
                                            content = ""
                                            load()
                                        }
                                        .onFailure { error = friendlyError(it) }
                                    saving = false
                                }
                            },
                            modifier = Modifier.padding(top = 10.dp),
                        )
                    }
                }
                if (posts.isEmpty()) {
                    item { NexEmptyState("Sem publicacoes", "A comunidade ainda nao tem publicacoes para o seu contexto.") }
                }
                items(posts, key = { it.id }) { post ->
                    CommunityPostCard(
                        post = post,
                        comment = commentByPost[post.id].orEmpty(),
                        onCommentChange = { commentByPost = commentByPost + (post.id to it.take(500)) },
                        onSendComment = {
                            val text = commentByPost[post.id].orEmpty().trim()
                            if (text.isBlank()) return@CommunityPostCard
                            scope.launch {
                                repository.createComment(post.id, text)
                                    .onSuccess {
                                        commentByPost = commentByPost - post.id
                                        load()
                                    }
                                    .onFailure { error = friendlyError(it) }
                            }
                        },
                    )
                }
            }
        }
    }
}

@Composable
private fun CommunityPostCard(
    post: CommunityPostDto,
    comment: String,
    onCommentChange: (String) -> Unit,
    onSendComment: () -> Unit,
) {
    NexCard {
        Text(post.authorName ?: "NexShape", color = Color.White, fontWeight = FontWeight.Black)
        post.createdAt?.let { Text(it, color = NexMuted, modifier = Modifier.padding(top = 2.dp)) }
        Text(post.content, color = Color.White, modifier = Modifier.padding(top = 10.dp))
        Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.padding(top = 10.dp)) {
            Text("${post.reactionsCount} reacoes", color = NexMuted)
            Text("${post.commentsCount} comentarios", color = NexMuted)
        }
        post.comments.take(3).forEach { item ->
            Column(modifier = Modifier.padding(top = 8.dp)) {
                Text(item.authorName ?: "Usuario", color = NexNeon, fontWeight = FontWeight.Bold)
                Text(item.content, color = NexMuted)
            }
        }
        OutlinedTextField(
            value = comment,
            onValueChange = onCommentChange,
            label = { Text("Comentar") },
            modifier = Modifier.fillMaxWidth().padding(top = 10.dp),
            singleLine = true,
        )
        TextButton(onClick = onSendComment, enabled = comment.isNotBlank()) {
            Text("Enviar comentario")
        }
    }
}
