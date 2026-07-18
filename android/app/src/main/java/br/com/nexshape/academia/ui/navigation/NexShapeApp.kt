package br.com.nexshape.academia.ui.navigation

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.layout.size
import androidx.compose.ui.Alignment
import androidx.compose.ui.unit.sp
import androidx.compose.ui.graphics.Color
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.EmojiEvents
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.Group
import androidx.compose.material.icons.filled.HelpOutline
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.MedicalServices
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.SelfImprovement
import androidx.compose.material.icons.filled.LocalMall
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.Icon
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.NavigationBar
import androidx.compose.material3.NavigationBarItem
import androidx.compose.material3.NavigationBarItemDefaults
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.unit.dp
import androidx.fragment.app.FragmentActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleEventObserver
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.data.local.AppMode
import br.com.nexshape.academia.data.local.SessionPreferences
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.data.repository.OfflineSyncRepository
import br.com.nexshape.academia.security.BiometricHelper
import br.com.nexshape.academia.ui.security.AppLockScreen
import br.com.nexshape.academia.ui.components.NexNeon
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import br.com.nexshape.academia.ui.agenda.AgendaScreen
import br.com.nexshape.academia.ui.chat.ChatScreen
import br.com.nexshape.academia.ui.clinical.ClinicalConductScreen
import br.com.nexshape.academia.ui.community.CommunityScreen
import br.com.nexshape.academia.ui.evolution.EvolutionScreen
import br.com.nexshape.academia.ui.health.ExamsMeasuresScreen
import br.com.nexshape.academia.ui.home.HomeScreen
import br.com.nexshape.academia.ui.login.LoginScreen
import br.com.nexshape.academia.ui.login.ProfileSelectorScreen
import br.com.nexshape.academia.ui.login.roleToCardData
import br.com.nexshape.academia.ui.login.ProfileCardData
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.AdminPanelSettings
import br.com.nexshape.academia.ui.nutrition.NutritionScreen
import br.com.nexshape.academia.ui.notifications.NotificationsScreen
import br.com.nexshape.academia.ui.professionals.ProfessionalsScreen
import br.com.nexshape.academia.ui.profile.ProfileScreen
import br.com.nexshape.academia.ui.professional.ProAgendaScreen
import br.com.nexshape.academia.ui.professional.ProAlertsScreen
import br.com.nexshape.academia.ui.professional.ProHomeScreen
import br.com.nexshape.academia.ui.professional.ProPatientCareScreen
import br.com.nexshape.academia.ui.professional.ProPatientsScreen
import br.com.nexshape.academia.ui.training.TrainingScreen
import br.com.nexshape.academia.ui.documents.DocumentsScreen
import br.com.nexshape.academia.ui.gamification.GamificationScreen
import br.com.nexshape.academia.ui.activerest.ActiveRestScreen
import br.com.nexshape.academia.ui.messages.MessagesScreen
import br.com.nexshape.academia.ui.shop.FitnessStoreScreen

private enum class StudentTab(val label: String) {
    Home("Início"),
    Training("Treino"),
    Evolution("Evolução"),
    Agenda("Agenda"),
    Nutrition("Nutrição"),
    Chat("Assistente IA"),
    Professionals("Mentores"),
    Profile("Perfil"),
    Documents("Documentos"),
    Gamification("Conquistas e rankings"),
    ActiveRest("Descanso ativo"),
    Community("Comunidade"),
    Messages("Chat"),
    Clinical("Clinico"),
    ExamsMeasures("Exames"),
    Notifications("Notificacoes"),
    FitnessStore("Shopping"),
    Hydration("Hidratação"),
}

private enum class ProTab(val label: String) {
    Dashboard("Painel"),
    Patients("Alunos"),
    Care("Clínico"),
    Agenda("Agenda"),
    Alerts("Alertas"),
    Profile("Perfil"),
}

