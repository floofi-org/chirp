package fi.floo.voice.server

import fi.floo.voice.httpCodeToError
import fi.floo.voice.throwableToError
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.engine.*
import io.ktor.server.netty.*
import io.ktor.server.plugins.cors.routing.*
import io.ktor.server.plugins.statuspages.*
import io.ktor.server.response.*
import io.ktor.server.velocity.*
import kotlinx.serialization.encodeToString
import kotlinx.serialization.json.Json

fun getServer(): NettyApplicationEngine {
    return embeddedServer(Netty, port = 8080, host = "0.0.0.0") {
        install(CORS) {
            allowCredentials = true
            allowSameOrigin = true

            allowHost("127.0.0.1:3000")
            allowHost("localhost:3000")
            allowHost("voice.floo.fi")

            allowMethod(HttpMethod.Get)
            allowMethod(HttpMethod.Post)
            allowMethod(HttpMethod.Put)
            allowMethod(HttpMethod.Delete)

            allowHeader(HttpHeaders.ContentType)
            allowHeader(HttpHeaders.Cookie)
            allowHeader(HttpHeaders.Origin)
            allowHeader(HttpHeaders.UserAgent)
            allowHeader(HttpHeaders.Host)
            allowHeader(HttpHeaders.Authorization)
        }

        install(StatusPages) {
            exception<Throwable> { call, cause ->
                call.respondText(text = Json.encodeToString(throwableToError(cause)),
                    status = HttpStatusCode.InternalServerError, contentType = ContentType.Application.Json)
            }
            status(HttpStatusCode.NotFound) { call, status ->
                call.respondText(text = Json.encodeToString(httpCodeToError(status)),
                    status = status, contentType = ContentType.Application.Json)
            }
        }

        install(Velocity)

        configureRouting()
    }
}