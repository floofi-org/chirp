package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseModel(
    val id: String,
    val name: String,
    val source: String,
    val version: String
)

typealias APIResponseModels = MutableList<APIResponseModel>