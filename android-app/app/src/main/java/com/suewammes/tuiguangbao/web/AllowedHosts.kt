package com.suewammes.tuiguangbao.web

import android.net.Uri

object AllowedHosts {
    fun isInternalHttps(uri: Uri): Boolean {
        return uri.scheme.equals("https", ignoreCase = true) &&
            HostPolicy.isInternalHost(uri.host) &&
            uri.userInfo == null
    }

    fun isPaymentHttps(uri: Uri): Boolean {
        return uri.scheme.equals("https", ignoreCase = true) &&
            HostPolicy.isPaymentHost(uri.host) &&
            uri.userInfo == null
    }

    fun isInternalHttps(url: String): Boolean = runCatching {
        isInternalHttps(Uri.parse(url))
    }.getOrDefault(false)

    fun isInternalHost(host: String?): Boolean = HostPolicy.isInternalHost(host)

    fun isTrustedPaymentOrigin(host: String?): Boolean {
        return HostPolicy.isTrustedPaymentOrigin(host)
    }
}
