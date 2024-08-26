package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class HandoffData(
    val token: String,
    val date: String
)