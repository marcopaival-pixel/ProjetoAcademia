package br.com.nexshape.academia.data.api

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class ActiveRestData(
    @Json(name = "is_premium_user") val isPremiumUser: Boolean = true,
    @Json(name = "is_off_day") val isOffDay: Boolean = false,
    @Json(name = "suggested_routine_id") val suggestedRoutineId: Int? = null,
    val routines: List<ActiveRestRoutineDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class ActiveRestRoutineDto(
    val id: Int,
    val title: String,
    val category: String,
    val duration: Int,
    val intensity: String,
    @Json(name = "recommended_level") val recommendedLevel: String? = null,
    val thumbnail: String? = null,
    val guide_image: String? = null,
    @Json(name = "video_id") val videoId: String? = null,
    val benefit: String? = null,
    @Json(name = "is_premium") val isPremium: Boolean = false,
    val exercises: List<String> = emptyList(),
    @Json(name = "execution_steps") val executionSteps: List<String> = emptyList(),
    val tips: List<String> = emptyList(),
    @Json(name = "common_errors") val commonErrors: List<String> = emptyList(),
    @Json(name = "is_favorite") val isFavorite: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class ActiveRestLogRequest(
    @Json(name = "duration_spent") val durationSpent: Int,
    @Json(name = "feedback_score") val feedbackScore: Int? = null,
)
