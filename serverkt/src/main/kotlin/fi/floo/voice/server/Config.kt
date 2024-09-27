package fi.floo.voice.server

import fi.floo.voice.types.Config
import kotlinx.serialization.json.Json
import java.io.File

fun loadConfig(): Config {
    File("data").mkdir()
    File("data/generations").mkdir()
    File("data/handoff").mkdir()
    File("data/session").mkdir()
    File("data/users").mkdir()
    File("data/keys").mkdir()

    val file = File("config.json")
    return Json.decodeFromString(file.readText())
}
