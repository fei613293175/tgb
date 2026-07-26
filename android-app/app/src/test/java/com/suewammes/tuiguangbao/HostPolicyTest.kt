package com.suewammes.tuiguangbao

import com.suewammes.tuiguangbao.web.HostPolicy
import org.junit.Assert.assertFalse
import org.junit.Assert.assertTrue
import org.junit.Test

class HostPolicyTest {
    @Test
    fun acceptsOnlyTheExactInternalHost() {
        assertTrue(HostPolicy.isInternalHost("TG.SUEWAMMES.COM"))
        assertFalse(HostPolicy.isInternalHost("evil.tg.suewammes.com"))
        assertFalse(HostPolicy.isInternalHost("tg.suewammes.com."))
        assertFalse(HostPolicy.isInternalHost(null))
    }

    @Test
    fun paymentGatewaysAreExactAndDoNotAllowSuffixTricks() {
        assertTrue(HostPolicy.isPaymentHost("sandcash.mixienet.com.cn"))
        assertTrue(HostPolicy.isPaymentHost("openapi.alipay.com"))
        assertTrue(HostPolicy.isPaymentHost("api.xunhupay.com"))
        assertFalse(HostPolicy.isPaymentHost("sandcash.mixienet.com.cn.evil.test"))
        assertFalse(HostPolicy.isPaymentHost("alipay.com"))
    }

    @Test
    fun alipayLaunchOriginCanOnlyBeSiteOrApprovedCashier() {
        assertTrue(HostPolicy.isTrustedPaymentOrigin("tg.suewammes.com"))
        assertTrue(HostPolicy.isTrustedPaymentOrigin("fuylink.cy253.top"))
        assertFalse(HostPolicy.isTrustedPaymentOrigin("example.com"))
        assertFalse(HostPolicy.isTrustedPaymentOrigin(null))
    }
}
