package fi.floo.voice.routing.auth

import fi.floo.voice.config
import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun authInit(call: ApplicationCall) {
    val redirect = if (config.development) {
        "http://127.0.0.1:8080"
    } else {
        "https://voice-api.floo.fi"
    }

    call.respondRedirect("https://account.equestria.dev/hub/api/rest/oauth2/auth" +
            "?client_id=${config.id}" +
            "&response_type=code" +
            "&redirect_uri=$redirect/auth/callback" +
            "&scope=Hub" +
            "&access_type=offline")
}
