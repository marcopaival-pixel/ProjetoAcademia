package br.com.nexshape.academia.ui.login

import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.imePadding
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.systemBarsPadding
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Email
import androidx.compose.material.icons.filled.Lock
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Checkbox
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.RadioButton
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Path
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.TextRange
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.TextFieldValue
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.fragment.app.FragmentActivity
import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.api.OnboardingProfileRequest
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.security.BiometricHelper
import com.google.android.gms.auth.api.signin.GoogleSignIn
import com.google.android.gms.auth.api.signin.GoogleSignInOptions
import com.google.android.gms.common.api.ApiException
import kotlinx.coroutines.launch

@Composable
fun LoginScreen(
    authRepository: AuthRepository,
    onLoggedIn: () -> Unit,
) {
    val context = LocalContext.current
    val activity = context as? FragmentActivity
    val scope = rememberCoroutineScope()
    var showingRegister by remember { mutableStateOf(false) }
    var email by remember { mutableStateOf(authRepository.savedEmail().orEmpty()) }
    var password by remember { mutableStateOf("") }
    var passwordVisible by remember { mutableStateOf(false) }
    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var notice by remember { mutableStateOf<String?>(null) }
    var showingForgotPassword by remember { mutableStateOf(false) }
    val biometricLoginAvailable = activity != null &&
        BiometricHelper.canAuthenticate(activity) &&
        authRepository.hasSavedToken()
    val googleClientId = BuildConfig.GOOGLE_WEB_CLIENT_ID
    val googleSignInClient = remember(googleClientId) {
        if (googleClientId.isNotBlank()) {
            GoogleSignIn.getClient(
                context,
                GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
                    .requestEmail()
                    .requestIdToken(googleClientId)
                    .build(),
            )
        } else {
            null
        }
    }
    val googleLauncher = rememberLauncherForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
        val task = GoogleSignIn.getSignedInAccountFromIntent(result.data)
        runCatching { task.getResult(ApiException::class.java) }
            .onSuccess { account ->
                val idToken = account.idToken
                if (idToken.isNullOrBlank()) {
                    error = "Nao foi possivel obter o token do Google."
                    loading = false
                    return@onSuccess
                }
                loading = true
                error = null
                notice = null
                scope.launch {
                    authRepository.loginWithGoogle(idToken)
                        .onSuccess { onLoggedIn() }
                        .onFailure { error = it.message }
                    loading = false
                }
            }
            .onFailure {
                loading = false
                error = "Login com Google cancelado ou indisponivel."
            }
    }

    val brandGreen = Color(0xFF10B981)
    val neonGreen = Color(0xFF19F5A6)
    val fieldColor = Color(0xFF111820)
    if (showingRegister) {
        RegisterScreen(
            authRepository = authRepository,
            brandGreen = brandGreen,
            neonGreen = neonGreen,
            fieldColor = fieldColor,
            onBackToLogin = { message ->
                showingRegister = false
                notice = message
                error = null
            },
            onRegisteredAndLoggedIn = onLoggedIn,
        )
        return
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF050708)),
    ) {
        NeonGymBackground(
            brandGreen = brandGreen,
            neonGreen = neonGreen,
            modifier = Modifier.fillMaxSize(),
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .systemBarsPadding()
                .imePadding()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 24.dp, vertical = 28.dp),
            verticalArrangement = Arrangement.SpaceBetween,
            horizontalAlignment = Alignment.CenterHorizontally,
        ) {
            Spacer(Modifier.height(8.dp))

            Column(
                modifier = Modifier.fillMaxWidth(),
                horizontalAlignment = Alignment.Start,
            ) {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    Box(
                        modifier = Modifier
                            .size(width = 5.dp, height = 44.dp)
                            .clip(RoundedCornerShape(8.dp))
                            .background(neonGreen),
                    )
                    Column {
                        Text(
                            text = "NEXSHAPE",
                            color = Color.White,
                            fontSize = 24.sp,
                            fontWeight = FontWeight.Black,
                        )
                        Text(
                            text = "ACADEMIA INTELIGENTE",
                            color = neonGreen,
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Black,
                        )
                    }
                }

                Spacer(Modifier.height(22.dp))

                Text(
                    text = "Transforme seu corpo com inteligencia.",
                    color = Color.White,
                    fontSize = 30.sp,
                    lineHeight = 36.sp,
                    fontWeight = FontWeight.Black,
                )
                Spacer(Modifier.height(10.dp))
                Text(
                    text = "Acesse sua plataforma personalizada de treino, agenda e evolucao.",
                    color = Color(0xFFA3AAB5),
                    fontSize = 15.sp,
                    lineHeight = 21.sp,
                )
            }

            Spacer(Modifier.height(28.dp))

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
                    .padding(18.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
            ) {
                Text(
                    text = "IDENTIFICACAO",
                    color = Color(0xFF6F7782),
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Black,
                    modifier = Modifier.fillMaxWidth(),
                )
                Spacer(Modifier.height(10.dp))

                OutlinedTextField(
                    value = email,
                    onValueChange = { email = it },
                    label = { Text("E-mail") },
                    leadingIcon = {
                        Icon(
                            imageVector = Icons.Filled.Email,
                            contentDescription = null,
                        )
                    },
                    modifier = Modifier.fillMaxWidth(),
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email),
                    singleLine = true,
                    shape = RoundedCornerShape(16.dp),
                    colors = loginFieldColors(
                        fieldColor = fieldColor,
                        brandGreen = brandGreen,
                    ),
                )

                Spacer(Modifier.height(14.dp))

                Text(
                    text = "CHAVE DE ACESSO",
                    color = Color(0xFF6F7782),
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Black,
                    modifier = Modifier.fillMaxWidth(),
                )
                Spacer(Modifier.height(10.dp))

                OutlinedTextField(
                    value = password,
                    onValueChange = { password = it },
                    label = { Text("Senha") },
                    leadingIcon = {
                        Icon(
                            imageVector = Icons.Filled.Lock,
                            contentDescription = null,
                        )
                    },
                    modifier = Modifier.fillMaxWidth(),
                    trailingIcon = {
                        IconButton(onClick = { passwordVisible = !passwordVisible }) {
                            Icon(
                                imageVector = if (passwordVisible) {
                                    Icons.Filled.VisibilityOff
                                } else {
                                    Icons.Filled.Visibility
                                },
                                contentDescription = if (passwordVisible) {
                                    "Ocultar senha"
                                } else {
                                    "Mostrar senha"
                                },
                            )
                        }
                    },
                    visualTransformation = if (passwordVisible) {
                        VisualTransformation.None
                    } else {
                        PasswordVisualTransformation()
                    },
                    keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password),
                    singleLine = true,
                    shape = RoundedCornerShape(16.dp),
                    colors = loginFieldColors(
                        fieldColor = fieldColor,
                        brandGreen = brandGreen,
                    ),
                )

                TextButton(
                    onClick = { showingForgotPassword = true },
                    modifier = Modifier.align(Alignment.End),
                ) {
                    Text(
                        text = "Esqueci minha senha",
                        color = neonGreen,
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Black,
                    )
                }

                error?.let {
                    Spacer(Modifier.height(4.dp))
                    Text(
                        text = it,
                        color = MaterialTheme.colorScheme.error,
                        fontSize = 14.sp,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.fillMaxWidth(),
                    )
                }
                notice?.let {
                    Spacer(Modifier.height(4.dp))
                    Text(
                        text = it,
                        color = neonGreen,
                        fontSize = 14.sp,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.fillMaxWidth(),
                    )
                }

                Spacer(Modifier.height(14.dp))

                Button(
                    onClick = {
                        loading = true
                        error = null
                        notice = null
                        scope.launch {
                            authRepository.login(email, password)
                                .onSuccess { onLoggedIn() }
                                .onFailure { error = it.message }
                            loading = false
                        }
                    },
                    enabled = !loading && email.isNotBlank() && password.isNotBlank(),
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(58.dp),
                    shape = RoundedCornerShape(18.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = brandGreen,
                        contentColor = Color(0xFF04110D),
                        disabledContainerColor = Color(0xFF1A2229),
                        disabledContentColor = Color(0xFF68717D),
                    ),
                ) {
                    if (loading) {
                        CircularProgressIndicator(
                            color = Color(0xFF04110D),
                            strokeWidth = 2.dp,
                            modifier = Modifier.size(22.dp),
                        )
                    } else {
                        Text(
                            text = "ENTRAR NA PLATAFORMA",
                            fontSize = 15.sp,
                            fontWeight = FontWeight.Black,
                        )
                    }
                }

                Spacer(Modifier.height(10.dp))

                Button(
                    onClick = {
                        val client = googleSignInClient
                        if (client == null) {
                            error = "Google Login nao configurado neste build."
                            return@Button
                        }
                        loading = true
                        error = null
                        notice = null
                        client.signOut().addOnCompleteListener {
                            googleLauncher.launch(client.signInIntent)
                        }
                    },
                    enabled = !loading,
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(54.dp),
                    shape = RoundedCornerShape(18.dp),
                    colors = ButtonDefaults.buttonColors(
                        containerColor = Color.White,
                        contentColor = Color(0xFF111820),
                        disabledContainerColor = Color(0xFF1A2229),
                        disabledContentColor = Color(0xFF68717D),
                    ),
                ) {
                    Text(
                        text = "ENTRAR COM GOOGLE",
                        fontSize = 14.sp,
                        fontWeight = FontWeight.Black,
                    )
                }

                Spacer(Modifier.height(10.dp))

                TextButton(onClick = {
                    notice = null
                    showingRegister = true
                }) {
                    Text(
                        text = "Ainda nao tem conta? Criar uma conta",
                        color = neonGreen,
                        fontSize = 13.sp,
                        fontWeight = FontWeight.Black,
                        textAlign = TextAlign.Center,
                    )
                }

                if (biometricLoginAvailable) {
                    Spacer(Modifier.height(8.dp))
                    Button(
                        onClick = {
                            error = null
                            BiometricHelper.authenticate(
                                activity = activity,
                                subtitle = "Use a biometria para entrar no NexShape",
                                onSuccess = {
                                    loading = true
                                    scope.launch {
                                        authRepository.unlockSavedSession()
                                            .onSuccess { onLoggedIn() }
                                            .onFailure { error = "Sessao expirada. Entre com e-mail e senha." }
                                        loading = false
                                    }
                                },
                                onError = { message -> error = message },
                            )
                        },
                        enabled = !loading,
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(54.dp),
                        shape = RoundedCornerShape(18.dp),
                        colors = ButtonDefaults.buttonColors(
                            containerColor = Color(0xFF182129),
                            contentColor = neonGreen,
                        ),
                    ) {
                        Text(
                            text = "ENTRAR COM BIOMETRIA",
                            fontSize = 14.sp,
                            fontWeight = FontWeight.Black,
                        )
                    }
                }
            }

            Spacer(Modifier.height(22.dp))

            Text(
                text = "Use o mesmo acesso cadastrado na plataforma NexShape.",
                color = Color(0xFF7D8591),
                fontSize = 13.sp,
                lineHeight = 18.sp,
                textAlign = TextAlign.Center,
                modifier = Modifier.padding(horizontal = 12.dp),
            )
        }

        if (showingForgotPassword) {
            ForgotPasswordDialog(
                initialEmail = email,
                authRepository = authRepository,
                neonGreen = neonGreen,
                fieldColor = fieldColor,
                brandGreen = brandGreen,
                onDismiss = { showingForgotPassword = false },
            )
        }
    }
}

