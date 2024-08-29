package fi.floo.voice.routing.auth

import fi.floo.voice.config
import fi.floo.voice.generateToken
import fi.floo.voice.httpCodeToError
import fi.floo.voice.types.AccessToken
import fi.floo.voice.types.HandoffData
import fi.floo.voice.types.UserData
import io.ktor.client.*
import io.ktor.client.call.*
import io.ktor.client.engine.cio.*
import io.ktor.client.plugins.contentnegotiation.*
import io.ktor.client.request.*
import io.ktor.http.*
import io.ktor.serialization.kotlinx.json.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import kotlinx.serialization.encodeToString
import kotlinx.serialization.json.Json
import java.io.File
import java.net.URLEncoder
import java.time.Instant
import java.util.*

suspend fun authCallback(call: ApplicationCall) {
    val redirect = if (config.development) {
        "http://127.0.0.1:8080"
    } else {
        "https://voice-api.floo.fi"
    }

    val code = call.parameters["code"]

    if (code == null) {
        call.respondRedirect("/auth/init")
        return
    }

    if (code.length > 8) {
        call.respond(HttpStatusCode.PayloadTooLarge, httpCodeToError(HttpStatusCode.PayloadTooLarge))
        return
    }

    val client = HttpClient(CIO) {
        install(ContentNegotiation) {
            json(Json { ignoreUnknownKeys = true })
        }
    }

    val accessToken = client.request("https://account.equestria.dev/hub/api/rest/oauth2/token") {
        method = HttpMethod.Post
        headers {
            append(
                HttpHeaders.Authorization, "Basic ${
                    Base64.getEncoder()
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

    val userData = response.body<UserData>()
    val userDataString = response.body<String>()
    val sessionToken = generateToken(96)

    File("data/session/$sessionToken").writer().use { f ->
        f.write(userDataString)
    }

    File("data/users/${userData.id}").writer().use { f ->
        f.write(userDataString)
    }

    call.response.headers.append(
        HttpHeaders.SetCookie,
        "SSB_SESSION_TOKEN=$sessionToken; SameSite=None; Path=/; Secure; HttpOnly; SameSite=None; " +
                "Max-Age=63072000")

    if (config.development) {
        val handoffToken = generateToken(32)
        val handoffData = HandoffData(sessionToken, Instant.now().toEpochMilli())
        File("data/handoff/$handoffToken").writer().use { f ->
            f.write(Json.encodeToString(handoffData))
        }

        call.respondRedirect("http://localhost:3000/handoff#$handoffToken")
    } else {
        call.respondRedirect("https://voice.floo.fi/app")
    }
}
