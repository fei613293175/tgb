plugins {
    id("com.android.application")
}

val signingVariables = mapOf(
    "path" to providers.environmentVariable("TGB_KEYSTORE_PATH"),
    "storePassword" to providers.environmentVariable("TGB_KEYSTORE_PASSWORD"),
    "alias" to providers.environmentVariable("TGB_KEY_ALIAS"),
    "keyPassword" to providers.environmentVariable("TGB_KEY_PASSWORD")
)
val releaseSigningReady = signingVariables.values.all { it.isPresent }
val releaseRequested = gradle.startParameter.taskNames.any {
    it.contains("release", ignoreCase = true)
}
if (releaseRequested && !releaseSigningReady) {
    throw GradleException(
        "Release tasks require TGB_KEYSTORE_PATH, TGB_KEYSTORE_PASSWORD, " +
            "TGB_KEY_ALIAS and TGB_KEY_PASSWORD."
    )
}

android {
    namespace = "com.suewammes.tuiguangbao"
    compileSdk = 37

    defaultConfig {
        applicationId = "com.suewammes.tuiguangbao"
        minSdk = 23
        targetSdk = 36
        versionCode = 3
        versionName = "1.0.2"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
        vectorDrawables.useSupportLibrary = true
        buildConfigField("String", "START_URL", "\"https://tg.suewammes.com/\"")
        buildConfigField("String", "USER_AGENT_SUFFIX", "\"TuiGuangBaoAndroid/1.0.2\"")
    }

    signingConfigs {
        create("release") {
            if (releaseSigningReady) {
                storeFile = file(signingVariables.getValue("path").get())
                storePassword = signingVariables.getValue("storePassword").get()
                keyAlias = signingVariables.getValue("alias").get()
                keyPassword = signingVariables.getValue("keyPassword").get()
                enableV1Signing = true
                enableV2Signing = true
                enableV3Signing = true
                enableV4Signing = true
            }
        }
    }

    buildTypes {
        debug {
            applicationIdSuffix = ".debug"
            versionNameSuffix = "-debug"
        }
        release {
            isMinifyEnabled = false
            signingConfig = signingConfigs.getByName("release")
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    buildFeatures {
        buildConfig = true
    }

    packaging {
        resources.excludes += setOf(
            "META-INF/DEPENDENCIES",
            "META-INF/LICENSE.md",
            "META-INF/LICENSE-notice.md"
        )
    }

    testOptions {
        unitTests.isIncludeAndroidResources = false
    }
}

dependencies {
    implementation("androidx.activity:activity-ktx:1.13.0")
    implementation("androidx.core:core-ktx:1.19.0")
    implementation("androidx.core:core-splashscreen:1.2.0")
    implementation("androidx.webkit:webkit:1.15.0")

    testImplementation("junit:junit:4.13.2")
    androidTestImplementation("androidx.test.ext:junit:1.3.0")
    androidTestImplementation("androidx.test.espresso:espresso-core:3.7.0")
}
