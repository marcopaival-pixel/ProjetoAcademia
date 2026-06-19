package br.com.nexshape.academia.data.api

import br.com.nexshape.academia.data.local.SessionPreferences
import br.com.nexshape.academia.data.local.TokenStore
import okhttp3.Interceptor
import okhttp3.Response

class AuthInterceptor(
    private val tokenStore: TokenStore,
    private val sessionPreferences: SessionPreferences? = null,
) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val requestBuilder = chain.request().newBuilder()
            .header("Accept", "application/json")

        tokenStore.getToken()?.let { token ->
            requestBuilder.header("Authorization", "Bearer $token")
        }

        sessionPreferences?.getActivePatientId()?.let { patientId ->
            requestBuilder.header("X-Active-Patient-Id", patientId.toString())
        }

        return chain.proceed(requestBuilder.build())
    }
}
