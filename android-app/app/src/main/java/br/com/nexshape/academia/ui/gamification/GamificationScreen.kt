package br.com.nexshape.academia.ui.gamification

import androidx.compose.foundation.background
import androidx.compose.foundation.border
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
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.LocalFireDepartment
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.ScrollableTabRow
import androidx.compose.material3.Tab
import androidx.compose.material3.TabRowDefaults
import androidx.compose.material3.TabRowDefaults.tabIndicatorOffset
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.api.BadgeDto
import br.com.nexshape.academia.data.api.GamificationData
import br.com.nexshape.academia.data.api.RankingUserDto
import br.com.nexshape.academia.data.repository.GamificationRepository
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
fun GamificationScreen(modifier: Modifier = Modifier) {
    val repository = remember { GamificationRepository() }
    val scope = rememberCoroutineScope()

    var data by remember { mutableStateOf<GamificationData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var selectedTab by remember { mutableStateOf(0) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.gamification()
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

    val tabs = listOf("Geral", "Consistência", "Força", "Nutrição", "Troféus")

    NexShapeScreen(
        title = "Arena e conquistas",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load, enabled = !loading) {
                Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
            }
        }
    ) {
        when {
            loading -> NexLoadingState("Carregando ranking e conquistas...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            data == null -> NexEmptyState(title = "Sem dados", message = "Nenhum dado encontrado no servidor.")
            data?.isPremiumUser == false -> GamificationPremiumLock()
            else -> {
                Column(modifier = Modifier.fillMaxSize()) {
                    ScrollableTabRow(
                        selectedTabIndex = selectedTab,
                        containerColor = Color.Transparent,
                        contentColor = Color.White,
                        edgePadding = 16.dp,
                        indicator = { tabPositions ->
                            TabRowDefaults.Indicator(
                                modifier = Modifier.tabIndicatorOffset(tabPositions[selectedTab]),
                                color = NexNeon
                            )
                        },
                        divider = {}
                    ) {
                        tabs.forEachIndexed { index, title ->
                            Tab(
                                selected = selectedTab == index,
                                onClick = { selectedTab = index },
                                text = {
                                    Text(
                                        title,
                                        fontWeight = if (selectedTab == index) FontWeight.Bold else FontWeight.Normal,
                                        fontSize = 13.sp
                                    )
                                }
                            )
                        }
                    }

                    Spacer(modifier = Modifier.height(14.dp))

                    val gamificationData = data!!
                    when (selectedTab) {
                        0 -> RankingList(
                            title = "Elite Geral",
                            subtitle = "Pontuação geral baseada em treinos, cargas e alimentação.",
                            list = gamificationData.rankings.elite,
                            unit = "pts"
                        )
                        1 -> RankingList(
                            title = "Reis da Consistência",
                            subtitle = "Dias únicos de treino registrados nos últimos 30 dias.",
                            list = gamificationData.rankings.consistency,
                            unit = "dias"
                        )
                        2 -> RankingList(
                            title = "Hall da Força",
                            subtitle = "Maior carga estimada para 1 repetição máxima (1RM).",
                            list = gamificationData.rankings.strength,
                            unit = "kg"
                        )
                        3 -> RankingList(
                            title = "Mestres da Nutrição",
                            subtitle = "Total de registros de refeição realizados na última semana.",
                            list = gamificationData.rankings.nutrition,
                            unit = "regs"
                        )
                        4 -> BadgesList(badges = gamificationData.badges)
                    }
                }
            }
        }
    }
}

@Composable
private fun GamificationPremiumLock() {
    NexCard(
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
    ) {
        Column(horizontalAlignment = Alignment.CenterHorizontally) {
            Icon(
                imageVector = Icons.Default.EmojiEvents,
                contentDescription = null,
                tint = NexNeon,
                modifier = Modifier.size(44.dp),
            )
            Text(
                "Ranking e conquistas Premium",
                color = Color.White,
                fontWeight = FontWeight.Black,
                fontSize = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(top = 12.dp),
            )
            Text(
                "Desbloqueie o plano Premium para acompanhar rankings globais, trofeus e desafios de performance.",
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
private fun RankingList(
    title: String,
    subtitle: String,
    list: List<RankingUserDto>,
    unit: String,
) {
    if (list.isEmpty()) {
        NexEmptyState(title = "Ranking Vazio", message = "Ainda não há dados suficientes para este ranking.")
    } else {
        LazyColumn(
            contentPadding = PaddingValues(bottom = 24.dp, start = 16.dp, end = 16.dp),
            verticalArrangement = Arrangement.spacedBy(10.dp)
        ) {
            item {
                Column(modifier = Modifier.padding(bottom = 8.dp)) {
                    Text(title, color = Color.White, fontWeight = FontWeight.Black, fontSize = 16.sp)
                    Text(subtitle, color = NexMuted, fontSize = 12.sp, lineHeight = 16.sp)
                }
            }
            items(list) { row ->
                RankingRow(row = row, unit = unit)
            }
        }
    }
}

@Composable
private fun RankingRow(row: RankingUserDto, unit: String) {
    val isPodium = row.position <= 3
    val bgColor = when {
        row.isCurrentUser -> Color(0xFF1E293B)
        else -> Color(0xFF0F172A)
    }
    val borderColor = when {
        row.isCurrentUser -> NexNeon.copy(alpha = 0.5f)
        row.position == 1 -> Color(0xFFFFD700).copy(alpha = 0.4f) // Gold
        row.position == 2 -> Color(0xFFC0C0C0).copy(alpha = 0.4f) // Silver
        row.position == 3 -> Color(0xFFCD7F32).copy(alpha = 0.4f) // Bronze
        else -> Color.White.copy(alpha = 0.08f)
    }
    val positionColor = when (row.position) {
        1 -> Color(0xFFFFD700)
        2 -> Color(0xFFC0C0C0)
        3 -> Color(0xFFCD7F32)
        else -> NexMuted
    }

    Row(
        verticalAlignment = Alignment.CenterVertically,
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(16.dp))
            .background(bgColor)
            .border(1.dp, borderColor, RoundedCornerShape(16.dp))
            .padding(14.dp)
    ) {
        Box(
            contentAlignment = Alignment.Center,
            modifier = Modifier
                .size(32.dp)
                .clip(CircleShape)
                .background(if (isPodium) positionColor.copy(alpha = 0.16f) else Color.Transparent)
        ) {
            Text(
                text = row.position.toString(),
                color = positionColor,
                fontWeight = FontWeight.Bold,
                fontSize = 14.sp
            )
        }

        Spacer(modifier = Modifier.width(12.dp))

        Text(
            text = row.name,
            color = Color.White,
            fontWeight = if (row.isCurrentUser) FontWeight.Black else FontWeight.Bold,
            fontSize = 14.sp,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis,
            modifier = Modifier.weight(1f)
        )

        Spacer(modifier = Modifier.width(8.dp))

        Text(
            text = "${row.score} $unit",
            color = if (row.isCurrentUser) NexNeon else Color.White,
            fontWeight = FontWeight.Black,
            fontSize = 13.sp,
            textAlign = TextAlign.End
        )
    }
}

@Composable
private fun BadgesList(badges: List<BadgeDto>) {
    if (badges.isEmpty()) {
        NexEmptyState(title = "Sem Conquistas", message = "Nenhum troféu ou conquista cadastrada.")
    } else {
        LazyColumn(
            contentPadding = PaddingValues(bottom = 24.dp, start = 16.dp, end = 16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            item {
                Column(modifier = Modifier.padding(bottom = 8.dp)) {
                    Text("Galeria de Conquistas", color = Color.White, fontWeight = FontWeight.Black, fontSize = 16.sp)
                    Text("Complete os desafios da plataforma para liberar novos troféus.", color = NexMuted, fontSize = 12.sp)
                }
            }
            items(badges) { badge ->
                BadgeRow(badge = badge)
            }
        }
    }
}

@Composable
private fun BadgeRow(badge: BadgeDto) {
    val icon = when (badge.code) {
        "camelo" -> Icons.Filled.WaterDrop
        "monstro" -> Icons.Filled.FitnessCenter
        "fogo" -> Icons.Filled.LocalFireDepartment
        else -> Icons.Filled.EmojiEvents
    }

    val iconColor = when {
        !badge.isUnlocked -> NexMuted
        badge.code == "camelo" -> Color(0xFF3B82F6)
        badge.code == "monstro" -> Color(0xFF10B981)
        badge.code == "fogo" -> Color(0xFFF97316)
        else -> Color(0xFFFFD700)
    }

    val bgColor = if (badge.isUnlocked) Color(0xFF0F172A) else Color(0xFF030712)
    val borderColor = if (badge.isUnlocked) iconColor.copy(alpha = 0.25f) else Color.White.copy(alpha = 0.05f)

    NexCard(
        modifier = Modifier
            .fillMaxWidth()
            .border(1.dp, borderColor, RoundedCornerShape(22.dp))
    ) {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier.fillMaxWidth()
        ) {
            Box(
                contentAlignment = Alignment.Center,
                modifier = Modifier
                    .size(48.dp)
                    .clip(CircleShape)
                    .background(iconColor.copy(alpha = 0.1f))
            ) {
                Icon(
                    imageVector = icon,
                    contentDescription = badge.title,
                    tint = iconColor,
                    modifier = Modifier.size(24.dp)
                )
            }

            Spacer(modifier = Modifier.width(14.dp))

            Column(modifier = Modifier.weight(1f)) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.SpaceBetween,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text(
                        text = badge.title,
                        color = if (badge.isUnlocked) Color.White else NexMuted,
                        fontWeight = FontWeight.Bold,
                        fontSize = 14.sp
                    )
                    Text(
                        text = if (badge.isUnlocked) "Desbloqueado" else "${badge.current}/${badge.meta}",
                        color = if (badge.isUnlocked) NexNeon else NexMuted,
                        fontWeight = FontWeight.Bold,
                        fontSize = 11.sp
                    )
                }
                
                Text(
                    text = badge.description,
                    color = NexMuted,
                    fontSize = 12.sp,
                    lineHeight = 16.sp,
                    modifier = Modifier.padding(top = 4.dp)
                )

                if (!badge.isUnlocked && badge.meta > 0) {
                    val progress = (badge.current.toFloat() / badge.meta.toFloat()).coerceIn(0f, 1f)
                    Spacer(modifier = Modifier.height(8.dp))
                    LinearProgressIndicator(
                        progress = progress,
                        color = iconColor,
                        trackColor = Color.White.copy(alpha = 0.08f),
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(4.dp)
                            .clip(CircleShape)
                    )
                }
            }
        }
    }
}
