package br.com.nexshape.academia.ui.nutrition

import android.Manifest
import android.content.Intent
import android.net.Uri
import android.speech.RecognizerIntent
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.CameraAlt
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.LocalFireDepartment
import androidx.compose.material.icons.filled.Mic
import androidx.compose.material.icons.filled.Restaurant
import androidx.compose.material.icons.filled.Search
import androidx.compose.material.icons.filled.SmartToy
import androidx.compose.material.icons.filled.WaterDrop
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.FilterChip
import androidx.compose.foundation.horizontalScroll
import androidx.compose.foundation.rememberScrollState
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.ModalBottomSheet
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
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.data.api.CreateFoodEntryRequest
import br.com.nexshape.academia.data.api.AnalyzeMealData
import br.com.nexshape.academia.data.api.FoodEntryDto
import br.com.nexshape.academia.data.api.HydrationEntryDto
import br.com.nexshape.academia.data.api.HydrationStatusData
import br.com.nexshape.academia.data.api.MealSuggestionData
import br.com.nexshape.academia.data.api.MealTemplateDto
import br.com.nexshape.academia.data.api.NutritionDiaryData
import br.com.nexshape.academia.data.api.NutritionTargetsDto
import br.com.nexshape.academia.data.api.NutritionTotalsDto
import br.com.nexshape.academia.data.api.WeeklyAuditData
import br.com.nexshape.academia.data.repository.AiCreditsRepository
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
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.MultipartBody
import okhttp3.RequestBody.Companion.asRequestBody
import java.io.File
import java.time.LocalDate
import java.util.Locale

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

private enum class RegisterMode {
    Ai,
    Photo,
    Voice,
    Search,
    Manual,
}

