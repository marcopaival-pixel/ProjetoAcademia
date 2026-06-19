package br.com.nexshape.academia.data.media

import android.content.Context
import br.com.nexshape.academia.data.api.ApiClient
import coil.ImageLoader
import coil.util.CoilUtils

object AuthenticatedImageLoader {
    @Volatile
    private var instance: ImageLoader? = null

    fun get(context: Context): ImageLoader {
        return instance ?: synchronized(this) {
            instance ?: ImageLoader.Builder(context.applicationContext)
                .okHttpClient(ApiClient.authenticatedClient())
                .crossfade(true)
                .build()
                .also { instance = it }
        }
    }

    fun shutdown() {
        instance?.let { CoilUtils.shutdown(it) }
        instance = null
    }
}
