package com.suewammes.tuiguangbao

import com.suewammes.tuiguangbao.web.ExternalNavigationPolicy
import org.junit.Assert.assertFalse
import org.junit.Assert.assertTrue
import org.junit.Test

class ExternalNavigationPolicyTest {
    @Test
    fun allowsBrowsableThirdPartyAppSchemes() {
        assertTrue(ExternalNavigationPolicy.isExternalAppScheme("alipays"))
        assertTrue(ExternalNavigationPolicy.isExternalAppScheme("market"))
        assertTrue(ExternalNavigationPolicy.isExternalAppScheme("mailto"))
        assertTrue(ExternalNavigationPolicy.isExternalAppScheme("tel"))
    }

    @Test
    fun blocksExecutableLocalAndInsecureNavigationSchemes() {
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("javascript"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("file"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("content"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("data"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("about"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme("http"))
        assertFalse(ExternalNavigationPolicy.isExternalAppScheme(null))
    }
}
