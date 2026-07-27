package com.suewammes.tuiguangbao

class StartupTransitionGate(
    private val startedAtMs: Long,
    private val minimumVisibleMs: Long = DEFAULT_MINIMUM_VISIBLE_MS
) {
    private var destinationReady = false

    fun markDestinationReady(nowMs: Long): Long {
        destinationReady = true
        return remainingDelay(nowMs)
    }

    fun remainingDelay(nowMs: Long): Long {
        if (!destinationReady) return Long.MAX_VALUE
        return (minimumVisibleMs - (nowMs - startedAtMs)).coerceAtLeast(0L)
    }

    companion object {
        const val DEFAULT_MINIMUM_VISIBLE_MS = 5_000L
    }
}
