package br.com.nexshape.academia.ui.home

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.Groups
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.SelfImprovement
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
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
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.data.repository.EvolutionRepository
import br.com.nexshape.academia.data.repository.NutritionRepository
import br.com.nexshape.academia.data.repository.TrainingRepository

@Composable
fun HomeScreen(
    modifier: Modifier = Modifier,
    authRepository: AuthRepository,
    onOpenTraining: () -> Unit,
    onOpenEvolution: () -> Unit,
    onOpenAgenda: () -> Unit,
    onOpenNutrition: () -> Unit,
    onOpenChat: () -> Unit,
    onOpenProfessionals: () -> Unit,
    onOpenProfile: () -> Unit,
) {
    var profile by remember { mutableStateOf<ProfileDto?>(null) }
    var summary by remember { mutableStateOf(HomeSummary()) }
    var error by remember { mutableStateOf<String?>(null) }
    val trainingRepository = remember { TrainingRepository() }
    val nutritionRepository = remember { NutritionRepository() }
    val evolutionRepository = remember { EvolutionRepository() }
    val agendaRepository = remember { AgendaRepository() }

    LaunchedEffect(Unit) {
        authRepository.loadProfile()
            .onSuccess { profile = it }
            .onFailure { error = it.message }

        val trainingCount = trainingRepository.listPlans().getOrNull()?.size
        val nutritionCalories = nutritionRepository.diary().getOrNull()?.totals?.calories
        val assessments = evolutionRepository.assessments().getOrNull().orEmpty()
        val photos = evolutionRepository.photos().getOrNull().orEmpty()
        val appointments = agendaRepository.appointments().getOrNull().orEmpty()
        val professionals = agendaRepository.professionals().getOrNull().orEmpty()

        summary = HomeSummary(
            trainingPlans = trainingCount,
            nutritionCalories = nutritionCalories,
            assessments = assessments.size,
            evolutionPhotos = photos.size,
            appointments = appointments.size,
            professionals = professionals.size,
            latestWeightKg = assessments.firstOrNull()?.weightKg,
        )
    }

    Box(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF050708)),
    ) {
        HomeNeonBackground(modifier = Modifier.fillMaxSize())

        when {
            profile != null -> HomeContent(
                profile = profile!!,
                summary = summary,
                onOpenTraining = onOpenTraining,
                onOpenEvolution = onOpenEvolution,
                onOpenAgenda = onOpenAgenda,
                onOpenNutrition = onOpenNutrition,
                onOpenChat = onOpenChat,
                onOpenProfessionals = onOpenProfessionals,
                onOpenProfile = onOpenProfile,
            )
            error != null -> Text(
                text = error!!,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(24.dp),
            )
            else -> CircularProgressIndicator(
                color = Color(0xFF10B981),
                modifier = Modifier.align(Alignment.Center),
            )
        }
    }
}

