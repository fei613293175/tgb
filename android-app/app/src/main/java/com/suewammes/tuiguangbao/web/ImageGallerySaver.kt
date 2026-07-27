package com.suewammes.tuiguangbao.web

import android.content.ContentValues
import android.content.Context
import android.media.MediaScannerConnection
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import android.util.Base64
import android.webkit.MimeTypeMap
import java.io.File
import java.io.FileOutputStream
import java.io.IOException
import java.io.InputStream
import java.net.HttpURLConnection
import java.net.URI
import java.net.URL
import java.util.Locale
import java.util.concurrent.Executors

class ImageGallerySaver(private val context: Context) {
    data class Request(
        val source: ImageSaveSource,
        val userAgent: String,
        val cookie: String?,
        val referer: String?
    )

    private data class ImagePayload(
        val input: InputStream,
        val mimeType: String,
        val suggestedName: String?
    )

    private val executor = Executors.newSingleThreadExecutor()

    fun save(request: Request, callback: (Result<Unit>) -> Unit) {
        executor.execute {
            val result = runCatching {
                val payload = openPayload(request)
                payload.input.use {
                    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                        saveWithMediaStore(payload)
                    } else {
                        saveLegacy(payload)
                    }
                }
            }
            android.os.Handler(context.mainLooper).post { callback(result) }
        }
    }

    fun close() {
        executor.shutdownNow()
    }

    private fun openPayload(request: Request): ImagePayload = when (val source = request.source) {
        is ImageSaveSource.DataUrl -> {
            val bytes = Base64.decode(source.base64Payload, Base64.DEFAULT)
            require(bytes.size <= MAX_IMAGE_BYTES) { "Image is too large" }
            ImagePayload(bytes.inputStream(), normalizeMime(source.mimeType), null)
        }
        is ImageSaveSource.Https -> openNetworkPayload(source.url, request)
    }

    private fun openNetworkPayload(url: String, request: Request): ImagePayload {
        var currentUrl = url
        val authenticatedHost = URI(url).host
        repeat(MAX_REDIRECTS + 1) { redirectCount ->
            val connection = (URL(currentUrl).openConnection() as HttpURLConnection).apply {
                connectTimeout = CONNECT_TIMEOUT_MS
                readTimeout = READ_TIMEOUT_MS
                instanceFollowRedirects = false
                requestMethod = "GET"
                setRequestProperty("User-Agent", request.userAgent)
                setRequestProperty("Accept", "image/avif,image/webp,image/apng,image/*,*/*;q=0.8")
                request.cookie
                    ?.takeIf { it.isNotBlank() && URI(currentUrl).host == authenticatedHost }
                    ?.let { setRequestProperty("Cookie", it) }
                request.referer?.takeIf {
                    runCatching { URI(it) }.getOrNull()?.let { referer ->
                        referer.scheme.equals("https", ignoreCase = true) &&
                            referer.host == URI(currentUrl).host
                    } == true
                }
                    ?.let { setRequestProperty("Referer", it) }
            }
            val status = connection.responseCode
            if (status in 300..399) {
                val location = connection.getHeaderField("Location")
                    ?: throw IOException("Redirect without location")
                connection.disconnect()
                check(redirectCount < MAX_REDIRECTS) { "Too many redirects" }
                val next = URL(URL(currentUrl), location).toString()
                check(ImageSaveSource.parse(next) is ImageSaveSource.Https) {
                    "Unsafe image redirect"
                }
                currentUrl = next
                return@repeat
            }
            if (status !in 200..299) {
                connection.disconnect()
                throw IOException("Image request failed: HTTP $status")
            }
            val contentLength = connection.contentLength.toLong()
            require(contentLength < 0 || contentLength <= MAX_IMAGE_BYTES) { "Image is too large" }
            val responseMime = connection.contentType?.substringBefore(';')?.trim()?.lowercase(Locale.ROOT)
            require(responseMime?.startsWith("image/") == true) { "Response is not an image" }
            val mimeType = normalizeMime(responseMime)
            val stream = LimitedInputStream(connection.inputStream, MAX_IMAGE_BYTES) {
                connection.disconnect()
            }
            return ImagePayload(
                input = stream,
                mimeType = mimeType,
                suggestedName = Uri.parse(currentUrl).lastPathSegment
            )
        }
        throw IOException("Image redirect failed")
    }

    private fun saveWithMediaStore(payload: ImagePayload) {
        val resolver = context.contentResolver
        val values = ContentValues().apply {
            put(MediaStore.Images.Media.DISPLAY_NAME, fileName(payload))
            put(MediaStore.Images.Media.MIME_TYPE, payload.mimeType)
            put(MediaStore.Images.Media.RELATIVE_PATH, "${Environment.DIRECTORY_PICTURES}/推广宝")
            put(MediaStore.Images.Media.IS_PENDING, 1)
        }
        val uri = resolver.insert(MediaStore.Images.Media.EXTERNAL_CONTENT_URI, values)
            ?: throw IOException("Unable to create gallery item")
        try {
            resolver.openOutputStream(uri, "w")?.use { output ->
                payload.input.copyTo(output)
            } ?: throw IOException("Unable to open gallery item")
            values.clear()
            values.put(MediaStore.Images.Media.IS_PENDING, 0)
            resolver.update(uri, values, null, null)
        } catch (error: Throwable) {
            resolver.delete(uri, null, null)
            throw error
        }
    }

    @Suppress("DEPRECATION")
    private fun saveLegacy(payload: ImagePayload) {
        val pictures = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_PICTURES)
        val directory = File(pictures, "推广宝")
        check(directory.exists() || directory.mkdirs()) { "Unable to create gallery directory" }
        val outputFile = uniqueFile(directory, fileName(payload))
        try {
            FileOutputStream(outputFile).use { payload.input.copyTo(it) }
            MediaScannerConnection.scanFile(
                context,
                arrayOf(outputFile.absolutePath),
                arrayOf(payload.mimeType),
                null
            )
        } catch (error: Throwable) {
            outputFile.delete()
            throw error
        }
    }

    private fun fileName(payload: ImagePayload): String {
        val extension = MimeTypeMap.getSingleton().getExtensionFromMimeType(payload.mimeType)
            ?.lowercase(Locale.ROOT)
            ?: "jpg"
        val suggestedBase = payload.suggestedName
            ?.substringBeforeLast('.')
            ?.replace(Regex("[^a-zA-Z0-9_-]"), "_")
            ?.take(40)
            ?.takeIf { it.isNotBlank() }
            ?: "tuiguangbao"
        return "${suggestedBase}_${System.currentTimeMillis()}.$extension"
    }

    private fun uniqueFile(directory: File, preferredName: String): File {
        var candidate = File(directory, preferredName)
        var suffix = 1
        while (candidate.exists()) {
            candidate = File(directory, "${preferredName.substringBeforeLast('.')}($suffix).${preferredName.substringAfterLast('.')}")
            suffix++
        }
        return candidate
    }

    private fun normalizeMime(value: String?): String {
        val mime = value?.trim()?.lowercase(Locale.ROOT).orEmpty()
        return if (mime.startsWith("image/")) mime else "image/jpeg"
    }

    private class LimitedInputStream(
        delegate: InputStream,
        private val maxBytes: Long,
        private val onClose: () -> Unit
    ) : java.io.FilterInputStream(delegate) {
        private var bytesRead = 0L

        override fun read(): Int {
            val value = super.read()
            if (value >= 0) checkLimit(1)
            return value
        }

        override fun read(buffer: ByteArray, offset: Int, length: Int): Int {
            val count = super.read(buffer, offset, length)
            if (count > 0) checkLimit(count.toLong())
            return count
        }

        private fun checkLimit(count: Long) {
            bytesRead += count
            if (bytesRead > maxBytes) throw IOException("Image is too large")
        }

        override fun close() {
            try {
                super.close()
            } finally {
                onClose()
            }
        }
    }

    private companion object {
        const val CONNECT_TIMEOUT_MS = 15_000
        const val READ_TIMEOUT_MS = 30_000
        const val MAX_REDIRECTS = 5
        const val MAX_IMAGE_BYTES = 25L * 1024L * 1024L
    }
}
