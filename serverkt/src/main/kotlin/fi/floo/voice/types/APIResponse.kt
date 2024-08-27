package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponse<T>(
    val error: APIResponseError?,
    val output: T?
)