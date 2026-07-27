package com.suewammes.tuiguangbao

import android.Manifest
import android.annotation.SuppressLint
import android.app.AlertDialog
import android.app.DownloadManager
import android.content.Context
import android.content.pm.PackageManager
import android.graphics.Color
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.SystemClock
import android.view.Gravity
import android.view.View
import android.view.ViewGroup
import android.webkit.CookieManager
import android.webkit.URLUtil
import android.webkit.WebSettings
import android.webkit.WebView
import android.widget.Button
import android.widget.FrameLayout
import android.widget.ImageView
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.core.content.ContextCompat
import androidx.core.content.res.ResourcesCompat
import androidx.core.splashscreen.SplashScreen.Companion.installSplashScreen
import androidx.core.view.WindowCompat
import androidx.core.view.WindowInsetsControllerCompat
import androidx.webkit.WebSettingsCompat
import androidx.webkit.WebViewFeature
import com.suewammes.tuiguangbao.web.AllowedHosts
import com.suewammes.tuiguangbao.web.ExternalIntentRouter
import com.suewammes.tuiguangbao.web.FileChooserCoordinator
import com.suewammes.tuiguangbao.web.ImageGallerySaver
import com.suewammes.tuiguangbao.web.ImageSaveSource
import com.suewammes.tuiguangbao.web.TgbChromeClient
import com.suewammes.tuiguangbao.web.TgbWebViewClient

