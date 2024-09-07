package fi.floo.voice.routing.api.v2.admin

import fi.floo.voice.config
import fi.floo.voice.getAuthenticationData
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun apiV2AdminAvailable(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseAvailable(config.admin == auth.userData.id)
    ))
}
