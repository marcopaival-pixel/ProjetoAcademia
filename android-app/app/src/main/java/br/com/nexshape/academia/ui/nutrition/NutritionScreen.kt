package br.com.nexshape.academia.ui.nutrition

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.Button
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.getValue
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.CreateFoodEntryRequest
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.repository.NutritionRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

@Composable
fun NutritionScreen(modifier: Modifier = Modifier) {
    val repository = remember { NutritionRepository() }
    val scope = rememberCoroutineScope()
    var diary by remember { mutableStateOf<NutritionDiaryData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var foodName by remember { mutableStateOf("") }
    var calories by remember { mutableStateOf("") }

    fun reload() {
        loading = true
        scope.launch {
            repository.diary()
                .onSuccess { diary = it }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    Column(modifier = modifier.fillMaxSize().padding(16.dp)) {
        Text("Diário alimentar")
        diary?.totals?.let {
            Text("Calorias: ${it.calories} kcal | P: ${it.proteinG}g C: ${it.carbsG}g G: ${it.fatG}g")
        }

        if (loading) {
            CircularProgressIndicator(modifier = Modifier.padding(top = 16.dp))
        } else {
            LazyColumn(modifier = Modifier.weight(1f).padding(vertical = 8.dp)) {
                items(diary?.entries.orEmpty()) { entry ->
                    Text("• ${entry.foodName} — ${entry.calories} kcal (${entry.mealType})")
                }
            }
        }

        OutlinedTextField(
            value = foodName,
            onValueChange = { foodName = it },
            label = { Text("Alimento") },
            modifier = Modifier.fillMaxWidth(),
        )
        OutlinedTextField(
            value = calories,
            onValueChange = { calories = it.filter { c -> c.isDigit() } },
            label = { Text("Calorias") },
            modifier = Modifier.fillMaxWidth().padding(top = 8.dp),
        )
        Button(
            onClick = {
                val kcal = calories.toIntOrNull() ?: return@Button
                scope.launch {
                    repository.addEntry(
                        CreateFoodEntryRequest(
                            entryDate = LocalDate.now().toString(),
                            foodName = foodName,
                            calories = kcal,
                            mealType = "snack",
                        ),
                    ).onSuccess {
                        foodName = ""
                        calories = ""
                        reload()
                    }
                }
            },
            modifier = Modifier.padding(top = 8.dp),
        ) {
            Text("Adicionar")
        }
    }
}
