package br.com.nexshape.academia.ui.shop

import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.FitnessCenter
import androidx.compose.material.icons.filled.LocalMall
import androidx.compose.material.icons.filled.NotificationsActive
import androidx.compose.material.icons.filled.School
import androidx.compose.material.icons.filled.ShoppingCart
import androidx.compose.material3.Icon
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.components.NexPrimaryButton
import br.com.nexshape.academia.ui.components.NexShapeScreen

@Composable
fun FitnessStoreScreen(modifier: Modifier = Modifier) {
    val context = LocalContext.current

    NexShapeScreen(
        title = "Shopping NexShape",
        subtitle = "Shopping NexShape em preparacao para suplementos, roupas, acessorios, equipamentos, cursos e servicos.",
        modifier = modifier,
    ) {
        Column(
            modifier = Modifier.verticalScroll(rememberScrollState()),
            verticalArrangement = Arrangement.spacedBy(14.dp),
        ) {
            LaunchBanner()

            NexCard {
                Row(
                    verticalAlignment = Alignment.CenterVertically,
                    horizontalArrangement = Arrangement.spacedBy(12.dp),
                ) {
                    Box(
                        modifier = Modifier
                            .size(54.dp)
                            .clip(RoundedCornerShape(18.dp))
                            .background(Color(0x1F10B981)),
                        contentAlignment = Alignment.Center,
                    ) {
                        Icon(
                            imageVector = Icons.Default.LocalMall,
                            contentDescription = null,
                            tint = NexNeon,
                            modifier = Modifier.size(30.dp),
                        )
                    }
                    Column(modifier = Modifier.weight(1f)) {
                        Text(
                            text = "Shopping NexShape",
                            color = Color.White,
                            fontSize = 22.sp,
                            lineHeight = 28.sp,
                            fontWeight = FontWeight.Black,
                        )
                        Text(
                            text = "Em breve voce podera comprar tudo para sua rotina fitness diretamente pelo aplicativo.",
                            color = NexMuted,
                            fontSize = 14.sp,
                            lineHeight = 20.sp,
                            modifier = Modifier.padding(top = 6.dp),
                        )
                    }
                }

                Spacer(Modifier.height(18.dp))

                NexPrimaryButton(
                    text = "Avise-me quando lancar",
                    onClick = {
                        Toast.makeText(
                            context,
                            "Voce sera avisado quando o Shopping NexShape lancar.",
                            Toast.LENGTH_SHORT,
                        ).show()
                    },
                    modifier = Modifier.fillMaxWidth(),
                )
            }

            StorePreviewCard(
                title = "Suplementos e bem-estar",
                description = "Produtos alinhados a treino, nutricao, recuperacao e saude.",
                icon = Icons.Default.FitnessCenter,
            )
            StorePreviewCard(
                title = "Roupas e acessorios",
                description = "Itens para academia, corrida, mobilidade e rotina ativa.",
                icon = Icons.Default.ShoppingCart,
            )
            StorePreviewCard(
                title = "Cursos e servicos",
                description = "Conteudos, consultorias e ofertas de parceiros fitness.",
                icon = Icons.Default.School,
            )
        }
    }
}

@Composable
private fun LaunchBanner() {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(22.dp))
            .background(
                Brush.horizontalGradient(
                    listOf(Color(0xFF10B981), Color(0xFF19F5A6)),
                ),
            )
            .padding(horizontal = 16.dp, vertical = 14.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(10.dp),
    ) {
        Icon(
            imageVector = Icons.Default.NotificationsActive,
            contentDescription = null,
            tint = Color(0xFF04110D),
        )
        Text(
            text = "Lancamento em breve",
            color = Color(0xFF04110D),
            fontSize = 13.sp,
            fontWeight = FontWeight.Black,
        )
    }
}

@Composable
private fun StorePreviewCard(
    title: String,
    description: String,
    icon: androidx.compose.ui.graphics.vector.ImageVector,
) {
    NexCard {
        Row(
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(12.dp),
        ) {
            Box(
                modifier = Modifier
                    .size(42.dp)
                    .clip(RoundedCornerShape(14.dp))
                    .background(Color(0x1F10B981))
                    .border(1.dp, NexNeon.copy(alpha = 0.16f), RoundedCornerShape(14.dp)),
                contentAlignment = Alignment.Center,
            ) {
                Icon(icon, contentDescription = null, tint = NexNeon, modifier = Modifier.size(22.dp))
            }
            Column(modifier = Modifier.weight(1f)) {
                Text(title, color = Color.White, fontWeight = FontWeight.Black)
                Text(
                    description,
                    color = NexMuted,
                    fontSize = 13.sp,
                    lineHeight = 18.sp,
                    modifier = Modifier.padding(top = 3.dp),
                )
            }
        }
    }
}