@Composable
private fun RegisterScreen(
    authRepository: AuthRepository,
    brandGreen: Color,
    neonGreen: Color,
    fieldColor: Color,
    onBackToLogin: (String?) -> Unit,
    onRegisteredAndLoggedIn: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var step by remember { mutableStateOf(0) }
    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var passwordConfirmation by remember { mutableStateOf("") }
    var accountType by remember { mutableStateOf<String?>(null) }
    var birthDate by remember { mutableStateOf(TextFieldValue("")) }
    var phone by remember { mutableStateOf(TextFieldValue("")) }
    var cpf by remember { mutableStateOf("") }
    var sex by remember { mutableStateOf("") }
    var weightKg by remember { mutableStateOf("") }
    var heightCm by remember { mutableStateOf("") }
    var goal by remember { mutableStateOf("") }
    var activityLevel by remember { mutableStateOf("") }
    var hasInjury by remember { mutableStateOf(false) }
    var hasDisease by remember { mutableStateOf(false) }
    var usesMedication by remember { mutableStateOf(false) }
    var injuryDetails by remember { mutableStateOf("") }
    var diseaseDetails by remember { mutableStateOf("") }
    var medicationDetails by remember { mutableStateOf("") }
    var fitnessNotes by remember { mutableStateOf("") }
    var acceptedTerms by remember { mutableStateOf(false) }
    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var success by remember { mutableStateOf<String?>(null) }
    var legalDialog by remember { mutableStateOf<LegalDialogType?>(null) }

    fun validateAccess(): String? {
        if (name.isBlank()) return "Nome obrigatorio."
        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email.trim()).matches()) return "E-mail invalido."
        if (password.length < 8) return "A senha deve ter pelo menos 8 caracteres."
        if (passwordConfirmation != password) return "A confirmacao deve ser igual a senha."
        if (accountType.isNullOrBlank()) return "Selecione o tipo de conta."
        return null
    }

    fun validateCurrentStep(): String? {
        return when (step) {
            0 -> validateAccess()
            1 -> {
                if (!birthDate.text.matches(Regex("\\d{2}/\\d{2}/\\d{4}"))) return "Informe a data no formato dd/mm/aaaa."
                if (birthDate.text.toApiDateOrNull() == null) return "Data de nascimento invalida."
                if (phone.text.onlyDigits().length < 10) return "Telefone obrigatorio."
                if (cpf.onlyDigits().length != 11) return "CPF obrigatorio para nota fiscal."
                if (sex.isBlank()) return "Selecione sexo/genero."
                null
            }
            2 -> {
                val weight = weightKg.replace(',', '.').toDoubleOrNull()
                val height = heightCm.toIntOrNull()
                if (weight == null || weight !in 20.0..500.0) return "Peso invalido."
                if (height == null || height !in 50..260) return "Altura invalida."
                if (goal.isBlank()) return "Selecione o objetivo principal."
                if (activityLevel.isBlank()) return "Selecione o nivel de atividade."
                null
            }
            4 -> if (!acceptedTerms) "Aceite os termos para criar a conta." else null
            else -> null
        }
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF050708)),
    ) {
        NeonGymBackground(
            brandGreen = brandGreen,
            neonGreen = neonGreen,
            modifier = Modifier.fillMaxSize(),
        )

        Column(
            modifier = Modifier
                .fillMaxSize()
                .systemBarsPadding()
                .imePadding()
                .verticalScroll(rememberScrollState())
                .padding(horizontal = 24.dp, vertical = 28.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
        ) {
            Text(
                text = "Criar conta",
                color = Color.White,
                fontSize = 30.sp,
                lineHeight = 36.sp,
                fontWeight = FontWeight.Black,
                modifier = Modifier.fillMaxWidth(),
            )
            Spacer(Modifier.height(8.dp))
            Text(
                text = "Onboarding rapido para personalizar sua experiencia.",
                color = Color(0xFFA3AAB5),
                fontSize = 15.sp,
                modifier = Modifier.fillMaxWidth(),
            )
            Spacer(Modifier.height(24.dp))

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
                    .padding(18.dp),
                horizontalAlignment = Alignment.CenterHorizontally,
            ) {
                Text(
                    text = "ETAPA ${step + 1} DE 5",
                    color = Color(0xFF6F7782),
                    fontSize = 11.sp,
                    fontWeight = FontWeight.Black,
                    modifier = Modifier.fillMaxWidth(),
                )
                Spacer(Modifier.height(8.dp))
                LinearProgressIndicator(
                    progress = { (step + 1) / 5f },
                    modifier = Modifier
                        .fillMaxWidth()
                        .height(6.dp)
                        .clip(RoundedCornerShape(8.dp)),
                    color = neonGreen,
                    trackColor = Color.White.copy(alpha = 0.10f),
                )
                Spacer(Modifier.height(18.dp))

                Text(
                    text = when (step) {
                        0 -> "Dados de acesso"
                        1 -> "Dados pessoais"
                        2 -> "Dados fisicos"
                        3 -> "Saude e restricoes"
                        else -> "Confirmacao"
                    },
                    color = Color.White,
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Black,
                    modifier = Modifier.fillMaxWidth(),
                )
                Spacer(Modifier.height(14.dp))

                when (step) {
                    0 -> {
                        RegisterField("Nome completo", name, { name = it }, fieldColor, brandGreen)
                        Spacer(Modifier.height(12.dp))
                        RegisterField("E-mail", email, { email = it }, fieldColor, brandGreen, KeyboardType.Email)
                        Spacer(Modifier.height(12.dp))
                        RegisterField("Senha", password, { password = it }, fieldColor, brandGreen, KeyboardType.Password, true)
                        Spacer(Modifier.height(12.dp))
                        RegisterField("Confirmar senha", passwordConfirmation, { passwordConfirmation = it }, fieldColor, brandGreen, KeyboardType.Password, true)
                        Spacer(Modifier.height(16.dp))
                        Text(
                            text = "TIPO DE CONTA INICIAL",
                            color = Color(0xFF6F7782),
                            fontSize = 11.sp,
                            fontWeight = FontWeight.Black,
                            modifier = Modifier.fillMaxWidth(),
                        )
                        AccountTypeOption("Aluno / Atleta", accountType == "aluno", { accountType = "aluno" }, neonGreen)
                        AccountTypeOption("Profissional / Treinador", accountType == "professional", { accountType = "professional" }, neonGreen)
                    }
                    1 -> {
                        MaskedRegisterField(
                            label = "Data de nascimento (dd/mm/aaaa)",
                            value = birthDate,
                            onValueChange = { birthDate = it.masked(::formatBirthDateDigits) },
                            fieldColor = fieldColor,
                            brandGreen = brandGreen,
                            keyboardType = KeyboardType.Number,
                        )
                        Spacer(Modifier.height(12.dp))
                        MaskedRegisterField(
                            label = "Telefone",
                            value = phone,
                            onValueChange = { phone = it.masked(::formatPhoneDigitsBr) },
                            fieldColor = fieldColor,
                            brandGreen = brandGreen,
                            keyboardType = KeyboardType.Phone,
                        )
                        Spacer(Modifier.height(12.dp))
                        RegisterField("CPF", cpf, { cpf = it }, fieldColor, brandGreen, KeyboardType.Number)
                        Spacer(Modifier.height(12.dp))
                        OptionGroup(
                            title = "SEXO / GENERO",
                            options = listOf("M" to "Masculino", "F" to "Feminino", "O" to "Outro"),
                            selected = sex,
                            onSelected = { sex = it },
                            color = neonGreen,
                        )
                    }
                    2 -> {
                        RegisterField("Peso atual (kg)", weightKg, { weightKg = it }, fieldColor, brandGreen, KeyboardType.Decimal)
                        Spacer(Modifier.height(12.dp))
                        RegisterField("Altura (cm)", heightCm, { heightCm = it }, fieldColor, brandGreen, KeyboardType.Number)
                        Spacer(Modifier.height(12.dp))
                        OptionGroup(
                            title = "OBJETIVO PRINCIPAL",
                            options = listOf(
                                "lose" to "Emagrecimento",
                                "gain" to "Hipertrofia",
                                "recomp" to "Condicionamento",
                                "maintain" to "Saude",
                                "lose_aggressive" to "Reabilitacao",
                                "performance" to "Performance",
                            ),
                            selected = goal,
                            onSelected = { goal = it },
                            color = neonGreen,
                        )
                        Spacer(Modifier.height(10.dp))
                        OptionGroup(
                            title = "NIVEL DE ATIVIDADE",
                            options = listOf(
                                "sedentary" to "Sedentario",
                                "light" to "Leve",
                                "moderate" to "Moderado",
                                "active" to "Ativo",
                                "very_active" to "Muito ativo",
                            ),
                            selected = activityLevel,
                            onSelected = { activityLevel = it },
                            color = neonGreen,
                        )
                    }
                    3 -> {
                        BooleanOption("Possui lesao?", hasInjury, { hasInjury = it }, neonGreen)
                        if (hasInjury) {
                            Spacer(Modifier.height(8.dp))
                            RegisterField("Detalhe da lesao", injuryDetails, { injuryDetails = it }, fieldColor, brandGreen)
                        }
                        BooleanOption("Possui restricao medica?", hasDisease, { hasDisease = it }, neonGreen)
                        if (hasDisease) {
                            Spacer(Modifier.height(8.dp))
                            RegisterField("Detalhe da restricao medica", diseaseDetails, { diseaseDetails = it }, fieldColor, brandGreen)
                        }
                        BooleanOption("Usa medicamento?", usesMedication, { usesMedication = it }, neonGreen)
                        if (usesMedication) {
                            Spacer(Modifier.height(8.dp))
                            RegisterField("Medicamento em uso", medicationDetails, { medicationDetails = it }, fieldColor, brandGreen)
                        }
                        Spacer(Modifier.height(12.dp))
                        RegisterField("Observacoes gerais de saude", fitnessNotes, { fitnessNotes = it }, fieldColor, brandGreen)
                    }
                    else -> {
                        SummaryLine("Nome", name)
                        SummaryLine("E-mail", email)
                        SummaryLine("Nascimento", birthDate.text)
                        SummaryLine("Telefone", phone.text)
                        SummaryLine("CPF", cpf.onlyDigits())
                        SummaryLine("Peso / altura", "${weightKg} kg / ${heightCm} cm")
                        SummaryLine("Objetivo", optionLabel(goalOptions(), goal))
                        SummaryLine("Atividade", optionLabel(activityOptions(), activityLevel))
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            verticalAlignment = Alignment.CenterVertically,
                        ) {
                            Checkbox(checked = acceptedTerms, onCheckedChange = { acceptedTerms = it })
                            Text(
                                text = "Aceito os termos de uso e politica de privacidade.",
                                color = Color.White,
                                fontSize = 13.sp,
                            )
                        }
                        Row(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(8.dp),
                        ) {
                            TextButton(
                                onClick = { legalDialog = LegalDialogType.Terms },
                            ) {
                                Text("Termos de Uso", color = neonGreen, fontWeight = FontWeight.Black)
                            }
                            TextButton(
                                onClick = { legalDialog = LegalDialogType.Privacy },
                            ) {
                                Text("Politica de Privacidade", color = neonGreen, fontWeight = FontWeight.Black)
                            }
                        }
                    }
                }

                error?.let {
                    Spacer(Modifier.height(8.dp))
                    Text(it, color = MaterialTheme.colorScheme.error, textAlign = TextAlign.Center)
                }
                success?.let {
                    Spacer(Modifier.height(8.dp))
                    Text(it, color = neonGreen, textAlign = TextAlign.Center)
                }

                Spacer(Modifier.height(16.dp))
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(10.dp),
                ) {
                    if (step > 0) {
                        Button(
                            onClick = {
                                error = null
                                step -= 1
                            },
                            enabled = !loading,
                            modifier = Modifier
                                .weight(1f)
                                .height(58.dp),
                            shape = RoundedCornerShape(18.dp),
                            colors = ButtonDefaults.buttonColors(
                                containerColor = Color(0xFF182129),
                                contentColor = neonGreen,
                            ),
                        ) {
                            Text("VOLTAR", fontWeight = FontWeight.Black)
                        }
                    }

                    Button(
                        onClick = {
                            val validationError = validateCurrentStep()
                            if (validationError != null) {
                                error = validationError
                                success = null
                                return@Button
                            }
                            error = null
                            success = null
                            if (step < 4) {
                                step += 1
                                return@Button
                            }

                            loading = true
                            scope.launch {
                                val onboarding = OnboardingProfileRequest(
                                    birthDate = birthDate.text.toApiDateOrNull().orEmpty(),
                                    phone = phone.text.onlyDigits(),
                                    cpf = cpf.onlyDigits(),
                                    sex = sex,
                                    heightCm = heightCm.toInt(),
                                    currentWeightKg = weightKg.replace(',', '.').toDouble(),
                                    goal = goal,
                                    activityLevel = activityLevel,
                                    hasInjury = hasInjury,
                                    injuryDetails = injuryDetails.takeIf { hasInjury && it.isNotBlank() },
                                    hasDisease = hasDisease,
                                    diseaseDetails = diseaseDetails.takeIf { hasDisease && it.isNotBlank() },
                                    usesMedication = usesMedication,
                                    medicationDetails = medicationDetails.takeIf { usesMedication && it.isNotBlank() },
                                    fitnessNotes = fitnessNotes.takeIf { it.isNotBlank() },
                                    acceptedTerms = acceptedTerms,
                                )
                                authRepository.register(
                                    name = name,
                                    email = email,
                                    password = password,
                                    passwordConfirmation = passwordConfirmation,
                                    accountType = accountType.orEmpty(),
                                    onboarding = onboarding,
                                ).onSuccess { loggedIn ->
                                    if (!loggedIn) {
                                        onBackToLogin("Seu cadastro sera efetuado apos a confirmacao pelo e-mail enviado.")
                                        return@onSuccess
                                    }
                                    onRegisteredAndLoggedIn()
                                }.onFailure {
                                    error = it.message
                                }
                                loading = false
                            }
                        },
                        enabled = !loading,
                        modifier = Modifier
                            .weight(1f)
                            .height(58.dp),
                        shape = RoundedCornerShape(18.dp),
                        colors = ButtonDefaults.buttonColors(
                            containerColor = brandGreen,
                            contentColor = Color(0xFF04110D),
                        ),
                    ) {
                        if (loading) {
                            CircularProgressIndicator(
                                color = Color(0xFF04110D),
                                strokeWidth = 2.dp,
                                modifier = Modifier.size(22.dp),
                            )
                        } else {
                            Text(if (step < 4) "CONTINUAR" else "CRIAR CONTA", fontWeight = FontWeight.Black)
                        }
                    }
                }

                TextButton(onClick = { onBackToLogin(null) }) {
                    Text("Voltar para login", color = neonGreen, fontWeight = FontWeight.Black)
                }
            }
        }

        legalDialog?.let { type ->
            LegalDialog(
                type = type,
                neonGreen = neonGreen,
                onDismiss = { legalDialog = null },
            )
        }
    }
}