@Composable
fun NexShapeApp() {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val lifecycleOwner = LocalLifecycleOwner.current
    ApiClient.init(context.applicationContext)
    val sessionPreferences = remember { ApiClient.sessionPreferences() }
    val appLockStore = remember { AppLockStore(context.applicationContext) }
    val authRepository = remember {
        AuthRepository(ApiClient.tokenStore(), context.applicationContext)
    }
    val activity = context as? FragmentActivity
    val biometricAvailable = activity != null && BiometricHelper.canAuthenticate(activity)
    var isLoggedIn by remember { mutableStateOf(authRepository.isLoggedIn()) }
    var isUnlocked by remember {
        mutableStateOf(!authRepository.isLoggedIn() || !appLockStore.shouldRequireUnlock(biometricAvailable))
    }
    var profile by remember { mutableStateOf<ProfileDto?>(null) }
    var appMode by remember { mutableStateOf(sessionPreferences.getAppMode()) }
    var showRoleSelector by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }

    DisposableEffect(lifecycleOwner, isLoggedIn) {
        if (!isLoggedIn) {
            return@DisposableEffect onDispose { }
        }
        val observer = LifecycleEventObserver { _, event ->
            if (event == Lifecycle.Event.ON_STOP && appLockStore.shouldRequireUnlock(biometricAvailable)) {
                isUnlocked = false
            }
        }
        lifecycleOwner.lifecycle.addObserver(observer)
        onDispose { lifecycleOwner.lifecycle.removeObserver(observer) }
    }

    if (!isLoggedIn) {
        LoginScreen(
            authRepository = authRepository,
            onLoggedIn = {
                isLoggedIn = true
                isUnlocked = true
                appMode = sessionPreferences.getAppMode()
            },
        )
        return
    }

    var showContextSelector by remember { mutableStateOf(false) }
    var contextOptions by remember { mutableStateOf<List<ProfileCardData>>(emptyList()) }

    // Seletor de perfil para usuários com múltiplos papéis
    if (showRoleSelector) {
        val userName = ApiClient.tokenStore().getName() ?: "Usuário"
        val availableRoles = ApiClient.tokenStore().getAvailableRoles()
        ProfileSelectorScreen(
            userName = userName,
            cards = availableRoles.mapNotNull { roleToCardData(it) },
            onCardSelected = { selectedRoleCard ->
                val selectedRole = selectedRoleCard.id
                ApiClient.tokenStore().saveActiveRole(selectedRole)
                ApiClient.tokenStore().setActiveRoleConfirmed(true)
                showRoleSelector = false
                
                if (selectedRole == "student" || selectedRole == "aluno" || selectedRole == "athlete") {
                    val contexts = profile?.accessContexts ?: emptyList()
                    if (contexts.size > 1) {
                        contextOptions = contexts.map { ctx ->
                            ProfileCardData(
                                id = "${ctx.type}:${ctx.id}",
                                title = ctx.label,
                                subtitle = when(ctx.type) {
                                    "personal" -> "Minha assinatura, meus treinos, minhas metas e minha evolução"
                                    else -> "Treinos, avaliações e orientações vinculadas"
                                },
                                features = emptyList(),
                                icon = when(ctx.type) {
                                    "personal" -> androidx.compose.material.icons.Icons.Default.FitnessCenter
                                    "professional" -> androidx.compose.material.icons.Icons.Default.Person
                                    else -> androidx.compose.material.icons.Icons.Default.AdminPanelSettings
                                },
                                gradient = when(ctx.type) {
                                    "personal" -> listOf(Color(0xFF1A73E8), Color(0xFF0D47A1))
                                    "professional" -> listOf(Color(0xFF2E7D32), Color(0xFF1B5E20))
                                    else -> listOf(Color(0xFF6A1B9A), Color(0xFF4A148C))
                                }
                            )
                        }
                        showContextSelector = true
                    } else {
                        if (contexts.isNotEmpty()) {
                            val firstCtx = contexts.first()
                            ApiClient.tokenStore().saveActiveContextId("${firstCtx.type}:${firstCtx.id}")
                        }
                        appMode = AppMode.STUDENT
                        sessionPreferences.setAppMode(AppMode.STUDENT)
                    }
                } else {
                    appMode = AppMode.PROFESSIONAL
                    sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                }
            },
        )
        return
    }

    if (showContextSelector) {
        val userName = ApiClient.tokenStore().getName() ?: "Usuário"
        ProfileSelectorScreen(
            userName = userName,
            titleText = "Como deseja acessar seu painel hoje?",
            cards = contextOptions,
            onCardSelected = { selectedContext ->
                ApiClient.tokenStore().saveActiveContextId(selectedContext.id)
                showContextSelector = false
                appMode = AppMode.STUDENT
                sessionPreferences.setAppMode(AppMode.STUDENT)
            }
        )
        return
    }

    if (appLockStore.shouldRequireUnlock(biometricAvailable) && !isUnlocked) {
        AppLockScreen(
            appLockStore = appLockStore,
            onUnlocked = { isUnlocked = true },
            onLogout = {
                scope.launch {
                    authRepository.logout()
                    isLoggedIn = false
                    isUnlocked = true
                }
            },
        )
        return
    }

    LaunchedEffect(isLoggedIn) {
        if (isLoggedIn) {
            withContext(Dispatchers.IO) {
                runCatching { OfflineSyncRepository(context.applicationContext).flush(context.applicationContext) }
            }
            authRepository.loadProfile()
                .onSuccess { loaded ->
                    profile = loaded
                    error = null
                    
                    val freshRoles = loaded.roles?.toMutableList() ?: mutableListOf()
                    if ((freshRoles.contains("student") || freshRoles.contains("aluno") || freshRoles.contains("athlete")) && loaded.studentStatus == "vinculado") {
                        if (!freshRoles.contains("paciente")) {
                            freshRoles.add("paciente")
                        }
                    }
                    ApiClient.tokenStore().saveAvailableRoles(freshRoles)
                    
                    val availableRoles = ApiClient.tokenStore().getAvailableRoles()
                    val selectableCards = availableRoles.mapNotNull { roleToCardData(it) }

                    if (selectableCards.size > 1 && !ApiClient.tokenStore().isActiveRoleConfirmed()) {
                        showRoleSelector = true
                    } else {
                        val activeRole = ApiClient.tokenStore().getActiveRole() ?: resolveStudentRole(availableRoles)
                        ApiClient.tokenStore().saveActiveRole(activeRole)

                        if (activeRole == "student" || activeRole == "aluno" || activeRole == "athlete") {
                            val contexts = loaded.accessContexts ?: emptyList()
                            if (contexts.size > 1 && ApiClient.tokenStore().getActiveContextId().isNullOrBlank()) {
                                contextOptions = contexts.map { ctx ->
                                    ProfileCardData(
                                        id = "${ctx.type}:${ctx.id}",
                                        title = ctx.label,
                                        subtitle = when(ctx.type) {
                                            "personal" -> "Minha assinatura, meus treinos, minhas metas e minha evolução"
                                            else -> "Treinos, avaliações e orientações vinculadas"
                                        },
                                        features = emptyList(),
                                        icon = when(ctx.type) {
                                            "personal" -> androidx.compose.material.icons.Icons.Default.FitnessCenter
                                            "professional" -> androidx.compose.material.icons.Icons.Default.Person
                                            else -> androidx.compose.material.icons.Icons.Default.AdminPanelSettings
                                        },
                                        gradient = when(ctx.type) {
                                            "personal" -> listOf(Color(0xFF1A73E8), Color(0xFF0D47A1))
                                            "professional" -> listOf(Color(0xFF2E7D32), Color(0xFF1B5E20))
                                            else -> listOf(Color(0xFF6A1B9A), Color(0xFF4A148C))
                                        }
                                    )
                                }
                                showContextSelector = true
                            } else {
                                if (ApiClient.tokenStore().getActiveContextId().isNullOrBlank() && contexts.isNotEmpty()) {
                                    val firstCtx = contexts.first()
                                    ApiClient.tokenStore().saveActiveContextId("${firstCtx.type}:${firstCtx.id}")
                                }
                                appMode = AppMode.STUDENT
                                sessionPreferences.setAppMode(AppMode.STUDENT)
                            }
                        } else {
                            appMode = AppMode.PROFESSIONAL
                            sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                        }
                    }
                }
                .onFailure { err ->
                    error = err.message ?: "Erro de conexao com o servidor"
                }
        }
    }

    val canUseStudent = profile?.isStudent == true
    val canUsePro = profile?.isProfessional == true

    val handleLogout: () -> Unit = {
        scope.launch {
            authRepository.logout()
            isLoggedIn = false
            isUnlocked = true
            profile = null
        }
    }

    if (profile == null) {
        Column(
            modifier = Modifier.padding(24.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Text(
                text = error ?: "Carregando perfil...",
                color = if (error != null) Color.Red else Color(0xFF19F5A6),
            )
            if (error != null) {
                androidx.compose.material3.Button(onClick = {
                    scope.launch {
                        authRepository.loadProfile()
                            .onSuccess { profile = it; error = null }
                            .onFailure { error = it.message ?: "Erro ao carregar perfil" }
                    }
                }) {
                    Text("Tentar Novamente")
                }
                Spacer(modifier = Modifier.height(8.dp))
                androidx.compose.material3.TextButton(onClick = handleLogout) {
                    Text("Voltar para o Login", color = NexNeon)
                }
            }
        }
        return
    }

    when {
        appMode == AppMode.PROFESSIONAL && canUsePro -> ProShell(
            sessionPreferences = sessionPreferences,
            authRepository = authRepository,
            appLockStore = appLockStore,
            canSwitchToStudent = canUseStudent,
            onSwitchMode = {
                sessionPreferences.setAppMode(AppMode.STUDENT)
                ApiClient.tokenStore().saveActiveRole(resolveStudentRole(ApiClient.tokenStore().getAvailableRoles()))
                ApiClient.tokenStore().setActiveRoleConfirmed(true)
                appMode = AppMode.STUDENT
            },
            onLogout = handleLogout,
        )
        canUseStudent -> StudentShell(
            profile = profile,
            authRepository = authRepository,
            appLockStore = appLockStore,
            canSwitchToPro = canUsePro,
            onSwitchMode = {
                sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                ApiClient.tokenStore().saveActiveRole(resolveProfessionalRole(ApiClient.tokenStore().getAvailableRoles()))
                ApiClient.tokenStore().setActiveRoleConfirmed(true)
                appMode = AppMode.PROFESSIONAL
            },
            onLogout = handleLogout,
        )
        else -> ProfileScreen(
            modifier = Modifier,
            authRepository = authRepository,
            appLockStore = appLockStore,
            showSubscription = false,
            onLogout = handleLogout,
        )
    }
}

