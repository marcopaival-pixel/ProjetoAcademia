package br.com.nexshape.academia.ui.nutrition

import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.LocalFireDepartment
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.FilterChip
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.rememberScrollState
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import br.com.nexshape.academia.data.api.CreateFoodEntryRequest
import br.com.nexshape.academia.data.api.FoodEntryDto
import br.com.nexshape.academia.data.api.HydrationEntryDto
import br.com.nexshape.academia.data.api.HydrationStatusData
import br.com.nexshape.academia.data.api.MealTemplateDto
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.api.NutritionTargetsDto
import br.com.nexshape.academia.data.api.NutritionTotalsDto
import br.com.nexshape.academia.data.repository.NutritionRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMetricCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexPanel
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPrimaryButton
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch
import java.time.LocalDate

private val mealOptions = listOf(
    "breakfast" to "Cafe",
    "lunch" to "Almoco",
    "dinner" to "Jantar",
    "snack" to "Lanche",
    "other" to "Outro",
)

private val unitOptions = listOf("g", "ml", "tbsp", "tsp", "cup", "slice", "un")
private val nutritionGoalOptions = listOf(
    "lose" to "Emagrecer",
    "lose_aggressive" to "Cut agressivo",
    "recomp" to "Recompor",
    "maintain" to "Manter",
    "gain" to "Ganhar massa",
    "performance" to "Performance",
)
private val nutritionSplitOptions = listOf(
    "cutting" to "Cutting",
    "maintenance" to "Manutencao",
    "bulking" to "Bulking",
)

