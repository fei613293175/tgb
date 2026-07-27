package com.suewammes.tuiguangbao

import org.junit.Assert.assertEquals
import org.junit.Assert.assertFalse
import org.junit.Assert.assertTrue
import org.junit.Test
import java.net.URI

class BuildContractTest {
    @Test
    fun startUrlIsExactHttpsProductionHost() {
        val uri = URI(BuildConfig.START_URL)
        assertEquals("https", uri.scheme)
        assertEquals("tg.suewammes.com", uri.host)
        assertTrue(uri.userInfo == null)
    }

    @Test
    fun packageAndBrandAreFrozen() {
        assertEquals("com.suewammes.tuiguangbao.debug", BuildConfig.APPLICATION_ID)
        assertEquals(3, BuildConfig.VERSION_CODE)
        assertEquals("1.0.2-debug", BuildConfig.VERSION_NAME)
        assertEquals("TuiGuangBaoAndroid/1.0.2", BuildConfig.USER_AGENT_SUFFIX)
        assertFalse(BuildConfig.START_URL.startsWith("http://"))
    }
}
