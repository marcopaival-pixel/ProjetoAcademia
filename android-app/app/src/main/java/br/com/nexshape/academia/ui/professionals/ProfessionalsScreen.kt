package br.com.nexshape.academia.ui.professionals

import android.widget.Toast
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.PaddingValues
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Switch
import androidx.compose.material3.SwitchDefaults
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.api.LinkedProfessionalDto
import br.com.nexshape.academia.data.repository.AgendaRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun ProfessionalsScreen(modifier: Modifier = Modifier) {
    val repository = remember { AgendaRepository() }
    val scope = rememberCoroutineScope()
    val context = LocalContext.current

    var professionals by remember { mutableStateOf<List<LinkedProfessionalDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var error by remember { mutableStateOf<String?>(null) }
    var actionInProgress by remember { mutableStateOf(false) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.professionals()
                .onSuccess {
                    professionals = it
                    loading = false
                }
                .onFailure {
                    error = friendlyError(it)
                    loading = false
                }
        }
    }

    LaunchedEffect(Unit) {
        load()
    }

    NexShapeScreen(
        title = "Mentores",
        subtitle = "Profissionais vinculados ao seu acompanhamento.",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load, enabled = !loading) {
                Icon(Icons.Default.Refresh, contentDescription = "Recarregar", tint = NexNeon)
            }
        }
    ) {
        when {
            loading -> NexLoadingState("Carregando mentores...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = ::load)
            professionals.isEmpty() -> NexEmptyState(
                title = "Sem vínculos ativos",
                message = "Você pode usar o NexShape como aluno independente. Quando houver vínculo com um profissional, permissões e dados compartilhados aparecerão aqui.",
            )
            else -> LazyColumn(
                contentPadding = PaddingValues(bottom = 24.dp),
                verticalArrangement = Arrangement.spacedBy(16.dp),
                modifier = Modifier.fillMaxSize()
            ) {
                items(professionals, key = { it.id }) { professional ->
                    ProfessionalCard(
                        professional = professional,
                        disabled = actionInProgress,
                        onUpdatePermissions = { linkId, updated ->
                            actionInProgress = true
                            scope.launch {
                                repository.updateLinkPermissions(linkId, updated)
                                    .onSuccess {
                                        Toast.makeText(context, "Permissões atualizadas com sucesso!", Toast.LENGTH_SHORT).show()
                                        // Update local state to reflect switch toggle immediately
                                        professionals = professionals.map { p ->
                                            if (p.linkId == linkId) {
                                                p.copy(permissions = updated)
                                            } else p
                                        }
                                    }
                                    .onFailure {
                                        Toast.makeText(context, "Erro ao atualizar permissões: ${it.message}", Toast.LENGTH_LONG).show()
                                    }
                                actionInProgress = false
                            }
                        },
                        onRevoke = { linkId ->
                            actionInProgress = true
                            scope.launch {
                                repository.revokeLink(linkId)
                                    .onSuccess {
                                        Toast.makeText(context, "Vínculo revogado com sucesso!", Toast.LENGTH_SHORT).show()
                                        professionals = professionals.filter { p -> p.linkId != linkId }
                                    }
                                    .onFailure {
                                        Toast.makeText(context, "Erro ao revogar vínculo: ${it.message}", Toast.LENGTH_LONG).show()
                                    }
                                actionInProgress = false
                            }
                        }
                    )
                }
            }
        }
    }
}