private fun goalOptions() = listOf(
    "lose" to "Emagrecimento",
    "gain" to "Hipertrofia",
    "recomp" to "Condicionamento",
    "maintain" to "Saude",
    "lose_aggressive" to "Reabilitacao",
    "performance" to "Performance",
)

@Composable
private fun ForgotPasswordDialog(
    initialEmail: String,
    authRepository: AuthRepository,
    neonGreen: Color,
    fieldColor: Color,
    brandGreen: Color,
    onDismiss: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var email by remember { mutableStateOf(initialEmail) }
    var loading by remember { mutableStateOf(false) }
    var message by remember { mutableStateOf<String?>(null) }
    var error by remember { mutableStateOf<String?>(null) }

    AlertDialog(
        onDismissRequest = { if (!loading) onDismiss() },
        confirmButton = {
            TextButton(
                onClick = {
                    if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email.trim()).matches()) {
                        error = "Informe um e-mail valido."
                        message = null
                        return@TextButton
                    }

                    loading = true
                    error = null
                    message = null
                    scope.launch {
                        authRepository.forgotPassword(email)
                            .onSuccess { message = it }
                            .onFailure { error = it.message }
                        loading = false
                    }
                },
                enabled = !loading,
            ) {
                Text(
                    text = if (loading) "Enviando..." else "Enviar",
                    color = neonGreen,
                    fontWeight = FontWeight.Black,
                )
            }
        },
        dismissButton = {
            TextButton(
                onClick = onDismiss,
                enabled = !loading,
            ) {
                Text("Voltar", color = Color(0xFF6B7280), fontWeight = FontWeight.Black)
            }
        },
        title = {
            Text("Recuperar senha", fontWeight = FontWeight.Black)
        },
        text = {
            Column {
                Text(
                    text = "Informe o e-mail cadastrado para receber as instrucoes de redefinicao de senha.",
                    color = Color(0xFF4B5563),
                    fontSize = 14.sp,
                    lineHeight = 19.sp,
                )
                Spacer(Modifier.height(14.dp))
                RegisterField(
                    label = "E-mail",
                    value = email,
                    onValueChange = { email = it },
                    fieldColor = fieldColor,
                    brandGreen = brandGreen,
                    keyboardType = KeyboardType.Email,
                )
                error?.let {
                    Spacer(Modifier.height(10.dp))
                    Text(it, color = MaterialTheme.colorScheme.error, fontSize = 13.sp)
                }
                message?.let {
                    Spacer(Modifier.height(10.dp))
                    Text(it, color = Color(0xFF047857), fontSize = 13.sp)
                }
            }
        },
    )
}

