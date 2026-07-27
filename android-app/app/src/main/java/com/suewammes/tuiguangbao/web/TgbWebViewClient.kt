package com.suewammes.tuiguangbao.web

import android.net.Uri
import android.net.http.SslError
import android.os.Build
import android.webkit.SafeBrowsingResponse
import android.webkit.SslErrorHandler
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.annotation.RequiresApi

class TgbWebViewClient(
    private val router: ExternalIntentRouter,
    private val onMainFrameError: () -> Unit,
    private val onMainFrameVisible: () -> Unit
) : WebViewClient() {

    override fun onPageCommitVisible(view: WebView, url: String) {
        onMainFrameVisible()
    }

    override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
        return router.route(
            uri = request.url,
            currentPage = view.url?.let(Uri::parse),
            hasUserGesture = request.hasGesture()
        )
    }

    @Deprecated("Legacy callback for old WebView implementations")
    override fun shouldOverrideUrlLoading(view: WebView, url: String): Boolean {
        return router.route(
            uri = Uri.parse(url),
            currentPage = view.url?.let(Uri::parse),
            hasUserGesture = false
        )
    }

    override fun onReceivedError(
        view: WebView,
        request: WebResourceRequest,
        error: WebResourceError
    ) {
        if (request.isForMainFrame) {
            onMainFrameError()
        }
    }

    override fun onReceivedSslError(
        view: WebView,
        handler: SslErrorHandler,
        error: SslError
    ) {
        handler.cancel()
        onMainFrameError()
    }

    @RequiresApi(Build.VERSION_CODES.O_MR1)
    override fun onSafeBrowsingHit(
        view: WebView,
        request: WebResourceRequest,
        threatType: Int,
        callback: SafeBrowsingResponse
    ) {
        callback.backToSafety(true)
        onMainFrameError()
    }
}
