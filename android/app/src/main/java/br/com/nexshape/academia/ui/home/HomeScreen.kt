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
import androidx.compose.material.icons.filled.CardGiftcard
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Construction
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.Groups
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.MedicalServices
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.SelfImprovement
import androidx.compose.material.icons.filled.LocalMall
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material.icons.filled.Star
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
import androidx.compose.ui.text.style.TextOverflow
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.data.repository.AiCreditsRepository
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
    onOpenDocuments: () -> Unit,
    onOpenGamification: () -> Unit,
    onOpenActiveRest: () -> Unit,
    onOpenCommunity: () -> Unit,
    onOpenMessages: () -> Unit,
    onOpenClinical: () -> Unit,
    onOpenExamsMeasures: () -> Unit,
    onOpenNotifications: () -> Unit,
    onOpenFitnessStore: () -> Unit,
    onOpenHydration: () -> Unit,
) {
    var profile by remember { mutableStateOf<ProfileDto?>(null) }
    var summary by remember { mutableStateOf(HomeSummary()) }
    var aiCredits by remember { mutableStateOf<Int?>(null) }
    var error by remember { mutableStateOf<String?>(null) }
    val trainingRepository = remember { TrainingRepository() }
    val nutritionRepository = remember { NutritionRepository() }
    val evolutionRepository = remember { EvolutionRepository() }
    val agendaRepository = remember { AgendaRepository() }
    val aiCreditsRepository = remember { AiCreditsRepository() }

    LaunchedEffect(Unit) {
        authRepository.loadProfile()
            .onSuccess { profile = it }
            .onFailure { error = it.message }

        aiCreditsRepository.balance()
            .onSuccess { aiCredits = it.balance }

        val trainingResponse = trainingRepository.getPlansResponse().getOrNull()
        val trainingCount = trainingResponse?.data?.size
        val nutritionCalories = nutritionRepository.diary().getOrNull()?.totals?.calories
        val assessments = evolutionRepository.assessments().getOrNull().orEmpty()
        val photos = evolutionRepository.photos().getOrNull().orEmpty()
        val appointments = agendaRepository.appointments().getOrNull().orEmpty()
        val professionals = agendaRepository.professionals().getOrNull().orEmpty()

        summary = HomeSummary(
            trainingPlans = trainingCount,
            hasProfessionalLink = trainingResponse?.meta?.hasProfessionalLink,
            canCreateOwnWorkout = trainingResponse?.meta?.canCreateOwnWorkout,
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
                aiCredits = aiCredits,
                onOpenTraining = onOpenTraining,
                onOpenEvolution = onOpenEvolution,
                onOpenAgenda = onOpenAgenda,
                onOpenNutrition = onOpenNutrition,
                onOpenChat = onOpenChat,
                onOpenProfessionals = onOpenProfessionals,
                onOpenProfile = onOpenProfile,
                onOpenDocuments = { onOpenDocuments() },
                onOpenGamification = { onOpenGamification() },
                onOpenActiveRest = { onOpenActiveRest() },
                onOpenCommunity = { onOpenCommunity() },
                onOpenMessages = { onOpenMessages() },
                onOpenClinical = { onOpenClinical() },
                onOpenExamsMeasures = { onOpenExamsMeasures() },
                onOpenNotifications = { onOpenNotifications() },
                onOpenFitnessStore = { onOpenFitnessStore() },
                onOpenHydration = { onOpenHydration() },
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
    aiCredits: Int?,
    onOpenTraining: () -> Unit,
    onOpenEvolution: () -> Unit,
    onOpenAgenda: () -> Unit,
    onOpenNutrition: () -> Unit,
    onOpenChat: () -> Unit,
    onOpenProfessionals: () -> Unit,
    onOpenProfile: () -> Unit,
    onOpenDocuments: () -> Unit,
    onOpenGamification: () -> Unit,
    onOpenActiveRest: () -> Unit,
    onOpenCommunity: () -> Unit,
    onOpenMessages: () -> Unit,
    onOpenClinical: () -> Unit,
    onOpenExamsMeasures: () -> Unit,
    onOpenNotifications: () -> Unit,
    onOpenFitnessStore: () -> Unit,
    onOpenHydration: () -> Unit,
) {
    val activeRole = remember { ApiClient.tokenStore().getActiveRole() }
    val isPatient = activeRole == "patient" || activeRole == "paciente"

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
                    text = if (isPatient) "PORTAL DE SAUDE" else "NEXSHAPE",
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

            Row(
                horizontalArrangement = Arrangement.spacedBy(10.dp),
                verticalAlignment = Alignment.CenterVertically,
            ) {
                HomeAiCreditsPill(aiCredits)
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
            text = if (isPatient) "Seu portal de acompanhamento clinico e saude." else "Seu hub de treino, nutricao e evolucao esta pronto para hoje.",
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
                title = "Agenda",
                value = "${summary.appointments ?: 0} consultas",
                icon = Icons.Filled.CalendarMonth,
                onClick = onOpenAgenda,
                modifier = Modifier.weight(1f),
            )
            QuickMetricCard(
                title = "Mentores",
                value = if ((summary.professionals ?: 0) > 0) {
                    "${summary.professionals} vinculados"
                } else {
                    "Independente"
                },
                icon = Icons.Filled.Groups,
                onClick = onOpenProfessionals,
                modifier = Modifier.weight(1f),
            )
        }

        Spacer(Modifier.height(26.dp))

        Text(
            text = "Recursos do NexShape",
            color = Color.White,
            fontSize = 18.sp,
            fontWeight = FontWeight.Black,
        )
        Text(
            text = "Tudo o que voce precisa em um so lugar.",
            color = Color(0xFFA3AAB5),
            fontSize = 13.sp,
            lineHeight = 18.sp,
            modifier = Modifier.padding(top = 4.dp, bottom = 14.dp),
        )

        if (isPatient) {
            ModuleCard(
                title = "Acompanhamento",
                description = "Protocolos, agenda e documentos do acompanhamento.",
                icon = Icons.Filled.MedicalServices,
                status = "Disponivel",
                onClick = onOpenClinical,
            )
            ModuleCard(
                title = "Avaliacoes e Exames",
                description = "Medidas, exames, laudos e evolucao.",
                icon = Icons.Filled.MonitorHeart,
                status = "Disponivel",
                onClick = onOpenExamsMeasures,
            )
            ModuleCard(
                title = "Relatórios e documentos",
                description = "Visualizar laudos, receitas e atestados emitidos.",
                icon = Icons.Filled.Description,
                status = "Disponivel",
                onClick = onOpenDocuments,
            )
            ModuleCard(
                title = "Chat",
                description = "Converse com profissionais e suporte.",
                icon = Icons.AutoMirrored.Filled.Chat,
                status = "Disponivel",
                onClick = onOpenMessages,
            )
            ModuleCard(
                title = "Notificacoes",
                description = "Mensagens e avisos pendentes da plataforma.",
                icon = Icons.Filled.Notifications,
                status = "Disponivel",
                onClick = onOpenNotifications,
            )
            ModuleCard(
                title = "Shopping NexShape",
                description = "Produtos, suplementos, cursos e servicos.",
                icon = Icons.Filled.LocalMall,
                status = "Em breve",
                onClick = onOpenFitnessStore,
            )

        } else {
            ModuleCard(
                title = "Comunidade",
                description = "Compartilhe sua evolucao.",
                icon = Icons.Filled.Groups,
                status = "Disponivel",
                onClick = onOpenCommunity,
            )
            ModuleCard(
                title = "Acompanhamento",
                description = "Protocolos e documentos.",
                icon = Icons.Filled.MedicalServices,
                status = "Disponivel",
                onClick = onOpenClinical,
            )
            ModuleCard(
                title = "Avaliacoes e Exames",
                description = "Medidas, exames e evolucao.",
                icon = Icons.Filled.MonitorHeart,
                status = "Disponivel",
                onClick = onOpenExamsMeasures,
            )
            ModuleCard(
                title = "Shopping NexShape",
                description = "Produtos e suplementos.",
                icon = Icons.Filled.LocalMall,
                status = "Em breve",
                onClick = onOpenFitnessStore,
            )

            ModuleCard(
                title = "Hidratacao",
                description = "Nex Hydra - Acompanhe seu consumo diario de agua.",
                icon = Icons.Filled.WaterDrop,
                status = "Disponivel",
                onClick = onOpenHydration,
            )
            ModuleCard(
                title = "Ranking e trofeus",
                description = "Sua posição na arena e conquistas desbloqueadas.",
                icon = Icons.Filled.EmojiEvents,
                status = if (profile.isPremium) "Disponivel" else "Requer assinatura",
                premium = !profile.isPremium,
                onClick = onOpenGamification,
            )
            ModuleCard(
                title = "Descanso ativo",
                description = "Sessões de alongamento, mobilidade e regeneração muscular.",
                icon = Icons.Filled.SelfImprovement,
                status = if (profile.isPremium) "Disponivel" else "Requer assinatura",
                premium = !profile.isPremium,
                onClick = onOpenActiveRest,
            )
            ModuleCard(
                title = "Relatórios e documentos",
                description = "Visualizar laudos, receitas e atestados emitidos.",
                icon = Icons.Filled.Description,
                status = "Disponivel",
                onClick = onOpenDocuments,
            )
            ModuleCard(
                title = "Notificacoes",
                description = "Mensagens e avisos pendentes da plataforma.",
                icon = Icons.Filled.Notifications,
                status = "Disponivel",
                onClick = onOpenNotifications,
            )
        }
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
                append("Use as abas abaixo para acompanhar treinos, evolucao, agenda, nutricao e conversar com o Assistente IA.")
                if (summary.hasProfessionalLink == false) {
                    append(" Voce esta em modo aluno independente.")
                }
                if (summary.trainingPlans == 0 && summary.canCreateOwnWorkout == true) {
                    append(" Comece criando sua primeira ficha.")
                }
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

@Composable
private fun HomeAiCreditsPill(credits: Int?) {
    Row(
        modifier = Modifier
            .clip(RoundedCornerShape(999.dp))
            .background(Color(0xFF071B16).copy(alpha = 0.94f))
            .border(1.dp, Color(0xFF19F5A6).copy(alpha = 0.36f), RoundedCornerShape(999.dp))
            .padding(horizontal = 10.dp, vertical = 7.dp),
        horizontalArrangement = Arrangement.spacedBy(6.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Text(
            text = "IA",
            color = Color(0xFF19F5A6),
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
            color = Color(0xFFA3AAB5),
            fontSize = 10.sp,
            fontWeight = FontWeight.Bold,
        )
    }
}

private data class HomeSummary(
    val trainingPlans: Int? = null,
    val hasProfessionalLink: Boolean? = null,
    val canCreateOwnWorkout: Boolean? = null,
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

        Column(
            modifier = Modifier
                .weight(1f)
                .padding(start = 12.dp),
        ) {
            Text(
                text = title,
                color = Color(0xFFA3AAB5),
                fontSize = 12.sp,
                fontWeight = FontWeight.Bold,
                maxLines = 1,
                overflow = TextOverflow.Ellipsis,
            )
            Text(
                text = value,
                color = Color.White,
                fontSize = 14.sp,
                lineHeight = 18.sp,
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
    showLockWhenDisabled: Boolean = true,
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
                imageVector = if (enabled || !showLockWhenDisabled) icon else Icons.Filled.Lock,
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

        StatusChip(status = status, enabled = enabled, premium = premium)
    }
}

@Composable
private fun StatusChip(
    status: String,
    enabled: Boolean,
    premium: Boolean,
) {
    val normalized = status.lowercase()
    val (icon, color) = when {
        !enabled -> Icons.Filled.Lock to Color(0xFF6F7782)
        "breve" in normalized -> Icons.Filled.Construction to Color(0xFFF59E0B)
        "assinatura" in normalized -> Icons.Filled.Lock to Color(0xFFF59E0B)
        "premium" in normalized || premium -> Icons.Filled.Star to Color(0xFFF59E0B)
        "novo" in normalized -> Icons.Filled.CardGiftcard to Color(0xFF38BDF8)
        else -> Icons.Filled.CheckCircle to Color(0xFF19F5A6)
    }

    Row(
        modifier = Modifier
            .clip(RoundedCornerShape(999.dp))
            .background(color.copy(alpha = 0.12f))
            .border(1.dp, color.copy(alpha = 0.28f), RoundedCornerShape(999.dp))
            .padding(horizontal = 8.dp, vertical = 5.dp),
        horizontalArrangement = Arrangement.spacedBy(4.dp),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Icon(
            imageVector = icon,
            contentDescription = null,
            tint = color,
            modifier = Modifier.size(13.dp),
        )
        Text(
            text = status,
            color = color,
            fontSize = 9.sp,
            fontWeight = FontWeight.Black,
            maxLines = 1,
            overflow = TextOverflow.Ellipsis,
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
