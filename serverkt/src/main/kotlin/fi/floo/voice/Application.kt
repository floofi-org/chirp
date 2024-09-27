package fi.floo.voice

import fi.floo.voice.server.getServer
import fi.floo.voice.server.loadConfig
import fi.floo.voice.types.BlockList

val config = loadConfig()
val blockList = BlockList()

fun main() {
    getServer().start(wait = true)
}
