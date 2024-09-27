package fi.floo.voice.routing.docs

import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun docs(call: ApplicationCall) {
    call.respondRedirect("/docs/v2")
}
