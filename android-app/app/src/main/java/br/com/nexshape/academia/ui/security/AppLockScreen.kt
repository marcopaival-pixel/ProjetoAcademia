package br.com.nexshape.academia.ui.security

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.Button
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.unit.dp
import androidx.fragment.app.FragmentActivity
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.security.BiometricHelper

@Composable
fun AppLockScreen(
    appLockStore: AppLockStore,
    onUnlocked: () -> Unit,
    onLogout: () -> Unit,
) {
    val context = LocalContext.current
    val activity = context as? FragmentActivity
    val biometricAvailable = activity != null && BiometricHelper.canAuthenticate(activity)
    var pin by remember { mutableStateOf("") }
    var error by remember { mutableStateOf<String?>(null) }

    fun tryBiometric() {
        if (activity == null || !appLockStore.isBiometricEnabled() || !biometricAvailable) return
        BiometricHelper.authenticate(
            activity = activity,
            subtitle = "Desbloqueie o NexShape",
            onSuccess = onUnlocked,
            onError = { message -> error = message },
        )
    }

    LaunchedEffect(Unit) {
        if (appLockStore.isBiometricEnabled() && biometricAvailable) {
            tryBiometric()
        }
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(24.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Text("NexShape", style = MaterialTheme.typography.headlineMedium)
        Text("App bloqueado", style = MaterialTheme.typography.bodyMedium, modifier = Modifier.padding(top = 8.dp))

        if (appLockStore.hasPin()) {
            Spacer(Modifier.height(24.dp))
            OutlinedTextField(
                value = pin,
                onValueChange = {
                    pin = it.filter { ch -> ch.isDigit() }.take(6)
                    error = null
                },
                label = { Text("PIN") },
                modifier = Modifier.fillMaxWidth(),
                visualTransformation = PasswordVisualTransformation(),
                keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.NumberPassword),
                singleLine = true,
            )
            error?.let {
                Text(it, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(top = 8.dp))
            }
            Spacer(Modifier.height(12.dp))
            Button(
                onClick = {
                    if (appLockStore.verifyPin(pin)) {
                        onUnlocked()
                    } else {
                        error = "PIN incorreto."
                        pin = ""
                    }
                },
                enabled = pin.length >= 4,
                modifier = Modifier.fillMaxWidth(),
            ) {
                Text("Desbloquear")
            }
        }

        if (appLockStore.isBiometricEnabled() && biometricAvailable) {
            OutlinedButton(
                onClick = { tryBiometric() },
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 12.dp),
            ) {
                Text("Usar biometria")
            }
        }

        OutlinedButton(
            onClick = onLogout,
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 24.dp),
        ) {
            Text("Sair da conta")
        }
    }
}
