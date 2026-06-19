package br.com.nexshape.academia.data.api

import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.local.TokenStore
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import okhttp3.HttpUrl.Companion.toHttpUrlOrNull
import okhttp3.Interceptor
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import okhttp3.Response

class TokenRefreshInterceptor(
    private val tokenStore: TokenStore,
) : Interceptor {
    private val lock = Any()
    private val moshi = Moshi.Builder().add(KotlinJsonAdapterFactory()).build()
    private val refreshClient = OkHttpClient.Builder().build()

    override fun intercept(chain: Interceptor.Chain): Response {
        val response = chain.proceed(chain.request())
        if (response.code != 401 || chain.request().header(RETRY_HEADER) == "1") {
            return response
        }
        response.close()

        synchronized(lock) {
            val refreshed = refreshToken() ?: return chain.proceed(chain.request())
            val retry = chain.request().newBuilder()
                .header("Authorization", "Bearer $refreshed")
                .header(RETRY_HEADER, "1")
                .build()
            return chain.proceed(retry)
        }
    }

    private fun refreshToken(): String? {
        val current = tokenStore.getToken() ?: return null
        val baseUrl = BuildConfig.API_BASE_URL.toHttpUrlOrNull() ?: return null
        val url = baseUrl.resolve("auth/refresh") ?: return null
        val body = """{"device_name":"nexshape-android"}"""
            .toRequestBody("application/json".toMediaType())

        val request = Request.Builder()
            .url(url)
            .post(body)
            .header("Authorization", "Bearer $current")
            .header("Accept", "application/json")
            .build()

        refreshClient.newCall(request).execute().use { response ->
            if (!response.isSuccessful) return null
            val payload = response.body?.string() ?: return null
            val tokenResponse = moshi.adapter(AuthTokenResponse::class.java).fromJson(payload) ?: return null
            tokenStore.saveToken(
                tokenResponse.accessToken,
                tokenStore.getEmail() ?: tokenResponse.user.email,
                tokenStore.getName() ?: tokenResponse.user.name,
            )
            return tokenResponse.accessToken
        }
    }

    companion object {
        private const val RETRY_HEADER = "X-Auth-Retry"
    }
}