@Composable
private fun HomeContent(
    profile: ProfileDto,
    summary: HomeSummary,
    onOpenTraining: () -> Unit,
    onOpenEvolution: () -> Unit,
    onOpenAgenda: () -> Unit,
    onOpenNutrition: () -> Unit,
    onOpenChat: () -> Unit,
    onOpenProfessionals: () -> Unit,
    onOpenProfile: () -> Unit,
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(horizontal = 22.dp, vertical = 26.dp),
    ) {
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically,
        ) {
            Column {
                Text(
                    text = "NEXSHAPE",
                    color = Color.White,
                    fontSize = 14.sp,
                    fontWeight = FontWeight.Black,
                )
                Text(
                    text = if (profile.isPremium) "Plano Premium ativo" else "Plano Free",
                    color = Color(0xFF19F5A6),
                    fontSize = 13.sp,
                    fontWeight = FontWeight.Bold,
                    modifier = Modifier.padding(top = 4.dp),
                )
            }

            Box(
                modifier = Modifier
                    .size(48.dp)
                    .clip(RoundedCornerShape(16.dp))
                    .background(Color(0xFF10B981)),
                contentAlignment = Alignment.Center,
            ) {
                Text(
                    text = profile.name.trim().firstOrNull()?.uppercase() ?: "N",
                    color = Color.White,
                    fontSize = 18.sp,
                    fontWeight = FontWeight.Black,
                )
            }
        }

        Spacer(Modifier.height(30.dp))

        Text(
            text = "Ola, ${profile.name}",
            color = Color.White,
            fontSize = 30.sp,
            lineHeight = 36.sp,
            fontWeight = FontWeight.Black,
        )

        Spacer(Modifier.height(10.dp))

        Text(
            text = "Seu hub de treino, nutricao e evolucao esta pronto para hoje.",
            color = Color(0xFFA3AAB5),
            fontSize = 15.sp,
            lineHeight = 21.sp,
        )

        Spacer(Modifier.height(26.dp))

        HighlightCard(
            profile = profile,
            summary = summary,
        )

        Spacer(Modifier.height(18.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            QuickMetricCard(
                title = "Treino",
                value = summary.trainingPlans?.let { "$it plano(s)" } ?: "Planos",
                icon = Icons.Filled.FitnessCenter,
                onClick = onOpenTraining,
                modifier = Modifier.weight(1f),
            )
            QuickMetricCard(
                title = "Agenda",
                value = "${summary.appointments ?: 0} consulta(s)",
                icon = Icons.Filled.CalendarMonth,
                onClick = onOpenAgenda,
                modifier = Modifier.weight(1f),
            )
        }

        Spacer(Modifier.height(12.dp))

        QuickMetricCard(
            title = "Nutricao",
            value = summary.nutritionCalories?.let { "$it kcal hoje" } ?: "Diario alimentar",
            icon = Icons.Filled.Restaurant,
            onClick = onOpenNutrition,
            modifier = Modifier.fillMaxWidth(),
        )

        Spacer(Modifier.height(12.dp))

        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            QuickMetricCard(
                title = "Evolucao",
                value = summary.latestWeightKg?.let { "${it} kg" }
                    ?: "${summary.assessments ?: 0} avaliacao(oes)",
                icon = Icons.Filled.MonitorHeart,
                onClick = onOpenEvolution,
                modifier = Modifier.weight(1f),
            )
            QuickMetricCard(
                title = "Mentores",
                value = "${summary.professionals ?: 0} vinculado(s)",
                icon = Icons.Filled.Groups,
                onClick = onOpenProfessionals,
                modifier = Modifier.weight(1f),
            )
        }

        Spacer(Modifier.height(26.dp))

        Text(
            text = "Modulos do painel web",
            color = Color.White,
            fontSize = 18.sp,
            fontWeight = FontWeight.Black,
        )
        Text(
            text = "Atalhos adaptados para mobile. Itens sem API v1 ficam marcados como pendencia.",
            color = Color(0xFFA3AAB5),
            fontSize = 13.sp,
            lineHeight = 18.sp,
            modifier = Modifier.padding(top = 4.dp, bottom = 14.dp),
        )

        ModuleCard(
            title = "NexBot",
            description = if (profile.isPremium) "Coach IA com historico do aluno." else "Free com limite diario de mensagens.",
            icon = Icons.AutoMirrored.Filled.Chat,
            status = if (profile.isPremium) "VIP" else "Free limitado",
            onClick = onOpenChat,
        )
        ModuleCard(
            title = "Assinatura",
            description = "Planos, upgrade e checkout mobile.",
            icon = Icons.Filled.Lock,
            status = "Disponivel",
            onClick = onOpenProfile,
        )
        ModuleCard(
            title = "Hidratacao",
            description = "Nex Hydra existe no web, mas falta endpoint API v1 dedicado.",
            icon = Icons.Filled.WaterDrop,
            status = "Pendente API",
            enabled = false,
        )
        ModuleCard(
            title = "Ranking e trofeus",
            description = "Arena e conquistas existem no web como recursos Premium.",
            icon = Icons.Filled.EmojiEvents,
            status = "Pendente API",
            premium = true,
            enabled = false,
        )
        ModuleCard(
            title = "Descanso ativo",
            description = "Modulo Premium no web; falta contrato API mobile.",
            icon = Icons.Filled.SelfImprovement,
            status = "Pendente API",
            premium = true,
            enabled = false,
        )
        ModuleCard(
            title = "Relatorios e documentos",
            description = "PDFs, laudos, receitas e atestados ainda precisam de API v1.",
            icon = Icons.Filled.Description,
            status = "Pendente API",
            enabled = false,
        )
    }
}

@Composable
private fun HighlightCard(
    profile: ProfileDto,
    summary: HomeSummary,
) {
    Column(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(28.dp))
            .background(Color(0xE60A0F14))
            .border(
                width = 1.dp,
                color = Color.White.copy(alpha = 0.08f),
                shape = RoundedCornerShape(28.dp),
            )
            .padding(20.dp),
    ) {
        Text(
            text = "ACESSO RAPIDO",
            color = Color(0xFF6F7782),
            fontSize = 11.sp,
            fontWeight = FontWeight.Black,
        )
        Spacer(Modifier.height(12.dp))
        Text(
            text = if (profile.isPremium) "Continue sua jornada premium" else "Explore os recursos do seu plano",
            color = Color.White,
            fontSize = 22.sp,
            lineHeight = 28.sp,
            fontWeight = FontWeight.Black,
        )
        Spacer(Modifier.height(8.dp))
        Text(
            text = buildString {
                append("Use as abas abaixo para acompanhar treinos, evolucao, agenda, nutricao e conversar com o NexBot.")
                if ((summary.evolutionPhotos ?: 0) > 0) {
                    append(" Voce ja tem ${summary.evolutionPhotos} foto(s) de evolucao registrada(s).")
                }
            },
            color = Color(0xFFA3AAB5),
            fontSize = 14.sp,
            lineHeight = 20.sp,
        )
    }
}

private data class HomeSummary(
    val trainingPlans: Int? = null,
    val nutritionCalories: Int? = null,
    val assessments: Int? = null,
    val evolutionPhotos: Int? = null,
    val appointments: Int? = null,
    val professionals: Int? = null,
    val latestWeightKg: Double? = null,
)