private fun activityOptions() = listOf(
    "sedentary" to "Sedentario",
    "light" to "Leve",
    "moderate" to "Moderado",
    "active" to "Ativo",
    "very_active" to "Muito ativo",
)

private fun optionLabel(options: List<Pair<String, String>>, selected: String): String {
    return options.firstOrNull { it.first == selected }?.second.orEmpty()
}

private fun String.onlyDigits(): String = filter { it.isDigit() }

private fun TextFieldValue.masked(formatter: (String) -> String): TextFieldValue {
    val formatted = formatter(text)
    return TextFieldValue(
        text = formatted,
        selection = TextRange(formatted.length),
    )
}

private fun formatBirthDateDigits(value: String): String {
    val digits = value.onlyDigits().take(8)
    return buildString {
        digits.forEachIndexed { index, char ->
            if (index == 2 || index == 4) append('/')
            append(char)
        }
    }
}

private fun String.toApiDateOrNull(): String? {
    val parts = split('/')
    if (parts.size != 3) return null

    val day = parts[0].toIntOrNull() ?: return null
    val month = parts[1].toIntOrNull() ?: return null
    val year = parts[2].toIntOrNull() ?: return null

    if (year !in 1900..2100 || month !in 1..12 || day !in 1..31) return null

    return "%04d-%02d-%02d".format(year, month, day)
}