@Composable
private fun StudentShell(
    profile: ProfileDto?,
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    canSwitchToPro: Boolean,
    onSwitchMode: () -> Unit,
    onLogout: () -> Unit,
) {
    val activeRole = remember { ApiClient.tokenStore().getActiveRole() }
    val isPatient = activeRole == "patient" || activeRole == "paciente"

    var selectedTab by remember(activeRole) { mutableStateOf(StudentTab.Home) }
    
    val bottomTabs = remember(profile?.modules, isPatient) {
        val fixedStart = StudentTab.Home
        val fixedEnd = listOf(StudentTab.Messages, StudentTab.Profile)
        
        val dynamicTabs = mutableListOf<StudentTab>()
        val modules = profile?.modules
        
        if (modules != null) {
            // Usa as permissões do backend, com ordem de prioridade
            if (modules.contains("training")) dynamicTabs.add(StudentTab.Training)
            if (modules.contains("clinical")) dynamicTabs.add(StudentTab.Clinical)
            if (modules.contains("nutrition")) dynamicTabs.add(StudentTab.Nutrition)
            if (modules.contains("evolution")) dynamicTabs.add(StudentTab.Evolution)
            if (modules.contains("agenda")) dynamicTabs.add(StudentTab.Agenda)
        } else {
            // Fallback baseado no papel
            if (isPatient) {
                dynamicTabs.add(StudentTab.Clinical)
                dynamicTabs.add(StudentTab.Evolution)
            } else {
                dynamicTabs.add(StudentTab.Training)
                dynamicTabs.add(StudentTab.Nutrition)
            }
        }
        
        val centralSlots = dynamicTabs.take(2)
        listOf(fixedStart) + centralSlots + fixedEnd
    }

    var showAssistenteSheet by remember { mutableStateOf(false) }

    Scaffold(
        floatingActionButton = {
            androidx.compose.material3.FloatingActionButton(
                onClick = { showAssistenteSheet = true },
                containerColor = br.com.nexshape.academia.ui.components.NexNeon,
                contentColor = Color.Black,
                shape = androidx.compose.foundation.shape.CircleShape,
                modifier = Modifier.padding(bottom = 16.dp)
            ) {
                Icon(Icons.AutoMirrored.Filled.Chat, contentDescription = "Assistente NexShape")
            }
        },
        bottomBar = {
            NavigationBar(
                containerColor = Color(0xFF080C10),
                contentColor = Color.White,
            ) {
                bottomTabs.forEach { tab ->
                    NavigationBarItem(
                        selected = selectedTab == tab,
                        onClick = { selectedTab = tab },
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
                                    StudentTab.Home -> Icons.Default.Home
                                    StudentTab.Training -> Icons.Default.FitnessCenter
                                    StudentTab.Evolution -> Icons.Default.MonitorHeart
                                    StudentTab.Agenda -> Icons.Default.CalendarMonth
                                    StudentTab.Nutrition -> Icons.Default.Restaurant
                                    StudentTab.Chat -> Icons.AutoMirrored.Filled.Chat
                                    StudentTab.Professionals -> Icons.Default.Group
                                    StudentTab.Profile -> Icons.Default.Person
                                    StudentTab.Documents -> Icons.Default.Description
                                    StudentTab.Gamification -> Icons.Default.EmojiEvents
                                    StudentTab.ActiveRest -> Icons.Default.SelfImprovement
                                    StudentTab.Community -> Icons.Default.Group
                                    StudentTab.Messages -> Icons.AutoMirrored.Filled.Chat
                                    StudentTab.Clinical -> Icons.Default.MedicalServices
                                    StudentTab.ExamsMeasures -> Icons.Default.Description
                                    StudentTab.Notifications -> Icons.Default.Notifications
                                    StudentTab.FitnessStore -> Icons.Default.LocalMall
                                    StudentTab.Hydration -> Icons.Default.WaterDrop
                                },
                                contentDescription = tab.label,
                            )
                        },
                        label = { Text(tab.label) },
                    )
                }
            }
        },
    ) { padding ->
        when (selectedTab) {
            StudentTab.Home -> HomeScreen(
                modifier = Modifier.padding(padding),
                authRepository = authRepository,
                onOpenTraining = { selectedTab = StudentTab.Training },
                onOpenEvolution = { selectedTab = StudentTab.Evolution },
                onOpenAgenda = { selectedTab = StudentTab.Agenda },
                onOpenNutrition = { selectedTab = StudentTab.Nutrition },
                onOpenChat = { selectedTab = StudentTab.Chat },
                onOpenProfessionals = { selectedTab = StudentTab.Professionals },
                onOpenProfile = { selectedTab = StudentTab.Profile },
                onOpenDocuments = { selectedTab = StudentTab.Documents },
                onOpenGamification = { selectedTab = StudentTab.Gamification },
                onOpenActiveRest = { selectedTab = StudentTab.ActiveRest },
                onOpenCommunity = { selectedTab = StudentTab.Community },
                onOpenMessages = { selectedTab = StudentTab.Messages },
                onOpenClinical = { selectedTab = StudentTab.Clinical },
                onOpenExamsMeasures = { selectedTab = StudentTab.ExamsMeasures },
                onOpenNotifications = { selectedTab = StudentTab.Notifications },
                onOpenFitnessStore = { selectedTab = StudentTab.FitnessStore },
                onOpenHydration = { selectedTab = StudentTab.Hydration },
            )
            StudentTab.Training -> TrainingScreen(
                modifier = Modifier.padding(padding),
                onNavigateToChat = { selectedTab = StudentTab.Chat }
            )
            StudentTab.Evolution -> EvolutionScreen(modifier = Modifier.padding(padding))
            StudentTab.Agenda -> AgendaScreen(modifier = Modifier.padding(padding))
            StudentTab.Nutrition -> NutritionScreen(modifier = Modifier.padding(padding))
            StudentTab.Chat -> ChatScreen(modifier = Modifier.padding(padding))
            StudentTab.Professionals -> ProfessionalsScreen(modifier = Modifier.padding(padding))
            StudentTab.Profile -> ProfileScreen(
                modifier = Modifier.padding(padding),
                authRepository = authRepository,
                appLockStore = appLockStore,
                canSwitchToPro = canSwitchToPro,
                onSwitchToPro = onSwitchMode,
                onLogout = onLogout,
            )
            StudentTab.Documents -> DocumentsScreen(modifier = Modifier.padding(padding))
            StudentTab.Gamification -> GamificationScreen(modifier = Modifier.padding(padding))
            StudentTab.ActiveRest -> ActiveRestScreen(modifier = Modifier.padding(padding))
            StudentTab.Community -> CommunityScreen(modifier = Modifier.padding(padding))
            StudentTab.Messages -> MessagesScreen(modifier = Modifier.padding(padding))
            StudentTab.Clinical -> ClinicalConductScreen(modifier = Modifier.padding(padding))
            StudentTab.ExamsMeasures -> ExamsMeasuresScreen(modifier = Modifier.padding(padding))
            StudentTab.Notifications -> NotificationsScreen(modifier = Modifier.padding(padding))
            StudentTab.FitnessStore -> FitnessStoreScreen(modifier = Modifier.padding(padding))
            StudentTab.Hydration -> br.com.nexshape.academia.ui.nutrition.HydrationScreen(modifier = Modifier.padding(padding))
        }
    }

    if (showAssistenteSheet) {
        AssistenteNexShapeSheet(
            onDismiss = { showAssistenteSheet = false },
            onAction = { action ->
                showAssistenteSheet = false
                when (action) {
                    "treino" -> selectedTab = StudentTab.Training
                    "nutricao" -> selectedTab = StudentTab.Nutrition
                    "evolucao" -> selectedTab = StudentTab.Evolution
                    "hidratacao" -> selectedTab = StudentTab.Hydration
                    "agenda" -> selectedTab = StudentTab.Agenda
                    "profissional" -> selectedTab = StudentTab.Messages
                    "suporte" -> selectedTab = StudentTab.Chat
                }
            }
        )
    }
}

