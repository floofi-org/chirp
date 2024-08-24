package fi.floo.voice

import io.ktor.client.*
import io.ktor.client.call.*
import io.ktor.client.engine.cio.*
import io.ktor.client.plugins.contentnegotiation.*
import io.ktor.client.request.*
import io.ktor.http.*
import io.ktor.serialization.kotlinx.json.*
import io.ktor.server.application.*
import io.ktor.server.http.content.*
import io.ktor.server.response.*
import io.ktor.server.routing.*
import io.ktor.util.Identity.encode
import kotlinx.serialization.Serializable
import kotlinx.serialization.encodeToString
import kotlinx.serialization.json.Json
import java.io.File
import java.net.URLEncoder
import java.security.SecureRandom
import java.time.Instant
import java.time.format.DateTimeFormatter
import java.util.*

@Serializable
data class AccessToken(
    val access_token: String,
    val token_type: String,
    val expires_in: Int,
    val refresh_token: String,
    val scope: String
)

@Serializable
data class UserData(
    val id: String
)

@Serializable
data class HandoffData(
    val token: String,
    val date: String
)

fun generateToken(size: Long): String {
    val chrs = "0123456789abcdefghijklmnopqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ"

    val secureRandom = SecureRandom.getInstanceStrong()

    val list = secureRandom
        .ints(size, 0, chrs.length)
        .mapToObj { i -> chrs[i] }
        .toList()

    return list.joinToString("")
}

fun Application.configureRouting() {
    routing {
        staticFiles("/assets", File("assets"))
        staticFiles("/favicon.ico", File("assets/favicon.ico"))

        get("/") {
            call.respondText("Floofi Voice Generator Server - Written in Kotlin with Ktor\n")
        }

        get("/auth/init") {
            val redirect = if (config.development) {
                "http://127.0.0.1:8080"
            } else {
                "https://voice-api.floo.fi"
            }

            call.respondRedirect("https://account.equestria.dev/hub/api/rest/oauth2/auth" +
                    "?client_id=${config.id}" +
                    "&response_type=code" +
                    "&redirect_uri=$redirect/auth/callback" +
                    "&scope=Hub" +
                    "&access_type=offline")
        }

        get("/auth/callback") {
            val redirect = if (config.development) {
                "http://127.0.0.1:8080"
            } else {
                "https://voice-api.floo.fi"
            }

            val code = call.parameters["code"]

            if (code == null) {
                call.respondRedirect("/auth/init")
            } else {
                val client = HttpClient(CIO) {
                    install(ContentNegotiation) {
                        json(Json { ignoreUnknownKeys = true })
                    }
                }

                val accessToken = client.request("https://account.equestria.dev/hub/api/rest/oauth2/token") {
                    method = HttpMethod.Post
                    headers {
                        append(HttpHeaders.Authorization, "Basic ${Base64.getEncoder()
                            .encodeToString("${config.id}:${config.secret}".toByteArray())}")
                        append(HttpHeaders.ContentType, "application/x-www-form-urlencoded")
                        append(HttpHeaders.Accept, "application/json")
                    }
                    setBody("grant_type=authorization_code" +
                            "&redirect_uri=${URLEncoder.encode("$redirect/auth/callback", "utf-8")}" +
                            "&code=$code")
                }.body<AccessToken>().access_token

                val response = client.request("https://account.equestria.dev/hub/api/rest/users/me") {
                    method = HttpMethod.Get
                    headers {
                        append(HttpHeaders.Authorization, "Bearer $accessToken")
                        append(HttpHeaders.Accept, "application/json")
                    }
                }
                client.close()

                val userDataString = response.body<String>()
                val sessionToken = generateToken(96)

                File("tokens/session/$sessionToken").writer().use { f ->
                    f.write(userDataString)
                }

                if (config.development) {
                    val handoffToken = generateToken(32)
                    val handoffData = HandoffData(handoffToken, DateTimeFormatter.ISO_INSTANT.format(Instant.now()))
                    File("tokens/handoff/$handoffToken").writer().use { f ->
                        f.write(Json.encodeToString(handoffData))
                    }

                    call.respondRedirect("http://127.0.0.1:3000/handoff#$handoffToken")
                } else {
                    /*call.response.headers.append(HttpHeaders.SetCookie,
                        "Set-Cookie: SSB_SESSION_TOKEN=$sessionToken; SameSite=None;" +
                                "Path=/; Secure; HttpOnly; SameSite=None; MaxAge=63,072,000")*/
                    call.respondRedirect("https://voice.floo.fi/app")
                }
            }
        }
    }
}
