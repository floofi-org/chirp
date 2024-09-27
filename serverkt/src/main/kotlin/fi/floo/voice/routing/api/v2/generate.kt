package fi.floo.voice.routing.api.v2

import fi.floo.voice.*
import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.request.*
import io.ktor.server.response.*
import java.io.File

val preprocessRegex = Regex("""[^a-zA-Z':\d()\[].,?! ~]""")

suspend fun apiV2Generate(call: ApplicationCall) {
    val generateRequestData = call.receive<GenerateRequestData>()
    val auth = getAuthenticationData(call, AuthenticationMode.Enforced)
    if (!auth.authenticated || auth.userData == null) return

    val id = generateJobID()

    if (generateRequestData.model.length > 20 || generateRequestData.input.length > 160) {
        call.respond(HttpStatusCode.PayloadTooLarge, httpCodeToError(HttpStatusCode.PayloadTooLarge))
        return
    }

    if (!auth.canEnqueueGeneration()) {
        call.respond(HttpStatusCode.TooManyRequests, httpCodeToError(HttpStatusCode.TooManyRequests))
        return
    }

    val model = config.models[generateRequestData.model]
    if (model == null || !model.enabled) {
        call.respond(HttpStatusCode.NotImplemented, httpCodeToError(HttpStatusCode.NotImplemented))
        return
    }

    val processedText = generateRequestData.input
        .trim()
        .replace(preprocessRegex, "")

    File("data/generations/$id").mkdir()
    File("data/generations/$id/model.txt").writeText(generateRequestData.model)
    File("data/generations/$id/author.txt").writeText(auth.userData.id)
    File("data/generations/$id/timestamp.txt").writeText((System.currentTimeMillis() / 1000).toString())
    File("data/generations/$id/input_orig.txt").writeText(generateRequestData.input)
    File("data/generations/$id/version.txt").writeText(model.version)

    if (!blockList.isStringFriendly(generateRequestData.input)) {
        File("data/generations/$id/held.txt").createNewFile()
        File("data/generations/$id/blocked.txt").createNewFile()
        call.respond(HttpStatusCode.BadRequest, httpCodeToError(HttpStatusCode.BadRequest))
        return
    }

    File("data/generations/$id/input.txt").writeText(processedText)
    call.respond(HttpStatusCode.OK, APIResponse(
        error = null,
        output = APIResponseGenerate(id)
    ))
}
