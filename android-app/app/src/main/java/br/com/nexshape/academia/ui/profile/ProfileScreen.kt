package br.com.nexshape.academia.ui.profile



import androidx.compose.foundation.layout.Column

import androidx.compose.foundation.layout.fillMaxSize

import androidx.compose.foundation.layout.padding

import androidx.compose.foundation.rememberScrollState

import androidx.compose.foundation.verticalScroll

import androidx.compose.material3.Button

import androidx.compose.material3.CircularProgressIndicator

import androidx.compose.material3.MaterialTheme

import androidx.compose.material3.OutlinedButton

import androidx.compose.material3.Text

import androidx.compose.runtime.Composable

import androidx.compose.runtime.LaunchedEffect

import androidx.compose.runtime.getValue

import androidx.compose.runtime.mutableStateOf

import androidx.compose.runtime.remember

import androidx.compose.runtime.rememberCoroutineScope

import androidx.compose.runtime.setValue

import androidx.compose.ui.Modifier

import androidx.compose.ui.unit.dp

import br.com.nexshape.academia.BuildConfig

import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.local.AppLockStore
import br.com.nexshape.academia.data.repository.AuthRepository
import br.com.nexshape.academia.ui.profile.AppLockSection
import br.com.nexshape.academia.ui.subscription.SubscriptionSection

import kotlinx.coroutines.launch



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



    LaunchedEffect(Unit) {

        authRepository.loadProfile().onSuccess { profile = it }

    }



    Column(

        modifier = modifier

            .fillMaxSize()

            .verticalScroll(rememberScrollState())

            .padding(20.dp),

    ) {

        Text("Perfil", style = MaterialTheme.typography.headlineSmall)

        if (profile == null) {

            CircularProgressIndicator(modifier = Modifier.padding(top = 12.dp))

        } else {

            Text(profile!!.name, modifier = Modifier.padding(top = 12.dp))

            Text(profile!!.email)

            Text(

                when {

                    profile!!.isProfessional && profile!!.isStudent -> "Aluno + Profissional"

                    profile!!.isProfessional -> "Profissional"

                    else -> if (profile!!.isPremium) "Premium" else "Free"

                },

                modifier = Modifier.padding(top = 8.dp),

            )

            profile!!.branding?.clinicName?.let {

                Text("Clínica: $it", style = MaterialTheme.typography.bodySmall)

            }

            profile!!.activePatientId?.let {

                Text("Aluno ativo: #$it", style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(top = 4.dp))

            }

        }



        if (showSubscription) {

            SubscriptionSection(modifier = Modifier.padding(top = 24.dp))

        }

        AppLockSection(
            appLockStore = appLockStore,
            modifier = Modifier.padding(top = 24.dp),
        )



        if (canSwitchToPro) {

            OutlinedButton(onClick = onSwitchToPro, modifier = Modifier.padding(top = 16.dp)) {

                Text("Modo Profissional")

            }

        }



        if (canSwitchToStudent) {

            OutlinedButton(onClick = onSwitchToStudent, modifier = Modifier.padding(top = 16.dp)) {

                Text("Modo Aluno")

            }

        }



        Text("API: ${BuildConfig.API_BASE_URL}", modifier = Modifier.padding(top = 16.dp))

        Text("App v${BuildConfig.VERSION_NAME}", style = MaterialTheme.typography.bodySmall)

        Button(

            onClick = {

                scope.launch {

                    authRepository.logout()

                    onLogout()

                }

            },

            modifier = Modifier.padding(top = 24.dp),

        ) {

            Text("Sair")

        }

    }

}


