package br.com.nexshape.academia.push

import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.DeviceRegisterRequest
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch

/**
 * Regista token FCM no backend Laravel (`POST /api/v1/devices`).
 * Invocar após login e quando o FirebaseMessaging retornar novo token.
 *
 * Para ativar FCM:
 * 1. Adicionar plugin `com.google.gms.google-services`
 * 2. Colocar `google-services.json` em app/
 * 3. Implementar FirebaseMessagingService e chamar [registerToken]
 */
object DeviceRegistration {
    fun registerToken(fcmToken: String) {
        CoroutineScope(Dispatchers.IO).launch {
            runCatching {
                ApiClient.api().registerDevice(DeviceRegisterRequest(token = fcmToken))
            }
        }
    }
}
