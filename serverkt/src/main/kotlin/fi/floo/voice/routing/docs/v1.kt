package fi.floo.voice.routing.docs

import fi.floo.voice.getAPIKey
import fi.floo.voice.getSession
import io.ktor.server.application.*
import io.ktor.server.response.*
import io.ktor.server.velocity.*

suspend fun docsV1(call: ApplicationCall) {
    val session = getSession(call)

    if (session == null) {
        call.respondRedirect("/auth/init")
    } else {
        val apiKey = getAPIKey(session.id)
        call.respond(VelocityContent("views/docs-v1.vl", mapOf("apiKey" to apiKey)))
    }
}
