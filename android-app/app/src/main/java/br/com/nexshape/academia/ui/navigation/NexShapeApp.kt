package br.com.nexshape.academia.ui.navigation

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.ui.graphics.Color
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.Chat
import androidx.compose.material.icons.filled.CalendarMonth
import androidx.compose.material.icons.filled.Dashboard
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.Group
import androidx.compose.material.icons.filled.Home
import androidx.compose.material.icons.filled.MedicalServices
import androidx.compose.material.icons.filled.MonitorHeart
import androidx.compose.material.icons.filled.Notifications
import androidx.compose.material.icons.filled.Person
import androidx.compose.material.icons.filled.Restaurant
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
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import br.com.nexshape.academia.ui.agenda.AgendaScreen
import br.com.nexshape.academia.ui.chat.ChatScreen
import br.com.nexshape.academia.ui.evolution.EvolutionScreen
import br.com.nexshape.academia.ui.home.HomeScreen
import br.com.nexshape.academia.ui.login.LoginScreen
import br.com.nexshape.academia.ui.login.ProfileSelectorScreen
import br.com.nexshape.academia.ui.login.roleToCardData
import br.com.nexshape.academia.ui.nutrition.NutritionScreen
import br.com.nexshape.academia.ui.professionals.ProfessionalsScreen
import br.com.nexshape.academia.ui.profile.ProfileScreen
import br.com.nexshape.academia.ui.professional.ProAgendaScreen
import br.com.nexshape.academia.ui.professional.ProAlertsScreen
import br.com.nexshape.academia.ui.professional.ProHomeScreen
import br.com.nexshape.academia.ui.professional.ProPatientCareScreen
import br.com.nexshape.academia.ui.professional.ProPatientsScreen
import br.com.nexshape.academia.ui.training.TrainingScreen

private enum class StudentTab(val label: String) {
    Home("Início"),
    Training("Treino"),
    Evolution("Evolução"),
    Agenda("Agenda"),
    Nutrition("Nutrição"),
    Chat("NexBot"),
    Professionals("Mentores"),
    Profile("Perfil"),
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

    // Seletor de perfil para usuários com múltiplos papéis
    if (showRoleSelector) {
        val userName = ApiClient.tokenStore().getName() ?: "Usuário"
        val availableRoles = ApiClient.tokenStore().getAvailableRoles()
        ProfileSelectorScreen(
            userName = userName,
            availableRoles = availableRoles,
            onRoleSelected = { selectedRole ->
                ApiClient.tokenStore().saveActiveRole(selectedRole)
                showRoleSelector = false
                // Mapear role selecionada para AppMode
                when (selectedRole) {
                    "professional", "instructor", "supervisor", "admin", "clinic_admin" -> {
                        appMode = AppMode.PROFESSIONAL
                        sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                    }
                    else -> {
                        appMode = AppMode.STUDENT
                        sessionPreferences.setAppMode(AppMode.STUDENT)
                    }
                }
            },
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
                    val availableRoles = ApiClient.tokenStore().getAvailableRoles()
                    val selectableCards = availableRoles.mapNotNull { roleToCardData(it) }

                    if (selectableCards.size > 1 && ApiClient.tokenStore().getActiveRole().isNullOrBlank()) {
                        showRoleSelector = true
                    } else if (loaded.isProfessional && !loaded.isStudent) {
                        appMode = AppMode.PROFESSIONAL
                        sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                        ApiClient.tokenStore().saveActiveRole(resolveProfessionalRole(availableRoles))
                    } else if (loaded.isStudent && !loaded.isProfessional) {
                        appMode = AppMode.STUDENT
                        sessionPreferences.setAppMode(AppMode.STUDENT)
                        ApiClient.tokenStore().saveActiveRole(resolveStudentRole(availableRoles))
                    } else if (ApiClient.tokenStore().getActiveRole().isNullOrBlank()) {
                        ApiClient.tokenStore().saveActiveRole(
                            when (appMode) {
                                AppMode.PROFESSIONAL -> resolveProfessionalRole(availableRoles)
                                AppMode.STUDENT -> resolveStudentRole(availableRoles)
                            },
                        )
                    }
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

    when {
        appMode == AppMode.PROFESSIONAL && canUsePro -> ProShell(
            sessionPreferences = sessionPreferences,
            authRepository = authRepository,
            appLockStore = appLockStore,
            canSwitchToStudent = canUseStudent,
            onSwitchMode = {
                sessionPreferences.setAppMode(AppMode.STUDENT)
                ApiClient.tokenStore().saveActiveRole(resolveStudentRole(ApiClient.tokenStore().getAvailableRoles()))
                appMode = AppMode.STUDENT
            },
            onLogout = handleLogout,
        )
        else -> StudentShell(
            authRepository = authRepository,
            appLockStore = appLockStore,
            canSwitchToPro = canUsePro,
            onSwitchMode = {
                sessionPreferences.setAppMode(AppMode.PROFESSIONAL)
                ApiClient.tokenStore().saveActiveRole(resolveProfessionalRole(ApiClient.tokenStore().getAvailableRoles()))
                appMode = AppMode.PROFESSIONAL
            },
            onLogout = handleLogout,
        )
    }
}

@Composable
private fun StudentShell(
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    canSwitchToPro: Boolean,
    onSwitchMode: () -> Unit,
    onLogout: () -> Unit,
) {
    var selectedTab by remember { mutableStateOf(StudentTab.Home) }
    val bottomTabs = listOf(
        StudentTab.Home,
        StudentTab.Training,
        StudentTab.Evolution,
        StudentTab.Nutrition,
        StudentTab.Profile,
    )

    Scaffold(
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
            )
            StudentTab.Training -> TrainingScreen(modifier = Modifier.padding(padding))
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
        }
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
