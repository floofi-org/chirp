package fi.floo.voice.server

import fi.floo.voice.httpCodeToError
import fi.floo.voice.throwableToError
import io.ktor.http.*
import io.ktor.serialization.kotlinx.json.*
import io.ktor.server.application.*
import io.ktor.server.engine.*
import io.ktor.server.netty.*
import io.ktor.server.plugins.contentnegotiation.*
import io.ktor.server.plugins.cors.routing.*
import io.ktor.server.plugins.statuspages.*
import io.ktor.server.response.*
import io.ktor.server.velocity.*
import kotlinx.serialization.encodeToString
import kotlinx.serialization.json.Json

fun getServer(): NettyApplicationEngine {
    return embeddedServer(Netty, port = 8080, host = "0.0.0.0") {
        install(ContentNegotiation) {
            json()
        }

        install(CORS) {
            allowCredentials = true
            allowSameOrigin = true

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
                call.respond(HttpStatusCode.InternalServerError, throwableToError(cause))
            }
            status(HttpStatusCode.NotFound, HttpStatusCode.MethodNotAllowed) { call, status ->
                call.respond(status, httpCodeToError(status))
            }
        }

        install(Velocity)

        configureRouting()
    }
}