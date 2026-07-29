package br.com.nexshape.academia.ui.paciente.mensagens

import android.util.Log
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import br.com.nexshape.academia.BuildConfig
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ApiSuccessResponse
import br.com.nexshape.academia.data.api.InternalMessageDto
import br.com.nexshape.academia.data.api.SendInternalMessageRequest
import com.pusher.client.Pusher
import com.pusher.client.PusherOptions
import com.pusher.client.channel.PrivateChannelEventListener
import com.pusher.client.channel.PusherEvent
import com.pusher.client.connection.ConnectionEventListener
import com.pusher.client.connection.ConnectionState
import com.pusher.client.connection.ConnectionStateChange
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import okhttp3.HttpUrl.Companion.toHttpUrlOrNull
import org.json.JSONObject

data class MensagensUiState(
    val isLoading: Boolean = false,
    val messages: List<InternalMessageDto> = emptyList(),
    val error: String? = null,
    val isConnected: Boolean = false
)

class PacienteMensagensViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(MensagensUiState())
    val uiState: StateFlow<MensagensUiState> = _uiState.asStateFlow()

    private var pusher: Pusher? = null
    private var conversationId: Int? = null

    init {
        fetchMessages()
    }

    private fun fetchMessages() {
        _uiState.update { it.copy(isLoading = true, error = null) }
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientMessages()
                if (response is ApiSuccessResponse) {
                    val data = response.data
                    conversationId = data.conversationId
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            messages = data.data
                        )
                    }
                    connectToPusher(data.conversationId)
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Erro ao carregar mensagens.") }
                }
            } catch (e: Exception) {
                Log.e("PacienteMensagens", "Erro fetchMessages", e)
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage) }
            }
        }
    }

    fun sendMessage(content: String) {
        if (content.isBlank()) return
        
        // Optimistic update opcional aqui
        
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val req = SendInternalMessageRequest(content = content)
                val response = ApiClient.api().sendPatientMessage(req)
                if (response is ApiSuccessResponse) {
                    val newMessage = response.data.data
                    // Adiciona na lista se já não estiver lá pelo pusher
                    _uiState.update { state ->
                        if (state.messages.none { it.id == newMessage.id }) {
                            state.copy(messages = state.messages + newMessage)
                        } else state
                    }
                }
            } catch (e: Exception) {
                Log.e("PacienteMensagens", "Erro sendMessage", e)
            }
        }
    }

    private fun connectToPusher(convId: Int) {
        if (pusher != null) return

        val token = ApiClient.tokenStore().getToken() ?: return
        
        // Extrai o host de BuildConfig.API_BASE_URL (ex: http://192.168.0.109:8001/api/)
        val httpUrl = BuildConfig.API_BASE_URL.toHttpUrlOrNull()
        val host = httpUrl?.host ?: "10.0.2.2"

        val options = PusherOptions().apply {
            setHost(host)
            setWsPort(8080)
            isUseTLS = false // Laravel Reverb sem TLS localmente
            // Em produção com Forge, isso seria true, e wssPort 443
            
            // Configurar a autorização para canais privados
            val authorizer = com.pusher.client.util.HttpAuthorizer("${BuildConfig.API_BASE_URL}broadcasting/auth")
            authorizer.setHeaders(mapOf("Authorization" to "Bearer $token"))
            setAuthorizer(authorizer)
        }

        // Chave do Reverb
        pusher = Pusher("j2ydkwigbkoijgxmpjwd", options)

        pusher?.connect(object : ConnectionEventListener {
            override fun onConnectionStateChange(change: ConnectionStateChange) {
                _uiState.update { it.copy(isConnected = change.currentState == ConnectionState.CONNECTED) }
            }

            override fun onError(message: String?, code: String?, e: Exception?) {
                Log.e("Pusher", "Erro Pusher: $message", e)
            }
        }, ConnectionState.ALL)

        val channelName = "private-conversation.$convId"
        
        try {
            val channel = pusher?.subscribePrivate(channelName)
            channel?.bind("App\\Events\\MessageSent", object : PrivateChannelEventListener {
                override fun onEvent(event: PusherEvent) {
                    try {
                        val json = JSONObject(event.data)
                        // A classe broadcastWith retorna json direto, ou embrulhado em algo
                        // Geralmente MessageSent vem o payload direto ou com o wrapper
                        // Vamos tratar caso venha envelopado (como costuma ser em eventos do Laravel)
                        val messageJson = if (json.has("message")) json.getJSONObject("message") else json

                        val id = messageJson.getInt("id")
                        val content = messageJson.getString("content")
                        val senderId = messageJson.getInt("sender_id")
                        val createdAt = messageJson.getString("created_at")

                        val isMine = false // Se recebemos via Pusher, e a gente não ignora, geralmente é de outro ou tratamos pelo senderId

                        val msgDto = InternalMessageDto(
                            id = id,
                            content = content,
                            isMine = isMine,
                            isRead = false,
                            createdAt = createdAt
                        )

                        _uiState.update { state ->
                            if (state.messages.none { it.id == id }) {
                                state.copy(messages = state.messages + msgDto)
                            } else state
                        }
                    } catch (e: Exception) {
                        Log.e("Pusher", "Falha ao parsear evento MessageSent", e)
                    }
                }

                override fun onSubscriptionSucceeded(channelName: String?) {}
                override fun onAuthenticationFailure(message: String?, e: Exception?) {}
            })
        } catch (e: Exception) {
             Log.e("Pusher", "Subscription failed", e)
        }
    }

    override fun onCleared() {
        super.onCleared()
        pusher?.disconnect()
    }
}
