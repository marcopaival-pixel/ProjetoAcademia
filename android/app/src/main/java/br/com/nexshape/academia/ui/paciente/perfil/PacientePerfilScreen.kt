package br.com.nexshape.academia.ui.paciente.perfil

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.ui.components.NexNeon

@Composable
fun PacientePerfilScreen(
    modifier: Modifier = Modifier,
    authRepository: AuthRepository,
    appLockStore: AppLockStore,
    canSwitchToStudent: Boolean,
    onSwitchToStudent: () -> Unit,
    onChangeContext: () -> Unit,
    onLogout: () -> Unit
) {
    Box(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10)),
        contentAlignment = Alignment.Center
    ) {
        Column(
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            Text(text = "Perfil do Paciente", color = Color.White, fontSize = 24.sp)
            
            Button(
                onClick = onChangeContext,
                colors = ButtonDefaults.buttonColors(containerColor = NexNeon, contentColor = Color.Black)
            ) {
                Text(text = "Trocar Vínculo/Profissional")
            }

            if (canSwitchToStudent) {
                Button(
                    onClick = onSwitchToStudent,
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF1F2937), contentColor = Color.White)
                ) {
                    Text(text = "Ir para Modo Aluno")
                }
            }
            
            Button(
                onClick = onLogout,
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFEF4444), contentColor = Color.White)
            ) {
                Text(text = "Sair")
            }
        }
    }
}
