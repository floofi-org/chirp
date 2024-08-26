package fi.floo.voice

import fi.floo.voice.server.getServer
import fi.floo.voice.server.loadConfig
import fi.floo.voice.types.Config

var config: Config = loadConfig()

fun main() {
    getServer().start(wait = true)
}
