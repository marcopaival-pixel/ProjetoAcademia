package br.com.nexshape.academia.data.api

import com.squareup.moshi.Json
import com.squareup.moshi.JsonClass

@JsonClass(generateAdapter = true)
data class GamificationData(
    @Json(name = "is_premium_user") val isPremiumUser: Boolean = true,
    val rankings: RankingsData,
    val badges: List<BadgeDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class RankingsData(
    val consistency: List<RankingUserDto> = emptyList(),
    val strength: List<RankingUserDto> = emptyList(),
    val nutrition: List<RankingUserDto> = emptyList(),
    val elite: List<RankingUserDto> = emptyList(),
)

@JsonClass(generateAdapter = true)
data class RankingUserDto(
    val position: Int,
    val name: String,
    val score: Int,
    @Json(name = "is_current_user") val isCurrentUser: Boolean = false,
)

@JsonClass(generateAdapter = true)
data class BadgeDto(
    val code: String,
    val title: String,
    val description: String = "",
    val meta: Int = 0,
    val current: Int = 0,
    @Json(name = "is_unlocked") val isUnlocked: Boolean = false,
    val color: String = "text-gray-500",
)
