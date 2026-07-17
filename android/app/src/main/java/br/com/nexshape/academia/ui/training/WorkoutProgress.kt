package br.com.nexshape.academia.ui.training

import java.util.concurrent.TimeUnit

fun workoutCompletionPercent(totalExercises: Int, completedExercises: Int): Int {
    if (totalExercises <= 0) return 0

    return ((completedExercises.coerceIn(0, totalExercises).toDouble() / totalExercises) * 100).toInt()
}

fun elapsedWorkoutLabel(startedAt: Long, now: Long = System.currentTimeMillis()): String {
    val minutes = TimeUnit.MILLISECONDS.toMinutes(now - startedAt).coerceAtLeast(0)

    return when {
        minutes < 1 -> "agora"
        minutes < 60 -> "${minutes}min"
        else -> "${minutes / 60}h ${minutes % 60}min"
    }
}