private fun resolveStudentRole(roles: List<String>): String? =
    when {
        "aluno" in roles -> "aluno"
        "paciente" in roles -> "paciente"
        else -> roles.firstOrNull()
    }

private fun resolveProfessionalRole(roles: List<String>): String? =
    listOf("professional", "instructor", "supervisor", "admin", "clinic_admin")
        .firstOrNull { it in roles }
        ?: roles.firstOrNull()

@Composable
private fun ProShell(
    sessionPreferences: SessionPreferences,
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    canSwitchToStudent: Boolean,
    onSwitchMode: () -> Unit,
    onLogout: () -> Unit,
) {
    var selectedTab by remember { mutableStateOf(ProTab.Dashboard) }
    val activePatientId = sessionPreferences.getActivePatientId()
    val activePatientName = sessionPreferences.getActivePatientName()

    Scaffold(
        topBar = {
            if (activePatientId != null) {
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 8.dp),
                ) {
                    Text(
                        "Aluno ativo: ${activePatientName ?: "#$activePatientId"}",
                        style = MaterialTheme.typography.bodySmall,
                        color = MaterialTheme.colorScheme.primary,
                    )
                }
            }
        },
        bottomBar = {
            NavigationBar {
                ProTab.entries.forEach { tab ->
                    val enabled = tab != ProTab.Care || activePatientId != null
                    NavigationBarItem(
                        selected = selectedTab == tab,
                        onClick = { if (enabled) selectedTab = tab },
                        enabled = enabled,
                        icon = {
                            Icon(
                                imageVector = when (tab) {
                                    ProTab.Dashboard -> Icons.Default.Dashboard
                                    ProTab.Patients -> Icons.Default.Group
                                    ProTab.Care -> Icons.Default.MedicalServices
                                    ProTab.Agenda -> Icons.Default.CalendarMonth
                                    ProTab.Alerts -> Icons.Default.Notifications
                                    ProTab.Profile -> Icons.Default.Person
                                },
                                contentDescription = tab.label,
                            )
                        },
                        label = { Text(tab.label) },
                    )
                }
            }
        },
    ) { padding ->
        when (selectedTab) {
            ProTab.Dashboard -> ProHomeScreen(modifier = Modifier.padding(padding))
            ProTab.Patients -> ProPatientsScreen(
                modifier = Modifier.padding(padding),
                onPatientSelected = { selectedTab = ProTab.Care },
            )
            ProTab.Care -> ProPatientCareScreen(modifier = Modifier.padding(padding))
            ProTab.Agenda -> ProAgendaScreen(modifier = Modifier.padding(padding))
            ProTab.Alerts -> ProAlertsScreen(modifier = Modifier.padding(padding))
            ProTab.Profile -> ProfileScreen(
                modifier = Modifier.padding(padding),
                authRepository = authRepository,
                appLockStore = appLockStore,
                showSubscription = false,
                canSwitchToStudent = canSwitchToStudent,
                onSwitchToStudent = onSwitchMode,
                onLogout = onLogout,
            )
        }
    }
}

