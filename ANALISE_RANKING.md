# Análise da Query de Ranking

## Resumo da Análise

### Tabelas Encontradas e Dados

#### 1. Tabela `f_historico_ranking_pobj`
- **Total de registros**: 3
- **Estrutura**:
  - `id`: ID do registro
  - `data`: Data do ranking (2025-11-22)
  - `funcional`: Funcional do colaborador
  - `grupo`: Grupo (valor: 2)
  - `ranking`: Posição no ranking (1, 2, 3)
  - `realizado`: Valor realizado

**Dados encontrados**:
| ID | Funcional | Ranking | Realizado |
|----|-----------|---------|-----------|
| 1  | i010001   | 1       | 780000.00 |
| 2  | i020212   | 2       | 128000.00 |
| 3  | i020213   | 3       | 126000.00 |

#### 2. Tabela `d_estrutura`
- **Total de registros**: 6
- **Cargos encontrados**:
  - `cargo_id = 1`: 1 registro
  - `cargo_id = 2`: 1 registro
  - `cargo_id = 3`: 2 registros (Gerente de Gestão)
  - `cargo_id = 4`: 3 registros (Gerente)

**Estrutura dos dados**:
- Todos os funcionais do ranking têm `cargo_id = 4` (Gerente)
- Existem 2 Gerentes de Gestão (`cargo_id = 3`):
  - `i020219` (Andre Gestor) - Agência 1267
  - `i020220` (Marcio Gestor) - Agência 1141

#### 3. Tabelas de Referência

**segmentos**:
- 1 registro: ID 1, Nome "Empresas"

**diretorias**:
- 1 registro: ID 8607, Nome "Empresas", segmento_id 1

**regionais**:
- 1 registro: ID 8486, Nome "SP Sul e Oeste", diretoria_id 8607

**agencias**:
- 2 registros:
  - ID 1141: "Campo Limpo 1", regional_id 8486
  - ID 1267: "Faria Lima 2", regional_id 8486

### Query Testada

A query completa do ranking foi testada e está funcionando corretamente:

```sql
SELECT
    h.data AS data,
    DATE_FORMAT(h.data, '%Y-%m') AS competencia,
    CAST(seg.id AS CHAR) AS segmento_id,
    seg.nome AS segmento,
    CAST(dir.id AS CHAR) AS diretoria_id,
    dir.nome AS diretoria_nome,
    CAST(reg.id AS CHAR) AS gerencia_id,
    reg.nome AS gerencia_nome,
    CAST(ag.id AS CHAR) AS agencia_id,
    ag.nome AS agencia_nome,
    CASE 
        WHEN est.cargo_id = 4 THEN est.funcional
        ELSE NULL
    END AS gerente_id,
    CASE 
        WHEN est.cargo_id = 4 THEN est.nome
        ELSE NULL
    END AS gerente_nome,
    CASE 
        WHEN est.cargo_id = 4 THEN ggestao.funcional
        WHEN est.cargo_id = 3 THEN est.funcional
        ELSE NULL
    END AS gerente_gestao_id,
    CASE 
        WHEN est.cargo_id = 4 THEN ggestao.nome
        WHEN est.cargo_id = 3 THEN est.nome
        ELSE NULL
    END AS gerente_gestao_nome,
    h.ranking AS rank,
    h.realizado AS realizado_mensal
FROM f_historico_ranking_pobj h
INNER JOIN d_estrutura est ON est.funcional = h.funcional
LEFT JOIN segmentos seg ON seg.id = est.segmento_id
LEFT JOIN diretorias dir ON dir.id = est.diretoria_id
LEFT JOIN regionais reg ON reg.id = est.regional_id
LEFT JOIN agencias ag ON ag.id = est.agencia_id
LEFT JOIN (
    SELECT g1.agencia_id, g1.funcional, g1.nome
    FROM d_estrutura g1
    INNER JOIN (
        SELECT agencia_id, MIN(id) AS min_id
        FROM d_estrutura
        WHERE cargo_id = 3 AND agencia_id IS NOT NULL
        GROUP BY agencia_id
    ) AS g2 ON g1.id = g2.min_id AND g1.agencia_id = g2.agencia_id
    WHERE g1.cargo_id = 3
) AS ggestao ON ggestao.agencia_id = est.agencia_id
ORDER BY h.ranking ASC, h.realizado DESC
```

### Resultados Esperados vs Reais

#### Resultado 1 (Ranking 1)
- **Funcional**: i010001
- **Gerente**: José Gerente
- **Agência**: Campo Limpo 1 (1141)
- **Gerente de Gestão**: Marcio Gestor (i020220)
- **Realizado**: 780000.00
- **Status**: ✅ Correto

#### Resultado 2 (Ranking 2)
- **Funcional**: i020212
- **Gerente**: Pedro Gerente
- **Agência**: Campo Limpo 1 (1141)
- **Gerente de Gestão**: Marcio Gestor (i020220)
- **Realizado**: 128000.00
- **Status**: ✅ Correto

#### Resultado 3 (Ranking 3)
- **Funcional**: i020213
- **Gerente**: Maria Gerente
- **Agência**: Faria Lima 2 (1267)
- **Gerente de Gestão**: Andre Gestor (i020219)
- **Realizado**: 126000.00
- **Status**: ✅ Correto

### Observações

1. **Nomes das Tabelas**: O código usa `getTableName()` do Doctrine, que retorna os nomes corretos:
   - `segmentos` (não `d_segmento`)
   - `diretorias` (não `d_diretoria`)
   - `regionais` (não `d_regional`)
   - `agencias` (não `d_agencia`)

2. **Cargo IDs**: 
   - `cargo_id = 3`: Gerente de Gestão
   - `cargo_id = 4`: Gerente

3. **Relacionamento Gerente de Gestão**: A subquery que busca o gerente de gestão por agência está funcionando corretamente, retornando o primeiro gerente de gestão encontrado para cada agência.

4. **Dados Completos**: Todos os JOINs estão retornando dados corretos, com os nomes das estruturas sendo preenchidos adequadamente.

### Conclusão

✅ A query está funcionando corretamente e retornando os dados esperados.
✅ Todos os relacionamentos estão corretos.
✅ Os dados de estrutura (segmento, diretoria, regional, agência) estão sendo preenchidos corretamente.
✅ O relacionamento entre gerente e gerente de gestão está funcionando.

**Não foram encontrados problemas na query ou nos dados.**

