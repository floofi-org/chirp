package fi.floo.voice.routing.api.v2

import fi.floo.voice.getAuthenticationData
import fi.floo.voice.httpCodeToError
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import java.io.File

suspend fun apiV2HistoryDelete(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    val id = call.parameters["id"]

    if (id == null) {
        call.respondRedirect("/auth/init")
        return
    }

    if (id.length > 96) {
        call.respond(HttpStatusCode.PayloadTooLarge, httpCodeToError(HttpStatusCode.PayloadTooLarge))
        return
    }

    Generation.fromId(id)?.let {
        if (it.data.status == "blocked" || it.data.author != auth.userData.id) {
            call.respond(HttpStatusCode.NotFound, httpCodeToError(HttpStatusCode.NotFound))
        } else if (it.data.status == "removed") {
            call.respond(HttpStatusCode.Conflict, httpCodeToError(HttpStatusCode.Conflict))
        } else {
            it.data.status = "removed"
            File("data/generations/${it.data.id}/removed.txt").createNewFile()

            call.respond(HttpStatusCode.OK, APIResponse(
                error = null,
                output = it.data
            ))
        }

        return
    }
}