private fun formatPhoneDigitsBr(value: String): String {
    val digits = value.onlyDigits().take(11)
    return when {
        digits.length <= 2 -> digits
        digits.length <= 3 -> "(${digits.take(2)}) ${digits.drop(2)}"
        digits.length <= 7 -> "(${digits.take(2)}) ${digits.drop(2).take(1)} ${digits.drop(3)}"
        else -> "(${digits.take(2)}) ${digits.drop(2).take(1)} ${digits.drop(3).take(4)}-${digits.drop(7)}"
    }
}

private enum class LegalDialogType {
    Terms,
    Privacy,
}

@Composable
private fun LegalDialog(
    type: LegalDialogType,
    neonGreen: Color,
    onDismiss: () -> Unit,
) {
    val isTerms = type == LegalDialogType.Terms
    AlertDialog(
        onDismissRequest = onDismiss,
        confirmButton = {
            TextButton(onClick = onDismiss) {
                Text("Voltar ao cadastro", color = neonGreen, fontWeight = FontWeight.Black)
            }
        },
        title = {
            Text(
                text = if (isTerms) "Termos de Uso" else "Politica de Privacidade",
                fontWeight = FontWeight.Black,
            )
        },
        text = {
            Column(
                modifier = Modifier.verticalScroll(rememberScrollState()),
            ) {
                Text(
                    text = if (isTerms) {
                        "Ao criar sua conta, voce concorda em usar a plataforma NexShape de forma correta, manter seus dados de acesso protegidos e informar dados verdadeiros para que treinos, avaliacoes e recomendacoes sejam calculados com seguranca. O uso do app depende do cumprimento das regras da plataforma e pode ser limitado em caso de abuso, fraude ou violacao de seguranca."
                    } else {
                        "A NexShape usa seus dados cadastrais, fisicos e de saude para criar sua experiencia no app, registrar evolucao, apoiar treinos, avaliacoes, pagamentos e emissao fiscal quando aplicavel. Dados sensiveis devem ser tratados com cuidado e usados somente para as finalidades da plataforma, conforme a LGPD."
                    },
                    color = Color(0xFF1F2937),
                    fontSize = 14.sp,
                    lineHeight = 20.sp,
                )
                Spacer(Modifier.height(12.dp))
                Text(
                    text = "Este resumo nao substitui a versao juridica publicada na plataforma web, mas permite revisar o essencial sem sair do cadastro Android.",
                    color = Color(0xFF4B5563),
                    fontSize = 13.sp,
                    lineHeight = 18.sp,
                )
            }
        },
    )
}

