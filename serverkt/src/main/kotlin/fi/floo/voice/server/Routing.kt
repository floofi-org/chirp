package fi.floo.voice.server

import fi.floo.voice.routing.auth.*
import fi.floo.voice.routing.docs.*
import fi.floo.voice.routing.*
import fi.floo.voice.routing.api.v2.*
import fi.floo.voice.routing.api.v2.admin.*
import io.ktor.server.application.*
import io.ktor.server.http.content.*
import io.ktor.server.routing.*

fun Application.configureRouting() {
    routing {
        staticResources("/assets", "static")

        get("/") { index(call) }
        get("/favicon.ico") { favicon(call) }
        get("/auth/") { auth(call) }
        get("/auth/init") { authInit(call) }
        get("/auth/callback") { authCallback(call) }
        get("/docs") { docs(call) }
        get("/docs/") { docs(call) }
        get("/docs/v1") { docsV1(call) }
        get("/docs/v1/") { docsV1(call) }
        get("/docs/v2") { docsV2(call) }
        get("/docs/v2/") { docsV2(call) }

        get("/api/v2/models") { apiV2Models(call) }
        get("/api/v2/status") { apiV2Status(call) }
        get("/api/v2/available") { apiV2Available(call) }
        get("/api/v2/history") { apiV2History(call) }
        get("/api/v2/history/{id}") { apiV2HistoryId(call) }
        delete("/api/v2/history/{id}") { apiV2HistoryDelete(call) }
        post("/api/v2/generate") { apiV2Generate(call) }
        post("/api/v2/handoff") { apiV2Handoff(call) }

        get("/api/v2/admin/available") { apiV2AdminAvailable(call) }
        get("/api/v2/admin/history") { apiV2AdminHistory(call) }
        delete("/api/v2/admin/history/{id}") { apiV2AdminHistoryDelete(call) }
    }
}
