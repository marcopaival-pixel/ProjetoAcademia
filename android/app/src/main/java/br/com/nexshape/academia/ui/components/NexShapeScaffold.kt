package br.com.nexshape.academia.ui.components

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ColumnScope
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.widthIn
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.repository.AiCreditsRepository
import org.json.JSONObject
import retrofit2.HttpException

val NexGreen = Color(0xFF10B981)
val NexNeon = Color(0xFF19F5A6)
val NexBg = Color(0xFF050708)
val NexPanel = Color(0xCC0B1117)
val NexMuted = Color(0xFFA3AAB5)

@Composable
fun NexShapeScreen(
    title: String,
    subtitle: String? = null,
    modifier: Modifier = Modifier,
    action: (@Composable () -> Unit)? = null,
    content: @Composable PaddingValues.() -> Unit,
) {
    val creditsRepository = remember { AiCreditsRepository() }
    var aiCredits by remember { mutableStateOf<Int?>(null) }

    LaunchedEffect(title) {
        creditsRepository.balance()
            .onSuccess { aiCredits = it.balance }
    }

    Box(
        modifier = modifier
            .fillMaxSize()
            .background(NexBg),
    ) {
        NexShapeBackground(Modifier.fillMaxSize())
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 20.dp, vertical = 22.dp),
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.Top,
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        text = title,
                        color = Color.White,
                        fontSize = 28.sp,
                        lineHeight = 34.sp,
                        fontWeight = FontWeight.Black,
                    )
                    subtitle?.let {
                        Text(
                            text = it,
                            color = NexMuted,
                            fontSize = 14.sp,
                            lineHeight = 20.sp,
                            modifier = Modifier.padding(top = 6.dp),
                        )
                    }
                }
                Column(
                    horizontalAlignment = Alignment.End,
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.widthIn(min = 92.dp),
                ) {
                    AiCreditsPill(aiCredits)
                    action?.invoke()
                }
            }
            Spacer(Modifier.height(18.dp))
            PaddingValues().content()
        }
    }
}

@Composable
private fun AiCreditsPill(credits: Int?) {
    Row(
        modifier = Modifier
            .clip(RoundedCornerShape(999.dp))
            .background(Color(0xFF071B16).copy(alpha = 0.94f))
            .border(1.dp, NexNeon.copy(alpha = 0.36f), RoundedCornerShape(999.dp))
            .padding(horizontal = 10.dp, vertical = 7.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(6.dp),
    ) {
        Text(
            text = "IA",
            color = NexNeon,
            fontSize = 11.sp,
            fontWeight = FontWeight.Black,
        )
        Text(
            text = credits?.toString() ?: "--",
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.Black,
        )
        Text(
            text = "creditos",
            color = NexMuted,
            fontSize = 10.sp,
            fontWeight = FontWeight.Bold,
        )
    }
}

@Composable
fun NexCard(
    modifier: Modifier = Modifier,
    content: @Composable ColumnScope.() -> Unit,
) {
    Column(
        modifier = modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(22.dp))
            .background(NexPanel)
            .border(1.dp, Color.White.copy(alpha = 0.08f), RoundedCornerShape(22.dp))
            .padding(16.dp),
        content = content,
    )
}

@Composable
fun NexMetricCard(
    title: String,
    value: String,
    modifier: Modifier = Modifier,
    icon: ImageVector? = null,
) {
    NexCard(modifier = modifier) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            icon?.let {
                Box(
                    modifier = Modifier
                        .size(40.dp)
                        .clip(RoundedCornerShape(14.dp))
                        .background(NexGreen.copy(alpha = 0.16f)),
                    contentAlignment = Alignment.Center,
                ) {
                    Icon(it, contentDescription = null, tint = NexNeon, modifier = Modifier.size(22.dp))
                }
            }
            Column(modifier = Modifier.padding(start = if (icon == null) 0.dp else 12.dp)) {
                Text(title, color = NexMuted, fontSize = 12.sp, fontWeight = FontWeight.Bold)
                Text(value, color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Black)
            }
        }
    }
}

@Composable
fun NexLoadingState(message: String = "Carregando dados...") {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            CircularProgressIndicator(color = NexNeon)
            Text(message, color = NexMuted, modifier = Modifier.padding(top = 12.dp))
        }
    }
}

@Composable
fun NexEmptyState(title: String, message: String) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        NexCard(modifier = Modifier.padding(horizontal = 6.dp)) {
            Text(title, color = Color.White, fontSize = 18.sp, fontWeight = FontWeight.Black)
            Text(message, color = NexMuted, modifier = Modifier.padding(top = 6.dp))
        }
    }
}

@Composable
fun NexErrorState(message: String, onRetry: (() -> Unit)? = null) {
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        NexCard(modifier = Modifier.padding(horizontal = 6.dp)) {
            Text("Nao foi possivel carregar", color = Color.White, fontSize = 18.sp, fontWeight = FontWeight.Black)
            Text(message, color = NexMuted, modifier = Modifier.padding(top = 6.dp))
            onRetry?.let {
                OutlinedButton(
                    onClick = it,
                    colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                    modifier = Modifier.padding(top = 12.dp),
                ) {
                    Icon(Icons.Default.Refresh, contentDescription = null, modifier = Modifier.size(18.dp))
                    Text("Tentar novamente", modifier = Modifier.padding(start = 8.dp))
                }
            }
        }
    }
}

