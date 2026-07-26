package com.suewammes.tuiguangbao.web

import android.content.ActivityNotFoundException
import android.content.Context
import android.content.Intent
import android.net.Uri

class ExternalIntentRouter(
    private val context: Context,
    private val onExternalAppStarted: () -> Unit,
    private val onBlocked: () -> Unit,
    private val onAlipayUnavailable: () -> Unit
) {
    private val alipaySchemes = setOf("alipays", "alipay")
    private val alipayHosts = setOf("platformapi")
    private val alipayPackage = "com.eg.android.AlipayGphone"

    fun route(uri: Uri, currentPage: Uri?, hasUserGesture: Boolean): Boolean {
        if (AllowedHosts.isInternalHttps(uri)) return false

        if (AllowedHosts.isPaymentHttps(uri)) {
            if (AllowedHosts.isTrustedPaymentOrigin(currentPage?.host)) {
                // Keep approved H5 cashier pages inside this WebView so their
                // later Alipay deep link is still intercepted by this router.
                return false
            }
            onBlocked()
            return true
        }

        if (uri.scheme.equals("https", ignoreCase = true)) {
            return openExternalHttps(uri, hasUserGesture)
        }

        val paymentUri = when {
            uri.scheme.equals("intent", ignoreCase = true) -> parseApprovedIntent(uri)
            uri.scheme?.lowercase() in alipaySchemes -> uri
            else -> null
        }

        if (
            paymentUri == null ||
            !AllowedHosts.isTrustedPaymentOrigin(currentPage?.host) ||
            paymentUri.scheme?.lowercase() !in alipaySchemes ||
            paymentUri.host?.lowercase() !in alipayHosts
        ) {
            onBlocked()
            return true
        }

        val safeIntent = Intent(Intent.ACTION_VIEW, paymentUri).apply {
            addCategory(Intent.CATEGORY_BROWSABLE)
            setPackage(alipayPackage)
        }
        return try {
            context.startActivity(safeIntent)
            onExternalAppStarted()
            true
        } catch (_: ActivityNotFoundException) {
            onAlipayUnavailable()
            true
        } catch (_: SecurityException) {
            onBlocked()
            true
        }
    }

    private fun parseApprovedIntent(uri: Uri): Uri? = runCatching {
        val parsed = Intent.parseUri(uri.toString(), Intent.URI_INTENT_SCHEME)
        if (parsed.component != null || parsed.selector != null) return@runCatching null
        if (parsed.`package` != null && parsed.`package` != alipayPackage) return@runCatching null
        parsed.data
    }.getOrNull()

    private fun openExternalHttps(uri: Uri, hasUserGesture: Boolean): Boolean {
        if (!hasUserGesture || uri.userInfo != null) {
            onBlocked()
            return true
        }
        val safeIntent = Intent(Intent.ACTION_VIEW, uri).apply {
            addCategory(Intent.CATEGORY_BROWSABLE)
        }
        return try {
            context.startActivity(safeIntent)
            true
        } catch (_: ActivityNotFoundException) {
            onBlocked()
            true
        } catch (_: SecurityException) {
            onBlocked()
            true
        }
    }
}
