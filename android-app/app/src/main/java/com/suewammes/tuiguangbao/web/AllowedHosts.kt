package com.suewammes.tuiguangbao.web

import android.net.Uri

object AllowedHosts {
    private val internalHosts = setOf("tg.suewammes.com")

    fun isInternalHttps(uri: Uri): Boolean {
        return uri.scheme.equals("https", ignoreCase = true) &&
            uri.host?.lowercase() in internalHosts &&
            uri.userInfo == null
    }

    fun isInternalHttps(url: String): Boolean = runCatching {
        isInternalHttps(Uri.parse(url))
    }.getOrDefault(false)

    fun isInternalHost(host: String?): Boolean = host?.lowercase() in internalHosts
}
