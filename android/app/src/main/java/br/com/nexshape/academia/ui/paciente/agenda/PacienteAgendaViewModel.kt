package br.com.nexshape.academia.ui.paciente.agenda

import android.util.Log
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ApiSuccessResponse
import br.com.nexshape.academia.data.api.AppointmentDto
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch

data class AgendaUiState(
    val isLoading: Boolean = false,
    val error: String? = null,
    val appointments: List<AppointmentDto> = emptyList()
)

class PacienteAgendaViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(AgendaUiState())
    val uiState: StateFlow<AgendaUiState> = _uiState.asStateFlow()

    init {
        fetchAppointments()
    }

    fun fetchAppointments() {
        _uiState.update { it.copy(isLoading = true, error = null) }
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientAppointments()
                if (response is ApiSuccessResponse) {
                    _uiState.update {
                        it.copy(isLoading = false, appointments = response.data.appointments)
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Erro ao carregar agenda") }
                }
            } catch (e: Exception) {
                Log.e("PacienteAgenda", "Erro fetchAppointments", e)
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage) }
            }
        }
    }
}
