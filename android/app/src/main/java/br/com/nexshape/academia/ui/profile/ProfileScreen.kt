package br.com.nexshape.academia.ui.profile

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.horizontalScroll
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.FilterChip
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Switch
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
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.api.UpdateProfileRequest
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPanel
import br.com.nexshape.academia.ui.components.NexPrimaryButton
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import br.com.nexshape.academia.ui.subscription.SubscriptionSection
import kotlinx.coroutines.launch

private val goalOptions = listOf(
    "lose" to "Emagrecer",
    "lose_aggressive" to "Cut agressivo",
    "recomp" to "Recompor",
    "maintain" to "Manter",
    "gain" to "Ganhar massa",
    "performance" to "Performance",
)

private val activityOptions = listOf(
    "sedentary" to "Sedentario",
    "light" to "Leve",
    "moderate" to "Moderado",
    "active" to "Ativo",
    "very_active" to "Atleta",
)

private val climateOptions = listOf(
    "cold" to "Frio",
    "moderate" to "Moderado",
    "hot" to "Quente",
)

@Composable
fun ProfileScreen(
    modifier: Modifier = Modifier,
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    showSubscription: Boolean = true,
    canSwitchToPro: Boolean = false,
    onSwitchToPro: () -> Unit = {},
    canSwitchToStudent: Boolean = false,
    onSwitchToStudent: () -> Unit = {},
    onLogout: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var profile by remember { mutableStateOf<ProfileDto?>(null) }
    var saving by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        authRepository.loadProfile()
            .onSuccess { profile = it }
            .onFailure { error = friendlyError(it) }
    }

    NexShapeScreen(
        title = "Perfil",
        subtitle = "Conta, assinatura e seguranca do app.",
        modifier = modifier,
    ) {
        LazyColumn(verticalArrangement = Arrangement.spacedBy(14.dp)) {
            item {
                NexCard {
                    val currentProfile = profile
                    if (currentProfile == null) {
                        CircularProgressIndicator(color = NexNeon)
                    } else {
                        Text(currentProfile.name, color = Color.White, fontWeight = FontWeight.Black)
                        Text(currentProfile.email, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                        Text(
                            text = profileLabel(currentProfile),
                            color = NexNeon,
                            fontWeight = FontWeight.Black,
                            modifier = Modifier.padding(top = 10.dp),
                        )
                        currentProfile.branding?.clinicName?.let {
                            Text("Clinica: $it", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                        }
                        currentProfile.activePatientId?.let {
                            Text("Aluno ativo: #$it", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                        }
                        val organizations = currentProfile.organizations.orEmpty()
                        if (organizations.isNotEmpty()) {
                            Spacer(Modifier.height(10.dp))
                            Text("Organizacoes vinculadas", color = Color.White, fontWeight = FontWeight.Bold)
                            organizations.forEach { organization ->
                                Text(
                                    "${organization.name}${organization.role?.let { " - $it" } ?: ""}",
                                    color = NexMuted,
                                    modifier = Modifier.padding(top = 3.dp),
                                )
                            }
                        }
                    }
                }
            }

            if (showSubscription) {
                item { SubscriptionSection(isPremium = profile?.isPremium == true) }
            }

            profile?.let { currentProfile ->
                item { ProfessionalRequestsSection(profile = currentProfile) }
            }

            profile?.let { currentProfile ->
                item {
                    ProfileEditCard(
                        profile = currentProfile,
                        saving = saving,
                        error = error,
                        onSave = { request ->
                            scope.launch {
                                saving = true
                                authRepository.updateProfile(request)
                                    .onSuccess {
                                        profile = it
                                        error = null
                                    }
                                    .onFailure { error = friendlyError(it) }
                                saving = false
                            }
                        },
                    )
                }
            }

            item {
                NexCard {
                    AppLockSection(appLockStore = appLockStore)
                }
            }

            if (canSwitchToPro || canSwitchToStudent) {
                item {
                    Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        if (canSwitchToPro) {
                            OutlinedButton(
                                onClick = onSwitchToPro,
                                modifier = Modifier.fillMaxWidth(),
                                colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                            ) {
                                Text("Modo Profissional")
                            }
                        }
                        if (canSwitchToStudent) {
                            OutlinedButton(
                                onClick = onSwitchToStudent,
                                modifier = Modifier.fillMaxWidth(),
                                colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                            ) {
                                Text("Modo Aluno")
                            }
                        }
                    }
                }
            }

            item {
                NexCard {
                    Text("API: ${BuildConfig.API_BASE_URL}", color = NexMuted)
                    Text("App v${BuildConfig.VERSION_NAME}", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                    Spacer(Modifier.height(12.dp))
                    TextButton(
                        onClick = {
                            scope.launch {
                                authRepository.logout()
                                onLogout()
                            }
                        },
                        colors = ButtonDefaults.textButtonColors(contentColor = NexNeon),
                    ) {
                        Text("Sair", fontWeight = FontWeight.Black)
                    }
                }
            }
        }
    }
}

private fun profileLabel(profile: ProfileDto): String = when {
    profile.isProfessional && profile.isStudent -> "Aluno + Profissional"
    profile.isProfessional -> "Profissional"
    else -> if (profile.isPremium) "Premium" else "Free"
}

@Composable
private fun ProfileEditCard(
    profile: ProfileDto,
    saving: Boolean,
    error: String?,
    onSave: (UpdateProfileRequest) -> Unit,
) {
    val details = profile.profile
    var name by remember(profile.id, profile.name) { mutableStateOf(profile.name) }
    var currentWeight by remember(profile.id, details?.currentWeightKg) { mutableStateOf(details?.currentWeightKg?.toString().orEmpty()) }
    var targetWeight by remember(profile.id, details?.targetWeightKg) { mutableStateOf(details?.targetWeightKg?.toString().orEmpty()) }
    var waterTarget by remember(profile.id, details?.waterTargetMl) { mutableStateOf(details?.waterTargetMl?.toString().orEmpty()) }
    var goal by remember(profile.id, details?.goal) { mutableStateOf(details?.goal ?: "maintain") }
    var activity by remember(profile.id, details?.activityLevel) { mutableStateOf(details?.activityLevel ?: "moderate") }
    var climate by remember(profile.id, details?.climate) { mutableStateOf(details?.climate ?: "moderate") }
    var autoWater by remember(profile.id, details?.isWaterTargetAuto) { mutableStateOf(details?.isWaterTargetAuto == true) }

    NexCard {
        Text("Dados e metas", color = Color.White, fontWeight = FontWeight.Black)
        Text("Altere os principais dados usados em treino, evolucao, nutricao e hidratacao.", color = NexMuted, modifier = Modifier.padding(top = 4.dp))

        ProfileTextField("Nome", name, { name = it }, KeyboardType.Text)
        Row(horizontalArrangement = Arrangement.spacedBy(10.dp), modifier = Modifier.padding(top = 10.dp)) {
            ProfileTextField("Peso atual", currentWeight, { currentWeight = onlyDecimal(it) }, KeyboardType.Decimal, Modifier.weight(1f))
            ProfileTextField("Peso meta", targetWeight, { targetWeight = onlyDecimal(it) }, KeyboardType.Decimal, Modifier.weight(1f))
        }
        ProfileTextField("Meta de agua (ml)", waterTarget, { waterTarget = it.filter(Char::isDigit) }, KeyboardType.Number)
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth().padding(top = 10.dp)) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Calculo hidrico automatico", color = Color.White, fontWeight = FontWeight.Bold)
                Text("Usa peso, atividade e clima.", color = NexMuted)
            }
            Switch(checked = autoWater, onCheckedChange = { autoWater = it })
        }
        ChipRow("Objetivo", goalOptions, goal) { goal = it }
        ChipRow("Atividade", activityOptions, activity) { activity = it }
        ChipRow("Clima", climateOptions, climate) { climate = it }

        error?.let {
            Text(it, color = Color(0xFFFF8A8A), modifier = Modifier.padding(top = 8.dp))
        }
        NexPrimaryButton(
            text = if (saving) "Salvando..." else "Salvar perfil",
            enabled = !saving && name.isNotBlank(),
            onClick = {
                onSave(
                    UpdateProfileRequest(
                        name = name.trim(),
                        currentWeightKg = currentWeight.toDoubleOrNull(),
                        targetWeightKg = targetWeight.toDoubleOrNull(),
                        waterTargetMl = waterTarget.toIntOrNull(),
                        isWaterTargetAuto = autoWater,
                        goal = goal,
                        activityLevel = activity,
                        climate = climate,
                    )
                )
            },
            modifier = Modifier.padding(top = 12.dp),
        )
    }
}

@Composable
private fun ProfileTextField(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
    keyboardType: KeyboardType,
    modifier: Modifier = Modifier.fillMaxWidth(),
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.padding(top = 10.dp),
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        colors = OutlinedTextFieldDefaults.colors(
            focusedTextColor = Color.White,
            unfocusedTextColor = Color.White,
            focusedBorderColor = NexNeon,
            unfocusedBorderColor = Color.White.copy(alpha = 0.28f),
            focusedLabelColor = NexNeon,
            unfocusedLabelColor = NexMuted,
            cursorColor = NexNeon,
            focusedContainerColor = NexPanel,
            unfocusedContainerColor = NexPanel,
        ),
    )
}

@Composable
private fun ChipRow(
    title: String,
    options: List<Pair<String, String>>,
    selected: String,
    onSelected: (String) -> Unit,
) {
    Text(title, color = NexMuted, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 12.dp))
    Row(
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        modifier = Modifier.horizontalScroll(rememberScrollState()).padding(top = 6.dp),
    ) {
        options.forEach { (value, label) ->
            FilterChip(
                selected = selected == value,
                onClick = { onSelected(value) },
                label = { Text(label) },
            )
        }
    }
}

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char -> char.isDigit() || (char == '.' && value.indexOf('.') == index) }
