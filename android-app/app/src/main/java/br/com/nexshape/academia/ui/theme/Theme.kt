package br.com.nexshape.academia.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.darkColorScheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable
import androidx.compose.ui.graphics.Color

private val Green = Color(0xFF10B981)
private val Dark = Color(0xFF0F172A)

private val LightColors = lightColorScheme(
    primary = Green,
    onPrimary = Color.White,
    secondary = Dark,
    background = Color(0xFFF8FAFC),
    surface = Color.White,
)

@Composable
fun NexShapeTheme(content: @Composable () -> Unit) {
    MaterialTheme(
        colorScheme = LightColors,
        content = content,
    )
}
