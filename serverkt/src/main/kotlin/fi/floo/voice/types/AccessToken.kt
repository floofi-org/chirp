package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Suppress("PropertyName")
@Serializable
data class AccessToken(
    val access_token: String,
    val token_type: String,
    val expires_in: Int,
    val refresh_token: String,
    val scope: String
)
