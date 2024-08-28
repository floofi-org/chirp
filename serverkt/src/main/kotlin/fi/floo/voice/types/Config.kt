package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class Config(
    val development: Boolean,
    val acl: Boolean,
    val admin: String,
    val id: String,
    val secret: String,
    val banned: Collection<String>,
    val models: Map<String, ModelConfig>
)
