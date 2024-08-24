package fi.floo.voice

import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.engine.*
import io.ktor.server.netty.*
import io.ktor.server.plugins.cors.routing.*
import io.ktor.server.plugins.statuspages.*
import io.ktor.server.response.*
import java.io.File
import kotlinx.serialization.*
import kotlinx.serialization.json.Json
import java.nio.file.Files
import java.nio.file.Paths

@Serializable
data class Config(
    val development: Boolean,
    val acl: Boolean,
    val admin: String,
    val id: String,
    val secret: String,
    val banned: Collection<String>
)

lateinit var config: Config

fun main() {
    Files.createDirectories(Paths.get("tokens"))
    Files.createDirectories(Paths.get("tokens/handoff"))
    Files.createDirectories(Paths.get("tokens/session"))
    val file = File("config.json")
    config = Json.decodeFromString(file.readText())

    embeddedServer(Netty, port = 8080, host = "0.0.0.0") {
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
                call.respondText(text = "500: $cause" , status = HttpStatusCode.InternalServerError)
            }
            status(HttpStatusCode.NotFound) { call, status ->
                call.respondText(text = "404 Not Found", status = status)
            }
        }
        module()
    }.start(wait = true)
}

fun Application.module() {
    configureRouting()
}
