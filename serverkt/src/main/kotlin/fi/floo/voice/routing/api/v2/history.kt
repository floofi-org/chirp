package fi.floo.voice.routing.api.v2

import fi.floo.voice.getAuthenticationData
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import java.lang.Integer.min

suspend fun apiV2History(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    val list: GenerationList = Generation.forUser(auth.userData)
    list.inner = list.inner
        .filter { it.data.status != "blocked" }
        .filter { it.data.status != "removed" }
        .toMutableList()

    val amount = call.parameters["amount"] ?: "30"
    val amountInt = amount.toInt()
    list.inner = list.inner.subList(0, min(amountInt + 1, list.inner.size))

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseHistory(list.inner.size, list.flatten())
    ))
}