@Composable
fun NutritionScreen(modifier: Modifier = Modifier) {
    val repository = remember { NutritionRepository() }
    val scope = rememberCoroutineScope()
    var diary by remember { mutableStateOf<NutritionDiaryData?>(null) }
    var hydration by remember { mutableStateOf<HydrationStatusData?>(null) }
    var mealTemplates by remember { mutableStateOf<List<MealTemplateDto>>(emptyList()) }
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
    var editingEntryId by remember { mutableStateOf<Int?>(null) }
    var goal by remember { mutableStateOf("maintain") }
    var split by remember { mutableStateOf("maintenance") }

    fun reload() {
        scope.launch {
            loading = true
            repository.diary()
                .onSuccess {
                    diary = it
                    goal = it.targets?.goal ?: goal
                    error = null
                }
                .onFailure { error = friendlyError(it) }
            repository.hydrationStatus()
                .onSuccess { hydration = it }
                .onFailure { if (error == null) error = friendlyError(it) }
            repository.mealTemplates()
                .onSuccess { mealTemplates = it }
                .onFailure { if (error == null) error = friendlyError(it) }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    NexShapeScreen(
        title = "Nutricao",
        subtitle = "Diario alimentar de hoje com dados reais da API.",
        modifier = modifier,
    ) {
        when {
            loading -> NexLoadingState("Carregando diario...")
            error != null -> NexErrorState(error.orEmpty(), onRetry = { reload() })
            else -> NutritionContent(
                diary = diary,
                hydration = hydration,
                mealTemplates = mealTemplates,
                saving = saving,
                foodName = foodName,
                calories = calories,
                protein = protein,
                carbs = carbs,
                fat = fat,
                amount = amount,
                unit = unit,
                mealType = mealType,
                goal = goal,
                split = split,
                onFoodName = { foodName = it },
                onCalories = { calories = onlyDecimal(it) },
                onProtein = { protein = onlyDecimal(it) },
                onCarbs = { carbs = onlyDecimal(it) },
                onFat = { fat = onlyDecimal(it) },
                onAmount = { amount = onlyDecimal(it) },
                onUnit = { unit = it },
                onMealType = { mealType = it },
                onGoal = { goal = it },
                onSplit = { split = it },
                editing = editingEntryId != null,
                onEdit = { entry ->
                    editingEntryId = entry.id
                    foodName = entry.foodName
                    calories = entry.calories.toString()
                    protein = (entry.proteinG ?: 0.0).toString()
                    carbs = (entry.carbsG ?: 0.0).toString()
                    fat = (entry.fatG ?: 0.0).toString()
                    amount = (entry.amount ?: 1.0).toString()
                    unit = entry.unit ?: "g"
                    mealType = entry.mealType
                },
                onDelete = { entry ->
                    scope.launch {
                        repository.deleteEntry(entry.id)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
                onAddWater = { amountMl ->
                    scope.launch {
                        repository.addWater(amountMl)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
                onDeleteWater = { entry ->
                    scope.launch {
                        repository.deleteWaterEntry(entry.id)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
                onApplyMealTemplate = { template ->
                    scope.launch {
                        repository.applyMealTemplate(template.id)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                    }
                },
                onUpdateGoal = {
                    scope.launch {
                        saving = true
                        repository.updateGoal(goal, split)
                            .onSuccess { reload() }
                            .onFailure { error = friendlyError(it) }
                        saving = false
                    }
                },
                onCancelEdit = {
                    editingEntryId = null
                    foodName = ""
                    calories = ""
                    protein = ""
                    carbs = ""
                    fat = ""
                    amount = "1"
                    unit = "g"
                    mealType = "snack"
                },
                onSave = {
                    val kcal = calories.toIntOrNull() ?: return@NutritionContent
                    if (foodName.isBlank()) return@NutritionContent

                    val request = CreateFoodEntryRequest(
                        entryDate = LocalDate.now().toString(),
                        foodName = foodName.trim(),
                        calories = kcal,
                        mealType = mealType,
                        amount = amount.toDoubleOrNull() ?: 1.0,
                        unit = unit.ifBlank { "g" },
                        proteinG = protein.toDoubleOrNull() ?: 0.0,
                        carbsG = carbs.toDoubleOrNull() ?: 0.0,
                        fatG = fat.toDoubleOrNull() ?: 0.0,
                    )
                    saving = true
                    scope.launch {
                        val result = editingEntryId
                            ?.let { repository.updateEntry(it, request) }
                            ?: repository.addEntry(request)

                        result.onSuccess {
                            editingEntryId = null
                            foodName = ""
                            calories = ""
                            protein = ""
                            carbs = ""
                            fat = ""
                            amount = "1"
                            unit = "g"
                            mealType = "snack"
                            reload()
                        }.onFailure { error = friendlyError(it) }
                        saving = false
                    }
                },
            )
        }
    }
}

@Composable
private fun NutritionContent(
    diary: NutritionDiaryData?,
    hydration: HydrationStatusData?,
    mealTemplates: List<MealTemplateDto>,
    saving: Boolean,
    foodName: String,
    calories: String,
    protein: String,
    carbs: String,
    fat: String,
    amount: String,
    unit: String,
    mealType: String,
    goal: String,
    split: String,
    onFoodName: (String) -> Unit,
    onCalories: (String) -> Unit,
    onProtein: (String) -> Unit,
    onCarbs: (String) -> Unit,
    onFat: (String) -> Unit,
    onAmount: (String) -> Unit,
    onUnit: (String) -> Unit,
    onMealType: (String) -> Unit,
    onGoal: (String) -> Unit,
    onSplit: (String) -> Unit,
    editing: Boolean,
    onEdit: (FoodEntryDto) -> Unit,
    onDelete: (FoodEntryDto) -> Unit,
    onAddWater: (Int) -> Unit,
    onDeleteWater: (HydrationEntryDto) -> Unit,
    onApplyMealTemplate: (MealTemplateDto) -> Unit,
    onUpdateGoal: () -> Unit,
    onCancelEdit: () -> Unit,
    onSave: () -> Unit,
) {
    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            MacroSummary(diary?.totals ?: NutritionTotalsDto(0, 0.0, 0.0, 0.0))
        }
        item {
            NutritionGoalCard(
                targets = diary?.targets,
                goal = goal,
                split = split,
                saving = saving,
                onGoal = onGoal,
                onSplit = onSplit,
                onUpdateGoal = onUpdateGoal,
            )
        }
        item {
            HydrationCard(
                hydration = hydration,
                onAddWater = onAddWater,
                onDeleteWater = onDeleteWater,
            )
        }
        item {
            MealTemplatesCard(
                templates = mealTemplates,
                onApply = onApplyMealTemplate,
            )
        }
        if (diary?.entries.orEmpty().isEmpty()) {
            item {
                NexEmptyState("Nenhuma refeicao registrada", "Adicione um alimento consumido hoje para acompanhar seus macros.")
            }
        } else {
            items(diary?.entries.orEmpty(), key = { it.id }) { entry ->
                FoodEntryCard(
                    entry = entry,
                    onEdit = { onEdit(entry) },
                    onDelete = { onDelete(entry) },
                )
            }
        }
        item {
            FoodForm(
                saving = saving,
                foodName = foodName,
                calories = calories,
                protein = protein,
                carbs = carbs,
                fat = fat,
                amount = amount,
                unit = unit,
                mealType = mealType,
                onFoodName = onFoodName,
                onCalories = onCalories,
                onProtein = onProtein,
                onCarbs = onCarbs,
                onFat = onFat,
                onAmount = onAmount,
                onUnit = onUnit,
                onMealType = onMealType,
                editing = editing,
                onCancelEdit = onCancelEdit,
                onSave = onSave,
            )
        }
    }
}

@Composable
private fun MealTemplatesCard(
    templates: List<MealTemplateDto>,
    onApply: (MealTemplateDto) -> Unit,
) {
    NexCard {
        Text("Plano alimentar", color = Color.White, fontWeight = FontWeight.Black)
        if (templates.isEmpty()) {
            Text(
                "Nenhum plano alimentar prescrito encontrado.",
                color = NexMuted,
                modifier = Modifier.padding(top = 6.dp),
            )
            return@NexCard
        }

        templates.take(3).forEach { template ->
            Column(modifier = Modifier.padding(top = 12.dp)) {
                Text(template.name, color = NexNeon, fontWeight = FontWeight.Bold)
                Text("${template.items.size} alimento(s) prescritos", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                template.items.take(4).forEach { item ->
                    Text(
                        "${mealLabel(item.mealType)}: ${item.foodName} (${item.calories} kcal)",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 3.dp),
                    )
                }
                TextButton(onClick = { onApply(template) }) {
                    Text("Aplicar ao diario de hoje")
                }
            }
        }
    }
}

@Composable
private fun HydrationCard(
    hydration: HydrationStatusData?,
    onAddWater: (Int) -> Unit,
    onDeleteWater: (HydrationEntryDto) -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Hidratacao", color = Color.White, fontWeight = FontWeight.Black)
                val consumed = hydration?.consumedMl ?: 0
                val target = hydration?.targetMl ?: 0
                Text("$consumed ml / $target ml", color = NexNeon, modifier = Modifier.padding(top = 4.dp))
                Text("${hydration?.percentage ?: 0}% da meta", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                hydration?.let {
                    Text(hydrationStatusLabel(it.status, it.expectedNowMl), color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                    Text(
                        if (it.isAuto) "Meta calculada automaticamente" else "Meta definida manualmente",
                        color = NexMuted,
                        modifier = Modifier.padding(top = 2.dp),
                    )
                }
            }
            Icon(Icons.Default.WaterDrop, contentDescription = null, tint = NexNeon)
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
            listOf(250, 350, 500).forEach { amount ->
                FilterChip(
                    selected = false,
                    onClick = { onAddWater(amount) },
                    label = { Text("+${amount}ml") },
                )
            }
        }
        hydration?.entries?.take(3)?.forEach { entry ->
            Row(
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 8.dp),
            ) {
                Text("${entry.amountMl} ml", color = NexMuted)
                IconButton(onClick = { onDeleteWater(entry) }) {
                    Icon(Icons.Default.Delete, contentDescription = "Excluir agua", tint = Color.White)
                }
            }
        }
    }
}

@Composable
private fun MacroSummary(totals: NutritionTotalsDto) {
    Column {
        Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.fillMaxWidth()) {
            NexMetricCard(
                title = "Hoje",
                value = "${totals.calories} kcal",
                icon = Icons.Default.LocalFireDepartment,
                modifier = Modifier.weight(1f),
            )
            NexMetricCard(
                title = "Entradas",
                value = "Diario",
                icon = Icons.Default.Restaurant,
                modifier = Modifier.weight(1f),
            )
        }
        NexCard(modifier = Modifier.padding(top = 12.dp)) {
            Text("Macros consumidos", color = Color.White, fontWeight = FontWeight.Black)
            Row(
                modifier = Modifier.padding(top = 10.dp),
                horizontalArrangement = Arrangement.spacedBy(14.dp),
            ) {
                Text("P ${totals.proteinG}g", color = NexMuted)
                Text("C ${totals.carbsG}g", color = NexMuted)
                Text("G ${totals.fatG}g", color = NexMuted)
            }
        }
    }
}

@Composable
private fun NutritionGoalCard(
    targets: NutritionTargetsDto?,
    goal: String,
    split: String,
    saving: Boolean,
    onGoal: (String) -> Unit,
    onSplit: (String) -> Unit,
    onUpdateGoal: () -> Unit,
) {
    NexCard {
        Text("Estrategia nutricional", color = Color.White, fontWeight = FontWeight.Black)
        Text(
            "Meta atual: ${nutritionGoalLabel(targets?.goal ?: goal)}",
            color = NexNeon,
            modifier = Modifier.padding(top = 4.dp),
        )
        Text(
            "${targets?.calories ?: "-"} kcal  P ${targets?.proteinG ?: "-"}g  C ${targets?.carbsG ?: "-"}g  G ${targets?.fatG ?: "-"}g",
            color = NexMuted,
            modifier = Modifier.padding(top = 4.dp),
        )
        Row(
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier
                .padding(top = 12.dp)
                .horizontalScroll(rememberScrollState()),
        ) {
            nutritionGoalOptions.forEach { (value, label) ->
                FilterChip(
                    selected = goal == value,
                    onClick = { onGoal(value) },
                    label = { Text(label) },
                )
            }
        }
        Row(
            horizontalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier
                .padding(top = 8.dp)
                .horizontalScroll(rememberScrollState()),
        ) {
            nutritionSplitOptions.forEach { (value, label) ->
                FilterChip(
                    selected = split == value,
                    onClick = { onSplit(value) },
                    label = { Text(label) },
                )
            }
        }
        NexPrimaryButton(
            text = if (saving) "Atualizando..." else "Atualizar estrategia",
            onClick = onUpdateGoal,
            enabled = !saving,
            modifier = Modifier.padding(top = 12.dp),
        )
    }
}

@Composable
private fun FoodEntryCard(
    entry: FoodEntryDto,
    onEdit: () -> Unit,
    onDelete: () -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text(entry.foodName, color = Color.White, fontWeight = FontWeight.Black)
                Text("${entry.calories} kcal - ${mealLabel(entry.mealType)}", color = NexNeon, modifier = Modifier.padding(top = 4.dp))
            }
            Row {
                IconButton(onClick = onEdit) {
                    Icon(Icons.Default.Edit, contentDescription = "Editar", tint = NexNeon)
                }
                IconButton(onClick = onDelete) {
                    Icon(Icons.Default.Delete, contentDescription = "Excluir", tint = Color.White)
                }
            }
        }
        Text(
            "P ${entry.proteinG ?: 0.0}g  C ${entry.carbsG ?: 0.0}g  G ${entry.fatG ?: 0.0}g",
            color = NexMuted,
            modifier = Modifier.padding(top = 4.dp),
        )
        Text("${entry.amount ?: 1.0} ${entry.unit ?: "g"}", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
    }
}

@Composable
private fun FoodForm(
    saving: Boolean,
    foodName: String,
    calories: String,
    protein: String,
    carbs: String,
    fat: String,
    amount: String,
    unit: String,
    mealType: String,
    onFoodName: (String) -> Unit,
    onCalories: (String) -> Unit,
    onProtein: (String) -> Unit,
    onCarbs: (String) -> Unit,
    onFat: (String) -> Unit,
    onAmount: (String) -> Unit,
    onUnit: (String) -> Unit,
    onMealType: (String) -> Unit,
    editing: Boolean,
    onCancelEdit: () -> Unit,
    onSave: () -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Text(
                if (editing) "Editar refeicao" else "Adicionar refeicao",
                color = Color.White,
                fontWeight = FontWeight.Black,
            )
            if (editing) {
                TextButton(onClick = onCancelEdit) { Text("Cancelar") }
            }
        }
        Spacer(Modifier.height(12.dp))
        OutlinedTextField(
            value = foodName,
            onValueChange = onFoodName,
            label = { Text("Alimento") },
            modifier = Modifier.fillMaxWidth(),
            singleLine = true,
            colors = nexTextFieldColors()
        )
        Row(modifier = Modifier.padding(top = 8.dp), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            NumberField("Kcal", calories, onCalories, Modifier.weight(1f))
            NumberField("Proteina", protein, onProtein, Modifier.weight(1f))
        }
        Row(modifier = Modifier.padding(top = 8.dp), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            NumberField("Carbo", carbs, onCarbs, Modifier.weight(1f))
            NumberField("Gordura", fat, onFat, Modifier.weight(1f))
        }
        Row(modifier = Modifier.padding(top = 8.dp), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            NumberField("Qtd.", amount, onAmount, Modifier.weight(1f))
            Column(modifier = Modifier.weight(1f)) {
                Text("Unidade", color = NexMuted, fontWeight = FontWeight.Bold)
                Row(
                    modifier = Modifier
                        .padding(top = 6.dp)
                        .horizontalScroll(rememberScrollState()),
                    horizontalArrangement = Arrangement.spacedBy(8.dp),
                ) {
                    unitOptions.forEach { option ->
                        FilterChip(
                            selected = unit == option,
                            onClick = { onUnit(option) },
                            label = { Text(option) },
                        )
                    }
                }
            }
        }
        Row(
            modifier = Modifier
                .padding(top = 8.dp)
                .horizontalScroll(rememberScrollState()),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            mealOptions.forEach { (value, label) ->
                FilterChip(selected = mealType == value, onClick = { onMealType(value) }, label = { Text(label) })
            }
        }
        NexPrimaryButton(
            text = if (saving) "Salvando..." else if (editing) "Salvar alteracao" else "Adicionar",
            onClick = onSave,
            enabled = !saving && foodName.isNotBlank() && calories.toIntOrNull() != null,
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 12.dp),
        )
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
        colors = nexTextFieldColors(),
    )
}

