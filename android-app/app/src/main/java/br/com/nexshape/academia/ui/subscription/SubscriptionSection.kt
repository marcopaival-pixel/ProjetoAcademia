package br.com.nexshape.academia.ui.subscription

import android.content.Intent
import android.net.Uri
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Button
import androidx.compose.material3.Card
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
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.platform.LocalLifecycleOwner
import androidx.compose.ui.unit.dp
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.LifecycleEventObserver
import br.com.nexshape.academia.data.api.PaymentStatusDto
import br.com.nexshape.academia.data.api.SubscriptionPlanDto
import br.com.nexshape.academia.data.repository.SubscriptionRepository
import br.com.nexshape.academia.ui.navigation.SubscriptionDeepLink
import br.com.nexshape.academia.ui.navigation.subscriptionStatusMessage
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.Locale

@Composable
fun SubscriptionSection(modifier: Modifier = Modifier) {
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    val lifecycleOwner = LocalLifecycleOwner.current
    val repository = remember { SubscriptionRepository() }
    var paymentStatus by remember { mutableStateOf<PaymentStatusDto?>(null) }
    var plans by remember { mutableStateOf<List<SubscriptionPlanDto>>(emptyList()) }
    var loading by remember { mutableStateOf(true) }
    var checkoutLoading by remember { mutableStateOf(false) }
    var message by remember { mutableStateOf<String?>(null) }
    var error by remember { mutableStateOf<String?>(null) }
    var awaitingPayment by remember { mutableStateOf(false) }
    var webReturnUrl by remember { mutableStateOf<String?>(null) }

    suspend fun reload() {
        loading = true
        repository.paymentStatus()
            .onSuccess { paymentStatus = it }
            .onFailure { error = it.message }
        repository.plans()
            .onSuccess { plans = it }
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
        Text("Assinatura", style = MaterialTheme.typography.titleMedium, modifier = Modifier.padding(bottom = 8.dp))

        when {
            loading -> CircularProgressIndicator()
            error != null -> Text(error!!, color = MaterialTheme.colorScheme.error)
            else -> {
                paymentStatus?.let { status ->
                    Text(
                        "Pagamentos via ${status.activeLabel ?: status.activeGateway ?: "—"}",
                        style = MaterialTheme.typography.bodyMedium,
                    )
                    val methods = buildList {
                        if (status.methods?.pix == true) add("PIX")
                        if (status.methods?.creditCard == true) add("Cartão")
                        if (status.methods?.boleto == true) add("Boleto")
                    }
                    if (methods.isNotEmpty()) {
                        Text(
                            "Métodos: ${methods.joinToString(", ")}",
                            modifier = Modifier.padding(top = 4.dp),
                            style = MaterialTheme.typography.bodySmall,
                        )
                    }
                }

                message?.let {
                    Text(it, color = MaterialTheme.colorScheme.primary, modifier = Modifier.padding(top = 8.dp))
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
                    ) {
                        Text("Já paguei — atualizar status")
                    }
                }

                if (plans.isEmpty()) {
                    Text("Nenhum plano disponível.", modifier = Modifier.padding(top = 12.dp))
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
                                        repository.checkout(plan.id)
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
                                                            message = "Checkout iniciado. Aguardando confirmação."
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

@Composable
private fun PlanCard(
    plan: SubscriptionPlanDto,
    loading: Boolean,
    onCheckout: () -> Unit,
) {
    val currency = NumberFormat.getCurrencyInstance(Locale("pt", "BR"))
    Card(modifier = Modifier.fillMaxWidth()) {
        Column(modifier = Modifier.padding(16.dp)) {
            Text(plan.name, style = MaterialTheme.typography.titleSmall)
            Text(currency.format(plan.price), modifier = Modifier.padding(top = 4.dp))
            plan.description?.let {
                Text(it, style = MaterialTheme.typography.bodySmall, modifier = Modifier.padding(top = 4.dp))
            }
            plan.billingCycle?.let {
                Text("Ciclo: $it", style = MaterialTheme.typography.bodySmall)
            }
            Button(
                onClick = onCheckout,
                enabled = !loading,
                modifier = Modifier.padding(top = 8.dp),
            ) {
                Text(if (plan.price <= 0) "Ativar grátis" else "Assinar")
            }
        }
    }
}
