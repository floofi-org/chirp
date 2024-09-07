package fi.floo.voice

import fi.floo.voice.types.*
import io.ktor.http.*
import io.ktor.server.application.*
import io.ktor.server.response.*
import kotlinx.serialization.json.Json
import java.io.File
import java.security.SecureRandom

fun generateToken(size: Long): String {
    val chrs = "0123456789abcdefghijklmnopqrstuvwxyz-_ABCDEFGHIJKLMNOPQRSTUVWXYZ"

    val secureRandom = SecureRandom.getInstanceStrong()

    val list = secureRandom
        .ints(size, 0, chrs.length)
        .mapToObj { i -> chrs[i] }
        .toList()

    return list.joinToString("")
}

fun generateAPIKey(): String = generateToken(48)
fun generateJobID(): String = generateToken(64)

fun getUserFromID(id: String): UserData? {
    val file = File("data/users/$id")
    val laxJson = Json { ignoreUnknownKeys = true }

    return if (file.exists()) {
        val dataString = file.readText()
        laxJson.decodeFromString(dataString)
    } else {
        null
    }
}

fun getSession(call: ApplicationCall): UserData? {
    var sessionCookie = call.request.cookies["SSB_SESSION_TOKEN"]
    val authenticationHeader = call.request.headers["Authorization"]
    val laxJson = Json { ignoreUnknownKeys = true }
    var data: UserData? = null

    if (sessionCookie == null && authenticationHeader != null) {
        if (authenticationHeader.startsWith("PrivateToken ")) {
            sessionCookie = authenticationHeader.substring(13)
        }
    }

    if (sessionCookie != null && !sessionCookie.contains('/') && !sessionCookie.contains('\\') &&
        !sessionCookie.contains('.')) {
        val file = File("data/session/$sessionCookie")

        if (file.exists()) {
            val dataString = file.readText()
            data = laxJson.decodeFromString(dataString)
        }
    }

    if (data == null && authenticationHeader != null && !authenticationHeader.contains('/') &&
        !authenticationHeader.contains('\\') && authenticationHeader != "." &&
        !authenticationHeader.contains("..")) {
        if (authenticationHeader.startsWith("Bearer ")) {
            val key = authenticationHeader.substring(7)
            val idFile = File("data/keys/$key")

            if (idFile.exists()) {
                val id = idFile.readText().trim()
                val dataFile = File("data/users/$id")

                if (dataFile.exists()) {
                    val dataString = dataFile.readText()
                    data = laxJson.decodeFromString(dataString)
                }
            }
        }
    }

    /*if (data != null && config.banned.contains(data.id)) {
        data = null
    }*/

    return data
}

fun getAPIKey(id: String): String {
    val file = File("data/keys/$id")

    if (!file.exists()) {
        file.writeText(generateAPIKey())
    }

    return file.readText().trim()
}

fun throwableToError(throwable: Throwable): APIResponse<Unit> {
    return APIResponse(
        error = APIResponseError(
            code = 500,
            name = "Internal Server Error",
            message = throwable.toString(),
            see = "https://voice-api.floo.fi/docs/"
        ),
        output = null
    )
}

fun httpCodeToError(code: HttpStatusCode): APIResponse<Unit> {
    return APIResponse(
        error = APIResponseError(
            code = code.value,
            name = code.description,
            message = null,
            see = "https://voice-api.floo.fi/docs/"
        ),
        output = null
    )
}

suspend fun getAuthenticationData(call: ApplicationCall, mode: AuthenticationMode): AuthenticationData {
    return when (mode) {
        AuthenticationMode.Disabled -> AuthenticationData(false, null, null)
        AuthenticationMode.Enforced, AuthenticationMode.Permissive -> {
            val session = getSession(call)

            if (session == null) {
                if (mode == AuthenticationMode.Enforced) {
                    call.respond(HttpStatusCode.Unauthorized, httpCodeToError(HttpStatusCode.Unauthorized))
                }

                AuthenticationData(false, null, null)
            } else {
                val apiKey = getAPIKey(session.id)

                if (config.banned.contains(session.id)) {
                    call.respond(HttpStatusCode.Forbidden, httpCodeToError(HttpStatusCode.Forbidden))
                    return AuthenticationData(false, null, null)
                }

                AuthenticationData(true, session, apiKey)
            }
        }
    }
}
