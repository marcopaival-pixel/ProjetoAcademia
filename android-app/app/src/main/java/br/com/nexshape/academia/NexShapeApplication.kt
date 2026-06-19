package br.com.nexshape.academia

import android.app.Application
import androidx.work.Constraints
import androidx.work.ExistingPeriodicWorkPolicy
import androidx.work.NetworkType
import androidx.work.PeriodicWorkRequestBuilder
import androidx.work.WorkManager
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.repository.OfflineSyncRepository
import br.com.nexshape.academia.observability.ClientErrorReporter
import br.com.nexshape.academia.sync.OfflineSyncWorker
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.SupervisorJob
import kotlinx.coroutines.launch
import java.util.concurrent.TimeUnit

class NexShapeApplication : Application() {
    private val appScope = CoroutineScope(SupervisorJob() + Dispatchers.IO)

    override fun onCreate() {
        super.onCreate()
        ApiClient.init(this)
        ClientErrorReporter.install(Thread.getDefaultUncaughtExceptionHandler())
        scheduleOfflineSync()
        appScope.launch {
            runCatching { OfflineSyncRepository(this@NexShapeApplication).flush(this@NexShapeApplication) }
        }
    }

    private fun scheduleOfflineSync() {
        val request = PeriodicWorkRequestBuilder<OfflineSyncWorker>(15, TimeUnit.MINUTES)
            .setConstraints(
                Constraints.Builder()
                    .setRequiredNetworkType(NetworkType.CONNECTED)
                    .build(),
            )
            .build()

        WorkManager.getInstance(this).enqueueUniquePeriodicWork(
            "nexshape_offline_sync",
            ExistingPeriodicWorkPolicy.KEEP,
            request,
        )
    }
}
