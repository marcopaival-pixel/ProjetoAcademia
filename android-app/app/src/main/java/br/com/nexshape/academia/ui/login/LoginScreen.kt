package br.com.nexshape.academia.ui.login

import android.content.Intent
import android.net.Uri
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
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
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
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.fragment.app.FragmentActivity
import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.security.BiometricHelper
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
    val biometricLoginAvailable = activity != null &&
        BiometricHelper.canAuthenticate(activity) &&
        authRepository.hasSavedToken()

    val brandGreen = Color(0xFF10B981)
    val neonGreen = Color(0xFF19F5A6)
    val fieldColor = Color(0xFF111820)
    val forgotPasswordUrl = remember {
        BuildConfig.API_BASE_URL
            .removeSuffix("/")
            .removeSuffix("/api/v1")
            .removeSuffix("/api")
            .plus("/forgot-password")
    }

    if (showingRegister) {
        RegisterScreen(
            authRepository = authRepository,
            brandGreen = brandGreen,
            neonGreen = neonGreen,
            fieldColor = fieldColor,
            onBackToLogin = { showingRegister = false },
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
                Box(
                    modifier = Modifier
                        .size(58.dp)
                        .clip(RoundedCornerShape(18.dp))
                        .background(brandGreen),
                    contentAlignment = Alignment.Center,
                ) {
                    Text(
                        text = "NX",
                        color = Color.White,
                        fontSize = 19.sp,
                        fontWeight = FontWeight.Black,
                    )
                }

                Spacer(Modifier.height(18.dp))

                Text(
                    text = "NEXSHAPE",
                    color = Color.White,
                    fontSize = 15.sp,
                    fontWeight = FontWeight.Black,
                )

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
                    onClick = {
                        context.startActivity(
                            Intent(Intent.ACTION_VIEW, Uri.parse(forgotPasswordUrl))
                        )
                    },
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

                Spacer(Modifier.height(14.dp))

                Button(
                    onClick = {
                        loading = true
                        error = null
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

                TextButton(onClick = { showingRegister = true }) {
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
    }
}

@Composable
private fun RegisterScreen(
    authRepository: AuthRepository,
    brandGreen: Color,
    neonGreen: Color,
    fieldColor: Color,
    onBackToLogin: () -> Unit,
    onRegisteredAndLoggedIn: () -> Unit,
) {
    val scope = rememberCoroutineScope()
    var name by remember { mutableStateOf("") }
    var email by remember { mutableStateOf("") }
    var password by remember { mutableStateOf("") }
    var passwordConfirmation by remember { mutableStateOf("") }
    var accountType by remember { mutableStateOf<String?>(null) }
    var loading by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var success by remember { mutableStateOf<String?>(null) }

    fun validate(): String? {
        if (name.isBlank()) return "Nome obrigatorio."
        if (!android.util.Patterns.EMAIL_ADDRESS.matcher(email.trim()).matches()) return "E-mail invalido."
        if (password.isBlank()) return "Senha obrigatoria."
        if (passwordConfirmation != password) return "A confirmacao deve ser igual a senha."
        if (accountType.isNullOrBlank()) return "Selecione o tipo de conta."
        return null
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
                text = "Comece com seu perfil inicial NexShape.",
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
                AccountTypeOption(
                    text = "Aluno / Atleta",
                    selected = accountType == "aluno",
                    onClick = { accountType = "aluno" },
                    color = neonGreen,
                )
                AccountTypeOption(
                    text = "Profissional / Treinador",
                    selected = accountType == "professional",
                    onClick = { accountType = "professional" },
                    color = neonGreen,
                )

                error?.let {
                    Spacer(Modifier.height(8.dp))
                    Text(it, color = MaterialTheme.colorScheme.error, textAlign = TextAlign.Center)
                }
                success?.let {
                    Spacer(Modifier.height(8.dp))
                    Text(it, color = neonGreen, textAlign = TextAlign.Center)
                }

                Spacer(Modifier.height(16.dp))
                Button(
                    onClick = {
                        val validationError = validate()
                        if (validationError != null) {
                            error = validationError
                            success = null
                            return@Button
                        }
                        loading = true
                        error = null
                        success = null
                        scope.launch {
                            authRepository.register(
                                name = name,
                                email = email,
                                password = password,
                                passwordConfirmation = passwordConfirmation,
                                accountType = accountType.orEmpty(),
                            ).onSuccess { loggedIn ->
                                success = "Conta criada com sucesso"
                                if (loggedIn) {
                                    onRegisteredAndLoggedIn()
                                } else {
                                    onBackToLogin()
                                }
                            }.onFailure {
                                error = it.message
                            }
                            loading = false
                        }
                    },
                    enabled = !loading,
                    modifier = Modifier
                        .fillMaxWidth()
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
                        Text("CRIAR CONTA", fontWeight = FontWeight.Black)
                    }
                }

                TextButton(onClick = onBackToLogin) {
                    Text("Voltar para login", color = neonGreen, fontWeight = FontWeight.Black)
                }
            }
        }
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
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = Modifier.fillMaxWidth(),
        keyboardOptions = KeyboardOptions(keyboardType = keyboardType),
        visualTransformation = if (password) PasswordVisualTransformation() else VisualTransformation.None,
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
