package br.com.nexshape.academia.ui.navigation

import android.content.Intent
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue

object SubscriptionDeepLink {
    var lastStatus by mutableStateOf<String?>(null)
        private set

    fun consume(): String? {
        val value = lastStatus
        lastStatus = null
        return value
    }

    fun handleIntent(intent: Intent?) {
        val uri = intent?.data ?: return
        if (uri.scheme != "nexshape" || uri.host != "subscription") return
        lastStatus = uri.pathSegments.firstOrNull() ?: uri.lastPathSegment
    }
}

fun subscriptionStatusMessage(status: String?): String? = when (status) {
    "success" -> "Pagamento confirmado! Seu plano será atualizado em instantes."
    "pending" -> "Pagamento em processamento. Verificaremos o status ao voltar."
    "cancelled" -> "Checkout cancelado. Você pode tentar novamente quando quiser."
    "failure" -> "Falha no pagamento. Tente outro método ou contacte o suporte."
    else -> null
}
