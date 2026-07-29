package br.com.nexshape.academia.ui.paciente.home

import android.util.Log
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ApiSuccessResponse
import br.com.nexshape.academia.data.api.PatientDashboardSummaryDto
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

data class DashboardUiState(
    val isLoading: Boolean = false,
    val error: String? = null,
    val summary: PatientDashboardSummaryDto? = null
)

class PacienteDashboardViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(DashboardUiState())
    val uiState: StateFlow<DashboardUiState> = _uiState.asStateFlow()

    init {
        fetchDashboard()
    }

    fun fetchDashboard() {
        _uiState.update { it.copy(isLoading = true, error = null) }
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientDashboard()
                if (response is ApiSuccessResponse) {
                    _uiState.update {
                        it.copy(isLoading = false, summary = response.data.summary)
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Erro ao carregar resumo") }
                }
            } catch (e: Exception) {
                Log.e("PacienteDashboard", "Erro fetchDashboard", e)
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage) }
            }
        }
    }
}