@Composable
fun NexPrimaryButton(
    text: String,
    onClick: () -> Unit,
    modifier: Modifier = Modifier,
    enabled: Boolean = true,
) {
    Button(
        onClick = onClick,
        enabled = enabled,
        modifier = modifier.height(52.dp),
        shape = RoundedCornerShape(18.dp),
        colors = ButtonDefaults.buttonColors(
            containerColor = NexGreen,
            contentColor = Color(0xFF04110D),
            disabledContainerColor = Color(0xFF1A2229),
            disabledContentColor = Color(0xFF68717D),
        ),
    ) {
        Text(text, fontWeight = FontWeight.Black)
    }
}

fun friendlyError(error: Throwable?): String {
    val apiMessage = extractApiErrorMessage(error)
    val message = apiMessage ?: error?.message

    return when {
        error == null -> "Tente novamente em alguns instantes."
        message?.containsAiProviderProblem() == true -> "O servico de inteligencia artificial esta temporariamente indisponivel. Por favor, tente novamente mais tarde."
        message?.contains("plan_limit_reached", ignoreCase = true) == true -> "Recurso bloqueado pelo limite do seu plano atual."
        message?.contains("premium_required", ignoreCase = true) == true -> "Este recurso esta disponivel apenas no plano Premium."
        message?.contains("limite", ignoreCase = true) == true -> message
        message?.contains("premium", ignoreCase = true) == true -> message
        message?.contains("403") == true -> "Seu perfil atual nao tem permissao para acessar estes dados. Confira se esta no perfil de aluno."
        message?.contains("401") == true -> "Sua sessao expirou. Entre novamente."
        message?.contains("404") == true -> "Este recurso ainda nao esta disponivel na API local. Atualize o backend e tente novamente."
        message?.contains("422") == true -> "Nao foi possivel processar os dados enviados. Verifique a imagem e tente novamente."
        message?.contains("timeout", ignoreCase = true) == true -> "A conexao demorou demais. Verifique sua internet."
        message?.contains("Unable to resolve host", ignoreCase = true) == true -> "Sem conexao com o servidor."
        !message.isNullOrBlank() -> message
        else -> "Nao foi possivel completar a operacao."
    }
}

private fun extractApiErrorMessage(error: Throwable?): String? {
    val http = error as? HttpException ?: return null
    val raw = http.response()?.errorBody()?.string() ?: return null

    return runCatching {
        val root = JSONObject(raw)
        val errorBody = root.optJSONObject("error")
        errorBody?.optString("message").orEmpty().ifBlank {
            root.optString("message")
        }.ifBlank {
            null
        }
    }.getOrNull()
}

private fun String.containsAiProviderProblem(): Boolean =
    contains("openai", ignoreCase = true) ||
        contains("api key", ignoreCase = true) ||
        contains("api_key", ignoreCase = true) ||
        contains("chave", ignoreCase = true) ||
        contains("quota", ignoreCase = true) ||
        contains("billing", ignoreCase = true) ||
        contains("token", ignoreCase = true) ||
        contains("sk-", ignoreCase = true)

@Composable
private fun NexShapeBackground(modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        drawRect(
            brush = Brush.verticalGradient(
                colors = listOf(Color(0xFF07100D), NexBg, Color(0xFF020304)),
                startY = 0f,
                endY = size.height,
            ),
        )
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(NexNeon.copy(alpha = 0.20f), Color.Transparent),
                center = androidx.compose.ui.geometry.Offset(size.width * 0.85f, size.height * 0.08f),
                radius = size.width * 0.86f,
            ),
            radius = size.width * 0.86f,
            center = androidx.compose.ui.geometry.Offset(size.width * 0.85f, size.height * 0.08f),
        )
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(NexGreen.copy(alpha = 0.14f), Color.Transparent),
                center = androidx.compose.ui.geometry.Offset(size.width * 0.1f, size.height * 0.88f),
                radius = size.width * 0.76f,
            ),
            radius = size.width * 0.76f,
            center = androidx.compose.ui.geometry.Offset(size.width * 0.1f, size.height * 0.88f),
        )
        repeat(4) { index ->
            val top = size.height * (0.08f + index * 0.13f)
            val path = Path().apply {
                moveTo(size.width * (0.54f + index * 0.04f), top)
                lineTo(size.width * 0.98f, top + size.height * 0.04f)
                lineTo(size.width * 0.82f, top + size.height * 0.1f)
            }
            drawPath(
                path = path,
                color = NexNeon.copy(alpha = 0.11f),
                style = androidx.compose.ui.graphics.drawscope.Stroke(width = 3.dp.toPx()),
            )
        }
    }
}
