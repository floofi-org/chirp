package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class APIResponse(
    val error: APIResponseError?,
    val output: APIResponseOutput?
)