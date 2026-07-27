package com.suewammes.tuiguangbao

import android.view.View
import android.view.ViewGroup
import android.webkit.WebSettings
import android.webkit.WebView
import androidx.core.view.ViewCompat
import androidx.core.view.WindowInsetsCompat
import androidx.test.core.app.ActivityScenario
import androidx.test.ext.junit.runners.AndroidJUnit4
import org.junit.Assert.assertEquals
import org.junit.Assert.assertFalse
import org.junit.Assert.assertNotNull
import org.junit.Assert.assertTrue
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class MainActivityWebViewTest {
    @Test
    fun webViewExtendsBehindSystemBarsWithoutNativeInsetPadding() {
        ActivityScenario.launch(MainActivity::class.java).use { scenario ->
            scenario.onActivity { activity ->
                val content = activity.findViewById<ViewGroup>(android.R.id.content)
                val root = content.getChildAt(0)
                val webView = findWebView(root)
                val windowInsets = ViewCompat.getRootWindowInsets(root)

                assertNotNull("root window insets must be available", windowInsets)
                assertNotNull("MainActivity must contain its H5 WebView", webView)

                val bars = windowInsets!!.getInsets(
                    WindowInsetsCompat.Type.statusBars() or
                        WindowInsetsCompat.Type.navigationBars()
                )
                assertTrue("status bar inset must be non-zero", bars.top > 0)
                assertEquals("native root must not duplicate the H5 left inset", 0, root.paddingLeft)
                assertEquals("native root must not duplicate the H5 top inset", 0, root.paddingTop)
                assertEquals("native root must not duplicate the H5 right inset", 0, root.paddingRight)
                assertEquals("native root must not duplicate the H5 bottom inset", 0, root.paddingBottom)
                assertEquals("WebView must not add a native top inset", 0, webView!!.paddingTop)
                assertEquals("WebView must not add a native bottom inset", 0, webView.paddingBottom)
            }
        }
    }

    @Test
    fun webViewAllowsScopedContentUrisButNeverRawFileAccess() {
        ActivityScenario.launch(MainActivity::class.java).use { scenario ->
            scenario.onActivity { activity ->
                val webView = findWebView(activity.window.decorView)
                assertNotNull("MainActivity must contain its H5 WebView", webView)
                assertTrue(
                    "Photo Picker/SAF content URIs must remain readable for H5 upload",
                    webView!!.settings.allowContentAccess
                )
                assertFalse(
                    "Raw file:// access must remain disabled",
                    webView.settings.allowFileAccess
                )
                assertTrue(
                    "WebView must expose the native long-press image action",
                    webView.hasOnLongClickListeners()
                )
                assertEquals(
                    WebSettings.MIXED_CONTENT_NEVER_ALLOW,
                    webView.settings.mixedContentMode
                )
            }
        }
    }

    private fun findWebView(view: View): WebView? {
        if (view is WebView) return view
        if (view !is ViewGroup) return null
        for (index in 0 until view.childCount) {
            findWebView(view.getChildAt(index))?.let { return it }
        }
        return null
    }
}
