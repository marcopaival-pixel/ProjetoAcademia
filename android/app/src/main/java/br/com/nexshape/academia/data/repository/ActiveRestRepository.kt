package br.com.nexshape.academia.data.repository

import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ActiveRestLogRequest
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class ActiveRestRepository {
    suspend fun getActiveRest() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().getActiveRest().data }
    }

    suspend fun toggleFavorite(id: Int) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().toggleActiveRestFavorite(id).data }
    }

    suspend fun storeLog(id: Int, durationSpent: Int, feedbackScore: Int?) = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().storeActiveRestLog(id, ActiveRestLogRequest(durationSpent, feedbackScore)).data }
    }
}