@OptIn(androidx.compose.material3.ExperimentalMaterial3Api::class)
@Composable
fun AssistenteNexShapeSheet(
    onDismiss: () -> Unit,
    onAction: (String) -> Unit
) {
    androidx.compose.material3.ModalBottomSheet(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF0D141C)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp)
        ) {
            Row(verticalAlignment = Alignment.CenterVertically) {
                Icon(Icons.AutoMirrored.Filled.Chat, contentDescription = null, tint = br.com.nexshape.academia.ui.components.NexNeon)
                Spacer(modifier = Modifier.width(8.dp))
                Text("Assistente NexShape", color = Color.White, fontSize = 20.sp, fontWeight = androidx.compose.ui.text.font.FontWeight.Bold)
            }
            Text("O que você deseja?", color = br.com.nexshape.academia.ui.components.NexMuted, modifier = Modifier.padding(top = 4.dp, bottom = 16.dp))

            val items = listOf(
                Triple("treino", "Treino", Icons.Default.FitnessCenter),
                Triple("nutricao", "Nutrição", Icons.Default.Restaurant),
                Triple("evolucao", "Evolução", Icons.Default.MonitorHeart),
                Triple("hidratacao", "Hidratação", Icons.Default.WaterDrop),
                Triple("agenda", "Agenda", Icons.Default.CalendarMonth),
                Triple("profissional", "Conversar com meu profissional", Icons.AutoMirrored.Filled.Chat),
                Triple("suporte", "Suporte / NexBot", Icons.Default.HelpOutline)
            )

            items.forEach { (action, label, icon) ->
                androidx.compose.material3.TextButton(
                    onClick = { onAction(action) },
                    modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp),
                    shape = androidx.compose.foundation.shape.RoundedCornerShape(8.dp)
                ) {
                    Row(
                        modifier = Modifier.fillMaxWidth(),
                        verticalAlignment = Alignment.CenterVertically
                    ) {
                        Icon(icon, contentDescription = null, tint = Color.White, modifier = Modifier.size(24.dp))
                        Spacer(modifier = Modifier.width(16.dp))
                        Text(label, color = Color.White, fontSize = 16.sp)
                    }
                }
            }
            Spacer(modifier = Modifier.height(24.dp))
        }
    }
}
