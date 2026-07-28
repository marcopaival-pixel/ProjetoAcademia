package br.com.nexshape.academia

import android.Manifest
import android.content.Intent
import android.os.Build
import android.os.Bundle
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.runtime.LaunchedEffect
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.fragment.app.FragmentActivity
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleEventObserver
import androidx.lifecycle.compose.LocalLifecycleOwner
import androidx.compose.runtime.DisposableEffect
import androidx.compose.ui.platform.LocalContext
import androidx.biometric.BiometricPrompt
import androidx.core.content.ContextCompat
import android.content.Context
import br.com.nexshape.academia.ui.navigation.NexShapeApp
import br.com.nexshape.academia.ui.navigation.SubscriptionDeepLink
import br.com.nexshape.academia.ui.theme.NexShapeTheme
import br.com.nexshape.academia.data.repository.AuthRepository // Assumindo que existe
import kotlinx.coroutines.launch

class MainActivity : FragmentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        installSplashScreen()
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        SubscriptionDeepLink.handleIntent(intent)
        setContent {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                val permissionLauncher = rememberLauncherForActivityResult(
                    ActivityResultContracts.RequestPermission(),
                ) { /* resultado tratado pelo sistema */ }

                LaunchedEffect(Unit) {
                    permissionLauncher.launch(Manifest.permission.POST_NOTIFICATIONS)
                }
            }
            val lifecycleOwner = LocalLifecycleOwner.current
            val context = LocalContext.current

            DisposableEffect(lifecycleOwner) {
                val observer = LifecycleEventObserver { _, event ->
                    if (event == Lifecycle.Event.ON_PAUSE) {
                        val prefs = context.getSharedPreferences("nexshape_session", Context.MODE_PRIVATE)
                        prefs.edit().putLong("last_background_time", System.currentTimeMillis()).apply()
                    } else if (event == Lifecycle.Event.ON_RESUME) {
                        val prefs = context.getSharedPreferences("nexshape_session", Context.MODE_PRIVATE)
                        val lastTime = prefs.getLong("last_background_time", 0L)
                        
                        if (lastTime > 0) {
                            val diffMinutes = (System.currentTimeMillis() - lastTime) / (60 * 1000)
                            // 30 minutos por segurança geral (o backend já invalida aos 60m para pacientes)
                            if (diffMinutes >= 30) {
                                requireBiometricAuth()
                            }
                        }
                    }
                }
                lifecycleOwner.lifecycle.addObserver(observer)
                onDispose { lifecycleOwner.lifecycle.removeObserver(observer) }
            }

            NexShapeTheme {
                NexShapeApp()
            }
        }
    }

    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        setIntent(intent)
        SubscriptionDeepLink.handleIntent(intent)
    }

    private fun requireBiometricAuth() {
        val executor = ContextCompat.getMainExecutor(this)
        val biometricPrompt = BiometricPrompt(this, executor,
            object : BiometricPrompt.AuthenticationCallback() {
                override fun onAuthenticationError(errorCode: Int, errString: CharSequence) {
                    super.onAuthenticationError(errorCode, errString)
                    forceLogout()
                }

                override fun onAuthenticationSucceeded(result: BiometricPrompt.AuthenticationResult) {
                    super.onAuthenticationSucceeded(result)
                    // Continua normalmente e reseta o tempo
                    val prefs = getSharedPreferences("nexshape_session", Context.MODE_PRIVATE)
                    prefs.edit().putLong("last_background_time", 0L).apply()
                }

                override fun onAuthenticationFailed() {
                    super.onAuthenticationFailed()
                    // Permite tentar novamente, mas não libera a tela
                }
            })

        val promptInfo = BiometricPrompt.PromptInfo.Builder()
            .setTitle("Sessão Expirada")
            .setSubtitle("Confirme sua identidade para continuar")
            .setNegativeButtonText("Fazer Login Novamente")
            .setAllowedAuthenticators(androidx.biometric.BiometricManager.Authenticators.BIOMETRIC_WEAK)
            .build()

        biometricPrompt.authenticate(promptInfo)
    }

    private fun forceLogout() {
        // Redirecionar para o fluxo de login
        // Dependendo de como AuthRepository limpa os dados:
        val prefs = getSharedPreferences("nexshape_session", Context.MODE_PRIVATE)
        prefs.edit().clear().apply()
        
        // Simplesmente limpando o token que AuthRepository ou ApiClient usam fará a UI reagir 
        // caso usem fluxos reativos de StateFlow.
        // Aqui enviamos um intent de reinício caso o app não reaja automaticamente:
        val intent = Intent(this, MainActivity::class.java).apply {
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK)
        }
        startActivity(intent)
        finish()
    }
}
