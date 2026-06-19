package br.com.nexshape.academia.sync

import android.content.Context
import androidx.work.CoroutineWorker
import androidx.work.WorkerParameters
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.repository.OfflineSyncRepository

class OfflineSyncWorker(
    appContext: Context,
    params: WorkerParameters,
) : CoroutineWorker(appContext, params) {

    override suspend fun doWork(): Result {
        return runCatching {
            ApiClient.init(applicationContext)
            if (!ApiClient.tokenStore().isLoggedIn()) {
                return Result.success()
            }
            OfflineSyncRepository(applicationContext).flush(applicationContext)
        }.fold(
            onSuccess = { Result.success() },
            onFailure = { Result.retry() },
        )
    }
}
