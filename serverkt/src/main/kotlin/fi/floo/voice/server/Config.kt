package fi.floo.voice.server

import fi.floo.voice.types.Config
import kotlinx.serialization.json.Json
import java.io.File
import java.nio.file.Files
import java.nio.file.Paths

fun loadConfig(): Config {
    Files.createDirectories(Paths.get("data"))
    Files.createDirectories(Paths.get("data/generations"))
    Files.createDirectories(Paths.get("data/handoff"))
    Files.createDirectories(Paths.get("data/session"))
    Files.createDirectories(Paths.get("data/users"))
    Files.createDirectories(Paths.get("data/keys"))

    val file = File("config.json")
    return Json.decodeFromString(file.readText())
}
