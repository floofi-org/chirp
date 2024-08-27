val kotlin_version: String by project
val logback_version: String by project
val ktor_version = "2.3.12"

plugins {
    kotlin("jvm") version "2.0.20"
    id("io.ktor.plugin") version "2.3.12"
    kotlin("plugin.serialization") version "2.0.0"
}

group = "fi.floo.voice"
version = "0.1.0"

application {
    mainClass.set("fi.floo.voice.ApplicationKt")

    val isDevelopment: Boolean = project.ext.has("development")
    applicationDefaultJvmArgs = listOf("-Dio.ktor.development=$isDevelopment")
}

ktor {
    fatJar {
        archiveFileName.set("serverkt.jar")
    }
}

repositories {
    mavenCentral()
}

dependencies {
    // Ktor server core
    implementation("io.ktor:ktor-server-core-jvm:$ktor_version")
    implementation("io.ktor:ktor-server-netty-jvm:$ktor_version")

    // Ktor server plugins
    implementation("io.ktor:ktor-server-cors:$ktor_version")
    implementation("io.ktor:ktor-server-status-pages:$ktor_version")
    implementation("io.ktor:ktor-server-velocity:$ktor_version")
    implementation("io.ktor:ktor-server-content-negotiation:$ktor_version")

    // Ktor client core
    implementation("io.ktor:ktor-client-core:$ktor_version")
    implementation("io.ktor:ktor-client-cio:$ktor_version")

    // Ktor client plugins
    implementation("io.ktor:ktor-client-content-negotiation:$ktor_version")

    // Ktor plugins
    implementation("io.ktor:ktor-serialization-kotlinx-json:$ktor_version")
    testImplementation("io.ktor:ktor-server-test-host-jvm:$ktor_version")

    // Kotlin plugins
    implementation("org.jetbrains.kotlinx:kotlinx-serialization-json:1.7.1")
    implementation("ch.qos.logback:logback-classic:$logback_version")
    testImplementation("org.jetbrains.kotlin:kotlin-test-junit:$kotlin_version")
}
