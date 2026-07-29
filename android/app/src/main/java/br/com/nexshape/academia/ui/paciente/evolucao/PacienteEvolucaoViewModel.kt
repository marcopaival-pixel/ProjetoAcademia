package br.com.nexshape.academia.ui.paciente.evolucao

import android.util.Log
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ApiSuccessResponse
import br.com.nexshape.academia.data.api.EvolutionAssessmentDto
import br.com.nexshape.academia.data.api.EvolutionChartDataDto
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

data class EvolucaoUiState(
    val isLoading: Boolean = false,
    val error: String? = null,
    val latest: EvolutionAssessmentDto? = null,
    val chartData: EvolutionChartDataDto? = null,
    val assessments: List<EvolutionAssessmentDto> = emptyList()
)

class PacienteEvolucaoViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(EvolucaoUiState())
    val uiState: StateFlow<EvolucaoUiState> = _uiState.asStateFlow()

    init {
        fetchEvolution()
    }

    private fun fetchEvolution() {
        _uiState.update { it.copy(isLoading = true, error = null) }
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientEvolution()
                if (response is ApiSuccessResponse) {
                    val data = response.data
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            latest = data.latest,
                            chartData = data.chartData,
                            assessments = data.assessments
                        )
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Erro ao carregar dados de evolução") }
                }
            } catch (e: Exception) {
                Log.e("PacienteEvolucao", "Erro fetchEvolution", e)
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage) }
            }
        }
    }
}
