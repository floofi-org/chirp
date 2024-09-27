package fi.floo.voice.routing.api.v2

import fi.floo.voice.getAuthenticationData
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun apiV2Available(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseAvailable(auth.canEnqueueGeneration())
    ))
}