@Composable
private fun ProfessionalCard(
    professional: LinkedProfessionalDto,
    disabled: Boolean,
    onUpdatePermissions: (Int, Map<String, Boolean>) -> Unit,
    onRevoke: (Int) -> Unit,
) {
    var showRevokeDialog by remember { mutableStateOf(false) }

    if (showRevokeDialog) {
        AlertDialog(
            onDismissRequest = { showRevokeDialog = false },
            title = { Text("Revogar Vínculo", fontWeight = FontWeight.Bold) },
            text = { Text("Deseja mesmo revogar o vínculo com ${professional.name}? Este profissional perderá acesso a todos os seus dados clínicos e de treino.") },
            confirmButton = {
                TextButton(
                    onClick = {
                        showRevokeDialog = false
                        professional.linkId?.let { onRevoke(it) }
                    }
                ) {
                    Text("Revogar", color = Color.Red)
                }
            },
            dismissButton = {
                TextButton(onClick = { showRevokeDialog = false }) {
                    Text("Cancelar", color = Color.White)
                }
            },
            containerColor = Color(0xFF0F172A),
            titleContentColor = Color.White,
            textContentColor = Color.White
        )
    }

    NexCard(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.fillMaxWidth()) {
            Text(professional.name, color = Color.White, fontWeight = FontWeight.Black, fontSize = 16.sp)
            professional.specialty?.takeIf { it.isNotBlank() }?.let {
                Text(it, color = NexNeon, modifier = Modifier.padding(top = 4.dp), fontSize = 13.sp)
            }
            professional.email?.takeIf { it.isNotBlank() }?.let {
                Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp), fontSize = 12.sp)
            }
            professional.branding?.clinicName?.takeIf { it.isNotBlank() }?.let {
                Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp), fontSize = 12.sp)
            }
            val serviceTypes = professional.serviceTypes.orEmpty().filter { it.isNotBlank() }
            if (serviceTypes.isNotEmpty()) {
                Text(
                    serviceTypes.joinToString(prefix = "Serviços: "),
                    color = NexMuted,
                    fontSize = 12.sp,
                    modifier = Modifier.padding(top = 6.dp),
                )
            }

            val linkId = professional.linkId
            if (linkId != null) {
                Spacer(modifier = Modifier.height(14.dp))
                Text("Permissões de Acesso de Saúde", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 13.sp)
                
                val currentPermissions = professional.permissions ?: emptyMap()
                val psychologyVal = currentPermissions["psychology_session_notes"] ?: false
                val restrictedVal = currentPermissions["restricted_notes"] ?: false

                PermissionSwitchRow(
                    label = "Notas de Psicologia",
                    description = "Notas de sessões e histórico psicológico",
                    checked = psychologyVal,
                    disabled = disabled,
                    onCheckedChange = { checked ->
                        onUpdatePermissions(
                            linkId,
                            currentPermissions.toMutableMap().apply { put("psychology_session_notes", checked) }
                        )
                    }
                )

                PermissionSwitchRow(
                    label = "Observações Restritas",
                    description = "Anotações médicas restritas e sensíveis",
                    checked = restrictedVal,
                    disabled = disabled,
                    onCheckedChange = { checked ->
                        onUpdatePermissions(
                            linkId,
                            currentPermissions.toMutableMap().apply { put("restricted_notes", checked) }
                        )
                    }
                )

                Spacer(modifier = Modifier.height(12.dp))
                OutlinedButton(
                    onClick = { showRevokeDialog = true },
                    enabled = !disabled,
                    colors = ButtonDefaults.outlinedButtonColors(
                        contentColor = Color.Red,
                        disabledContentColor = NexMuted
                    ),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text("Revogar Vínculo Profissional", fontSize = 12.sp)
                }
            }
        }
    }
}

@Composable
private fun PermissionSwitchRow(
    label: String,
    description: String,
    checked: Boolean,
    disabled: Boolean,
    onCheckedChange: (Boolean) -> Unit,
) {
    Row(
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.SpaceBetween,
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 8.dp)
    ) {
        Column(modifier = Modifier.weight(1f)) {
            Text(label, color = Color.White, fontSize = 13.sp, fontWeight = FontWeight.Bold)
            Text(description, color = NexMuted, fontSize = 11.sp)
        }
        Spacer(modifier = Modifier.width(12.dp))
        Switch(
            checked = checked,
            onCheckedChange = onCheckedChange,
            enabled = !disabled,
            colors = SwitchDefaults.colors(
                checkedThumbColor = NexNeon,
                checkedTrackColor = NexNeon.copy(alpha = 0.5f),
                uncheckedThumbColor = Color.White,
                uncheckedTrackColor = NexMuted.copy(alpha = 0.5f)
            )
        )
    }
}
