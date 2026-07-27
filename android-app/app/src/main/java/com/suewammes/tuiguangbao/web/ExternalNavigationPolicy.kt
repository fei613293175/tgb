package com.suewammes.tuiguangbao.web

object ExternalNavigationPolicy {
    private val blockedSchemes = setOf(
        "about",
        "blob",
        "content",
        "data",
        "file",
        "http",
        "javascript"
    )

    fun isExternalAppScheme(scheme: String?): Boolean {
        val normalized = scheme?.lowercase()?.takeIf { it.isNotBlank() } ?: return false
        return normalized !in blockedSchemes && normalized != "https" && normalized != "intent"
    }
}
