package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class HandoffRequestData(
    val token: String
)
