package br.com.nexshape.academia.data.local

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

data class ActiveWorkoutDraft(
    val sessionId: Int? = null,
    val planId: Int,
    val planName: String,
    val startedAt: Long,
    val completedExerciseIds: Set<Int> = emptySet(),
)

class ActiveWorkoutStore(context: Context) {
    private val prefs = EncryptedSharedPreferences.create(
        context,
        "nexshape_active_workout",
        MasterKey.Builder(context).setKeyScheme(MasterKey.KeyScheme.AES256_GCM).build(),
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM,
    )

    fun get(): ActiveWorkoutDraft? {
        val planId = prefs.getInt(KEY_PLAN_ID, -1)
        val planName = prefs.getString(KEY_PLAN_NAME, null)
        val startedAt = prefs.getLong(KEY_STARTED_AT, 0L)
        if (planId <= 0 || planName.isNullOrBlank() || startedAt <= 0L) return null

        return ActiveWorkoutDraft(
            sessionId = prefs.getInt(KEY_SESSION_ID, -1).takeIf { it > 0 },
            planId = planId,
            planName = planName,
            startedAt = startedAt,
            completedExerciseIds = prefs.getStringSet(KEY_COMPLETED_EXERCISES, emptySet()).orEmpty()
                .mapNotNull { it.toIntOrNull() }
                .toSet(),
        )
    }

    fun start(planId: Int, planName: String, sessionId: Int? = null) {
        prefs.edit()
            .apply {
                if (sessionId != null && sessionId > 0) {
                    putInt(KEY_SESSION_ID, sessionId)
                } else {
                    remove(KEY_SESSION_ID)
                }
            }
            .putInt(KEY_PLAN_ID, planId)
            .putString(KEY_PLAN_NAME, planName)
            .putLong(KEY_STARTED_AT, System.currentTimeMillis())
            .putStringSet(KEY_COMPLETED_EXERCISES, emptySet())
            .apply()
    }

    fun setExerciseCompleted(exerciseId: Int, completed: Boolean) {
        val current = get() ?: return
        val updated = if (completed) {
            current.completedExerciseIds + exerciseId
        } else {
            current.completedExerciseIds - exerciseId
        }

        prefs.edit()
            .putStringSet(KEY_COMPLETED_EXERCISES, updated.map { it.toString() }.toSet())
            .apply()
    }

    fun clear() {
        prefs.edit().clear().apply()
    }

    companion object {
        private const val KEY_PLAN_ID = "plan_id"
        private const val KEY_SESSION_ID = "session_id"
        private const val KEY_PLAN_NAME = "plan_name"
        private const val KEY_STARTED_AT = "started_at"
        private const val KEY_COMPLETED_EXERCISES = "completed_exercises"
    }
}
