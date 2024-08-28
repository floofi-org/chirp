package fi.floo.voice.routing.docs

import fi.floo.voice.getAPIKey
import fi.floo.voice.getSession
import io.ktor.server.application.*
import io.ktor.server.response.*
import io.ktor.server.velocity.*

suspend fun docsV2(call: ApplicationCall) {
    val session = getSession(call)

    if (session == null) {
        call.respondRedirect("/auth/init")
    } else {
        val apiKey = getAPIKey(session.id)
        call.respond(VelocityContent("resources/views/docs-v2.vl", mapOf("apiKey" to apiKey)))
    }
}
