package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseOutput(
    val code: Int,
    val name: String,
    val message: String?,
    val see: String = "https://voice-api.floo.fi/docs/"
)