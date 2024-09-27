package fi.floo.voice.routing.api.v2.admin

import fi.floo.voice.config
import fi.floo.voice.getAuthenticationData
import fi.floo.voice.httpCodeToError
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import java.io.File
import java.lang.Integer.min

suspend fun apiV2AdminHistory(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    if (config.admin != auth.userData.id) {
        call.respond(HttpStatusCode.Forbidden, httpCodeToError(HttpStatusCode.Forbidden))
        return
    }

    val list: GenerationList = Generation.getAll()
    list.inner = list.inner
        .filter { !File("data/generations/${it.data.id}/reviewed.txt").exists() }
        .toMutableList()

    val amount = call.parameters["amount"] ?: "30"
    val amountInt = amount.toInt()
    list.inner = list.inner.subList(0, min(amountInt + 1, list.inner.size))

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseHistory(list.inner.size, list.flatten())
    ))
}
