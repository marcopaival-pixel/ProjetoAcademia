package br.com.nexshape.academia.ui.home

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.getValue
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.repository.AuthRepository

@Composable
fun HomeScreen(
    modifier: Modifier = Modifier,
    authRepository: AuthRepository,
) {
    var profile by remember { mutableStateOf<ProfileDto?>(null) }
    var error by remember { mutableStateOf<String?>(null) }

    LaunchedEffect(Unit) {
        authRepository.loadProfile()
            .onSuccess { profile = it }
            .onFailure { error = it.message }
    }

    Column(modifier = modifier.fillMaxSize().padding(20.dp)) {
        when {
            profile != null -> {
                Text("Olá, ${profile!!.name}", style = MaterialTheme.typography.headlineSmall)
                Text(
                    if (profile!!.isPremium) "Plano Premium ativo" else "Plano Free",
                    modifier = Modifier.padding(top = 8.dp),
                )
                Text(
                    "Use as abas abaixo para treino, nutrição e NexBot.",
                    modifier = Modifier.padding(top = 16.dp),
                )
            }
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            else -> CircularProgressIndicator()
        }
    }
}