@Composable
private fun nexTextFieldColors() = OutlinedTextFieldDefaults.colors(
    focusedTextColor = Color.White,
    unfocusedTextColor = Color.White,
    focusedBorderColor = NexNeon,
    unfocusedBorderColor = Color.White.copy(alpha = 0.28f),
    focusedLabelColor = NexNeon,
    unfocusedLabelColor = NexMuted,
    cursorColor = NexNeon,
    focusedContainerColor = NexPanel,
    unfocusedContainerColor = NexPanel,
)

private fun mealLabel(value: String): String = mealOptions.firstOrNull { it.first == value }?.second ?: value

private fun nutritionGoalLabel(value: String): String =
    nutritionGoalOptions.firstOrNull { it.first == value }?.second ?: value

private fun hydrationStatusLabel(status: String, expectedNowMl: Int): String = when (status) {
    "ahead" -> "Acima do ritmo esperado para agora."
    "behind" -> "Abaixo do ritmo esperado: objetivo parcial ${expectedNowMl} ml."
    else -> if (expectedNowMl > 0) "Dentro do ritmo: objetivo parcial ${expectedNowMl} ml." else "Dentro do ritmo."
}

private fun onlyDecimal(value: String): String =
    value.filterIndexed { index, char -> char.isDigit() || (char == '.' && value.indexOf('.') == index) }
