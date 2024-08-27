package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseStatus(
    val user: String
)