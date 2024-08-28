package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class ModelConfig(
    val name: String,
    val source: String,
    val version: String,
    val enabled: Boolean
)
