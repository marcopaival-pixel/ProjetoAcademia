package br.com.nexshape.academia.ui.paciente.documentos

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Download
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import br.com.nexshape.academia.data.api.PatientDocumentDto
import br.com.nexshape.academia.ui.components.NexNeon

@Composable
fun PacienteDocumentosScreen(
    modifier: Modifier = Modifier,
    viewModel: PacienteDocumentosViewModel = viewModel()
) {
    val state by viewModel.uiState.collectAsState()
    val context = LocalContext.current

    val onDownload: (String, Int) -> Unit = { type, id ->
        viewModel.downloadDocument(context, type, id)
    }

    Column(
        modifier = modifier
            .fillMaxSize()
            .background(Color(0xFF080C10))
            .padding(16.dp)
    ) {
        Text(text = "Prontuário e Documentos", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Bold)
        Spacer(modifier = Modifier.height(16.dp))

        if (state.isLoading) {
            CircularProgressIndicator(color = NexNeon, modifier = Modifier.align(Alignment.CenterHorizontally))
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(16.dp)) {
                item {
                    DocumentSectionTitle("Laudos e Evoluções")
                }
                if (state.reports.isEmpty()) {
                    item { EmptyStateText("Nenhum laudo encontrado.") }
                } else {
                    items(state.reports) { doc ->
                        DocumentItem(doc, onClick = { onDownload("report", doc.id) })
                    }
                }

                item {
                    DocumentSectionTitle("Receitas")
                }
                if (state.prescriptions.isEmpty()) {
                    item { EmptyStateText("Nenhuma receita encontrada.") }
                } else {
                    items(state.prescriptions) { doc ->
                        DocumentItem(doc, onClick = { onDownload("prescription", doc.id) })
                    }
                }

                item {
                    DocumentSectionTitle("Atestados")
                }
                if (state.certificates.isEmpty()) {
                    item { EmptyStateText("Nenhum atestado encontrado.") }
                } else {
                    items(state.certificates) { doc ->
                        DocumentItem(doc, onClick = { onDownload("certificate", doc.id) })
                    }
                }
            }
        }
    }
}

@Composable
fun DocumentSectionTitle(title: String) {
    Text(
        text = title,
        color = NexNeon,
        fontSize = 18.sp,
        fontWeight = FontWeight.SemiBold,
        modifier = Modifier.padding(top = 8.dp, bottom = 4.dp)
    )
}

@Composable
fun EmptyStateText(text: String) {
    Text(text = text, color = Color.Gray, fontSize = 14.sp)
}

@Composable
fun DocumentItem(doc: PatientDocumentDto, onClick: () -> Unit) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable { onClick() },
        colors = CardDefaults.cardColors(containerColor = Color(0xFF131A24)),
        shape = RoundedCornerShape(12.dp)
    ) {
        Row(
            modifier = Modifier
                .padding(16.dp)
                .fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.SpaceBetween
        ) {
            Column {
                Text(text = doc.title, color = Color.White, fontSize = 16.sp, fontWeight = FontWeight.Medium)
                if (doc.date != null) {
                    Text(text = doc.date, color = Color.Gray, fontSize = 14.sp)
                }
            }
            Icon(Icons.Default.Download, contentDescription = "Download", tint = NexNeon)
        }
    }
}
