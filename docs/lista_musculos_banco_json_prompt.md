# Lista Completa de Músculos para Banco de Dados, JSON e Prompt de Implementação

Este documento contém:
1) Estrutura recomendada de banco de dados
2) Lista completa de grupos musculares e músculos
3) JSON pronto para importação (seed)
4) Prompt pronto para implementar a funcionalidade no sistema

---

# 1) Estrutura Recomendada de Banco de Dados

## Tabela: muscle_groups

Campos:

id (INT - PK)  
name (VARCHAR)  
region (VARCHAR)  
is_active (BOOLEAN)

---

## Tabela: muscles

Campos:

id (INT - PK)  
group_id (INT - FK)  
name (VARCHAR)  
type (VARCHAR)  
(is: Primário, Secundário, Estabilizador)  
is_active (BOOLEAN)

---

# 2) Lista Completa de Grupos Musculares

1 - Ombros
2 - Braços
3 - Antebraços
4 - Peitoral
5 - Abdômen
6 - Costas
7 - Lombar
8 - Escápula
9 - Quadríceps
10 - Posterior de Coxa
11 - Glúteos
12 - Adutores
13 - Abdutores
14 - Panturrilhas
15 - Canela
16 - Core

---

# 3) Lista Completa de Músculos (Seed SQL)

```sql
INSERT INTO muscle_groups (id, name, region, is_active) VALUES
(1, 'Ombros', 'Membros Superiores', true),
(2, 'Braços', 'Membros Superiores', true),
(3, 'Antebraços', 'Membros Superiores', true),
(4, 'Peitoral', 'Tronco Anterior', true),
(5, 'Abdômen', 'Tronco Anterior', true),
(6, 'Costas', 'Tronco Posterior', true),
(7, 'Lombar', 'Tronco Posterior', true),
(8, 'Escápula', 'Tronco Posterior', true),
(9, 'Quadríceps', 'Membros Inferiores', true),
(10, 'Posterior de Coxa', 'Membros Inferiores', true),
(11, 'Glúteos', 'Membros Inferiores', true),
(12, 'Adutores', 'Membros Inferiores', true),
(13, 'Abdutores', 'Membros Inferiores', true),
(14, 'Panturrilhas', 'Membros Inferiores', true),
(15, 'Canela', 'Membros Inferiores', true),
(16, 'Core', 'Estabilizadores', true);
```

---

```sql
INSERT INTO muscles (id, group_id, name, type, is_active) VALUES

-- Ombros
(1, 1, 'Deltoide Anterior', 'Primário', true),
(2, 1, 'Deltoide Lateral', 'Primário', true),
(3, 1, 'Deltoide Posterior', 'Primário', true),
(4, 1, 'Manguito Rotador', 'Estabilizador', true),

-- Braços
(5, 2, 'Bíceps Braquial', 'Primário', true),
(6, 2, 'Braquial', 'Secundário', true),
(7, 2, 'Tríceps Braquial', 'Primário', true),

-- Antebraços
(8, 3, 'Braquiorradial', 'Secundário', true),
(9, 3, 'Flexores do Pulso', 'Secundário', true),
(10, 3, 'Extensores do Pulso', 'Secundário', true),

-- Peitoral
(11, 4, 'Peitoral Superior', 'Primário', true),
(12, 4, 'Peitoral Médio', 'Primário', true),
(13, 4, 'Peitoral Inferior', 'Primário', true),
(14, 4, 'Peitoral Menor', 'Secundário', true),

-- Abdômen
(15, 5, 'Reto Abdominal Superior', 'Primário', true),
(16, 5, 'Reto Abdominal Inferior', 'Primário', true),
(17, 5, 'Oblíquo Externo', 'Primário', true),
(18, 5, 'Oblíquo Interno', 'Primário', true),
(19, 5, 'Transverso do Abdômen', 'Estabilizador', true),
(20, 5, 'Serrátil Anterior', 'Secundário', true),

-- Costas
(21, 6, 'Latíssimo do Dorso', 'Primário', true),
(22, 6, 'Trapézio Superior', 'Primário', true),
(23, 6, 'Trapézio Médio', 'Primário', true),
(24, 6, 'Trapézio Inferior', 'Primário', true),
(25, 6, 'Romboide Maior', 'Secundário', true),
(26, 6, 'Romboide Menor', 'Secundário', true),

-- Lombar
(27, 7, 'Eretores da Espinha', 'Estabilizador', true),
(28, 7, 'Quadrado Lombar', 'Estabilizador', true),

-- Escápula
(29, 8, 'Infraespinhal', 'Secundário', true),
(30, 8, 'Redondo Maior', 'Secundário', true),
(31, 8, 'Redondo Menor', 'Secundário', true),

-- Quadríceps
(32, 9, 'Reto Femoral', 'Primário', true),
(33, 9, 'Vasto Lateral', 'Primário', true),
(34, 9, 'Vasto Medial', 'Primário', true),
(35, 9, 'Vasto Intermédio', 'Primário', true),
(36, 9, 'Sartório', 'Secundário', true),

-- Posterior de Coxa
(37, 10, 'Bíceps Femoral', 'Primário', true),
(38, 10, 'Semitendíneo', 'Primário', true),
(39, 10, 'Semimembranáceo', 'Primário', true),

-- Glúteos
(40, 11, 'Glúteo Maior', 'Primário', true),
(41, 11, 'Glúteo Médio', 'Primário', true),
(42, 11, 'Glúteo Menor', 'Primário', true),

-- Adutores
(43, 12, 'Adutor Longo', 'Primário', true),
(44, 12, 'Adutor Curto', 'Primário', true),
(45, 12, 'Adutor Magno', 'Primário', true),

-- Abdutores
(46, 13, 'Tensor da Fáscia Lata', 'Secundário', true),

-- Panturrilhas
(47, 14, 'Gastrocnêmio', 'Primário', true),
(48, 14, 'Sóleo', 'Primário', true),

-- Canela
(49, 15, 'Tibial Anterior', 'Primário', true),

-- Core
(50, 16, 'Psoas Ilíaco', 'Estabilizador', true),
(51, 16, 'Multífidos', 'Estabilizador', true);
```

