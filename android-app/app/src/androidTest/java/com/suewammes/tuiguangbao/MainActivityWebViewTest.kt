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
    fun nativeRootOwnsSystemBarInsetsWithoutDoublePaddingTheWebView() {
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
                assertEquals(bars.left, root.paddingLeft)
                assertEquals(bars.top, root.paddingTop)
                assertEquals(bars.right, root.paddingRight)
                assertEquals(bars.bottom, root.paddingBottom)
                assertEquals("WebView must not add a second top inset", 0, webView!!.paddingTop)
                assertEquals("WebView must not add a second bottom inset", 0, webView.paddingBottom)
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
