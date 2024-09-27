package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponseError(
    val code: Int,
    val name: String,
    val message: String?,
    val see: String
)
