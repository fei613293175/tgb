package com.suewammes.tuiguangbao.web

import android.net.Uri
import android.os.Message
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebView
import android.webkit.WebViewClient

class TgbChromeClient(
    private val fileChooser: FileChooserCoordinator,
    private val onNewWindow: (Uri, Boolean) -> Unit
) : WebChromeClient() {

    override fun onShowFileChooser(
        webView: WebView,
        filePathCallback: ValueCallback<Array<Uri>>,
        fileChooserParams: FileChooserParams
    ): Boolean = fileChooser.launch(filePathCallback, fileChooserParams)

    override fun onCreateWindow(
        view: WebView,
        isDialog: Boolean,
        isUserGesture: Boolean,
        resultMsg: Message
    ): Boolean {
        val child = WebView(view.context)
        child.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(
                childView: WebView,
                request: android.webkit.WebResourceRequest
            ): Boolean {
                onNewWindow(request.url, isUserGesture || request.hasGesture())
                childView.destroy()
                return true
            }
        }
        val transport = resultMsg.obj as? WebView.WebViewTransport ?: return false
        transport.webView = child
        resultMsg.sendToTarget()
        return true
    }

    fun cancelPendingFileChooser() = fileChooser.cancel()
}
