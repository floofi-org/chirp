package fi.floo.voice.routing.api.v2

import fi.floo.voice.httpCodeToError
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import kotlinx.serialization.json.Json
import java.io.File
import java.time.Instant

suspend fun apiV2Handoff(call: ApplicationCall) {
    val handoffRequestData = call.receive<HandoffRequestData>()

    if (handoffRequestData.token.length > 32) {
        call.respond(HttpStatusCode.PayloadTooLarge, httpCodeToError(HttpStatusCode.PayloadTooLarge))
        return
    }

    val file = File("data/handoff/${handoffRequestData.token}")

    if (file.exists()) {
        val dataString = file.readText()
        val data = Json.decodeFromString<HandoffData>(dataString)

        val then = data.date
        val now = Instant.now().toEpochMilli()
        val diff = now - then

        if (diff < 120_000) {
            file.delete()
            call.respond(HttpStatusCode.OK, APIResponse(
                error = null,
                output = HandoffRequestData(
                    token = data.token
                )
            ))
            return
        }
    }

    call.respond(HttpStatusCode.Unauthorized, httpCodeToError(HttpStatusCode.Unauthorized))
}
