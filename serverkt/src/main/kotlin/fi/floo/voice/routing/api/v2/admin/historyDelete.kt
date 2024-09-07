package fi.floo.voice.routing.api.v2.admin

import fi.floo.voice.config
import fi.floo.voice.getAuthenticationData
import fi.floo.voice.httpCodeToError
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import java.io.File

suspend fun apiV2AdminHistoryDelete(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    if (config.admin != auth.userData.id) {
        call.respond(HttpStatusCode.Forbidden, httpCodeToError(HttpStatusCode.Forbidden))
        return
    }

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
        File("data/generations/${it.data.id}/reviewed.txt").createNewFile()
        call.respond(HttpStatusCode.OK, APIResponse(
            error = null,
            output = it.data
        ))
    }
}
