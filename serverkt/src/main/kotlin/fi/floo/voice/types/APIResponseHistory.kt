package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseHistory(
    val history: FlatGenerationList
)
