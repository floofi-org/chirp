package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class GenerationData(
    val id: String,
    val model: String,
    val version: String,
    val authorId: String,
    val authorName: String,
    val time: String,
    val timeTs: Long,
    val audioUrl: String,
    val graphUrl: String,
    val input: String,
    var status: String
)