class MainActivity : ComponentActivity() {
    private lateinit var root: FrameLayout
    private lateinit var webView: WebView
    private lateinit var errorPanel: View
    private lateinit var startupOverlay: View
    private lateinit var startupGate: StartupTransitionGate
    private lateinit var chromeClient: TgbChromeClient
    private lateinit var router: ExternalIntentRouter
    private lateinit var imageGallerySaver: ImageGallerySaver
    private var pendingImageRequest: ImageGallerySaver.Request? = null
    private var refreshAfterExternalApp = false
    private val storagePermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { granted ->
        val request = pendingImageRequest.also { pendingImageRequest = null }
        if (granted && request != null) {
            saveImage(request)
        } else if (!granted) {
            toast(R.string.storage_permission_denied)
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        installSplashScreen()
        super.onCreate(savedInstanceState)
        startupGate = StartupTransitionGate(SystemClock.elapsedRealtime())
        configureSystemBars()
        createContent()
        configureWebView()
        configureBackNavigation()

        if (savedInstanceState == null || webView.restoreState(savedInstanceState) == null) {
            webView.loadUrl(BuildConfig.START_URL)
        }
    }

    private fun configureSystemBars() {
        WindowCompat.setDecorFitsSystemWindows(window, false)
        window.statusBarColor = getColor(R.color.tgb_background)
        window.navigationBarColor = getColor(R.color.tgb_surface)
        WindowInsetsControllerCompat(window, window.decorView).apply {
            isAppearanceLightStatusBars = true
            isAppearanceLightNavigationBars = true
        }
    }

    private fun createContent() {
        root = FrameLayout(this).apply {
            setBackgroundColor(getColor(R.color.tgb_background))
        }
        webView = WebView(this).apply {
            setBackgroundColor(getColor(R.color.tgb_background))
        }
        root.addView(
            webView,
            FrameLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT
            )
        )

        errorPanel = buildErrorPanel().also {
            it.visibility = View.GONE
            root.addView(
                it,
                FrameLayout.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT,
                    ViewGroup.LayoutParams.MATCH_PARENT
                )
            )
        }
        startupOverlay = buildStartupOverlay().also {
            root.addView(
                it,
                FrameLayout.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT,
                    ViewGroup.LayoutParams.MATCH_PARENT
                )
            )
        }
        setContentView(root)
    }

    private fun buildStartupOverlay(): View {
        val typeface = ResourcesCompat.getFont(this, R.font.noto_sans_sc_regular)
        return LinearLayout(this).apply {
            id = R.id.startup_transition
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER
            isClickable = true
            isFocusable = true
            setPadding(dp(32), dp(32), dp(32), dp(32))
            setBackgroundColor(getColor(R.color.tgb_background))

            addView(ImageView(context).apply {
                setImageResource(R.drawable.tuiguangbao_brand_mark)
                scaleType = ImageView.ScaleType.CENTER_INSIDE
                contentDescription = getString(R.string.app_name)
            }, LinearLayout.LayoutParams(dp(112), dp(112)))

            addView(TextView(context).apply {
                text = getString(R.string.app_name)
                textSize = 28f
                setTextColor(getColor(R.color.tgb_text))
                this.typeface = typeface
                gravity = Gravity.CENTER
                setPadding(0, dp(18), 0, 0)
            })

            addView(TextView(context).apply {
                text = getString(R.string.startup_tagline)
                textSize = 15f
                setTextColor(getColor(R.color.tgb_muted))
                this.typeface = typeface
                gravity = Gravity.CENTER
                setPadding(0, dp(8), 0, dp(24))
            })

            addView(ProgressBar(context).apply {
                id = R.id.startup_loading_indicator
                isIndeterminate = true
                indeterminateTintList = getColorStateList(R.color.tgb_primary)
                contentDescription = getString(R.string.startup_loading)
            }, LinearLayout.LayoutParams(dp(30), dp(30)))

            addView(TextView(context).apply {
                text = getString(R.string.startup_loading)
                textSize = 14f
                setTextColor(getColor(R.color.tgb_muted))
                this.typeface = typeface
                gravity = Gravity.CENTER
                setPadding(0, dp(10), 0, 0)
            })
        }
    }

    private fun buildErrorPanel(): View {
        val typeface = ResourcesCompat.getFont(this, R.font.noto_sans_sc_regular)
        return LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER
            setPadding(dp(32), dp(32), dp(32), dp(32))
            setBackgroundColor(getColor(R.color.tgb_background))

            addView(TextView(context).apply {
                text = getString(R.string.offline_title)
                textSize = 22f
                setTextColor(getColor(R.color.tgb_text))
                this.typeface = typeface
                gravity = Gravity.CENTER
            })
            addView(TextView(context).apply {
                text = getString(R.string.offline_message)
                textSize = 16f
                setTextColor(getColor(R.color.tgb_muted))
                this.typeface = typeface
                gravity = Gravity.CENTER
                setPadding(0, dp(12), 0, dp(20))
            })
            addView(Button(context).apply {
                text = getString(R.string.retry)
                textSize = 16f
                minWidth = dp(144)
                minHeight = dp(48)
                setTextColor(Color.WHITE)
                backgroundTintList = getColorStateList(R.color.tgb_primary)
                this.typeface = typeface
                setOnClickListener {
                    errorPanel.visibility = View.GONE
                    webView.reload()
                }
            })
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun configureWebView() {
        WebView.setWebContentsDebuggingEnabled(BuildConfig.DEBUG)
        CookieManager.getInstance().apply {
            setAcceptCookie(true)
            setAcceptThirdPartyCookies(webView, false)
        }
        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            allowFileAccess = false
            // Photo Picker/SAF returns a scoped content:// URI. WebView must be
            // able to read that granted URI in order to stream an H5 upload.
            allowContentAccess = true
            mixedContentMode = WebSettings.MIXED_CONTENT_NEVER_ALLOW
            javaScriptCanOpenWindowsAutomatically = false
            setSupportMultipleWindows(true)
            builtInZoomControls = false
            displayZoomControls = false
            mediaPlaybackRequiresUserGesture = true
            userAgentString = "$userAgentString ${BuildConfig.USER_AGENT_SUFFIX}"
        }
        if (WebViewFeature.isFeatureSupported(WebViewFeature.SAFE_BROWSING_ENABLE)) {
            WebSettingsCompat.setSafeBrowsingEnabled(webView.settings, true)
        }

        router = ExternalIntentRouter(
            context = this,
            onExternalAppStarted = { refreshAfterExternalApp = true },
            onBlocked = { toast(R.string.blocked_link) },
            onAlipayUnavailable = { toast(R.string.alipay_unavailable) }
        )
        val fileChooser = FileChooserCoordinator(this)
        chromeClient = TgbChromeClient(
            fileChooser = fileChooser,
            onNewWindow = { uri, hasGesture ->
                if (uri.scheme.equals("https", ignoreCase = true)) {
                    webView.loadUrl(uri.toString())
                } else {
                    router.route(uri, webView.url?.let(Uri::parse), hasGesture)
                }
            }
        )
        webView.webChromeClient = chromeClient
        webView.webViewClient = TgbWebViewClient(
            router = router,
            onMainFrameError = {
                errorPanel.visibility = View.VISIBLE
                markStartupDestinationReady()
            },
            onMainFrameVisible = ::markStartupDestinationReady
        )
        webView.setDownloadListener { url, userAgent, contentDisposition, mimeType, _ ->
            enqueueTrustedDownload(url, userAgent, contentDisposition, mimeType)
        }
        imageGallerySaver = ImageGallerySaver(applicationContext)
        webView.setOnLongClickListener { view ->
            val hit = (view as WebView).hitTestResult
            if (
                hit.type != WebView.HitTestResult.IMAGE_TYPE &&
                hit.type != WebView.HitTestResult.SRC_IMAGE_ANCHOR_TYPE
            ) {
                return@setOnLongClickListener false
            }
            val source = ImageSaveSource.parse(hit.extra) ?: return@setOnLongClickListener false
            showImageActions(source)
            true
        }
    }

    private fun markStartupDestinationReady() {
        if (!::startupOverlay.isInitialized || startupOverlay.visibility != View.VISIBLE) return
        val delay = startupGate.markDestinationReady(SystemClock.elapsedRealtime())
        startupOverlay.removeCallbacks(dismissStartupOverlay)
        startupOverlay.postDelayed(dismissStartupOverlay, delay)
    }

    private val dismissStartupOverlay = Runnable {
        if (!::startupOverlay.isInitialized || startupOverlay.visibility != View.VISIBLE) {
            return@Runnable
        }
        startupOverlay.animate()
            .alpha(0f)
            .setDuration(280L)
            .withEndAction {
                startupOverlay.visibility = View.GONE
                if (::root.isInitialized) root.removeView(startupOverlay)
            }
            .start()
    }

    private fun showImageActions(source: ImageSaveSource) {
        AlertDialog.Builder(this)
            .setTitle(R.string.image_action_title)
            .setItems(arrayOf(getString(R.string.save_to_gallery))) { _, _ ->
                val sourceUrl = (source as? ImageSaveSource.Https)?.url
                val request = ImageGallerySaver.Request(
                    source = source,
                    userAgent = webView.settings.userAgentString,
                    cookie = sourceUrl?.let { CookieManager.getInstance().getCookie(it) },
                    referer = webView.url
                )
                saveImageWithPermission(request)
            }
            .setNegativeButton(R.string.cancel, null)
            .show()
    }

    private fun saveImageWithPermission(request: ImageGallerySaver.Request) {
        if (
            Build.VERSION.SDK_INT <= Build.VERSION_CODES.P &&
            ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE) !=
            PackageManager.PERMISSION_GRANTED
        ) {
            pendingImageRequest = request
            storagePermissionLauncher.launch(Manifest.permission.WRITE_EXTERNAL_STORAGE)
            return
        }
        saveImage(request)
    }

    private fun saveImage(request: ImageGallerySaver.Request) {
        imageGallerySaver.save(request) { result ->
            toast(if (result.isSuccess) R.string.image_saved else R.string.image_save_failed)
        }
    }

    private fun enqueueTrustedDownload(
        url: String,
        userAgent: String?,
        contentDisposition: String?,
        mimeType: String?
    ) {
        if (!AllowedHosts.isInternalHttps(url)) {
            toast(R.string.download_failed)
            return
        }
        runCatching {
            val request = DownloadManager.Request(Uri.parse(url)).apply {
                setMimeType(mimeType)
                setTitle(URLUtil.guessFileName(url, contentDisposition, mimeType))
                setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                userAgent?.takeIf { it.isNotBlank() }?.let { addRequestHeader("User-Agent", it) }
                CookieManager.getInstance().getCookie(url)
                    ?.takeIf { it.isNotBlank() }
                    ?.let { addRequestHeader("Cookie", it) }
            }
            (getSystemService(Context.DOWNLOAD_SERVICE) as DownloadManager).enqueue(request)
        }.onSuccess {
            toast(R.string.download_started)
        }.onFailure {
            toast(R.string.download_failed)
        }
    }

    private fun configureBackNavigation() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                when {
                    errorPanel.visibility == View.VISIBLE -> {
                        errorPanel.visibility = View.GONE
                        webView.loadUrl(BuildConfig.START_URL)
                    }
                    webView.canGoBack() -> webView.goBack()
                    else -> showExitConfirmation()
                }
            }
        })
    }

    private fun showExitConfirmation() {
        AlertDialog.Builder(this)
            .setTitle(R.string.exit_title)
            .setMessage(R.string.exit_message)
            .setNegativeButton(R.string.cancel, null)
            .setPositiveButton(R.string.exit) { _, _ -> finish() }
            .show()
    }

    override fun onResume() {
        super.onResume()
        if (refreshAfterExternalApp && ::webView.isInitialized) {
            refreshAfterExternalApp = false
            webView.postDelayed({ webView.reload() }, 500)
        }
    }

    override fun onSaveInstanceState(outState: Bundle) {
        webView.saveState(outState)
        super.onSaveInstanceState(outState)
    }

    override fun onDestroy() {
        if (::chromeClient.isInitialized) chromeClient.cancelPendingFileChooser()
        if (::imageGallerySaver.isInitialized) imageGallerySaver.close()
        if (::startupOverlay.isInitialized) {
            startupOverlay.removeCallbacks(dismissStartupOverlay)
            startupOverlay.animate().cancel()
        }
        if (::webView.isInitialized) {
            webView.stopLoading()
            webView.webChromeClient = null
            webView.removeAllViews()
            webView.destroy()
        }
        super.onDestroy()
    }

    private fun toast(message: Int) = Toast.makeText(this, message, Toast.LENGTH_LONG).show()
    private fun dp(value: Int): Int = (value * resources.displayMetrics.density).toInt()
}
