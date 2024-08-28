package fi.floo.voice.routing.auth

import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun auth(call: ApplicationCall) {
    call.respondRedirect("/auth/init")
}
