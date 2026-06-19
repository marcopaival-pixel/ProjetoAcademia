package br.com.nexshape.academia.ui.profile

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Switch
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import androidx.fragment.app.FragmentActivity
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.security.BiometricHelper

@Composable
fun AppLockSection(
    appLockStore: AppLockStore,
    modifier: Modifier = Modifier,
) {
    val context = LocalContext.current
    val biometricAvailable = (context as? FragmentActivity)?.let { BiometricHelper.canAuthenticate(it) } == true
    var lockEnabled by remember { mutableStateOf(appLockStore.isLockEnabled()) }
    var biometricEnabled by remember { mutableStateOf(appLockStore.isBiometricEnabled()) }
    var showPinDialog by remember { mutableStateOf(false) }

    Column(modifier = modifier) {
        Text("Segurança do app", style = MaterialTheme.typography.titleMedium, modifier = Modifier.padding(bottom = 8.dp))

        SettingRow(
            label = "Bloquear ao sair do app",
            checked = lockEnabled,
            onCheckedChange = { enabled ->
                if (enabled && !appLockStore.hasPin() && !biometricAvailable) {
                    showPinDialog = true
                    return@SettingRow
                }
                lockEnabled = enabled
                appLockStore.setLockEnabled(enabled)
                if (enabled && !appLockStore.hasPin() && biometricAvailable) {
                    appLockStore.setBiometricEnabled(true)
                    biometricEnabled = true
                }
            },
        )

        if (biometricAvailable) {
            SettingRow(
                label = "Desbloqueio biométrico",
                checked = biometricEnabled,
                enabled = lockEnabled,
                onCheckedChange = { enabled ->
                    biometricEnabled = enabled
                    appLockStore.setBiometricEnabled(enabled)
                    if (lockEnabled && !enabled && !appLockStore.hasPin()) {
                        showPinDialog = true
                    }
                },
            )
        }

        TextButton(
            onClick = { showPinDialog = true },
            enabled = lockEnabled,
            modifier = Modifier.padding(top = 4.dp),
        ) {
            Text(if (appLockStore.hasPin()) "Alterar PIN" else "Definir PIN")
        }

        if (lockEnabled && !appLockStore.hasPin() && !biometricEnabled) {
            Text(
                "Defina um PIN ou ative a biometria para usar o bloqueio.",
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(top = 4.dp),
            )
        }
    }

    if (showPinDialog) {
        PinSetupDialog(
            onDismiss = { showPinDialog = false },
            onConfirm = { pin ->
                appLockStore.setPin(pin)
                lockEnabled = true
                appLockStore.setLockEnabled(true)
                showPinDialog = false
            },
        )
    }
}

@Composable
private fun SettingRow(
    label: String,
    checked: Boolean,
    enabled: Boolean = true,
    onCheckedChange: (Boolean) -> Unit,
) {
    androidx.compose.foundation.layout.Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 4.dp),
        horizontalArrangement = androidx.compose.foundation.layout.Arrangement.SpaceBetween,
        verticalAlignment = androidx.compose.ui.Alignment.CenterVertically,
    ) {
        Text(label, modifier = Modifier.weight(1f))
        Switch(checked = checked, onCheckedChange = onCheckedChange, enabled = enabled)
    }
}

@Composable
private fun PinSetupDialog(
    onDismiss: () -> Unit,
    onConfirm: (String) -> Unit,
) {
    var pin by remember { mutableStateOf("") }
    var confirm by remember { mutableStateOf("") }
    var error by remember { mutableStateOf<String?>(null) }

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("PIN de segurança") },
        text = {
            Column {
                Text("Use 4 a 6 dígitos. Será pedido ao reabrir o app.")
                OutlinedTextField(
                    value = pin,
                    onValueChange = { pin = it.filter { ch -> ch.isDigit() }.take(6) },
                    label = { Text("PIN") },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 8.dp),
                    singleLine = true,
                )
                OutlinedTextField(
                    value = confirm,
                    onValueChange = { confirm = it.filter { ch -> ch.isDigit() }.take(6) },
                    label = { Text("Confirmar PIN") },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 8.dp),
                    singleLine = true,
                )
                error?.let {
                    Text(it, color = MaterialTheme.colorScheme.error, modifier = Modifier.padding(top = 8.dp))
                }
            }
        },
        confirmButton = {
            TextButton(
                onClick = {
                    when {
                        pin.length < 4 -> error = "PIN deve ter pelo menos 4 dígitos."
                        pin != confirm -> error = "Os PINs não coincidem."
                        else -> onConfirm(pin)
                    }
                },
            ) { Text("Salvar") }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) { Text("Cancelar") }
        },
    )
}
