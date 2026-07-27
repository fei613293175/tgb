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

    fun route(uri: Uri, currentPage: Uri?, hasUserGesture: Boolean): Boolean {
        val scheme = uri.scheme?.lowercase()

        if (scheme == "https") {
            // HTTPS navigation stays in the WebView, including third-party
            // cashiers and their redirect chain.
            return false
        }
        if (scheme == "intent") {
            return openSanitizedIntent(uri)
        }
        if (!ExternalNavigationPolicy.isExternalAppScheme(scheme)) {
            onBlocked()
            return true
        }

        val intent = Intent(Intent.ACTION_VIEW, uri).apply {
            addCategory(Intent.CATEGORY_BROWSABLE)
        }
        return startExternal(intent, scheme in alipaySchemes)
    }

    private fun openSanitizedIntent(uri: Uri): Boolean {
        val intent = runCatching {
            Intent.parseUri(uri.toString(), Intent.URI_INTENT_SCHEME).apply {
                action = Intent.ACTION_VIEW
                component = null
                selector = null
                clipData = null
                flags = 0
                replaceExtras(android.os.Bundle())
                categories?.toList()?.forEach(::removeCategory)
                addCategory(Intent.CATEGORY_BROWSABLE)
            }
        }.getOrNull()

        val targetScheme = intent?.data?.scheme?.lowercase()
        if (intent == null || !ExternalNavigationPolicy.isExternalAppScheme(targetScheme)) {
            onBlocked()
            return true
        }
        return startExternal(intent, targetScheme in alipaySchemes)
    }

    private fun startExternal(intent: Intent, isAlipay: Boolean): Boolean {
        return try {
            context.startActivity(intent)
            onExternalAppStarted()
            true
        } catch (_: ActivityNotFoundException) {
            if (isAlipay) onAlipayUnavailable() else onBlocked()
            true
        } catch (_: SecurityException) {
            onBlocked()
            true
        }
    }
}
