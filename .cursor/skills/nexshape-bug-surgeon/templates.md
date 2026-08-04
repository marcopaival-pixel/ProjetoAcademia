# Templates de resposta — Bug Surgeon

## Diagnóstico

```markdown
## DIAGNÓSTICO DO BUG

**Incidente:** BUG-2026-00871

**Erro identificado:**
[Tentativa de acessar propriedade em objeto nulo / mensagem exata]

**Arquivo provável:**
`backend/app/Services/ExampleService.php`

**Linha:** 87

**Trecho identificado:**
```php
$professionalId = $student->professional->id;
```

**Sintoma vs causa:**
- Sintoma: [onde o usuário vê o erro]
- Causa raiz: [onde o código falha de fato]

**Causa:**
[Explicação em linguagem clara]

**Evidências:**
1. Stack trace aponta para ExampleService.php:87
2. [Query read-only no banco]
3. Rota utiliza Controller@method
4. [Reprodução em teste, se houver]

**Confiança do diagnóstico:** 96%

**Status:** Causa confirmada por reprodução do erro.
```

Se confiança baixa:

```markdown
**Confiança do diagnóstico:** 65%

**Status:** Hipótese provável, mas ainda não confirmada.

**Ação:** Nenhuma alteração recomendada até confirmar a causa.
```

## Impacto

```markdown
## ANÁLISE DE IMPACTO

**Arquivo que precisa ser alterado:**
- `backend/app/Services/ExampleService.php`

**Arquivos analisados, mas que não precisam ser alterados:**
- Controller.php
- Model.php
- routes/web.php

**Funções afetadas:**
- ExampleService::create()

**Perfis potencialmente afetados:**
- Aluno independente
- Aluno vinculado a profissional

**Perfis não afetados:**
- Paciente, Clínica, Recepcionista, Superadmin

**Banco de dados:** Nenhuma alteração de estrutura necessária.

**Rotas:** Nenhuma rota será alterada.

**Tenancy:** Filtros tenant_id/clinic_id permanecem intactos.

**Risco da alteração:** Baixo.

**Possível efeito colateral:**
[Descrever]
```

## Proposta de patch

```markdown
## PROPOSTA DE CORREÇÃO MÍNIMA

**Código atual**
```php
$professionalId = $student->professional->id;
```

**Código proposto**
```php
$professionalId = $student->professional?->id;
```

**Diff proposto**
```diff
- $professionalId = $student->professional->id;
+ $professionalId = $student->professional?->id;
```

**Justificativa**
[Uma frase]

**Limite da alteração**
- Arquivos autorizáveis: 1
- Máximo: 1 bloco, 1 linha funcional
- Proibidos: rotas, migrations, auth, permissões, config produção
```

## Relatório final

```markdown
## RELATÓRIO PÓS-CORREÇÃO

**Incidente:** BUG-2026-00871
**Commit:** 7a93fd2
**Branch:** bugfix/BUG-2026-00871-descricao

### Teste específico do bug
| Cenário | Esperado | Resultado |
|---------|----------|-----------|
| Aluno sem profissional cria avaliação | Sucesso | APROVADO |

### Regressão
| Cenário | Esperado | Resultado |
|---------|----------|-----------|
| Aluno vinculado cria avaliação | professional_id preenchido | APROVADO |

### Papéis
- Aluno independente: aprovado
- Aluno vinculado: aprovado
- Profissional: aprovado
- Paciente / Clínica / Superadmin: não afetado

### Diff final
| Métrica | Valor |
|---------|-------|
| Arquivos modificados | 1 |
| Linhas + / - | 1 / 1 |
| Migrations | 0 |
| Dependências | 0 |
| Rotas | 0 |
| Permissões | 0 |

### Hash
- Aprovado: 84fa...
- Executado: 84fa...
- Match: sim

### Rollback
```bash
git revert 7a93fd2
```
```
