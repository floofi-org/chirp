package fi.floo.voice.types

class AuthenticationData(
    val authenticated: Boolean,
    val userData: UserData?,
    val apiKey: String?
) {
    fun canEnqueueGeneration(): Boolean {
        if (!authenticated || userData == null) return false

        val list: GenerationList = Generation.forUser(userData)
        val isBusy = list.inner
            .any { it.data.status == "generating" || it.data.status == "queued" }

        return !isBusy
    }
}