---

# 4) JSON Pronto para Importar

```json
{
  "muscle_groups": [
    { "id": 1, "name": "Ombros", "region": "Membros Superiores" },
    { "id": 2, "name": "Braços", "region": "Membros Superiores" },
    { "id": 3, "name": "Antebraços", "region": "Membros Superiores" },
    { "id": 4, "name": "Peitoral", "region": "Tronco Anterior" },
    { "id": 5, "name": "Abdômen", "region": "Tronco Anterior" },
    { "id": 6, "name": "Costas", "region": "Tronco Posterior" },
    { "id": 7, "name": "Lombar", "region": "Tronco Posterior" },
    { "id": 8, "name": "Escápula", "region": "Tronco Posterior" },
    { "id": 9, "name": "Quadríceps", "region": "Membros Inferiores" },
    { "id": 10, "name": "Posterior de Coxa", "region": "Membros Inferiores" },
    { "id": 11, "name": "Glúteos", "region": "Membros Inferiores" },
    { "id": 12, "name": "Adutores", "region": "Membros Inferiores" },
    { "id": 13, "name": "Abdutores", "region": "Membros Inferiores" },
    { "id": 14, "name": "Panturrilhas", "region": "Membros Inferiores" },
    { "id": 15, "name": "Canela", "region": "Membros Inferiores" },
    { "id": 16, "name": "Core", "region": "Estabilizadores" }
  ]
}
```

---

# 5) Prompt Pronto para Implementar a Funcionalidade

Use este prompt diretamente no seu gerador de código ou IA:

"""
Criar um componente de seleção de músculos para sistema de treino e saúde.

Requisitos:

1) O campo deve permitir buscar músculos por nome

2) Deve existir autocomplete em tempo real

3) O usuário pode selecionar múltiplos músculos

4) Os músculos selecionados devem aparecer como tags

5) Deve mostrar contador:

Músculos Selecionados (3)

6) Deve permitir remover músculo com botão X

7) Deve carregar dados das tabelas:

muscle_groups
muscles

8) Deve permitir filtrar por grupo muscular

Exemplo:

Ombros
Braços
Peitoral
Quadríceps

9) Deve aceitar Enter para adicionar músculo

10) Deve ser compatível com:

Laravel
Blade
Alpine.js

11) Deve suportar integração futura com IA de prescrição de treino

12) Deve salvar os músculos selecionados na tabela:

exercise_muscles

Campos:

exercise_id
muscle_id

13) Interface deve ser responsiva

14) Deve permitir expansão futura para:

Relatórios
IA
Estatísticas
Progressão de treino
"""

---

Se necessário, o próximo passo pode ser:

- Seeder Laravel pronto
- Migration pronta
- API endpoint
- Autocomplete com busca dinâmica
- Integração com exercícios

