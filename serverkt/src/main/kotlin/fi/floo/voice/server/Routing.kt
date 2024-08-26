package fi.floo.voice.server

import fi.floo.voice.routing.auth.*
import fi.floo.voice.routing.docs.*
import fi.floo.voice.routing.*
import io.ktor.server.application.*
import io.ktor.server.http.content.*
import io.ktor.server.routing.*
import java.io.File

fun Application.configureRouting() {
    routing {
        staticFiles("/assets", File("resources/static"))
        staticFiles("/favicon.ico", File("resources/static/favicon.ico"))

        get("/") { index(call) }
        get("/auth/") { auth(call) }
        get("/auth/init") { authInit(call) }
        get("/auth/callback") { authCallback(call) }
        get("/docs") { docs(call) }
        get("/docs/v1") { docsV1(call) }
        get("/docs/v2") { docsV2(call) }
    }
}
