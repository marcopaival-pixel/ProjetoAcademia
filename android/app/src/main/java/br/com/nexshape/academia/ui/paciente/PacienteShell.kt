package br.com.nexshape.academia.ui.paciente

import androidx.compose.foundation.layout.padding
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.Icon
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.NavigationBarItemDefaults
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.ui.profile.ProfileScreen
import br.com.nexshape.academia.ui.home.HomeScreen

private enum class PacienteTab(val label: String) {
    Home("Início"),
    Evolution("Evolução"),
    Acompanhamento("Acompanhamento"),
    Messages("Mensagens"),
    Profile("Perfil"),
}

@Composable
fun PacienteShell(
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    canSwitchMode: Boolean,
    onSwitchMode: () -> Unit,
    onChangeContext: () -> Unit,
    onLogout: () -> Unit,
) {
    var selectedTab by remember { mutableStateOf(PacienteTab.Home) }
    var acompanhamentoRoute by remember { mutableStateOf<String?>(null) }

    Scaffold(
        bottomBar = {
            NavigationBar(
                containerColor = Color(0xFF080C10),
                contentColor = Color.White,
            ) {
                PacienteTab.entries.forEach { tab ->
                    NavigationBarItem(
                        selected = selectedTab == tab,
                        onClick = { 
                            selectedTab = tab 
                            if (tab != PacienteTab.Acompanhamento) acompanhamentoRoute = null
                        },
                        colors = NavigationBarItemDefaults.colors(
                            selectedIconColor = Color(0xFF04110D),
                            selectedTextColor = Color(0xFF19F5A6),
                            indicatorColor = Color(0xFF10B981),
                            unselectedIconColor = Color(0xFFA3AAB5),
                            unselectedTextColor = Color(0xFFA3AAB5),
                        ),
                        icon = {
                            Icon(
                                imageVector = when (tab) {
                                    PacienteTab.Home -> Icons.Default.Home
                                    PacienteTab.Evolution -> Icons.Default.MonitorHeart
                                    PacienteTab.Acompanhamento -> Icons.Default.Description
                                    PacienteTab.Messages -> Icons.AutoMirrored.Filled.Chat
                                    PacienteTab.Profile -> Icons.Default.Person
                                },
                                contentDescription = tab.label,
                            )
                        },
                        label = { 
                            Text(
                                text = tab.label, 
                                maxLines = 1, 
                                overflow = androidx.compose.ui.text.style.TextOverflow.Ellipsis, 
                                fontSize = 10.sp
                            ) 
                        },
                    )
                }
            }
        },
    ) { padding ->
        when (selectedTab) {
            PacienteTab.Home -> {
                br.com.nexshape.academia.ui.paciente.home.PacienteHomeScreen(
                    modifier = Modifier.padding(padding),
                    authRepository = authRepository,
                    onOpenEvolution = { selectedTab = PacienteTab.Evolution },
                    onOpenAgenda = { selectedTab = PacienteTab.Acompanhamento }, // Agenda provisoriamente no Acompanhamento
                    onOpenDocuments = { selectedTab = PacienteTab.Acompanhamento }
                )
            }
            PacienteTab.Evolution -> {
                br.com.nexshape.academia.ui.paciente.evolucao.PacienteEvolucaoScreen(
                    modifier = Modifier.padding(padding)
                )
            }
            PacienteTab.Acompanhamento -> {
                when (acompanhamentoRoute) {
                    "avaliacoes" -> br.com.nexshape.academia.ui.health.ExamsMeasuresScreen(
                        modifier = Modifier.padding(padding)
                    )
                    "treino" -> br.com.nexshape.academia.ui.training.TrainingScreen(
                        modifier = Modifier.padding(padding),
                        onNavigateToChat = { selectedTab = PacienteTab.Messages }
                    )
                    "nutricao" -> br.com.nexshape.academia.ui.nutrition.NutritionScreen(
                        modifier = Modifier.padding(padding)
                    )
                    "documentos" -> br.com.nexshape.academia.ui.documents.DocumentsScreen(
                        modifier = Modifier.padding(padding)
                    )
                    else -> br.com.nexshape.academia.ui.paciente.acompanhamento.AcompanhamentoHubScreen(
                        modifier = Modifier.padding(padding),
                        onNavigate = { route ->
                            acompanhamentoRoute = route
                        }
                    )
                }
            }
            PacienteTab.Messages -> {
                br.com.nexshape.academia.ui.paciente.mensagens.PacienteMensagensScreen(
                    modifier = Modifier.padding(padding)
                )
            }
            PacienteTab.Profile -> br.com.nexshape.academia.ui.paciente.perfil.PacientePerfilScreen(
                modifier = Modifier.padding(padding),
                authRepository = authRepository,
                appLockStore = appLockStore,
                canSwitchToStudent = canSwitchMode,
                onSwitchToStudent = onSwitchMode,
                onChangeContext = onChangeContext,
                onLogout = onLogout
            )
        }
    }
}
