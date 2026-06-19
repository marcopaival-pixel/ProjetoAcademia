package br.com.nexshape.academia.observability

import android.util.Log
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ClientErrorRequest
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.SupervisorJob
import kotlinx.coroutines.launch

object ClientErrorReporter {
    private const val TAG = "ClientErrorReporter"
    private val scope = CoroutineScope(SupervisorJob() + Dispatchers.IO)

    fun install(defaultHandler: Thread.UncaughtExceptionHandler?) {
        Thread.setDefaultUncaughtExceptionHandler { thread, throwable ->
            report(
                type = "uncaught",
                message = throwable.message ?: throwable.javaClass.simpleName,
                stack = throwable.stackTraceToString(),
                url = "thread:${thread.name}",
            )
            defaultHandler?.uncaughtException(thread, throwable)
        }
    }

    fun report(
        type: String = "error",
        message: String,
        stack: String? = null,
        url: String? = null,
    ) {
        if (message.isBlank()) return

        scope.launch {
            runCatching {
                ApiClient.api().reportClientError(
                    ClientErrorRequest(
                        type = type,
                        message = message.take(2000),
                        stack = stack?.take(10000),
                        url = url?.take(500),
                    ),
                )
            }.onFailure {
                Log.w(TAG, "Failed to report client error", it)
            }
        }
    }
}
