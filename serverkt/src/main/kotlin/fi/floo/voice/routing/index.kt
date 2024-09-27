package fi.floo.voice.routing

import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun index(call: ApplicationCall) {
    call.respondRedirect("/docs/v2")
}
