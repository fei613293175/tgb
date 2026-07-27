package com.suewammes.tuiguangbao

import com.suewammes.tuiguangbao.web.ImageSaveSource
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull
import org.junit.Assert.assertTrue
import org.junit.Test

class ImageSaveSourceTest {
    @Test
    fun acceptsHttpsImagesWithoutEmbeddedCredentials() {
        val source = ImageSaveSource.parse("https://tg.suewammes.com/data/a.jpg?x=1")
        assertEquals(
            ImageSaveSource.Https("https://tg.suewammes.com/data/a.jpg?x=1"),
            source
        )
    }

    @Test
    fun acceptsBase64ImageDataUrl() {
        val source = ImageSaveSource.parse("data:image/png;base64,iVBORw0KGgo=")
        assertTrue(source is ImageSaveSource.DataUrl)
        source as ImageSaveSource.DataUrl
        assertEquals("image/png", source.mimeType)
        assertEquals("iVBORw0KGgo=", source.base64Payload)
    }

    @Test
    fun rejectsUnsafeOrUnsupportedSources() {
        assertNull(ImageSaveSource.parse("http://tg.suewammes.com/a.jpg"))
        assertNull(ImageSaveSource.parse("https://user:pass@tg.suewammes.com/a.jpg"))
        assertNull(ImageSaveSource.parse("javascript:alert(1)"))
        assertNull(ImageSaveSource.parse("data:text/html;base64,PGgxPk5vPC9oMT4="))
        assertNull(ImageSaveSource.parse("blob:https://tg.suewammes.com/id"))
    }

    @Test
    fun rejectsOversizedInlineImageBeforeDecoding() {
        val oversized = "data:image/png;base64," + "A".repeat(35_000_001)
        assertNull(ImageSaveSource.parse(oversized))
    }
}
