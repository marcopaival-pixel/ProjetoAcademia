package br.com.nexshape.academia.ui.subscription

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.height
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.DisposableEffect
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleEventObserver
import br.com.nexshape.academia.data.api.PaymentStatusDto
import br.com.nexshape.academia.data.api.SubscriptionPlanDto
import br.com.nexshape.academia.data.repository.SubscriptionRepository
import br.com.nexshape.academia.ui.components.NexCard
import br.com.nexshape.academia.ui.components.NexGreen
import br.com.nexshape.academia.ui.components.NexMuted
import br.com.nexshape.academia.ui.components.NexNeon
import br.com.nexshape.academia.ui.navigation.SubscriptionDeepLink
import br.com.nexshape.academia.ui.navigation.subscriptionStatusMessage
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.Locale

@Composable
fun SubscriptionSection(
    modifier: Modifier = Modifier,
    isPremium: Boolean = false,
) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val lifecycleOwner = LocalLifecycleOwner.current
    val repository = remember { SubscriptionRepository() }
    var paymentStatus by remember { mutableStateOf<PaymentStatusDto?>(null) }
    var plans by remember { mutableStateOf<List<SubscriptionPlanDto>>(emptyList()) }
    var activeSubscription by remember { mutableStateOf<br.com.nexshape.academia.data.api.CurrentSubscriptionData?>(null) }
    var loading by remember { mutableStateOf(true) }
    var checkoutLoading by remember { mutableStateOf(false) }
    var cancelLoading by remember { mutableStateOf(false) }
    var message by remember { mutableStateOf<String?>(null) }
    var error by remember { mutableStateOf<String?>(null) }
    var awaitingPayment by remember { mutableStateOf(false) }
    var webReturnUrl by remember { mutableStateOf<String?>(null) }

    suspend fun reload() {
        loading = true
        error = null
        repository.paymentStatus()
            .onSuccess { paymentStatus = it }
            .onFailure { error = it.message }
        repository.plans()
            .onSuccess { plans = it }
            .onFailure { if (error == null) error = it.message }
        repository.current()
            .onSuccess { activeSubscription = it }
            .onFailure { if (error == null) error = it.message }
        loading = false
    }

    LaunchedEffect(Unit) { reload() }

    val deepLinkStatus = SubscriptionDeepLink.lastStatus
    LaunchedEffect(deepLinkStatus) {
        val status = SubscriptionDeepLink.consume() ?: return@LaunchedEffect
        message = subscriptionStatusMessage(status)
        awaitingPayment = status == "pending"
        reload()
    }

    DisposableEffect(lifecycleOwner, awaitingPayment) {
        if (!awaitingPayment) {
            return@DisposableEffect onDispose { }
        }
        val observer = LifecycleEventObserver { _, event ->
            if (event == Lifecycle.Event.ON_RESUME) {
                scope.launch { reload() }
            }
        }
        lifecycleOwner.lifecycle.addObserver(observer)
        onDispose { lifecycleOwner.lifecycle.removeObserver(observer) }
    }

    Column(modifier = modifier) {
        Text(
            "Assinatura",
            color = Color.White,
            fontWeight = FontWeight.Black,
            modifier = Modifier.padding(bottom = 8.dp),
        )

        when {
            loading -> CircularProgressIndicator(color = NexNeon)
            error != null -> Text(error.orEmpty(), color = MaterialTheme.colorScheme.error)
            else -> {
                val sub = activeSubscription?.subscription
                if (sub != null) {
                    NexCard(modifier = Modifier.fillMaxWidth().padding(bottom = 12.dp)) {
                        Text("Sua Assinatura Ativa", color = Color.White, fontWeight = FontWeight.Black)
                        Spacer(modifier = Modifier.height(4.dp))
                        Text("Plano: ${sub.plan?.name ?: "Personalizado"}", color = NexNeon, fontWeight = FontWeight.Bold)
                        sub.startDate?.let { Text("Início: $it", color = NexMuted) }
                        sub.endDate?.let { Text("Expiração: $it", color = NexMuted) }
                        sub.nextBillingDate?.let { Text("Próxima Cobrança: $it", color = NexMuted) }
                        sub.cancelledAt?.let {
                            Text("Cancelada em: $it", color = MaterialTheme.colorScheme.error, fontWeight = FontWeight.Bold, modifier = Modifier.padding(top = 6.dp))
                            Text("Acesso Premium garantido até o fim do ciclo.", color = NexMuted)
                        } ?: run {
                            Button(
                                onClick = {
                                    cancelLoading = true
                                    scope.launch {
                                        repository.cancel()
                                            .onSuccess {
                                                message = "Cancelamento da assinatura agendado."
                                                reload()
                                            }
                                            .onFailure { error = it.message }
                                        cancelLoading = false
                                    }
                                },
                                enabled = !cancelLoading,
                                modifier = Modifier.padding(top = 10.dp),
                                colors = ButtonDefaults.buttonColors(
                                    containerColor = Color(0xFF8C2727),
                                    contentColor = Color.White
                                )
                            ) {
                                Text(if (cancelLoading) "Processando..." else "Cancelar Assinatura", fontWeight = FontWeight.Bold)
                            }
                        }
                    }
                    return@Column
                }

                if (isPremium) {
                    NexCard(modifier = Modifier.fillMaxWidth()) {
                        Text("Plano Premium ativo", color = NexGreen, fontWeight = FontWeight.Black)
                        Text(
                            "Sua assinatura ja esta ativa nesta conta.",
                            color = NexMuted,
                            modifier = Modifier.padding(top = 4.dp),
                        )
                    }
                    return@Column
                }

                paymentStatus?.let { status ->
                    Text(
                        "Pagamentos via ${status.activeLabel ?: status.activeGateway ?: "-"}",
                        color = NexMuted,
                    )
                    val methods = buildList {
                        if (status.methods?.pix == true) add("PIX")
                        if (status.methods?.creditCard == true) add("Cartao")
                        if (status.methods?.boleto == true) add("Boleto")
                    }
                    if (methods.isNotEmpty()) {
                        Text(
                            "Metodos: ${methods.joinToString(", ")}",
                            color = NexMuted,
                            modifier = Modifier.padding(top = 4.dp),
                        )
                    }
                }

                message?.let {
                    Text(it, color = NexNeon, modifier = Modifier.padding(top = 8.dp))
                }

                if (awaitingPayment) {
                    OutlinedButton(
                        onClick = {
                            val url = webReturnUrl
                            if (!url.isNullOrBlank()) {
                                context.startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                            } else {
                                context.startActivity(
                                    Intent(Intent.ACTION_VIEW, Uri.parse("nexshape://subscription/pending")),
                                )
                            }
                        },
                        modifier = Modifier.padding(top = 8.dp),
                        colors = ButtonDefaults.outlinedButtonColors(contentColor = NexNeon),
                    ) {
                        Text("Ja paguei - atualizar status")
                    }
                }

                if (plans.isEmpty()) {
                    Text("Nenhum plano disponivel.", color = NexMuted, modifier = Modifier.padding(top = 12.dp))
                } else {
                    Column(
                        modifier = Modifier.padding(top = 12.dp),
                        verticalArrangement = Arrangement.spacedBy(8.dp),
                    ) {
                        plans.forEach { plan ->
                            PlanCard(
                                plan = plan,
                                loading = checkoutLoading,
                                onCheckout = {
                                    checkoutLoading = true
                                    message = null
                                    scope.launch {
                                        repository.checkout(plan.id, preferredPaymentMethod(paymentStatus))
                                            .onSuccess { result ->
                                                when (result.status) {
                                                    "activated" -> {
                                                        awaitingPayment = false
                                                        message = "Plano ${result.plan ?: plan.name} ativado!"
                                                    }
                                                    "pending_payment" -> {
                                                        awaitingPayment = true
                                                        webReturnUrl = result.appReturnLinks?.get("web_pending")
                                                            ?: result.appReturnLinks?.get("web_success")
                                                        val url = result.checkoutUrl
                                                        if (!url.isNullOrBlank()) {
                                                            context.startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                                                            message = "Complete o pagamento no navegador e volte ao app."
                                                        } else {
                                                            message = "Checkout iniciado. Aguardando confirmacao."
                                                        }
                                                    }
                                                    else -> message = "Status: ${result.status}"
                                                }
                                            }
                                            .onFailure { error = it.message }
                                        checkoutLoading = false
                                    }
                                },
                            )
                        }
                    }
                }
            }
        }
    }
}

