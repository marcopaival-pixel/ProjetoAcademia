# Manual Funcional Detalhado: Portal do Aluno Soberano - NexShape

Este manual descreve o ecossistema digital do **Aluno NexShape**, focado em performance, autonomia e soberania de dados. Diferente do Portal do Paciente (clínico), este módulo é projetado para que o atleta gerencie sua própria evolução.

---

## Índice
1. [Filosofia: O Aluno Soberano](#1-filosofia-o-aluno-soberano)
2. [Inventário de Módulos e Views](#2-inventário-de-módulos-e-views)
3. [Funcionalidades de Performance (Soberanas)](#3-funcionalidades-de-performance-soberanas)
4. [Gestão de IA e Créditos](#4-gestão-de-ia-e-créditos)
5. [Guia de Uso: Dia a Dia do Atleta](#5-guia-de-uso-dia-a-dia-do-atleta)
6. [Tabela de Rotas e Acessos](#6-tabela-de-rotas-e-acessos)
7. [Regras de Negócio e Gamificação](#7-regras-de-negócio-e-gamificação)

---

## 1. Filosofia: O Aluno Soberano

O NexShape separa a **Performance Esportiva** da **Conduta Clínica**. 
- **Soberania**: O aluno é dono de seus dados de evolução (fotos, pesos, medidas).
- **Independência**: Funcionalidades como Score de Saúde, Análise de IA e Montagem de Treinos não exigem a aprovação de um profissional para usuários Premium.
- **Privacidade**: Dados de treinos criados pelo aluno são invisíveis para profissionais vinculados, a menos que o aluno opte por compartilhar.

---

## 2. Inventário de Módulos e Views

| Módulo | Caminho das Views | Propósito |
| :--- | :--- | :--- |
| **Performance Hub** | `resources/views/evolution/` | Central de análise visual, fotos e gráficos. |
| **Treinamento** | `resources/views/progression/` | Gestão de planilhas, cargas e execução de treinos. |
| **Nutrição** | `resources/views/nutrition/` | Diário alimentar, sugestão de IA e auditoria de dieta. |
| **Gamificação** | `resources/views/student/` | Troféus, badges e conquistas de carreira. |
| **Comunidade** | `resources/views/community/` | Feed social e interação entre membros. |
| **Bio-Análise** | `resources/views/body-analysis/` | Histórico de bioimpedância e medidas antropométricas. |

---

## 3. Funcionalidades de Performance (Soberanas)

### 3.1 Hub de Evolução Premium (`/evolution`)
- **Health Score**: Algoritmo que processa dados de composição corporal para gerar uma nota de 0-100.
- **Share Card**: Geração dinâmica de cartões de conquista (9:16) para Instagram/WhatsApp.
- **Galeria Antes & Depois**: Comparativo tri-axial (Frente, Lado, Costas) com controle de data.

### 3.2 Progressão de Carga (`/progression/plans`)
- **Orquestração Tática**: Criação de rotinas de treino personalizadas.
- **1RM Estimado**: Cálculo automático de força máxima baseada no histórico de repetições.
- **ACWR (Acute:Chronic Workload Ratio)**: Indicador de risco de lesão baseado na carga de trabalho recente.

### 3.3 Nutrição Inteligente (`/nutrition`)
- **Auditoria de Dieta via IA**: Relatório semanal que analisa os macros e dá uma "Nota NexShape".
- **Sugestão Biohacking**: IA sugere a próxima refeição baseada nos macros que ainda faltam no dia.
- **Logging Natural**: Registro de alimentos via voz ou texto simples (Processamento de Linguagem Natural).

---

## 4. Gestão de IA e Créditos

O Aluno possui uma "Carteira de Créditos de IA" que permite utilizar recursos avançados de forma autônoma:
- **Consumo**: Cada auditoria ou sugestão consome 1 crédito.
- **Renovação**: Créditos são renovados mensalmente conforme o plano (Free, Premium, Elite).
- **Compra Avulsa**: Possibilidade de adquirir pacotes extras diretamente no dashboard.

---

## 5. Guia de Uso: Dia a Dia do Atleta

1. **Check-in Matinal**: Registro de peso e hidratação no Dashboard Principal.
2. **Execução de Treino**: Acesso à planilha ativa, registro de cargas e tempo de descanso.
3. **Log Alimentar**: Uso do scanner de fotos ou texto para manter os macros em dia.
4. **Análise de IA**: Solicitar sugestão de refeição para bater a meta de proteínas no jantar.
5. **Revisão Semanal**: Gerar o Share Card de evolução para celebrar a consistência.

---

## 6. Tabela de Rotas e Acessos

| Rota | Descrição | Permissão Requerida |
| :--- | :--- | :--- |
| `dashboard` | Painel Geral de Performance | `access_portal` |
| `evolution.index` | Hub de Fotos e Score | `view_evolution` |
| `progression.plans.index` | Minhas Planilhas de Treino | `manage_workouts` |
| `nutrition.index` | Diário e Estratégia Nutricional | `manage_diet` |
| `student.trophies` | Galeria de Conquistas | `access_portal` |
| `community.index` | Feed Social | `access_portal` |

---

## 7. Regras de Negócio e Gamificação

- **Gamificação**: O sistema de troféus (`student/trophies`) recompensa a consistência (ex: 12 dias seguidos de treino).
- **Vínculo com Mentor**: Se o aluno possuir um "Mentor" (Personal/Nutri), este pode prescrever itens que aparecerão no portal, mas o aluno mantém sua área de "Autogestão" privada.
- **Bloqueio por Limite**: Usuários Free possuem limite de 3 planilhas de treino e 1 auditoria de IA por mês.

---
**Documento atualizado conforme auditoria de arquitetura soberana.**
**Data**: 12 de Maio de 2026.
**Status**: Versão 2.0 - Desacoplamento Concluído.
