# Suplemento do dicionário de dados — Shopping (jun/2026)

Módulo **marketplace interno** (NexShape Shopping). Migrações `2026_06_29_100000` … `100007`.

Documento principal: `dicionario_dados.md` — fundir quando conveniente.

**Agente de domínio:** `agente-dominio-shopping.mdc`

---

## Visão geral

| Tabela | Model | Notas |
|--------|-------|-------|
| `shop_vendors` | `ShopVendor` | Vendedores por `academy_company_id` |
| `shop_categories` | `ShopCategory` | Categorias hierárquicas |
| `shop_suppliers` | `ShopSupplier` | Fornecedores por tenant |
| `shop_products` | `ShopProduct` | Físico / digital / serviço; soft delete |
| `shop_product_images` | `ShopProductImage` | Imagens do produto |
| `shop_coupons` | `ShopCoupon` | Cupons de desconto |
| `shop_coupon_usages` | `ShopCouponUsage` | Uso de cupom |
| `shop_carts` | `ShopCart` | Carrinho (user ou sessão) |
| `shop_cart_items` | `ShopCartItem` | Itens no carrinho |
| `shop_orders` | `ShopOrder` | Pedidos; soft delete |
| `shop_order_items` | `ShopOrderItem` | Snapshot do produto na compra |
| `shop_wishlists` | `ShopWishlist` | Lista de desejos |
| `shop_points_wallets` | `ShopPointsWallet` | Carteira de pontos fidelidade |
| `shop_points_transactions` | `ShopPointsTransaction` | Movimentos de pontos |
| `shop_recommendations` | `ShopRecommendation` | Cache de recomendações (TTL 24h) |

---

## TABELA: shop_products (resumo)

| Coluna | Tipo | Notas |
|--------|------|-------|
| academy_company_id | BIGINT FK | Tenant |
| vendor_id | BIGINT FK | `shop_vendors`, RESTRICT |
| category_id, supplier_id | BIGINT FK nullable | |
| type | ENUM | physical, digital, service |
| name, slug, sku | VARCHAR | slug único |
| price, sale_price, cost_price | DECIMAL | |
| manage_stock, stock_quantity | | Estoque físico |
| status | ENUM | draft, pending_review, published, archived |
| deleted_at | TIMESTAMP | Soft delete |

---

## TABELA: shop_orders (resumo)

| Coluna | Tipo | Notas |
|--------|------|-------|
| academy_company_id | BIGINT FK | Tenant |
| user_id | BIGINT FK | `users`, **RESTRICT** |
| order_number | VARCHAR | único, ex. SHP-2026-00001 |
| status | ENUM | pending … refunded |
| subtotal, discount_amount, shipping_amount, total | DECIMAL | |
| payment_method, payment_gateway, gateway_payment_id | | |
| shipping_address | JSON | |
| deleted_at | TIMESTAMP | Soft delete |

---

## Integridade referencial (padrão do módulo)

- **`users`:** `RESTRICT` em pedidos e cupons — não apaga histórico de compra em hard delete.
- **`academy_company_id`:** `CASCADE` — dados shop são por tenant.
- **Auditoria:** `php artisan app:db:orphans` inclui checks `shop_*`.

---

## Legado removido

### TABELA: omnichannel_tables (removida)

Stub vazio criado por `2026_04_13_162617_create_omnichannel_tables.php`. **Removida** por `2026_06_29_120000_fix_financial_and_appointment_fk_cascade.php` se vazia.

OmniChannel real: tabelas `omni_*` (`omni_companies`, `omni_conversations`, …) — ver migração `2026_04_09_000000_create_omnichannel_tables.php`.

---

## Índice de sincronização

```bash
rg "Schema::create\('shop_" laravel-app/database/migrations -l
rg "class Shop" laravel-app/app/Models -l
```
