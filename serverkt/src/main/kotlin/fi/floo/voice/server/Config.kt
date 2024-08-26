package fi.floo.voice.server

import fi.floo.voice.types.Config
import kotlinx.serialization.json.Json
import java.io.File
import java.nio.file.Files
import java.nio.file.Paths

fun loadConfig(): Config {
    Files.createDirectories(Paths.get("tokens"))
    Files.createDirectories(Paths.get("tokens/handoff"))
    Files.createDirectories(Paths.get("tokens/session"))
    Files.createDirectories(Paths.get("tokens/users"))
    Files.createDirectories(Paths.get("tokens/keys"))

    val file = File("config.json")
    return Json.decodeFromString(file.readText())
}