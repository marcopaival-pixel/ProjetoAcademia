package br.com.nexshape.academia.data.repository

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.LoginRequest
import br.com.nexshape.academia.data.api.ProfileDto
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
            appContext?.let { PushTokenManager.registerIfAvailable(it) }
            profile
        }.recoverCatching { error ->
            throw mapError(error)
        }
    }

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
        }
    }

    fun isLoggedIn(): Boolean = tokenStore.isLoggedIn()

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
}