private fun preferredPaymentMethod(status: PaymentStatusDto?): String =
    when {
        status?.methods?.pix == true -> "pix"
        status?.methods?.creditCard == true -> "credit_card"
        status?.methods?.boleto == true -> "boleto"
        else -> "pix"
    }

@Composable
private fun PlanCard(
    plan: SubscriptionPlanDto,
    loading: Boolean,
    onCheckout: () -> Unit,
) {
    val currency = NumberFormat.getCurrencyInstance(Locale("pt", "BR"))
    NexCard(modifier = Modifier.fillMaxWidth()) {
        Text(plan.name, color = Color.White, fontWeight = FontWeight.Black)
        Text(currency.format(plan.price), color = Color.White, modifier = Modifier.padding(top = 4.dp))
        plan.description?.let {
            Text(it, color = NexMuted, modifier = Modifier.padding(top = 4.dp))
        }
        plan.billingCycle?.let {
            Text("Ciclo: $it", color = NexMuted)
        }
        Button(
            onClick = onCheckout,
            enabled = !loading,
            modifier = Modifier.padding(top = 8.dp),
            colors = ButtonDefaults.buttonColors(
                containerColor = NexGreen,
                contentColor = Color(0xFF04110D),
            ),
        ) {
            Text(if (plan.price <= 0) "Ativar gratis" else "Assinar", fontWeight = FontWeight.Black)
        }
    }
}
