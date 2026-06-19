package br.com.nexshape.academia.push

import android.util.Log
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage

class NexShapeFirebaseMessagingService : FirebaseMessagingService() {
    override fun onNewToken(token: String) {
        super.onNewToken(token)
        Log.d(TAG, "Novo token FCM recebido")
        DeviceRegistration.registerToken(token)
    }

    override fun onMessageReceived(message: RemoteMessage) {
        super.onMessageReceived(message)
        val title = message.notification?.title ?: message.data["title"] ?: "NexShape"
        val body = message.notification?.body ?: message.data["body"] ?: message.data["message"] ?: ""
        if (body.isNotBlank()) {
            PushNotificationHelper.show(applicationContext, title, body)
        }
        Log.d(TAG, "Push recebido: $title — type=${message.data["type"]}")
    }

    companion object {
        private const val TAG = "NexShapeFCM"
    }
}