@Composable
fun NutritionScreen(modifier: Modifier = Modifier) {
    val repository = remember { NutritionRepository() }
    val aiCreditsRepository = remember { AiCreditsRepository() }
    val scope = rememberCoroutineScope()
    val context = LocalContext.current
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
    var showFoodForm by remember { mutableStateOf(false) }
    var showRegisterChooser by remember { mutableStateOf(false) }
    var showAiEntryChooser by remember { mutableStateOf(false) }
    var registerMode by remember { mutableStateOf(RegisterMode.Ai) }
    var aiCredits by remember { mutableStateOf<Int?>(null) }
    var goal by remember { mutableStateOf("maintain") }
    var split by remember { mutableStateOf("maintenance") }
    var aiDescription by remember { mutableStateOf("") }
    var analyzingMeal by remember { mutableStateOf(false) }
    var processingPhoto by remember { mutableStateOf(false) }
    var analyzedMeal by remember { mutableStateOf<AnalyzeMealData?>(null) }
    var aiFeedback by remember { mutableStateOf<String?>(null) }
    var nutritionAiLoading by remember { mutableStateOf(false) }
    var mealSuggestion by remember { mutableStateOf<MealSuggestionData?>(null) }
    var weeklyAudit by remember { mutableStateOf<WeeklyAuditData?>(null) }
    var nutritionAiFeedback by remember { mutableStateOf<String?>(null) }

    fun applyAnalyzedMeal(result: AnalyzeMealData, feedback: String) {
        analyzedMeal = result
        foodName = result.foodName
        calories = result.calories.toString()
        protein = result.proteinG.toString()
        carbs = result.carbsG.toString()
        fat = result.fatG.toString()
        amount = (result.amount ?: 1.0).toString()
        unit = result.unit ?: "refeicao"
        mealType = result.mealType
        aiFeedback = feedback
    }

    val photoPicker = rememberLauncherForActivityResult(ActivityResultContracts.GetContent()) { uri: Uri? ->
        uri ?: return@rememberLauncherForActivityResult
        registerMode = RegisterMode.Photo
        showFoodForm = true
        scope.launch {
            processingPhoto = true
            aiFeedback = "Validando foto antes de usar credito IA..."
            runCatching {
                val mimeType = context.contentResolver.getType(uri) ?: "image/jpeg"
                val extension = extensionForNutritionMimeType(mimeType)
                    ?: throw IllegalArgumentException("Envie uma foto em JPG, PNG ou WebP. Nenhum credito foi consumido.")
                val temp = File.createTempFile("nutrition_", ".$extension", context.cacheDir)
                context.contentResolver.openInputStream(uri)?.use { input ->
                    temp.outputStream().use { output -> input.copyTo(output) }
                } ?: throw IllegalArgumentException("Nao foi possivel abrir a foto selecionada.")
                val part = MultipartBody.Part.createFormData(
                    "photo",
                    temp.name,
                    temp.asRequestBody(mimeType.toMediaTypeOrNull()),
                )
                repository.analyzeMealPhoto(part, mealType).getOrThrow()
            }.onSuccess { result ->
                applyAnalyzedMeal(result, "Foto analisada. Confira e registre a refeicao.")
            }.onFailure {
                aiFeedback = friendlyError(it)
            }
            processingPhoto = false
        }
    }

    val speechLauncher = rememberLauncherForActivityResult(ActivityResultContracts.StartActivityForResult()) { result ->
        val spoken = result.data
            ?.getStringArrayListExtra(RecognizerIntent.EXTRA_RESULTS)
            ?.firstOrNull()
            .orEmpty()
        if (spoken.isBlank()) {
            aiFeedback = "Nao foi possivel capturar a voz. Tente novamente ou descreva a refeicao por texto."
            return@rememberLauncherForActivityResult
        }
        registerMode = RegisterMode.Voice
        showFoodForm = true
        aiDescription = spoken
        scope.launch {
            analyzingMeal = true
            aiFeedback = "Voz capturada. Analisando com IA..."
            repository.analyzeMeal(spoken, mealType)
                .onSuccess { applyAnalyzedMeal(it, "Voz analisada. Confira e registre a refeicao.") }
                .onFailure { aiFeedback = friendlyError(it) }
            analyzingMeal = false
        }
    }

    val audioPermissionLauncher = rememberLauncherForActivityResult(ActivityResultContracts.RequestPermission()) { granted ->
        if (granted) {
            speechLauncher.launch(nutritionSpeechIntent())
        } else {
            aiFeedback = "Permissao de microfone negada. Ative o microfone para registrar por voz."
        }
    }

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
            aiCreditsRepository.balance()
                .onSuccess { aiCredits = it.balance }
            loading = false
        }
    }

    LaunchedEffect(Unit) { reload() }

    NexShapeScreen(
        title = "Nutricao",
        subtitle = "Diario alimentar de hoje.",
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
                showFoodForm = showFoodForm,
                showRegisterChooser = showRegisterChooser,
                showAiEntryChooser = showAiEntryChooser,
                registerMode = registerMode,
                aiCredits = aiCredits,
                aiDescription = aiDescription,
                analyzingMeal = analyzingMeal || processingPhoto,
                analyzedMeal = analyzedMeal,
                aiFeedback = aiFeedback,
                nutritionAiLoading = nutritionAiLoading,
                mealSuggestion = mealSuggestion,
                weeklyAudit = weeklyAudit,
                nutritionAiFeedback = nutritionAiFeedback,
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
                onStartRegister = {
                    showRegisterChooser = true
                },
                onDismissRegisterChooser = {
                    showRegisterChooser = false
                },
                onOpenAiEntryChooser = {
                    showRegisterChooser = false
                    showAiEntryChooser = true
                },
                onDismissAiEntryChooser = {
                    showAiEntryChooser = false
                },
                onRegisterMode = {
                    registerMode = it
                    showFoodForm = true
                    showRegisterChooser = false
                    showAiEntryChooser = false
                    analyzedMeal = null
                    aiFeedback = null
                    if (it == RegisterMode.Photo) {
                        photoPicker.launch("image/*")
                    }
                    if (it == RegisterMode.Voice) {
                        audioPermissionLauncher.launch(Manifest.permission.RECORD_AUDIO)
                    }
                },
                onAiDescription = {
                    aiDescription = it
                    aiFeedback = null
                },
                onAnalyzeMeal = {
                    if (aiDescription.isBlank()) return@NutritionContent
                    scope.launch {
                        analyzingMeal = true
                        aiFeedback = null
                        repository.analyzeMeal(aiDescription, mealType)
                            .onSuccess { result ->
                                applyAnalyzedMeal(result, "Analise pronta. Confira e registre a refeicao.")
                            }
                            .onFailure { aiFeedback = friendlyError(it) }
                        analyzingMeal = false
                    }
                },
                onSaveAnalyzedMeal = {
                    val result = analyzedMeal ?: return@NutritionContent
                    saving = true
                    scope.launch {
                        repository.addEntry(
                            CreateFoodEntryRequest(
                                entryDate = LocalDate.now().toString(),
                                foodName = result.foodName,
                                calories = result.calories,
                                mealType = result.mealType,
                                amount = result.amount ?: 1.0,
                                unit = result.unit ?: "refeicao",
                                proteinG = result.proteinG,
                                carbsG = result.carbsG,
                                fatG = result.fatG,
                            ),
                        ).onSuccess {
                            aiFeedback = "Refeicao registrada: ${result.calories} kcal."
                            analyzedMeal = null
                            aiDescription = ""
                            showFoodForm = false
                            reload()
                        }.onFailure { error = friendlyError(it) }
                        saving = false
                    }
                },
                onSuggestMeal = {
                    scope.launch {
                        nutritionAiLoading = true
                        nutritionAiFeedback = null
                        repository.suggestMeal()
                            .onSuccess {
                                mealSuggestion = it
                                weeklyAudit = null
                            }
                            .onFailure { nutritionAiFeedback = friendlyError(it) }
                        aiCreditsRepository.balance().onSuccess { aiCredits = it.balance }
                        nutritionAiLoading = false
                    }
                },
                onWeeklyAudit = {
                    scope.launch {
                        nutritionAiLoading = true
                        nutritionAiFeedback = null
                        repository.weeklyAudit()
                            .onSuccess {
                                weeklyAudit = it
                                mealSuggestion = null
                            }
                            .onFailure { nutritionAiFeedback = friendlyError(it) }
                        aiCreditsRepository.balance().onSuccess { aiCredits = it.balance }
                        nutritionAiLoading = false
                    }
                },
                editing = editingEntryId != null,
                onEdit = { entry ->
                    editingEntryId = entry.id
                    showFoodForm = true
                    registerMode = RegisterMode.Manual
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
                    showFoodForm = false
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
                            showFoodForm = false
                            foodName = ""
                            calories = ""
                            protein = ""
                            carbs = ""
                            fat = ""
                            amount = "1"
                            unit = "g"
                            mealType = "snack"
                            analyzedMeal = null
                            aiDescription = ""
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
    showFoodForm: Boolean,
    showRegisterChooser: Boolean,
    showAiEntryChooser: Boolean,
    registerMode: RegisterMode,
    aiCredits: Int?,
    aiDescription: String,
    analyzingMeal: Boolean,
    analyzedMeal: AnalyzeMealData?,
    aiFeedback: String?,
    nutritionAiLoading: Boolean,
    mealSuggestion: MealSuggestionData?,
    weeklyAudit: WeeklyAuditData?,
    nutritionAiFeedback: String?,
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
    onStartRegister: () -> Unit,
    onDismissRegisterChooser: () -> Unit,
    onOpenAiEntryChooser: () -> Unit,
    onDismissAiEntryChooser: () -> Unit,
    onRegisterMode: (RegisterMode) -> Unit,
    onAiDescription: (String) -> Unit,
    onAnalyzeMeal: () -> Unit,
    onSaveAnalyzedMeal: () -> Unit,
    onSuggestMeal: () -> Unit,
    onWeeklyAudit: () -> Unit,
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
    val entries = diary?.entries.orEmpty()
    if (showRegisterChooser) {
        RegisterChoiceSheet(
            onDismiss = onDismissRegisterChooser,
            aiCredits = aiCredits,
            onAi = onOpenAiEntryChooser,
            onChoose = onRegisterMode,
        )
    }
    if (showAiEntryChooser) {
        AiEntryChoiceSheet(
            onDismiss = onDismissAiEntryChooser,
            aiCredits = aiCredits,
            onChoose = onRegisterMode,
        )
    }

    LazyColumn(verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            MacroSummary(
                totals = diary?.totals ?: NutritionTotalsDto(0, 0.0, 0.0, 0.0),
                targets = diary?.targets,
                onStartRegister = onStartRegister,
            )
        }
        if (showFoodForm || editing) {
            item {
            RegisterMealCard(
                selectedMode = registerMode,
                aiCredits = aiCredits,
                aiDescription = aiDescription,
                analyzingMeal = analyzingMeal,
                analyzedMeal = analyzedMeal,
                aiFeedback = aiFeedback,
                onMode = onRegisterMode,
                onChangeMode = onStartRegister,
                onAiDescription = onAiDescription,
                onAnalyzeMeal = onAnalyzeMeal,
                onSaveAnalyzedMeal = onSaveAnalyzedMeal,
            )
            }
        }
        item {
            NutritionAiCard(
                aiCredits = aiCredits,
                loading = nutritionAiLoading,
                mealSuggestion = mealSuggestion,
                weeklyAudit = weeklyAudit,
                feedback = nutritionAiFeedback,
                onSuggestMeal = onSuggestMeal,
                onWeeklyAudit = onWeeklyAudit,
            )
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
        item {
            MealsOfDayCard(
                entries = entries,
                onStartRegister = onStartRegister,
            )
        }
        if (entries.isNotEmpty()) {
            items(entries, key = { it.id }) { entry ->
                FoodEntryCard(
                    entry = entry,
                    onEdit = { onEdit(entry) },
                    onDelete = { onDelete(entry) },
                )
            }
        }
        if (editing || (showFoodForm && (registerMode == RegisterMode.Manual || registerMode == RegisterMode.Search))) {
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
}

@Composable
private fun NutritionAiCard(
    aiCredits: Int?,
    loading: Boolean,
    mealSuggestion: MealSuggestionData?,
    weeklyAudit: WeeklyAuditData?,
    feedback: String?,
    onSuggestMeal: () -> Unit,
    onWeeklyAudit: () -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Nutricao IA", color = Color.White, fontWeight = FontWeight.Black)
                Text(creditText(aiCredits), color = NexMuted, modifier = Modifier.padding(top = 4.dp))
            }
            Icon(Icons.Default.SmartToy, contentDescription = null, tint = NexNeon)
        }
        Row(horizontalArrangement = Arrangement.spacedBy(8.dp), modifier = Modifier.padding(top = 12.dp)) {
            TextButton(
                onClick = onSuggestMeal,
                enabled = !loading,
                colors = ButtonDefaults.textButtonColors(
                    containerColor = NexNeon.copy(alpha = 0.14f),
                    contentColor = Color.White,
                ),
                shape = RoundedCornerShape(12.dp),
            ) {
                Text(if (loading) "Aguarde..." else "Sugerir refeicao")
            }
            TextButton(
                onClick = onWeeklyAudit,
                enabled = !loading,
                colors = ButtonDefaults.textButtonColors(
                    containerColor = Color.Black.copy(alpha = 0.35f),
                    contentColor = Color.White,
                ),
                shape = RoundedCornerShape(12.dp),
            ) {
                Text(if (loading) "Aguarde..." else "Auditoria semanal")
            }
        }
        mealSuggestion?.let { suggestion ->
            Column(modifier = Modifier.padding(top = 12.dp)) {
                Text("Sugestao de refeicao", color = NexNeon, fontWeight = FontWeight.Bold)
                Text(
                    "Restam ${suggestion.remaining.remainingKcal.toInt()} kcal | P ${suggestion.remaining.remainingProteinG.toInt()}g",
                    color = NexMuted,
                    modifier = Modifier.padding(top = 4.dp),
                )
                Text(suggestion.suggestion, color = Color.White, modifier = Modifier.padding(top = 8.dp))
            }
        }
        weeklyAudit?.let { audit ->
            Column(modifier = Modifier.padding(top = 12.dp)) {
                Text("Auditoria nutricional", color = NexNeon, fontWeight = FontWeight.Bold)
                Text("${audit.daysAnalyzed} dia(s) analisados", color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                Text(audit.audit, color = Color.White, modifier = Modifier.padding(top = 8.dp))
            }
        }
        feedback?.let {
            Text(it, color = NexNeon, modifier = Modifier.padding(top = 10.dp))
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
private fun MacroSummary(
    totals: NutritionTotalsDto,
    targets: NutritionTargetsDto?,
    onStartRegister: () -> Unit,
) {
    val targetCalories = targets?.calories ?: 0
    val remaining = (targetCalories - totals.calories).coerceAtLeast(0)
    val progress = if (targetCalories > 0) {
        (totals.calories.toFloat() / targetCalories.toFloat()).coerceIn(0f, 1f)
    } else {
        0f
    }
    Column {
        NexCard {
            Text("Diario Alimentar", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
            Row(
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 12.dp),
            ) {
                Text("${totals.calories} kcal", color = NexNeon, fontWeight = FontWeight.Bold)
                Text("${totals.proteinG}g P", color = NexMuted, fontWeight = FontWeight.Bold)
                Text("${totals.carbsG}g C", color = NexMuted, fontWeight = FontWeight.Bold)
                Text("${totals.fatG}g G", color = NexMuted, fontWeight = FontWeight.Bold)
            }
            Row(
                horizontalArrangement = Arrangement.SpaceBetween,
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 14.dp),
            ) {
                Text("Meta: ${if (targetCalories > 0) "$targetCalories kcal" else "-"}", color = NexMuted)
                Text("Restam: ${if (targetCalories > 0) "$remaining kcal" else "-"}", color = NexNeon)
            }
            LinearProgressIndicator(
                progress = { progress },
                modifier = Modifier
                    .fillMaxWidth()
                    .padding(top = 10.dp),
                color = NexNeon,
                trackColor = Color.White.copy(alpha = 0.12f),
            )
            NexPrimaryButton(
                text = "+ Registrar refeicao",
                onClick = onStartRegister,
                modifier = Modifier.padding(top = 12.dp),
            )
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun RegisterChoiceSheet(
    onDismiss: () -> Unit,
    aiCredits: Int?,
    onAi: () -> Unit,
    onChoose: (RegisterMode) -> Unit,
) {
    ModalBottomSheet(
        onDismissRequest = onDismiss,
        containerColor = NexPanel,
    ) {
        Column(
            verticalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier
                .fillMaxWidth()
                .padding(20.dp),
        ) {
            Text("Como deseja registrar?", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
            Text("Escolha o fluxo mais rapido para esta refeicao", color = NexMuted, fontWeight = FontWeight.Bold, fontSize = 11.sp)
            RegisterChoiceButton(
                label = "Registrar com IA",
                description = "Digite, envie foto ou fale para identificacao automatica.",
                meta = "Recomendado • ${creditText(aiCredits)}",
                icon = Icons.Default.SmartToy,
                highlighted = true,
                onClick = onAi,
            )
            RegisterChoiceButton(
                label = "Analisar foto da refeicao",
                description = "Tire uma foto do prato para reconhecimento dos alimentos.",
                meta = "Consome 1 credito IA",
                icon = Icons.Default.CameraAlt,
                onClick = { onChoose(RegisterMode.Photo) },
            )
            RegisterChoiceButton(
                label = "Registrar por voz",
                description = "Conte o que voce comeu.",
                meta = "Consome 1 credito IA",
                icon = Icons.Default.Mic,
                onClick = { onChoose(RegisterMode.Voice) },
            )
            RegisterChoiceButton(
                label = "Pesquisar alimento",
                description = "Procure alimentos no banco nutricional.",
                icon = Icons.Default.Search,
                onClick = { onChoose(RegisterMode.Search) },
            )
            RegisterChoiceButton(
                label = "Preencher manualmente",
                description = "Informe todos os dados da refeicao.",
                icon = Icons.Default.Add,
                onClick = { onChoose(RegisterMode.Manual) },
            )
            Spacer(Modifier.height(10.dp))
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
private fun AiEntryChoiceSheet(
    onDismiss: () -> Unit,
    aiCredits: Int?,
    onChoose: (RegisterMode) -> Unit,
) {
    ModalBottomSheet(
        onDismissRequest = onDismiss,
        containerColor = NexPanel,
    ) {
        Column(
            verticalArrangement = Arrangement.spacedBy(8.dp),
            modifier = Modifier
                .fillMaxWidth()
                .padding(20.dp),
        ) {
            Text("Registrar com IA", color = Color.White, fontWeight = FontWeight.Black, fontSize = 20.sp)
            Text(creditText(aiCredits), color = NexMuted, fontWeight = FontWeight.Bold, fontSize = 11.sp)
            RegisterChoiceButton(
                label = "Digitar refeicao",
                description = "Descreva em linguagem natural o que voce comeu.",
                meta = "Consome 1 credito IA",
                icon = Icons.Default.SmartToy,
                highlighted = true,
                onClick = { onChoose(RegisterMode.Ai) },
            )
            RegisterChoiceButton(
                label = "Enviar foto",
                description = "A imagem sera validada antes de usar IA.",
                meta = "Consome 1 credito IA se for alimento",
                icon = Icons.Default.CameraAlt,
                onClick = { onChoose(RegisterMode.Photo) },
            )
            RegisterChoiceButton(
                label = "Falar",
                description = "Transcreva sua refeicao e analise com IA.",
                meta = "Consome 1 credito IA",
                icon = Icons.Default.Mic,
                onClick = { onChoose(RegisterMode.Voice) },
            )
            Spacer(Modifier.height(10.dp))
        }
    }
}

@Composable
private fun RegisterChoiceButton(
    label: String,
    description: String,
    meta: String? = null,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
    highlighted: Boolean = false,
    onClick: () -> Unit,
) {
    TextButton(
        onClick = onClick,
        modifier = Modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        colors = ButtonDefaults.textButtonColors(
            containerColor = if (highlighted) NexNeon.copy(alpha = 0.14f) else Color.Black.copy(alpha = 0.35f),
            contentColor = Color.White,
        ),
    ) {
        Row(
            horizontalArrangement = Arrangement.spacedBy(12.dp),
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 8.dp),
        ) {
            Icon(icon, contentDescription = null, tint = NexNeon)
            Column(modifier = Modifier.weight(1f)) {
                Text(label, color = Color.White, fontWeight = FontWeight.Black)
                Text(description, color = NexMuted, fontSize = 12.sp, modifier = Modifier.padding(top = 2.dp))
                meta?.let {
                    Text(it, color = NexNeon, fontSize = 12.sp, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 2.dp))
                }
            }
        }
    }
}

@Composable
private fun RegisterMealCard(
    selectedMode: RegisterMode,
    aiCredits: Int?,
    aiDescription: String,
    analyzingMeal: Boolean,
    analyzedMeal: AnalyzeMealData?,
    aiFeedback: String?,
    onMode: (RegisterMode) -> Unit,
    onChangeMode: () -> Unit,
    onAiDescription: (String) -> Unit,
    onAnalyzeMeal: () -> Unit,
    onSaveAnalyzedMeal: () -> Unit,
) {
    NexCard {
        Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
            Text(registerModeTitle(selectedMode), color = Color.White, fontWeight = FontWeight.Black)
            TextButton(onClick = onChangeMode) {
                Text("Trocar")
            }
        }
        val hint = when (selectedMode) {
            RegisterMode.Ai -> "Descreva sua refeicao. Ex.: 200g de frango, 100g arroz e salada. ${creditText(aiCredits)}"
            RegisterMode.Photo -> "A foto sera validada antes da IA para evitar gasto de credito quando a imagem nao for alimento."
            RegisterMode.Voice -> "Registro por voz vai transcrever o que voce comeu e seguir o fluxo de IA."
            RegisterMode.Search -> "Pesquise alimentos no banco nutricional e confirme os dados abaixo."
            RegisterMode.Manual -> "Preencha os dados nutricionais abaixo."
        }
        Text(hint, color = NexMuted, modifier = Modifier.padding(top = 10.dp))
        if (selectedMode == RegisterMode.Ai) {
                OutlinedTextField(
                    value = aiDescription,
                    onValueChange = onAiDescription,
                    label = { Text("Descreva sua refeicao") },
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(top = 10.dp),
                    minLines = 3,
                    colors = nexTextFieldColors(),
                )
                NexPrimaryButton(
                    text = if (analyzingMeal) "Analisando..." else "Analisar com IA",
                    onClick = onAnalyzeMeal,
                    enabled = !analyzingMeal && aiDescription.isNotBlank(),
                    modifier = Modifier.padding(top = 10.dp),
                )
        }
        if (selectedMode == RegisterMode.Photo) {
            NexPrimaryButton(
                text = if (analyzingMeal) "Analisando foto..." else "Selecionar foto",
                onClick = { onMode(RegisterMode.Photo) },
                enabled = !analyzingMeal,
                modifier = Modifier.padding(top = 10.dp),
            )
        }
        if (selectedMode == RegisterMode.Voice) {
            NexPrimaryButton(
                text = if (analyzingMeal) "Analisando voz..." else "Falar agora",
                onClick = { onMode(RegisterMode.Voice) },
                enabled = !analyzingMeal,
                modifier = Modifier.padding(top = 10.dp),
            )
        }
        analyzedMeal?.let { meal ->
            Column(modifier = Modifier.padding(top = 10.dp)) {
                Text("Refeicao analisada", color = Color.White, fontWeight = FontWeight.Black)
                Text("${meal.calories} kcal", color = NexNeon, modifier = Modifier.padding(top = 4.dp))
                Text(
                    "P ${meal.proteinG}g  C ${meal.carbsG}g  G ${meal.fatG}g",
                    color = NexMuted,
                    modifier = Modifier.padding(top = 4.dp),
                )
                Text(
                    "Confianca ${(meal.confidence * 100).toInt()}% | ${meal.source ?: "ia"}",
                    color = NexMuted,
                    modifier = Modifier.padding(top = 4.dp),
                )
                meal.notes?.takeIf { it.isNotBlank() }?.let {
                    Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
                }
                NexPrimaryButton(
                    text = "Registrar resultado",
                    onClick = onSaveAnalyzedMeal,
                    modifier = Modifier.padding(top = 10.dp),
                )
            }
        }
        aiFeedback?.let {
            Text(it, color = NexNeon, modifier = Modifier.padding(top = 8.dp))
        }
    }
}

private fun registerModeTitle(mode: RegisterMode): String = when (mode) {
    RegisterMode.Ai -> "Registrar com IA"
    RegisterMode.Photo -> "Analisar foto da refeicao"
    RegisterMode.Voice -> "Registrar por voz"
    RegisterMode.Search -> "Pesquisar alimento"
    RegisterMode.Manual -> "Preencher manualmente"
}

private fun creditText(aiCredits: Int?): String = aiCredits
    ?.let { "Voce possui $it credito${if (it == 1) "" else "s"} disponiveis" }
    ?: "Consome 1 credito IA"

private fun extensionForNutritionMimeType(mimeType: String): String? = when (mimeType.lowercase()) {
    "image/jpeg", "image/jpg" -> "jpg"
    "image/png" -> "png"
    "image/webp" -> "webp"
    else -> null
}

private fun nutritionSpeechIntent(): Intent = Intent(RecognizerIntent.ACTION_RECOGNIZE_SPEECH).apply {
    putExtra(RecognizerIntent.EXTRA_LANGUAGE_MODEL, RecognizerIntent.LANGUAGE_MODEL_FREE_FORM)
    putExtra(RecognizerIntent.EXTRA_LANGUAGE, Locale("pt", "BR"))
    putExtra(RecognizerIntent.EXTRA_PROMPT, "Fale o que voce comeu")
}

@Composable
private fun MealsOfDayCard(
    entries: List<FoodEntryDto>,
    onStartRegister: () -> Unit,
) {
    NexCard {
        Text("Refeicoes do dia", color = Color.White, fontWeight = FontWeight.Black)
        mealOptions.take(4).forEach { (meal, label) ->
            val mealEntries = entries.filter { it.mealType == meal }
            val calories = mealEntries.sumOf { it.calories }
            val protein = mealEntries.sumOf { it.proteinG ?: 0.0 }
            val carbs = mealEntries.sumOf { it.carbsG ?: 0.0 }
            val fat = mealEntries.sumOf { it.fatG ?: 0.0 }
            Column(modifier = Modifier.padding(top = 12.dp)) {
                Row(horizontalArrangement = Arrangement.SpaceBetween, modifier = Modifier.fillMaxWidth()) {
                    Text(label, color = Color.White, fontWeight = FontWeight.Bold)
                    Text("${mealEntries.size} itens | $calories kcal", color = NexNeon)
                }
                Text("P ${protein}g  C ${carbs}g  G ${fat}g", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                if (mealEntries.isEmpty()) {
                    Text("Nenhum alimento registrado.", color = NexMuted, modifier = Modifier.padding(top = 2.dp))
                    TextButton(onClick = onStartRegister) {
                        Text("+ Adicionar alimento")
                    }
                }
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