@Composable
private fun OptionGroup(
    title: String,
    options: List<Pair<String, String>>,
    selected: String,
    onSelected: (String) -> Unit,
    color: Color,
) {
    Text(
        text = title,
        color = Color(0xFF6F7782),
        fontSize = 11.sp,
        fontWeight = FontWeight.Black,
        modifier = Modifier.fillMaxWidth(),
    )
    options.forEach { (value, label) ->
        AccountTypeOption(
            text = label,
            selected = selected == value,
            onClick = { onSelected(value) },
            color = color,
        )
    }
}

@Composable
private fun BooleanOption(
    text: String,
    checked: Boolean,
    onCheckedChange: (Boolean) -> Unit,
    color: Color,
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        Checkbox(checked = checked, onCheckedChange = onCheckedChange)
        TextButton(onClick = { onCheckedChange(!checked) }) {
            Text(text = text, color = if (checked) color else Color.White)
        }
    }
}

@Composable
private fun SummaryLine(label: String, value: String) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 4.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
    ) {
        Text(label, color = Color(0xFF7D8591), fontSize = 13.sp)
        Text(value.ifBlank { "-" }, color = Color.White, fontSize = 13.sp, textAlign = TextAlign.End)
    }
}

@Composable
private fun RegisterField(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
    fieldColor: Color,
    brandGreen: Color,
    keyboardType: KeyboardType = KeyboardType.Text,
    password: Boolean = false,
) {
    var passwordVisible by remember { mutableStateOf(false) }

    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = Modifier.fillMaxWidth(),
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        visualTransformation = if (password && !passwordVisible) {
            PasswordVisualTransformation()
        } else {
            VisualTransformation.None
        },
        trailingIcon = if (password) {
            {
                IconButton(onClick = { passwordVisible = !passwordVisible }) {
                    Icon(
                        imageVector = if (passwordVisible) {
                            Icons.Filled.VisibilityOff
                        } else {
                            Icons.Filled.Visibility
                        },
                        contentDescription = if (passwordVisible) {
                            "Ocultar senha"
                        } else {
                            "Mostrar senha"
                        },
                        tint = brandGreen,
                    )
                }
            }
        } else {
            null
        },
        singleLine = true,
        shape = RoundedCornerShape(16.dp),
        colors = loginFieldColors(fieldColor = fieldColor, brandGreen = brandGreen),
    )
}

