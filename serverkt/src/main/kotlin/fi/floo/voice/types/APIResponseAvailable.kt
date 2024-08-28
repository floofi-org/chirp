package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseAvailable(
    val available: Boolean
)