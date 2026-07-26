package com.suewammes.tuiguangbao.web

import android.net.Uri
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import androidx.activity.ComponentActivity
import androidx.activity.result.PickVisualMediaRequest
import androidx.activity.result.contract.ActivityResultContracts

class FileChooserCoordinator(activity: ComponentActivity) {
    private var callback: ValueCallback<Array<Uri>>? = null

    private val pickSingle = activity.registerForActivityResult(
        ActivityResultContracts.PickVisualMedia()
    ) { uri ->
        finish(uri?.let { arrayOf(it) })
    }

    private val pickMultiple = activity.registerForActivityResult(
        ActivityResultContracts.PickMultipleVisualMedia(20)
    ) { uris ->
        finish(uris.takeIf { it.isNotEmpty() }?.toTypedArray())
    }

    private val openSingle = activity.registerForActivityResult(
        ActivityResultContracts.OpenDocument()
    ) { uri ->
        finish(uri?.let { arrayOf(it) })
    }

    private val openMultiple = activity.registerForActivityResult(
        ActivityResultContracts.OpenMultipleDocuments()
    ) { uris ->
        finish(uris.takeIf { it.isNotEmpty() }?.toTypedArray())
    }

    fun launch(
        filePathCallback: ValueCallback<Array<Uri>>,
        params: WebChromeClient.FileChooserParams
    ): Boolean {
        callback?.onReceiveValue(null)
        callback = filePathCallback

        val accepted = params.acceptTypes
            .map { it.trim().lowercase() }
            .filter { it.isNotEmpty() }
            .ifEmpty { listOf("*/*") }
        val multiple = params.mode == WebChromeClient.FileChooserParams.MODE_OPEN_MULTIPLE
        val imagesOnly = accepted.all { it == "image/*" || it.startsWith("image/") }

        return runCatching {
            if (imagesOnly) {
                val request = PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly)
                if (multiple) pickMultiple.launch(request) else pickSingle.launch(request)
            } else {
                val mimeTypes = accepted.toTypedArray()
                if (multiple) openMultiple.launch(mimeTypes) else openSingle.launch(mimeTypes)
            }
            true
        }.getOrElse {
            finish(null)
            false
        }
    }

    fun cancel() = finish(null)

    private fun finish(uris: Array<Uri>?) {
        callback?.onReceiveValue(uris)
        callback = null
    }
}
