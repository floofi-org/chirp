package fi.floo.voice.routing

import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun favicon(call: ApplicationCall) {
    call.respondRedirect("/assets/favicon.ico")
}
