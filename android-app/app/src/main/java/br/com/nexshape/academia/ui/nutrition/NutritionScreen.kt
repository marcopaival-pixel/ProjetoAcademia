package br.com.nexshape.academia.ui.nutrition

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.Button
import androidx.compose.material3.Card
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.FilterChip
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.CreateFoodEntryRequest
import br.com.nexshape.academia.data.api.FoodEntryDto
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.api.NutritionTotalsDto
import br.com.nexshape.academia.data.repository.NutritionRepository
import kotlinx.coroutines.launch
import java.time.LocalDate

private val mealOptions = listOf(
    "breakfast" to "Cafe",
    "lunch" to "Almoco",
    "dinner" to "Jantar",
    "snack" to "Lanche",
    "other" to "Outro",
)

@Composable
fun NutritionScreen(modifier: Modifier = Modifier) {
    val repository = remember { NutritionRepository() }
    val scope = rememberCoroutineScope()
    var diary by remember { mutableStateOf<NutritionDiaryData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var saving by remember { mutableStateOf(false) }
    var error by remember { mutableStateOf<String?>(null) }
    var foodName by remember { mutableStateOf("") }
    var calories by remember { mutableStateOf("") }
    var protein by remember { mutableStateOf("") }
    var carbs by remember { mutableStateOf("") }
    var fat by remember { mutableStateOf("") }
    var amount by remember { mutableStateOf("1") }
    var unit by remember { mutableStateOf("g") }
    var mealType by remember { mutableStateOf("snack") }

    fun reload() {
        loading = true
        scope.launch {
            repository.diary()
                .onSuccess {
                    diary = it
                    error = null
                }
                .onFailure { error = it.message }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    Column(
        modifier = modifier
            .fillMaxSize()
            .padding(16.dp),
    ) {
        Text("Diario alimentar", style = MaterialTheme.typography.headlineSmall)
        Text(
            "Hoje",
            style = MaterialTheme.typography.bodySmall,
            color = MaterialTheme.colorScheme.onSurfaceVariant,
            modifier = Modifier.padding(top = 2.dp),
        )

        diary?.totals?.let {
            MacroSummary(totals = it, modifier = Modifier.padding(top = 12.dp))
        }

        error?.let {
            Text(
                it,
                color = MaterialTheme.colorScheme.error,
                modifier = Modifier.padding(top = 8.dp),
            )
        }

        if (loading) {
            CircularProgressIndicator(modifier = Modifier.padding(top = 16.dp))
        } else {
            LazyColumn(
                modifier = Modifier
                    .weight(1f)
                    .padding(vertical = 12.dp),
                verticalArrangement = Arrangement.spacedBy(8.dp),
            ) {
                items(diary?.entries.orEmpty(), key = { it.id }) { entry ->
                    FoodEntryCard(entry)
                }
            }
        }

        Text("Adicionar refeicao", style = MaterialTheme.typography.titleMedium)
        OutlinedTextField(
            value = foodName,
            onValueChange = { foodName = it },
            label = { Text("Alimento") },
            modifier = Modifier.fillMaxWidth(),
            singleLine = true,
        )

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 8.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            NumberField("Kcal", calories, { calories = onlyDecimal(it) }, Modifier.weight(1f))
            NumberField("Proteina", protein, { protein = onlyDecimal(it) }, Modifier.weight(1f))
        }
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 8.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            NumberField("Carbo", carbs, { carbs = onlyDecimal(it) }, Modifier.weight(1f))
            NumberField("Gordura", fat, { fat = onlyDecimal(it) }, Modifier.weight(1f))
        }
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 8.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            NumberField("Qtd.", amount, { amount = onlyDecimal(it) }, Modifier.weight(1f))
            OutlinedTextField(
                value = unit,
                onValueChange = { unit = it.take(8) },
                label = { Text("Unidade") },
                modifier = Modifier.weight(1f),
                singleLine = true,
            )
        }

        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 8.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp),
        ) {
            mealOptions.forEach { (value, label) ->
                FilterChip(
                    selected = mealType == value,
                    onClick = { mealType = value },
                    label = { Text(label) },
                )
            }
        }

        Button(
            onClick = {
                val kcal = calories.toIntOrNull() ?: return@Button
                if (foodName.isBlank()) return@Button

                saving = true
                scope.launch {
                    repository.addEntry(
                        CreateFoodEntryRequest(
                            entryDate = LocalDate.now().toString(),
                            foodName = foodName.trim(),
                            calories = kcal,
                            mealType = mealType,
                            amount = amount.toDoubleOrNull() ?: 1.0,
                            unit = unit.ifBlank { "g" },
                            proteinG = protein.toDoubleOrNull() ?: 0.0,
                            carbsG = carbs.toDoubleOrNull() ?: 0.0,
                            fatG = fat.toDoubleOrNull() ?: 0.0,
                        ),
                    ).onSuccess {
                        foodName = ""
                        calories = ""
                        protein = ""
                        carbs = ""
                        fat = ""
                        amount = "1"
                        unit = "g"
                        mealType = "snack"
                        reload()
                    }.onFailure { error = it.message }
                    saving = false
                }
            },
            enabled = !saving && foodName.isNotBlank() && calories.toIntOrNull() != null,
            modifier = Modifier.padding(top = 10.dp),
        ) {
            Text(if (saving) "Salvando..." else "Adicionar")
        }
    }
}

@Composable
private fun MacroSummary(totals: NutritionTotalsDto, modifier: Modifier = Modifier) {
    Card(modifier = modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(14.dp)) {
            Text("${totals.calories} kcal", style = MaterialTheme.typography.titleLarge)
            Row(
                modifier = Modifier.padding(top = 8.dp),
                horizontalArrangement = Arrangement.spacedBy(12.dp),
            ) {
                Text("P ${totals.proteinG}g")
                Text("C ${totals.carbsG}g")
                Text("G ${totals.fatG}g")
            }
        }
    }
}

@Composable
private fun FoodEntryCard(entry: FoodEntryDto) {
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(12.dp)) {
            Text(entry.foodName, style = MaterialTheme.typography.titleSmall)
            Text("${entry.calories} kcal - ${entry.mealType}", style = MaterialTheme.typography.bodySmall)
            Text(
                "P ${entry.proteinG ?: 0.0}g  C ${entry.carbsG ?: 0.0}g  G ${entry.fatG ?: 0.0}g",
                style = MaterialTheme.typography.bodySmall,
            )
        }
    }
}

@Composable
private fun NumberField(
    label: String,
    value: String,
    onValueChange: (String) -> Unit,
    modifier: Modifier = Modifier,
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier,
        singleLine = true,
        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Decimal),
    )
}

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char ->
        char.isDigit() || (char == '.' && value.indexOf('.') == index)
    }
