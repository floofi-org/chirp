package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class GenerateRequestData(
    val input: String,
    val model: String
)
