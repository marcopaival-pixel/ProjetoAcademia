package br.com.nexshape.academia.data.repository

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.LoginRequest
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.api.RegisterRequest
import br.com.nexshape.academia.data.local.TokenStore
import br.com.nexshape.academia.push.PushTokenManager
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import retrofit2.HttpException

class AuthRepository(
    private val tokenStore: TokenStore,
    private val appContext: Context? = null,
) {
    suspend fun login(email: String, password: String): Result<ProfileDto> = withContext(Dispatchers.IO) {
        runCatching {
            val response = ApiClient.api().login(LoginRequest(email.trim(), password))
            tokenStore.saveToken(response.accessToken, response.user.email, response.user.name)
            val profile = ApiClient.api().profile().data
            
            val availableRoles = profile.roles ?: emptyList()
            tokenStore.saveAvailableRoles(availableRoles)
            
            if (availableRoles.size == 1) {
                tokenStore.saveActiveRole(availableRoles.first())
            } else {
                tokenStore.saveActiveRole(null)
                tokenStore.saveActiveTenant(null)
            }

            appContext?.let { PushTokenManager.registerIfAvailable(it) }
            profile
        }.recoverCatching { error ->
            throw mapError(error)
        }
    }

    suspend fun register(
        name: String,
        email: String,
        password: String,
        passwordConfirmation: String,
        accountType: String,
    ): Result<Boolean> = withContext(Dispatchers.IO) {
        runCatching {
            val response = ApiClient.api().register(
                RegisterRequest(
                    name = name.trim(),
                    email = email.trim(),
                    password = password,
                    passwordConfirmation = passwordConfirmation,
                    accountType = accountType,
                )
            )

            val token = response.accessToken
            val user = response.user
            if (!token.isNullOrBlank() && user != null) {
                tokenStore.saveToken(token, user.email, user.name)
                tokenStore.saveAvailableRoles(user.roles ?: emptyList())
                user.roles?.singleOrNull()?.let { tokenStore.saveActiveRole(it) }
                appContext?.let { PushTokenManager.registerIfAvailable(it) }
                true
            } else {
                false
            }
        }.recoverCatching { error ->
            throw mapRegisterError(error)
        }
    }

    suspend fun unlockSavedSession(): Result<ProfileDto> = loadProfile()

    suspend fun loadProfile(): Result<ProfileDto> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().profile().data
        }.recoverCatching { throw mapError(it) }
    }

    suspend fun logout() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().logout() }
        tokenStore.clear()
        if (appContext != null) {
            ApiClient.sessionPreferences().clear()
            
            // Segurança: Limpar a base de dados offline e cancelar tarefas agendadas (WorkManager)
            br.com.nexshape.academia.data.local.AppDatabase.get(appContext).clearAllTables()
            androidx.work.WorkManager.getInstance(appContext).cancelAllWork()
        }
    }

    fun isLoggedIn(): Boolean = tokenStore.isLoggedIn()

    fun hasSavedToken(): Boolean = tokenStore.isLoggedIn()

    fun savedEmail(): String? = tokenStore.getEmail()

    private fun mapError(error: Throwable): Exception {
        if (error is HttpException) {
            val message = when (error.code()) {
                401, 422 -> "E-mail ou senha inválidos."
                429 -> "Muitas tentativas. Aguarde um momento."
                else -> "Erro de conexão (${error.code()})."
            }
            return Exception(message)
        }
        return Exception("Sem conexão com o servidor. Verifique a rede e a URL da API.")
    }
    private fun mapRegisterError(error: Throwable): Exception {
        if (error is HttpException) {
            val message = when (error.code()) {
                422 -> "Verifique os dados informados para criar a conta."
                429 -> "Muitas tentativas. Aguarde um momento."
                else -> "Erro de conexÃ£o (${error.code()})."
            }
            return Exception(message)
        }
        return Exception("Sem conexÃ£o com o servidor. Verifique a rede e a URL da API.")
    }
}