@Composable
private fun MaskedRegisterField(
    label: String,
    value: TextFieldValue,
    onValueChange: (TextFieldValue) -> Unit,
    fieldColor: Color,
    brandGreen: Color,
    keyboardType: KeyboardType = KeyboardType.Text,
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = Modifier.fillMaxWidth(),
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        singleLine = true,
        shape = RoundedCornerShape(16.dp),
        colors = loginFieldColors(fieldColor = fieldColor, brandGreen = brandGreen),
    )
}

@Composable
private fun AccountTypeOption(
    text: String,
    selected: Boolean,
    onClick: () -> Unit,
    color: Color,
) {
    Row(
        modifier = Modifier.fillMaxWidth(),
        verticalAlignment = Alignment.CenterVertically,
    ) {
        RadioButton(selected = selected, onClick = onClick)
        TextButton(onClick = onClick) {
            Text(text = text, color = if (selected) color else Color.White)
        }
    }
}

@Composable
private fun loginFieldColors(
    fieldColor: Color,
    brandGreen: Color,
) = OutlinedTextFieldDefaults.colors(
    focusedTextColor = Color.White,
    unfocusedTextColor = Color.White,
    focusedBorderColor = brandGreen,
    unfocusedBorderColor = Color.White.copy(alpha = 0.12f),
    focusedLabelColor = brandGreen,
    unfocusedLabelColor = Color(0xFF7D8591),
    focusedLeadingIconColor = brandGreen,
    unfocusedLeadingIconColor = Color(0xFF6F7782),
    focusedTrailingIconColor = brandGreen,
    unfocusedTrailingIconColor = Color(0xFF6F7782),
    cursorColor = brandGreen,
    focusedContainerColor = fieldColor,
    unfocusedContainerColor = fieldColor,
)

