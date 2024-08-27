package fi.floo.voice.types

data class AuthenticationData(
    val authenticated: Boolean,
    val userData: UserData?,
    val apiKey: String?
)