package fi.floo.voice.types

import kotlinx.serialization.Serializable

@Serializable
data class UserData(
    val id: String,
    val name: String
)
