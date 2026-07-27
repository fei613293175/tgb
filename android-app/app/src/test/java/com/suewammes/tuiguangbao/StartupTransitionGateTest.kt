package com.suewammes.tuiguangbao

import org.junit.Assert.assertEquals
import org.junit.Test

class StartupTransitionGateTest {
    @Test
    fun neverDismissesBeforeDestinationIsReady() {
        val gate = StartupTransitionGate(startedAtMs = 1_000L)
        assertEquals(Long.MAX_VALUE, gate.remainingDelay(nowMs = 20_000L))
    }

    @Test
    fun keepsOverlayForAtLeastFiveSecondsAfterFastPageCommit() {
        val gate = StartupTransitionGate(startedAtMs = 1_000L)
        assertEquals(4_750L, gate.markDestinationReady(nowMs = 1_250L))
        assertEquals(1L, gate.remainingDelay(nowMs = 5_999L))
        assertEquals(0L, gate.remainingDelay(nowMs = 6_000L))
    }

    @Test
    fun dismissesImmediatelyWhenDestinationBecomesReadyAfterMinimumTime() {
        val gate = StartupTransitionGate(startedAtMs = 1_000L)
        assertEquals(0L, gate.markDestinationReady(nowMs = 7_000L))
    }
}
