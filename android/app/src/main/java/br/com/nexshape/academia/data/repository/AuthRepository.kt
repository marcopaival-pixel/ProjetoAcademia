package br.com.nexshape.academia.data.repository

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ForgotPasswordRequest
import br.com.nexshape.academia.data.api.GoogleLoginRequest
import br.com.nexshape.academia.data.api.LoginRequest
import br.com.nexshape.academia.data.api.OnboardingProfileRequest
import br.com.nexshape.academia.data.api.ProfileDto
import br.com.nexshape.academia.data.api.RegisterRequest
import br.com.nexshape.academia.data.api.UpdateProfileRequest
import br.com.nexshape.academia.data.local.TokenStore
import br.com.nexshape.academia.push.PushTokenManager
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import org.json.JSONObject
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

            val availableRoles = resolveAvailableRoles(profile)
            tokenStore.saveAvailableRoles(availableRoles)

            tokenStore.saveDefaultActiveRole(resolveDefaultActiveRole(availableRoles))
            tokenStore.saveActiveTenant(null)

            appContext?.let { PushTokenManager.registerIfAvailable(it) }
            profile
        }.recoverCatching { error ->
            throw mapError(error)
        }
    }

    suspend fun loginWithGoogle(idToken: String): Result<ProfileDto> = withContext(Dispatchers.IO) {
        runCatching {
            val response = ApiClient.api().googleLogin(GoogleLoginRequest(idToken))
            tokenStore.saveToken(response.accessToken, response.user.email, response.user.name)
            val profile = ApiClient.api().profile().data

            val availableRoles = resolveAvailableRoles(profile)
            tokenStore.saveAvailableRoles(availableRoles)
            tokenStore.saveDefaultActiveRole(resolveDefaultActiveRole(availableRoles))
            tokenStore.saveActiveTenant(null)

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
        onboarding: OnboardingProfileRequest? = null,
    ): Result<Boolean> = withContext(Dispatchers.IO) {
        runCatching {
            val response = ApiClient.api().register(
                RegisterRequest(
                    name = name.trim(),
                    email = email.trim(),
                    password = password,
                    passwordConfirmation = passwordConfirmation,
                    accountType = accountType,
                    birthDate = onboarding?.birthDate,
                    phone = onboarding?.phone,
                    cpf = onboarding?.cpf,
                    sex = onboarding?.sex,
                    heightCm = onboarding?.heightCm,
                    currentWeightKg = onboarding?.currentWeightKg,
                    goal = onboarding?.goal,
                    activityLevel = onboarding?.activityLevel,
                    hasInjury = onboarding?.hasInjury,
                    injuryDetails = onboarding?.injuryDetails,
                    hasDisease = onboarding?.hasDisease,
                    diseaseDetails = onboarding?.diseaseDetails,
                    usesMedication = onboarding?.usesMedication,
                    medicationDetails = onboarding?.medicationDetails,
                    fitnessNotes = onboarding?.fitnessNotes,
                    acceptedTerms = onboarding?.acceptedTerms,
                )
            )

            val token = response.accessToken
            val user = response.user
            if (!token.isNullOrBlank() && user != null) {
                tokenStore.saveToken(token, user.email, user.name)
                tokenStore.saveAvailableRoles(user.roles ?: emptyList())
                tokenStore.saveDefaultActiveRole(resolveDefaultActiveRole(user.roles ?: emptyList()))
                appContext?.let { PushTokenManager.registerIfAvailable(it) }
                true
            } else {
                false
            }
        }.recoverCatching { error ->
            throw mapRegisterError(error)
        }
    }

    suspend fun completeOnboarding(body: OnboardingProfileRequest): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().completeOnboarding(body)
            Unit
        }.recoverCatching { error ->
            throw mapRegisterError(error)
        }
    }

    suspend fun forgotPassword(email: String): Result<String> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().forgotPassword(ForgotPasswordRequest(email.trim())).message
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

    suspend fun updateProfile(body: UpdateProfileRequest): Result<ProfileDto> = withContext(Dispatchers.IO) {
        runCatching {
            ApiClient.api().updateProfile(body).data
        }.recoverCatching { throw mapRegisterError(it) }
    }

    suspend fun logout() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().logout() }
        tokenStore.clear()
        if (appContext != null) {
            ApiClient.sessionPreferences().clear()
            br.com.nexshape.academia.data.local.AppDatabase.get(appContext).clearAllTables()
            androidx.work.WorkManager.getInstance(appContext).cancelAllWork()
        }
    }

    fun isLoggedIn(): Boolean = tokenStore.isLoggedIn()

    fun hasSavedToken(): Boolean = tokenStore.isLoggedIn()

    fun savedEmail(): String? = tokenStore.getEmail()

    private fun mapError(error: Throwable): Exception {
        if (error is HttpException) {
            val apiMessage = extractValidationMessage(error)
            if (!apiMessage.isNullOrBlank()) {
                return Exception(apiMessage)
            }

            val message = when (error.code()) {
                401, 422 -> "E-mail ou senha invalidos."
                429 -> "Muitas tentativas. Aguarde um momento."
                else -> "Erro de conexao (${error.code()})."
            }
            return Exception(message)
        }
        return Exception("Sem conexao com o servidor. Verifique a rede e a URL da API.")
    }

    private fun mapRegisterError(error: Throwable): Exception {
        if (error is HttpException) {
            val apiMessage = extractValidationMessage(error)
            if (!apiMessage.isNullOrBlank()) {
                return Exception(apiMessage)
            }

            val message = when (error.code()) {
                422 -> "Verifique os dados informados para criar a conta."
                429 -> "Muitas tentativas. Aguarde um momento."
                else -> "Erro de conexao (${error.code()})."
            }
            return Exception(message)
        }
        return Exception("Sem conexao com o servidor. Verifique a rede e a URL da API.")
    }

    private fun extractValidationMessage(error: HttpException): String? {
        val raw = error.response()?.errorBody()?.string() ?: return null
        return runCatching {
            val root = JSONObject(raw)
            val errorBody = root.optJSONObject("error")
            val message = errorBody?.optString("message").orEmpty().ifBlank {
                root.optString("message")
            }
            val errors = errorBody?.optJSONObject("errors") ?: root.optJSONObject("errors")
            val firstField = errors?.keys()?.asSequence()?.firstOrNull()
            val firstError = firstField
                ?.let { errors.optJSONArray(it) }
                ?.optString(0)

            firstError?.ifBlank { null } ?: message.ifBlank { null }
        }.getOrNull()
    }

    private fun resolveDefaultActiveRole(roles: List<String>): String? =
        when {
            "aluno" in roles -> "aluno"
            "paciente" in roles -> "paciente"
            "professional" in roles -> "professional"
            "instructor" in roles -> "instructor"
            "supervisor" in roles -> "supervisor"
            "clinic_admin" in roles -> "clinic_admin"
            "admin" in roles -> "admin"
            else -> roles.firstOrNull()
        }

    private fun resolveAvailableRoles(profile: ProfileDto): List<String> {
        val roles = profile.roles?.toMutableList() ?: mutableListOf()
        if ((roles.contains("student") || roles.contains("aluno") || roles.contains("athlete")) && profile.studentStatus == "vinculado") {
            if (!roles.contains("paciente")) {
                roles.add("paciente")
            }
        }
        return roles
    }
}
