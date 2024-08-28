package fi.floo.voice.types

import java.io.File
import java.time.Instant
import java.time.format.DateTimeFormatter
import java.util.*

class Generation(val data: GenerationData) {
    val time = Date(data.timeTs)

    companion object {
        fun forUser(user: UserData): GenerationList {
            val list = GenerationList(mutableListOf())
            val directory = File("data/generations")
            val files = directory.listFiles()?.filter { it.isDirectory }

            if (files != null) {
                for (file in files) {
                    list.inner.add(fromDirectory(file))
                    list.inner.sortBy { it.time }
                    list.inner = list.inner
                        .filter { it.data.version != "0" }
                        .filter { it.data.model != "" }
                        .filter { it.data.author == user.id }
                        .toMutableList()
                }
            }

            return list
        }

        private fun fromDirectory(directory: File): Generation {
            val author = File("${directory.absolutePath}/author.txt").readText()
            val complete = File("${directory.absolutePath}/complete.txt").exists()
            val blocked = File("${directory.absolutePath}/blocked.txt").exists()
            val input = File("${directory.absolutePath}/input_orig.txt").readText()
            val process = File("${directory.absolutePath}/process.txt").exists()
            val removed = File("${directory.absolutePath}/removed.txt").exists()
            val timestamp = File("${directory.absolutePath}/timestamp.txt").readText()

            val versionFile = File("${directory.absolutePath}/version.txt")
            val version = if (versionFile.exists()) {
                versionFile.readText()
            } else {
                "0"
            }

            val modelFile = File("${directory.absolutePath}/model.txt")
            val model = if (modelFile.exists()) {
                modelFile.readText()
            } else {
                ""
            }

            val timeTs = timestamp.toLong()
            val timeInstant = Instant.ofEpochSecond(timeTs)
            val dateIso = DateTimeFormatter.ISO_INSTANT.format(timeInstant)

            val status = if (blocked) {
                "blocked"
            } else if (removed) {
                "removed"
            } else if (complete) {
                "processed"
            } else if (process) {
                "generating"
            } else {
                "queued"
            }

            val data = GenerationData(
                id = directory.name,
                model = model,
                version = version,
                author = author,
                time = dateIso,
                timeTs = timeTs,
                audioUrl = "",
                input = input,
                status = status
            )

            return Generation(data)
        }
    }
}