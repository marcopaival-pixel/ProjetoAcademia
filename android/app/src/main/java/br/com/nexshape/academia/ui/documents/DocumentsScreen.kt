package br.com.nexshape.academia.ui.documents

import android.content.ContentValues
import android.content.Context
import android.os.Environment
import android.provider.MediaStore
import android.widget.Toast
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Description
import androidx.compose.material.icons.filled.Download
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.material.icons.filled.Refresh
import androidx.compose.material3.IconButton
import br.com.nexshape.academia.data.api.MedicalDocumentDto
import br.com.nexshape.academia.data.api.MedicalDocumentsData
import br.com.nexshape.academia.data.repository.MedicalDocumentsRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexEmptyState
import br.com.nexshape.academia.ui.components.NexErrorState
import br.com.nexshape.academia.ui.components.NexLoadingState
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexShapeScreen
import br.com.nexshape.academia.ui.components.friendlyError
import kotlinx.coroutines.launch

@Composable
fun DocumentsScreen(
    modifier: Modifier = Modifier,
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val repository = remember { MedicalDocumentsRepository() }

    var loading by remember { mutableStateOf(true) }
    var downloadingId by remember { mutableStateOf<Int?>(null) }
    var data by remember { mutableStateOf<MedicalDocumentsData?>(null) }
    var error by remember { mutableStateOf<String?>(null) }

    fun load() {
        loading = true
        error = null
        scope.launch {
            repository.getDocuments()
                .onSuccess {
                    data = it
                    loading = false
                }
                .onFailure {
                    error = friendlyError(it)
                    loading = false
                }
        }
    }

    LaunchedEffect(Unit) {
        load()
    }

    NexShapeScreen(
        title = "Relatórios e documentos",
        modifier = modifier,
        action = {
            IconButton(onClick = ::load) {
                Icon(
                    imageVector = Icons.Default.Refresh,
                    contentDescription = "Recarregar",
                    tint = NexNeon
                )
            }
        }
    ) {
        when {
            loading -> NexLoadingState("Carregando documentos...")
            error != null -> NexErrorState(
                message = error ?: "",
                onRetry = ::load
            )
            data == null || (data!!.reports.isEmpty() && data!!.prescriptions.isEmpty() && data!!.certificates.isEmpty()) -> {
                NexEmptyState(
                    title = "Nenhum documento",
                    message = "Nenhum documento médico encontrado."
                )
            }
            else -> {
                LazyColumn(
                    verticalArrangement = Arrangement.spacedBy(16.dp),
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(horizontal = 16.dp)
                ) {
                    val reports = data!!.reports
                    if (reports.isNotEmpty()) {
                        item {
                            SectionHeader(title = "Laudos clínicos")
                        }
                        items(reports) { document ->
                            DocumentItem(
                                document = document,
                                isDownloading = downloadingId == document.id,
                                onDownload = {
                                    scope.launch {
                                        downloadingId = document.id
                                        repository.downloadReport(document.id)
                                            .onSuccess { body ->
                                                savePdfToDownloads(context, "laudo-${document.id}.pdf", body.bytes())
                                            }
                                            .onFailure {
                                                Toast.makeText(context, "Erro ao baixar laudo: ${it.message}", Toast.LENGTH_LONG).show()
                                            }
                                        downloadingId = null
                                    }
                                }
                            )
                        }
                    }

                    val prescriptions = data!!.prescriptions
                    if (prescriptions.isNotEmpty()) {
                        item {
                            SectionHeader(title = "Receitas médicas")
                        }
                        items(prescriptions) { document ->
                            DocumentItem(
                                document = document,
                                isDownloading = downloadingId == document.id,
                                onDownload = {
                                    scope.launch {
                                        downloadingId = document.id
                                        repository.downloadPrescription(document.id)
                                            .onSuccess { body ->
                                                savePdfToDownloads(context, "receita-${document.id}.pdf", body.bytes())
                                            }
                                            .onFailure {
                                                Toast.makeText(context, "Erro ao baixar receita: ${it.message}", Toast.LENGTH_LONG).show()
                                            }
                                        downloadingId = null
                                    }
                                }
                            )
                        }
                    }

                    val certificates = data!!.certificates
                    if (certificates.isNotEmpty()) {
                        item {
                            SectionHeader(title = "Atestados Medicos")
                        }
                        items(certificates) { document ->
                            DocumentItem(
                                document = document,
                                isDownloading = downloadingId == document.id,
                                onDownload = {
                                    scope.launch {
                                        downloadingId = document.id
                                        repository.downloadCertificate(document.id)
                                            .onSuccess { body ->
                                                savePdfToDownloads(context, "atestado-${document.id}.pdf", body.bytes())
                                            }
                                            .onFailure {
                                                Toast.makeText(context, "Erro ao baixar atestado: ${it.message}", Toast.LENGTH_LONG).show()
                                            }
                                        downloadingId = null
                                    }
                                }
                            )
                        }
                    }
                }
            }
        }
    }
}

