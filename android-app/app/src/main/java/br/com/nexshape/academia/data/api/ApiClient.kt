package br.com.nexshape.academia.data.api

import android.content.Context
import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.local.SessionPreferences
import br.com.nexshape.academia.data.local.TokenStore
import com.squareup.moshi.Moshi
import com.squareup.moshi.kotlin.reflect.KotlinJsonAdapterFactory
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.moshi.MoshiConverterFactory
import java.util.concurrent.TimeUnit

object ApiClient {
    private lateinit var tokenStore: TokenStore
    private lateinit var sessionPreferences: SessionPreferences
    private lateinit var api: NexShapeApi
    private lateinit var okHttpClient: OkHttpClient

    fun init(context: Context) {
        if (::api.isInitialized) return
        tokenStore = TokenStore(context.applicationContext)
        sessionPreferences = SessionPreferences(context.applicationContext)

        val logging = HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) {
                HttpLoggingInterceptor.Level.BODY
            } else {
                HttpLoggingInterceptor.Level.NONE
            }
        }

        okHttpClient = OkHttpClient.Builder()
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(60, TimeUnit.SECONDS)
            .writeTimeout(60, TimeUnit.SECONDS)
            .addInterceptor(AuthInterceptor(tokenStore, sessionPreferences))
            .addInterceptor(TokenRefreshInterceptor(tokenStore))
            .addInterceptor(logging)
            .build()

        val moshi = Moshi.Builder()
            .add(KotlinJsonAdapterFactory())
            .build()

        api = Retrofit.Builder()
            .baseUrl(BuildConfig.API_BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(MoshiConverterFactory.create(moshi))
            .build()
            .create(NexShapeApi::class.java)
    }

    fun api(): NexShapeApi = api

    fun tokenStore(): TokenStore = tokenStore

    fun sessionPreferences(): SessionPreferences = sessionPreferences

    fun authenticatedClient(): OkHttpClient = okHttpClient
}