@Composable
private fun NeonGymBackground(
    brandGreen: Color,
    neonGreen: Color,
    modifier: Modifier = Modifier,
) {
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

        val glowCenterTop = androidx.compose.ui.geometry.Offset(
            x = size.width * 0.78f,
            y = size.height * 0.16f,
        )
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(neonGreen.copy(alpha = 0.24f), Color.Transparent),
                center = glowCenterTop,
                radius = size.width * 0.78f,
            ),
            radius = size.width * 0.78f,
            center = glowCenterTop,
        )

        val glowCenterBottom = androidx.compose.ui.geometry.Offset(
            x = size.width * 0.2f,
            y = size.height * 0.85f,
        )
        drawCircle(
            brush = Brush.radialGradient(
                colors = listOf(brandGreen.copy(alpha = 0.18f), Color.Transparent),
                center = glowCenterBottom,
                radius = size.width * 0.72f,
            ),
            radius = size.width * 0.72f,
            center = glowCenterBottom,
        )

        val neonStroke = Stroke(width = 3.dp.toPx())
        repeat(5) { index ->
            val top = size.height * (0.08f + index * 0.095f)
            val left = size.width * (0.48f + index * 0.035f)
            val path = Path().apply {
                moveTo(left, top)
                lineTo(size.width * 0.96f, top + size.height * 0.035f)
                lineTo(size.width * 0.86f, top + size.height * 0.09f)
                lineTo(left - size.width * 0.08f, top + size.height * 0.045f)
                close()
            }
            drawPath(
                path = path,
                color = neonGreen.copy(alpha = 0.13f),
                style = neonStroke,
            )
        }

        repeat(7) { index ->
            val x = size.width * (0.14f + index * 0.13f)
            drawLine(
                color = Color.White.copy(alpha = 0.05f),
                start = androidx.compose.ui.geometry.Offset(x, size.height * 0.28f),
                end = androidx.compose.ui.geometry.Offset(x - size.width * 0.2f, size.height),
                strokeWidth = 1.dp.toPx(),
            )
        }

        drawRect(
            brush = Brush.horizontalGradient(
                colors = listOf(
                    Color(0xF2050708),
                    Color(0xB2050708),
                    Color.Transparent,
                ),
                startX = 0f,
                endX = size.width,
            )
        )
    }
}
