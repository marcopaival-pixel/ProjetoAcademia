# Módulo de Progressão de Carga - Detalhamento Técnico

Este documento descreve a arquitetura e funcionalidades do módulo de progressão de carga.

## 1. Estrutura de Dados (Migrations)

### Tabela `workout_plans`
| Coluna | Tipo | Descrição |
| --- | --- | --- |
| id | bigint | PK |
| user_id | int | FK -> users |
| name | string | Nome do treino (ex: Treino A - Empurrar) |
| description | text | Observações gerais |
| created_at/updated_at | timestamps | |

### Tabela `workout_plan_exercises`
| Coluna | Tipo | Descrição |
| --- | --- | --- |
| id | bigint | PK |
| workout_plan_id | bigint | FK -> workout_plans |
| exercise_id | bigint | FK -> exercises_catalog |
| position | int | Ordem de execução |
| notes | string | Observações pro exercício |

### Tabela `exercise_sets`
| Coluna | Tipo | Descrição |
| --- | --- | --- |
| id | bigint | PK |
| workout_plan_exercise_id | bigint | FK |
| set_number | int | |
| reps_target | int | Alvo de repetições |
| weight_target | decimal | Carga sugerida |

### Tabela `progression_logs`
| Coluna | Tipo | Descrição |
| --- | --- | --- |
| id | bigint | PK |
| user_id | int | |
| workout_plan_exercise_id | bigint | |
| exercise_id | bigint | |
| date | date | Data do registro |
| set_number | int | |
| reps_done | int | Repetições realizadas |
| weight_kg | decimal | Carga utilizada |
| rpe | int | Esforço percebido (1-10) |

## 2. Tecnologias
- **Backend:** Laravel 11+
- **Frontend:** Blade, CSS Vanilla (Aesthetics Premium), Chart.js (Gráficos)
- **Banco:** MySQL (XAMPP local)

## 3. Fluxo de Usuário
1. Usuário cria um **Plano de Treino**.
2. Adiciona exercícios do **Catálogo** ao plano.
3. Define as **Séries** (sets) desejadas.
4. Ao treinar, abre o plano e registra **Cargas e Repetições**.
5. O sistema calcula volume total e estima 1RM.
6. Dashboards exibem evolução por exercício e grupo muscular.
