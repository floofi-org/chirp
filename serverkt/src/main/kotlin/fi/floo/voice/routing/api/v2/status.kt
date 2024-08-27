package fi.floo.voice.routing.api.v2

import fi.floo.voice.config
import fi.floo.voice.getAuthenticationData
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import kotlinx.serialization.encodeToString
import kotlinx.serialization.json.Json

suspend fun apiV2Status(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseStatus(
            user = auth.userData.id
        )
    ))
}