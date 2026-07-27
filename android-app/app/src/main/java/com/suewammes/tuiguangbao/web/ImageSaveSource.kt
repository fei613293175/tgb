package com.suewammes.tuiguangbao.web

import java.net.URI

sealed interface ImageSaveSource {
    data class Https(val url: String) : ImageSaveSource
    data class DataUrl(val mimeType: String, val base64Payload: String) : ImageSaveSource

    companion object {
        private val dataUrlPattern = Regex(
            "^data:(image/[a-zA-Z0-9.+-]+);base64,([a-zA-Z0-9+/=\\r\\n]+)$",
            RegexOption.IGNORE_CASE
        )

        fun parse(raw: String?): ImageSaveSource? {
            val value = raw?.trim()?.takeIf { it.isNotEmpty() } ?: return null
            dataUrlPattern.matchEntire(value)?.let { match ->
                if (match.groupValues[2].length > MAX_BASE64_CHARACTERS) return null
                return DataUrl(
                    mimeType = match.groupValues[1].lowercase(),
                    base64Payload = match.groupValues[2]
                )
            }

            val uri = runCatching { URI(value) }.getOrNull() ?: return null
            return if (
                uri.scheme.equals("https", ignoreCase = true) &&
                !uri.host.isNullOrBlank() &&
                uri.userInfo == null
            ) {
                Https(value)
            } else {
                null
            }
        }

        private const val MAX_BASE64_CHARACTERS = 35_000_000
    }
}
