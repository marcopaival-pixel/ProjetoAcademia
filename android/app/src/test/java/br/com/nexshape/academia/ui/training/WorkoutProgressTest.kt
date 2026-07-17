package br.com.nexshape.academia.ui.training

import org.junit.Assert.assertEquals
import org.junit.Test

class WorkoutProgressTest {
    @Test
    fun completionPercentHandlesEmptyWorkout() {
        assertEquals(0, workoutCompletionPercent(totalExercises = 0, completedExercises = 3))
    }

    @Test
    fun completionPercentClampsCompletedCount() {
        assertEquals(100, workoutCompletionPercent(totalExercises = 4, completedExercises = 9))
        assertEquals(0, workoutCompletionPercent(totalExercises = 4, completedExercises = -2))
    }

    @Test
    fun completionPercentCalculatesIntegerProgress() {
        assertEquals(50, workoutCompletionPercent(totalExercises = 4, completedExercises = 2))
        assertEquals(33, workoutCompletionPercent(totalExercises = 3, completedExercises = 1))
    }

    @Test
    fun elapsedWorkoutLabelFormatsMinutesAndHours() {
        val now = 1_000_000L

        assertEquals("agora", elapsedWorkoutLabel(startedAt = now - 30_000L, now = now))
        assertEquals("12min", elapsedWorkoutLabel(startedAt = now - 12 * 60_000L, now = now))
        assertEquals("2h 5min", elapsedWorkoutLabel(startedAt = now - 125 * 60_000L, now = now))
    }
}
