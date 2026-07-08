package br.com.nexshape.academia.ui.login

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.animation.core.Spring
import androidx.compose.animation.core.spring
import androidx.compose.animation.fadeIn
import androidx.compose.animation.slideInVertically
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
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.AdminPanelSettings
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.MedicalServices
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
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
import androidx.compose.ui.graphics.vector.ImageVector
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import kotlinx.coroutines.delay

/**
 * Modelo de dado para um cartão de perfil exibido na tela de seleção.
 */
data class ProfileCardData(
    val role: String,
    val title: String,
    val subtitle: String,
    val features: List<String>,
    val icon: ImageVector,
    val gradient: List<Color>,
)

/**
 * Mapeia uma role string retornada pela API para os dados de exibição do Card.
 */
fun roleToCardData(role: String): ProfileCardData? = when (role) {
    "paciente", "athlete" -> ProfileCardData(
        role = role,
        title = "Atleta",
        subtitle = "Sua jornada de performance",
        features = listOf("Treinos personalizados", "Evolução física", "Metas e conquistas"),
        icon = Icons.Default.FitnessCenter,
        gradient = listOf(Color(0xFF1A73E8), Color(0xFF0D47A1)),
    )
    "professional", "instructor", "supervisor" -> ProfileCardData(
        role = role,
        title = "Profissional",
        subtitle = "Gestão de atletas e prescrições",
        features = listOf("Gerenciar atletas", "Prescrever treinos", "Avaliações físicas"),
        icon = Icons.Default.Person,
        gradient = listOf(Color(0xFF2E7D32), Color(0xFF1B5E20)),
    )
    "admin", "clinic_admin" -> ProfileCardData(
        role = role,
        title = "Clínica / Academia",
        subtitle = "Administração do centro esportivo",
        features = listOf("Gestão de equipe", "Financeiro", "Relatórios"),
        icon = Icons.Default.AdminPanelSettings,
        gradient = listOf(Color(0xFF6A1B9A), Color(0xFF4A148C)),
    )
    else -> null
}

/**
 * Tela de Seleção de Perfil — exibida quando o usuário possui múltiplos papéis.
 *
 * @param userName Nome de exibição do usuário logado.
 * @param availableRoles Lista de roles disponíveis (ex: ["paciente", "professional"]).
 * @param onRoleSelected Callback disparado quando o usuário escolhe um papel.
 */
@Composable
fun ProfileSelectorScreen(
    userName: String,
    availableRoles: List<String>,
    onRoleSelected: (role: String) -> Unit,
) {
    // Filtra apenas as roles que têm representação visual
    val cards = availableRoles.mapNotNull { roleToCardData(it) }

    // Controle de animação de entrada
    var visible by remember { mutableStateOf(false) }
    LaunchedEffect(Unit) {
        delay(100)
        visible = true
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(
                    colors = listOf(Color(0xFF0A0E1A), Color(0xFF111827)),
                ),
            ),
    ) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 24.dp, vertical = 48.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
        ) {
            // Header
            AnimatedVisibility(
                visible = visible,
                enter = fadeIn() + slideInVertically(
                    animationSpec = spring(stiffness = Spring.StiffnessLow),
                    initialOffsetY = { -40 },
                ),
            ) {
                Column(horizontalAlignment = Alignment.CenterHorizontally) {
                    Text(
                        text = "👋 Bem-vindo, ${userName.split(" ").first()}!",
                        color = Color.White,
                        fontSize = 26.sp,
                        fontWeight = FontWeight.Bold,
                    )
                    Spacer(Modifier.height(8.dp))
                    Text(
                        text = "Como deseja acessar o NexShape hoje?",
                        color = Color(0xFF9CA3AF),
                        fontSize = 15.sp,
                        textAlign = TextAlign.Center,
                    )
                }
            }

            Spacer(Modifier.height(36.dp))

            // Cards de perfil
            cards.forEachIndexed { index, card ->
                AnimatedVisibility(
                    visible = visible,
                    enter = fadeIn() + slideInVertically(
                        animationSpec = spring(
                            stiffness = Spring.StiffnessLow,
                            dampingRatio = Spring.DampingRatioMediumBouncy,
                        ),
                        initialOffsetY = { 80 * (index + 1) },
                    ),
                ) {
                    ProfileCard(card = card, onSelect = { onRoleSelected(card.role) })
                }
                Spacer(Modifier.height(16.dp))
            }
        }
    }
}

@Composable
private fun ProfileCard(
    card: ProfileCardData,
    onSelect: () -> Unit,
) {
    var isPressed by remember { mutableStateOf(false) }

    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(20.dp))
            .border(
                width = 1.dp,
                color = Color(0xFF1F2937),
                shape = RoundedCornerShape(20.dp),
            )
            .clickable { onSelect() },
        colors = CardDefaults.cardColors(containerColor = Color(0xFF1C2333)),
        elevation = CardDefaults.cardElevation(defaultElevation = 8.dp),
    ) {
        Column(modifier = Modifier.padding(20.dp)) {
            // Topo do card: ícone + título
            Row(verticalAlignment = Alignment.CenterVertically) {
                Box(
                    modifier = Modifier
                        .size(52.dp)
                        .clip(RoundedCornerShape(14.dp))
                        .background(Brush.linearGradient(card.gradient)),
                    contentAlignment = Alignment.Center,
                ) {
                    Icon(
                        imageVector = card.icon,
                        contentDescription = card.title,
                        tint = Color.White,
                        modifier = Modifier.size(28.dp),
                    )
                }
                Spacer(Modifier.width(16.dp))
                Column {
                    Text(
                        text = card.title,
                        color = Color.White,
                        fontWeight = FontWeight.Bold,
                        fontSize = 18.sp,
                    )
                    Text(
                        text = card.subtitle,
                        color = Color(0xFF9CA3AF),
                        fontSize = 13.sp,
                    )
                }
            }

            Spacer(Modifier.height(16.dp))

            // Features do perfil
            card.features.forEach { feature ->
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    modifier = Modifier.padding(vertical = 3.dp),
                ) {
                    Box(
                        modifier = Modifier
                            .size(6.dp)
                            .clip(RoundedCornerShape(50))
                            .background(card.gradient.first()),
                    )
                    Spacer(Modifier.width(10.dp))
                    Text(
                        text = feature,
                        color = Color(0xFFD1D5DB),
                        fontSize = 13.sp,
                    )
                }
            }

            Spacer(Modifier.height(20.dp))

            // Botão de entrada
            Button(
                onClick = onSelect,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = card.gradient.first(),
                ),
            ) {
                Text(
                    text = "Entrar como ${card.title}",
                    fontWeight = FontWeight.SemiBold,
                    modifier = Modifier.padding(vertical = 4.dp),
                )
            }
        }
    }
}
