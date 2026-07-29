package br.com.nexshape.academia.ui.paciente.documentos

import android.content.Intent
import android.util.Log
import androidx.core.content.FileProvider
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import br.com.nexshape.academia.data.api.ApiClient
import br.com.nexshape.academia.data.api.ApiSuccessResponse
import br.com.nexshape.academia.data.api.PatientDocumentDto
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.flow.update
import kotlinx.coroutines.launch
import java.io.File

data class DocumentosUiState(
    val isLoading: Boolean = false,
    val reports: List<PatientDocumentDto> = emptyList(),
    val prescriptions: List<PatientDocumentDto> = emptyList(),
    val certificates: List<PatientDocumentDto> = emptyList(),
    val error: String? = null,
    val downloadError: String? = null,
)

class PacienteDocumentosViewModel : ViewModel() {
    private val _uiState = MutableStateFlow(DocumentosUiState())
    val uiState: StateFlow<DocumentosUiState> = _uiState.asStateFlow()

    init {
        fetchDocuments()
    }

    fun fetchDocuments() {
        _uiState.update { it.copy(isLoading = true, error = null) }
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val response = ApiClient.api().getPatientMedicalRecords()
                if (response is ApiSuccessResponse) {
                    val data = response.data
                    _uiState.update {
                        it.copy(
                            isLoading = false,
                            reports = data.reports,
                            prescriptions = data.prescriptions,
                            certificates = data.certificates,
                        )
                    }
                } else {
                    _uiState.update { it.copy(isLoading = false, error = "Erro ao carregar documentos") }
                }
            } catch (e: Exception) {
                Log.e("PacienteDocumentos", "Erro fetchDocuments", e)
                _uiState.update { it.copy(isLoading = false, error = e.localizedMessage) }
            }
        }
    }

    fun downloadDocument(context: android.content.Context, type: String, id: Int) {
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val body = ApiClient.api().downloadPatientMedicalRecord(type, id)
                val bytes = body.bytes()
                val filename = "nexshape-$type-$id.pdf"
                val file = File(context.cacheDir, filename)
                file.writeBytes(bytes)

                val uri = FileProvider.getUriForFile(
                    context,
                    "${context.packageName}.fileprovider",
                    file,
                )

                val intent = Intent(Intent.ACTION_VIEW).apply {
                    setDataAndType(uri, "application/pdf")
                    addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
                    addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                }
                context.startActivity(intent)
                _uiState.update { it.copy(downloadError = null) }
            } catch (e: Exception) {
                Log.e("PacienteDocumentos", "Erro downloadDocument", e)
                _uiState.update { it.copy(downloadError = e.localizedMessage ?: "Erro ao baixar documento") }
            }
        }
    }
}
