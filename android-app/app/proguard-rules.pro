# NexShape Academia — ProGuard / R8

-keepattributes Signature, InnerClasses, EnclosingMethod, *Annotation*

# Moshi + Retrofit DTOs
-keep @com.squareup.moshi.JsonClass class * { *; }
-keepclassmembers class * {
    @com.squareup.moshi.FromJson <methods>;
    @com.squareup.moshi.ToJson <methods>;
}
-keepclasseswithmembers class * {
    @retrofit2.http.* <methods>;
}
-keep interface br.com.nexshape.academia.data.api.NexShapeApi { *; }
-keep class br.com.nexshape.academia.data.api.** { *; }

# Room
-keep class * extends androidx.room.RoomDatabase
-keep @androidx.room.Entity class *
-dontwarn androidx.room.paging.**

# Coil
-dontwarn coil.**

# OkHttp / Conscrypt
-dontwarn okhttp3.**
-dontwarn okio.**
-dontwarn org.conscrypt.**

# Firebase
-keep class com.google.firebase.** { *; }
-dontwarn com.google.firebase.**

# Biometric
-keep class androidx.biometric.** { *; }
