package fi.floo.voice.routing.api.v2

import fi.floo.voice.config
import fi.floo.voice.getAuthenticationData
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*

suspend fun apiV2Models(call: ApplicationCall) {
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated) return

    val models: APIResponseModels = mutableListOf()

    for ((id, model) in config.models) {
        if (model.enabled) models.add(
            APIResponseModel(
                id,
                model.name,
                model.source,
                model.version
            )
        )
    }

    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseModelsObject(models.size, models)
    ))
}
