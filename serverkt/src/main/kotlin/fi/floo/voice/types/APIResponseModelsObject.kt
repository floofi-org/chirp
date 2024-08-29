package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseModelsObject(
    val total: Int,
    val models: APIResponseModels
)
