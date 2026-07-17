package br.com.nexshape.academia.ui.activerest

import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Close
import androidx.compose.material.icons.filled.Favorite
import androidx.compose.material.icons.filled.FavoriteBorder
import androidx.compose.material.icons.filled.PlayArrow
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.SelfImprovement
import androidx.compose.material.icons.filled.Star
import androidx.compose.material.icons.filled.StarBorder
import androidx.compose.material.icons.filled.Timer
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.FilterChip
import androidx.compose.material3.FilterChipDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.window.DialogProperties
import br.com.nexshape.academia.data.api.ActiveRestData
import br.com.nexshape.academia.data.api.ActiveRestRoutineDto
import br.com.nexshape.academia.data.repository.ActiveRestRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch

@Composable
fun ActiveRestScreen(modifier: Modifier = Modifier) {
    val repository = remember { ActiveRestRepository() }
    val scope = rememberCoroutineScope()
    val context = LocalContext.current

    var data by remember { mutableStateOf<ActiveRestData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var activeCategory by remember { mutableStateOf("Todos") }
    var activeRoutine by remember { mutableStateOf<ActiveRestRoutineDto?>(null) }
    var ratingRoutine by remember { mutableStateOf<ActiveRestRoutineDto?>(null) }
    var sessionDurationSeconds by remember { mutableStateOf(0) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.getActiveRest()
                .onSuccess {
                    data = it
                    loading = false
                }
                .onFailure {
                    error = friendlyError(it)
                    loading = false
                }
        }
    }

    LaunchedEffect(Unit) {
        load()
    }

    // Modal de Execução de Treino
    activeRoutine?.let { routine ->
        ActiveRestExecutionDialog(
            routine = routine,
            onDismiss = { activeRoutine = null },
            onFinish = { duration ->
                sessionDurationSeconds = duration
                ratingRoutine = routine
                activeRoutine = null
            }
        )
    }

    // Modal de Feedback de Classificação
    ratingRoutine?.let { routine ->
        ActiveRestFeedbackDialog(
            routine = routine,
            durationSeconds = sessionDurationSeconds,
            onDismiss = { ratingRoutine = null },
            onSubmit = { rating ->
                scope.launch {
                    repository.storeLog(routine.id, sessionDurationSeconds, rating)
                        .onSuccess {
                            Toast.makeText(context, "Sessão registrada com sucesso!", Toast.LENGTH_SHORT).show()
                            load()
                        }
                        .onFailure {
                            Toast.makeText(context, "Erro ao registrar: ${it.message}", Toast.LENGTH_LONG).show()
                        }
                    ratingRoutine = null
                }
            }
        )
    }

    NexShapeScreen(
        title = "Descanso ativo",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load, enabled = !loading) {
                Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
            }
        }
    ) {
        when {
            loading -> NexLoadingState("Carregando rotinas...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            data == null -> NexEmptyState(title = "Sem rotinas", message = "Nenhuma rotina cadastrada.")
            data?.isPremiumUser == false -> ActiveRestPremiumLock()
            else -> {
                val currentData = data!!
                val categories = listOf("Todos") + currentData.routines.map { it.category }.distinct()
                val filteredRoutines = currentData.routines.filter {
                    activeCategory == "Todos" || it.category == activeCategory
                }

                LazyColumn(
                    contentPadding = PaddingValues(bottom = 24.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    // Header card de dia OFF/ON
                    item {
                        RestDayStatusCard(isOffDay = currentData.isOffDay)
                    }

                    // Sugestão do dia
                    val suggested = currentData.routines.find { it.id == currentData.suggestedRoutineId }
                    if (suggested != null && activeCategory == "Todos") {
                        item {
                            SuggestedRoutineSection(
                                routine = suggested,
                                onStart = { activeRoutine = suggested }
                            )
                        }
                    }

                    // Filtros horizontais
                    item {
                        LazyRow(
                            horizontalArrangement = Arrangement.spacedBy(8.dp),
                            contentPadding = PaddingValues(horizontal = 16.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            items(categories) { category ->
                                val selected = category == activeCategory
                                FilterChip(
                                    selected = selected,
                                    onClick = { activeCategory = category },
                                    label = { Text(category) },
                                    colors = FilterChipDefaults.filterChipColors(
                                        selectedContainerColor = NexNeon,
                                        selectedLabelColor = Color.Black,
                                        containerColor = Color(0xFF0F172A),
                                        labelColor = Color.White
                                    ),
                                    border = FilterChipDefaults.filterChipBorder(
                                        borderColor = if (selected) Color.Transparent else Color.White.copy(alpha = 0.1f),
                                        enabled = true,
                                        selected = selected
                                    )
                                )
                            }
                        }
                    }

                    // Lista de rotinas
                    if (filteredRoutines.isEmpty()) {
                        item {
                            NexEmptyState(
                                title = "Nenhuma rotina",
                                message = "Nenhuma rotina encontrada para a categoria selecionada."
                            )
                        }
                    } else {
                        items(filteredRoutines, key = { it.id }) { routine ->
                            RoutineCard(
                                routine = routine,
                                onStart = { activeRoutine = routine },
                                onToggleFavorite = {
                                    scope.launch {
                                        repository.toggleFavorite(routine.id)
                                            .onSuccess { res ->
                                                val isFav = res["is_favorite"] as? Boolean ?: false
                                                data = currentData.copy(
                                                    routines = currentData.routines.map { r ->
                                                        if (r.id == routine.id) r.copy(isFavorite = isFav) else r
                                                    }
                                                )
                                            }
                                    }
                                }
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun ActiveRestPremiumLock() {
    NexCard(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
    ) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Icon(
                imageVector = Icons.Default.SelfImprovement,
                contentDescription = null,
                tint = NexNeon,
                modifier = Modifier.size(44.dp),
            )
            Text(
                "Descanso ativo Premium",
                color = Color.White,
                fontWeight = FontWeight.Black,
                fontSize = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(top = 12.dp),
            )
            Text(
                "Protocolos guiados de mobilidade e recuperacao ficam disponiveis ao desbloquear o plano Premium.",
                color = NexMuted,
                fontSize = 13.sp,
                lineHeight = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(top = 8.dp),
            )
        }
    }
}

@Composable
private fun RestDayStatusCard(isOffDay: Boolean) {
    val title = if (isOffDay) "Hoje é Dia de Descanso! 🔋" else "Dia de Treino Ativo ⚡"
    val subtitle = if (isOffDay) {
        "Aproveite para realizar sessões de mobilidade ou regeneração muscular para recuperar o corpo."
    } else {
        "Realize treinos curtos de flexibilidade ou alongamento pós-treino para manter a articulação saudável."
    }
    val cardColor = if (isOffDay) Color(0xFF0F2D24) else Color(0xFF1E1E24)

    Card(
        colors = CardDefaults.cardColors(containerColor = cardColor),
        shape = RoundedCornerShape(22.dp),
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
            .border(1.dp, Color.White.copy(alpha = 0.08f), RoundedCornerShape(22.dp))
    ) {
        Column(modifier = Modifier.padding(18.dp)) {
            Text(title, color = Color.White, fontWeight = FontWeight.Black, fontSize = 16.sp)
            Text(subtitle, color = NexMuted, fontSize = 12.sp, lineHeight = 16.sp, modifier = Modifier.padding(top = 4.dp))
        }
    }
}

@Composable
private fun SuggestedRoutineSection(
    routine: ActiveRestRoutineDto,
    onStart: () -> Unit,
) {
    Column(modifier = Modifier.padding(horizontal = 16.dp)) {
        Text(
            "Sugestão do Dia",
            color = Color.White,
            fontWeight = FontWeight.Black,
            fontSize = 15.sp,
            modifier = Modifier.padding(bottom = 10.dp)
        )
        NexCard(
            modifier = Modifier
                .fillMaxWidth()
                .border(1.dp, NexNeon.copy(alpha = 0.3f), RoundedCornerShape(22.dp))
        ) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        routine.title,
                        color = Color.White,
                        fontWeight = FontWeight.Bold,
                        fontSize = 15.sp
                    )
                    Text(
                        "${routine.category} • ${routine.duration} mins • ${routine.intensity}",
                        color = NexNeon,
                        fontSize = 12.sp,
                        modifier = Modifier.padding(top = 2.dp)
                    )
                    routine.benefit?.let {
                        Text(
                            it,
                            color = NexMuted,
                            fontSize = 12.sp,
                            maxLines = 1,
                            modifier = Modifier.padding(top = 6.dp)
                        )
                    }
                }
                IconButton(
                    onClick = onStart,
                    modifier = Modifier
                        .size(46.dp)
                        .clip(CircleShape)
                        .background(NexNeon)
                ) {
                    Icon(
                        imageVector = Icons.Default.PlayArrow,
                        contentDescription = "Iniciar",
                        tint = Color.Black
                    )
                }
            }
        }
    }
}

@Composable
private fun RoutineCard(
    routine: ActiveRestRoutineDto,
    onStart: () -> Unit,
    onToggleFavorite: () -> Unit,
) {
    NexCard(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
    ) {
        Column(modifier = Modifier.fillMaxWidth()) {
            Row(
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier.fillMaxWidth()
            ) {
                Column(modifier = Modifier.weight(1f)) {
                    Text(
                        routine.title,
                        color = Color.White,
                        fontWeight = FontWeight.Bold,
                        fontSize = 15.sp
                    )
                    Text(
                        "${routine.category} • ${routine.duration} min • Nível ${routine.recommendedLevel ?: "Iniciante"}",
                        color = NexMuted,
                        fontSize = 12.sp,
                        modifier = Modifier.padding(top = 2.dp)
                    )
                }
                IconButton(onClick = onToggleFavorite) {
                    Icon(
                        imageVector = if (routine.isFavorite) Icons.Default.Favorite else Icons.Default.FavoriteBorder,
                        contentDescription = "Favoritar",
                        tint = if (routine.isFavorite) Color.Red else NexMuted
                    )
                }
            }

            routine.benefit?.takeIf { it.isNotBlank() }?.let {
                Text(
                    text = "Benefício: $it",
                    color = NexMuted,
                    fontSize = 12.sp,
                    modifier = Modifier.padding(top = 8.dp)
                )
            }

            Spacer(modifier = Modifier.height(12.dp))
            OutlinedButton(
                onClick = onStart,
                colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                modifier = Modifier.fillMaxWidth()
            ) {
                Icon(Icons.Default.PlayArrow, contentDescription = null, modifier = Modifier.size(16.dp))
                Text("Iniciar Sessão", modifier = Modifier.padding(start = 8.dp), fontSize = 12.sp)
            }
        }
    }
}

@Composable
private fun ActiveRestExecutionDialog(
    routine: ActiveRestRoutineDto,
    onDismiss: () -> Unit,
    onFinish: (Int) -> Unit,
) {
    var secondsElapsed by remember { mutableStateOf(0) }
    var isRunning by remember { mutableStateOf(true) }

    LaunchedEffect(isRunning) {
        while (isRunning) {
            delay(1000)
            secondsElapsed += 1
        }
    }

    Dialog(
        onDismissRequest = {}, // Force explicit finish/exit action
        properties = DialogProperties(usePlatformDefaultWidth = false)
    ) {
        Box(
            modifier = Modifier
                .fillMaxSize()
                .background(Color(0xFF090D16))
        ) {
            Column(
                modifier = Modifier
                    .fillMaxSize()
                    .padding(20.dp)
                    .verticalScroll(rememberScrollState())
            ) {
                // Top Header Row
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text(
                        routine.category.uppercase(),
                        color = NexNeon,
                        fontWeight = FontWeight.Bold,
                        fontSize = 12.sp
                    )
                    IconButton(onClick = onDismiss) {
                        Icon(Icons.Default.Close, contentDescription = "Fechar", tint = Color.White)
                    }
                }

                Text(
                    routine.title,
                    color = Color.White,
                    fontWeight = FontWeight.Black,
                    fontSize = 22.sp,
                    modifier = Modifier.padding(top = 8.dp)
                )

                // Visual Timer
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(vertical = 18.dp)
                        .clip(RoundedCornerShape(16.dp))
                        .background(Color.White.copy(alpha = 0.04f))
                        .border(1.dp, Color.White.copy(alpha = 0.06f), RoundedCornerShape(16.dp))
                        .padding(16.dp)
                ) {
                    Icon(
                        imageVector = Icons.Default.Timer,
                        contentDescription = null,
                        tint = NexNeon,
                        modifier = Modifier.size(24.dp)
                    )
                    Spacer(modifier = Modifier.width(12.dp))
                    Column {
                        val minutes = secondsElapsed / 60
                        val seconds = secondsElapsed % 60
                        Text(
                            text = String.format("%02d:%02d", minutes, seconds),
                            color = Color.White,
                            fontWeight = FontWeight.Black,
                            fontSize = 20.sp
                        )
                        Text(
                            "Tempo da sessão",
                            color = NexMuted,
                            fontSize = 11.sp
                        )
                    }
                }

                // Exercises List
                if (routine.exercises.isNotEmpty()) {
                    Text("Exercícios inclusos:", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    routine.exercises.forEach { exe ->
                        Text("• $exe", color = Color.White, fontSize = 13.sp, modifier = Modifier.padding(top = 4.dp, start = 8.dp))
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }

                // Execution Steps
                if (routine.executionSteps.isNotEmpty()) {
                    Text("Passo a passo:", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    routine.executionSteps.forEachIndexed { idx, step ->
                        Text("${idx + 1}. $step", color = NexMuted, fontSize = 13.sp, lineHeight = 18.sp, modifier = Modifier.padding(top = 6.dp))
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }

                // Tips
                if (routine.tips.isNotEmpty()) {
                    Text("Dicas importantes:", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    routine.tips.forEach { tip ->
                        Text("💡 $tip", color = Color(0xFFFFB03A), fontSize = 12.sp, lineHeight = 16.sp, modifier = Modifier.padding(top = 4.dp))
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }

                // Common errors
                if (routine.commonErrors.isNotEmpty()) {
                    Text("Erros comuns a evitar:", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 14.sp)
                    routine.commonErrors.forEach { err ->
                        Text("⚠️ $err", color = Color.Red, fontSize = 12.sp, lineHeight = 16.sp, modifier = Modifier.padding(top = 4.dp))
                    }
                    Spacer(modifier = Modifier.height(16.dp))
                }

                Spacer(modifier = Modifier.weight(1f))
                Spacer(modifier = Modifier.height(16.dp))

                Button(
                    onClick = {
                        isRunning = false
                        onFinish(secondsElapsed)
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = NexNeon),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(48.dp)
                ) {
                    Text("Finalizar Sessão", color = Color.Black, fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
private fun ActiveRestFeedbackDialog(
    routine: ActiveRestRoutineDto,
    durationSeconds: Int,
    onDismiss: () -> Unit,
    onSubmit: (Int) -> Unit,
) {
    var rating by remember { mutableStateOf(5) }

    Dialog(onDismissRequest = onDismiss) {
        NexCard(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp)
        ) {
            Column(
                horizontalAlignment = Alignment.CenterHorizontally,
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(
                    "Como você se sente?",
                    color = Color.White,
                    fontWeight = FontWeight.Black,
                    fontSize = 18.sp,
                    textAlign = TextAlign.Center
                )
                Text(
                    "Avalie a sessão de ${routine.title} para nos ajudar a calcular seu score de recuperação.",
                    color = NexMuted,
                    fontSize = 12.sp,
                    textAlign = TextAlign.Center,
                    lineHeight = 16.sp,
                    modifier = Modifier.padding(top = 4.dp, bottom = 16.dp)
                )

                Row(
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.padding(bottom = 20.dp)
                ) {
                    (1..5).forEach { star ->
                        val active = star <= rating
                        IconButton(onClick = { rating = star }) {
                            Icon(
                                imageVector = if (active) Icons.Default.Star else Icons.Default.StarBorder,
                                contentDescription = "$star estrela(s)",
                                tint = if (active) Color(0xFFFFB03A) else NexMuted,
                                modifier = Modifier.size(36.dp)
                            )
                        }
                    }
                }

                Button(
                    onClick = { onSubmit(rating) },
                    colors = ButtonDefaults.buttonColors(containerColor = NexNeon),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text("Enviar Avaliação", color = Color.Black, fontWeight = FontWeight.Bold)
                }

                Spacer(modifier = Modifier.height(8.dp))

                TextButton(onClick = onDismiss, modifier = Modifier.fillMaxWidth()) {
                    Text("Pular", color = Color.White)
                }
            }
        }
    }
}