@Composable
private fun SectionHeader(title: String) {
    Text(
        text = title,
        color = Color.White,
        fontWeight = FontWeight.Bold,
        fontSize = 16.sp,
        modifier = Modifier.padding(vertical = 4.dp)
    )
}

@Composable
private fun DocumentItem(
    document: MedicalDocumentDto,
    isDownloading: Boolean,
    onDownload: () -> Unit,
) {
    NexCard {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            modifier = Modifier.fillMaxWidth()
        ) {
            Icon(
                imageVector = Icons.Default.Description,
                contentDescription = null,
                tint = NexNeon
            )
            Spacer(modifier = Modifier.width(12.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = document.title,
                    color = Color.White,
                    fontWeight = FontWeight.Bold,
                    fontSize = 14.sp
                )
                if (!document.description.isNullOrBlank()) {
                    Text(
                        text = document.description,
                        color = NexMuted,
                        fontSize = 12.sp,
                        maxLines = 2,
                        modifier = Modifier.padding(top = 2.dp)
                    )
                }
                Text(
                    text = "Emitido por: ${document.professionalName ?: "Profissional"}" +
                            if (document.date != null) " em ${document.date}" else "",
                    color = NexMuted,
                    fontSize = 11.sp,
                    modifier = Modifier.padding(top = 4.dp)
                )
            }
            Spacer(modifier = Modifier.width(8.dp))
            TextButton(
                onClick = onDownload,
                enabled = !isDownloading,
                colors = ButtonDefaults.textButtonColors(
                    contentColor = NexNeon,
                    disabledContentColor = NexMuted
                )
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Icon(
                        imageVector = Icons.Default.Download,
                        contentDescription = "Baixar",
                        modifier = Modifier.width(18.dp)
                    )
                    Spacer(modifier = Modifier.width(4.dp))
                    Text(if (isDownloading) "Baixando..." else "PDF", fontSize = 12.sp)
                }
            }
        }
    }
}

private fun savePdfToDownloads(context: Context, filename: String, bytes: ByteArray) {
    try {
        val resolver = context.contentResolver
        val contentValues = ContentValues().apply {
            put(MediaStore.MediaColumns.DISPLAY_NAME, filename)
            put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
            put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS)
        }
        val uri = resolver.insert(MediaStore.Downloads.EXTERNAL_CONTENT_URI, contentValues)
        if (uri != null) {
            resolver.openOutputStream(uri)?.use { outputStream ->
                outputStream.write(bytes)
            }
            Toast.makeText(context, "$filename salvo em Downloads!", Toast.LENGTH_LONG).show()
        } else {
            Toast.makeText(context, "Erro ao obter URI de Downloads", Toast.LENGTH_SHORT).show()
        }
    } catch (e: Exception) {
        e.printStackTrace()
        Toast.makeText(context, "Falha ao baixar: ${e.message}", Toast.LENGTH_LONG).show()
    }
}
