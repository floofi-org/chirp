package fi.floo.voice.routing

import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun index(call: ApplicationCall) {
    call.respondText("Floofi Voice Generator Server - Written in Kotlin with Ktor\n")
}