@Composable
private fun QuickMetricCard(
    title: String,
    value: String,
    icon: ImageVector,
    onClick: (() -> Unit)? = null,
    modifier: Modifier = Modifier,
) {
    Row(
        modifier = modifier
            .then(if (onClick != null) Modifier.clickable(onClick = onClick) else Modifier)
            .clip(RoundedCornerShape(22.dp))
            .background(Color(0xCC0B1117))
            .border(
                width = 1.dp,
                color = Color.White.copy(alpha = 0.08f),
                shape = RoundedCornerShape(22.dp),
            )
            .padding(16.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Box(
            modifier = Modifier
                .size(40.dp)
                .clip(RoundedCornerShape(14.dp))
                .background(Color(0x1F10B981)),
            contentAlignment = Alignment.Center,
        ) {
            Icon(
                imageVector = icon,
                contentDescription = null,
                tint = Color(0xFF19F5A6),
                modifier = Modifier.size(22.dp),
            )
        }

        Column(modifier = Modifier.padding(start = 12.dp)) {
            Text(
                text = title,
                color = Color(0xFFA3AAB5),
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
            )
            Text(
                text = value,
                color = Color.White,
                fontSize = 15.sp,
                fontWeight = FontWeight.Black,
                modifier = Modifier.padding(top = 3.dp),
            )
        }
    }
}

@Composable
private fun ModuleCard(
    title: String,
    description: String,
    icon: ImageVector,
    status: String,
    enabled: Boolean = true,
    premium: Boolean = false,
    onClick: (() -> Unit)? = null,
) {
    val borderColor = when {
        !enabled -> Color(0x33FFFFFF)
        premium -> Color(0x55F59E0B)
        else -> Color.White.copy(alpha = 0.08f)
    }

    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(bottom = 10.dp)
            .then(if (enabled && onClick != null) Modifier.clickable(onClick = onClick) else Modifier)
            .clip(RoundedCornerShape(22.dp))
            .background(if (enabled) Color(0xCC0B1117) else Color(0x880B1117))
            .border(1.dp, borderColor, RoundedCornerShape(22.dp))
            .padding(16.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Box(
            modifier = Modifier
                .size(42.dp)
                .clip(RoundedCornerShape(14.dp))
                .background(if (premium) Color(0x22F59E0B) else Color(0x1F10B981)),
            contentAlignment = Alignment.Center,
        ) {
            Icon(
                imageVector = if (enabled) icon else Icons.Filled.Lock,
                contentDescription = null,
                tint = if (premium) Color(0xFFF59E0B) else Color(0xFF19F5A6),
                modifier = Modifier.size(22.dp),
            )
        }

        Column(
            modifier = Modifier
                .weight(1f)
                .padding(horizontal = 12.dp),
        ) {
            Text(
                text = title,
                color = if (enabled) Color.White else Color(0xFF8B929D),
                fontSize = 15.sp,
                fontWeight = FontWeight.Black,
            )
            Text(
                text = description,
                color = Color(0xFFA3AAB5),
                fontSize = 12.sp,
                lineHeight = 17.sp,
                modifier = Modifier.padding(top = 3.dp),
            )
        }

        Text(
            text = status,
            color = when {
                !enabled -> Color(0xFF6F7782)
                premium -> Color(0xFFF59E0B)
                else -> Color(0xFF19F5A6)
            },
            fontSize = 10.sp,
            fontWeight = FontWeight.Black,
        )
    }
}

@Composable
private fun HomeNeonBackground(modifier: Modifier = Modifier) {
    Canvas(modifier = modifier) {
        drawRect(
            brush = Brush.verticalGradient(
                colors = listOf(
                    Color(0xFF07100D),
                    Color(0xFF050708),
                    Color(0xFF020304),
                ),
                startY = 0f,
                endY = size.height,
            )
        )

        val topGlow = androidx.compose.ui.geometry.Offset(size.width * 0.85f, size.height * 0.05f)
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(Color(0xFF19F5A6).copy(alpha = 0.22f), Color.Transparent),
                center = topGlow,
                radius = size.width * 0.9f,
            ),
            radius = size.width * 0.9f,
            center = topGlow,
        )

        val bottomGlow = androidx.compose.ui.geometry.Offset(size.width * 0.1f, size.height * 0.9f)
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(Color(0xFF10B981).copy(alpha = 0.16f), Color.Transparent),
                center = bottomGlow,
                radius = size.width * 0.75f,
            ),
            radius = size.width * 0.75f,
            center = bottomGlow,
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
                color = Color(0xFF19F5A6).copy(alpha = 0.12f),
                style = androidx.compose.ui.graphics.drawscope.Stroke(width = 3.dp.toPx()),
            )
        }

        drawRect(
            brush = Brush.verticalGradient(
                colors = listOf(
                    Color.Transparent,
                    Color(0xD9050708),
                ),
                startY = size.height * 0.45f,
                endY = size.height,
            )
        )
    }
}
