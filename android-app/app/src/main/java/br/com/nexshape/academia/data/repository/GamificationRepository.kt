package br.com.nexshape.academia.data.repository

import br.com.nexshape.academia.data.api.ApiClient
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext

class GamificationRepository {
    suspend fun gamification() = withContext(Dispatchers.IO) {
        runCatching { ApiClient.api().studentGamification().data }
    }
}
