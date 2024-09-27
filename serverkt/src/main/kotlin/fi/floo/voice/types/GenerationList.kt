package fi.floo.voice.types

class GenerationList(var inner: MutableList<Generation>) {
    fun flatten(): FlatGenerationList {
        val output = mutableListOf<GenerationData>()

        for (item in inner) {
            output.add(item.data)
        }

        return output.toList()
    }
}

typealias FlatGenerationList = List<GenerationData>
