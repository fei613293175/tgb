package com.suewammes.tuiguangbao.web

object HostPolicy {
    private val internalHosts = setOf("tg.suewammes.com")

    // Exact HTTPS payment gateway hosts observed in the production plugin source.
    // Additions require a sanitized redirect sample and a source/evidence update.
    private val paymentHosts = setOf(
        "api.xunhupay.com",
        "fuylink.cy253.top",
        "mapi.alipay.com",
        "openapi.alipay.com",
        "sandcash.mixienet.com.cn",
        "wappaygw.alipay.com"
    )

    fun isInternalHost(host: String?): Boolean = normalize(host) in internalHosts

    fun isPaymentHost(host: String?): Boolean = normalize(host) in paymentHosts

    fun isTrustedPaymentOrigin(host: String?): Boolean {
        return isInternalHost(host) || isPaymentHost(host)
    }

    private fun normalize(host: String?): String? {
        return host?.trim()?.lowercase()?.takeIf {
            it.isNotEmpty() && !it.endsWith(".")
        }
    }
}
