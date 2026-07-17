package br.com.nexshape.academia.push

import android.content.Context
import android.util.Log
import br.com.nexshape.academia.data.api.ApiClient
import com.google.firebase.FirebaseApp
import com.google.firebase.messaging.FirebaseMessaging
import kotlin.coroutines.resume
import kotlin.coroutines.suspendCoroutine

object PushTokenManager {
    private const val TAG = "PushTokenManager"

    suspend fun registerIfAvailable(context: Context) {
        if (!isFirebaseConfigured(context)) {
            Log.d(TAG, "Firebase não configurado — push ignorado.")
            return
        }

        runCatching {
            val token = fetchToken()
            if (!token.isNullOrBlank()) {
                DeviceRegistration.registerToken(token)
            }
        }.onFailure {
            Log.w(TAG, "Falha ao obter token FCM: ${it.message}")
        }
    }

    private suspend fun fetchToken(): String? = suspendCoroutine { continuation ->
        FirebaseMessaging.getInstance().token
            .addOnCompleteListener { task ->
                if (task.isSuccessful) {
                    continuation.resume(task.result)
                } else {
                    continuation.resume(null)
                }
            }
    }

    private fun isFirebaseConfigured(context: Context): Boolean {
        return runCatching {
            FirebaseApp.getApps(context).isNotEmpty()
        }.getOrDefault(false)
    }
}
