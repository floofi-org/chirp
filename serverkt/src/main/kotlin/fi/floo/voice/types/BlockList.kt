package fi.floo.voice.types

import java.io.File

class BlockList {
    private val blockListSpaceRegex = Regex(""" +""")
    private val blockListAlphabeticalRegex = Regex("""[^a-z\d]""")

    private val file = File("blocklist.txt")
    private val inner = file.readLines()
        .filter { it.isNotEmpty() }
        .map { it.trim().lowercase() }

    private fun containsBlockedWord(string: String, word: String): Boolean =
        (string.contains(word) && word.length > 4) || (string.contains(" $word "))

    fun isStringFriendly(string: String): Boolean {
        if (string.isEmpty()) return true
        val processed = " ${string.trim().lowercase()
            .replace(blockListAlphabeticalRegex, " ")
            .replace(blockListSpaceRegex, " ")} "

        for (item in inner) {
            if (containsBlockedWord(processed, item)) {
                return false
            }
        }

        return true
    }
}
