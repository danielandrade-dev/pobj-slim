# Especificação do JSON do Resumo

## Endpoint
`GET /api/pobj/resumo`

## Estrutura Completa do JSON

```json
{
  "produtos": Produto[],
  "produtosMensais": ProdutoMensal[],
  "variavel": Variavel[],
  "businessSnapshot": BusinessSnapshot
}
```

---

## 1. `produtos` - Array de Produto

**Usado em:** `ProdutosCards.vue` (modo cards)

### Estrutura de cada item:
```typescript
{
  id: string                    // ID do produto (id_indicador ou id)
  id_familia: string           // ID da família
  familia: string              // Nome da família
  id_indicador: string         // ID do indicador
  indicador: string            // Nome do indicador
  id_subindicador?: string     // ID do subindicador (opcional)
  subindicador?: string        // Nome do subindicador (opcional)
  metrica: string              // 'valor' | 'qtd' | 'perc'
  peso: number                 // Peso do indicador
  meta?: number                // Meta total (soma de todas as metas)
  realizado?: number           // Realizado total (soma de todos os realizados)
  pontos?: number              // Pontos realizados
  pontos_meta?: number         // Pontos da meta (geralmente igual ao peso)
  variavel_meta?: number       // Variável meta (não usado atualmente)
  variavel_realizado?: number // Variável realizado (não usado atualmente)
  ating?: number               // Percentual de atingimento (realizado/meta)
  atingido?: boolean           // true se ating >= 1
  ultima_atualizacao?: string  // Data da última atualização (formato YYYY-MM-DD)
}
```

### Uso no ProdutosCards.vue:
- **Agrupamento:** Por `id_familia` e `familia`
- **Exibição:** Cards com badge de percentual, meta, realizado, pontos
- **Cálculos:**
  - `calculateAtingimento()`: `(realizado / meta) * 100`
  - `calculatePontosRatio()`: `(pontos / pontos_meta) * 100`
- **Tooltip:** Usa `businessSnapshot` para calcular projeções

---

## 2. `produtosMensais` - Array de ProdutoMensal

**Usado em:** `ResumoLegacy.vue` (modo legacy/tabela)

### Estrutura de cada item:
```typescript
{
  id: string                    // ID do produto
  id_indicador: string         // ID do indicador
  indicador: string            // Nome do indicador
  id_familia: string           // ID da família
  familia: string              // Nome da família
  id_subindicador?: string     // ID do subindicador (opcional)
  subindicador?: string        // Nome do subindicador (opcional)
  metrica: string              // 'valor' | 'qtd' | 'perc'
  peso: number                 // Peso do indicador
  meta?: number                // Meta total (soma de todos os meses)
  realizado?: number           // Realizado total (soma de todos os meses)
  ating?: number               // Percentual de atingimento
  atingido?: boolean           // true se ating >= 1
  ultima_atualizacao?: string  // Data da última atualização
  meses: Array<{               // Dados mensais agrupados
    mes: string                // Formato: "YYYY-MM" (ex: "2024-01")
    meta: number               // Meta do mês
    realizado: number          // Realizado do mês
    atingimento: number        // Percentual de atingimento do mês
  }>
}
```

### Uso no ResumoLegacy.vue:
- **Agrupamento:** Por `id_familia` e `familia`, depois por `id_indicador`
- **Subindicadores:** Se `id_subindicador` existe, vira `children[]` do indicador
- **Exibição:** Tabela com colunas: Subindicador, Peso, Métrica, Meta, Realizado, etc.
- **Cálculos adicionais:**
  - `referenciaHoje`: `(meta / diasTotais) * diasDecorridos`
  - `projecao`: `((realizado / diasDecorridos) * diasRestantes) + realizado`
  - `metaDiariaNecessaria`: `(meta - realizado) / diasRestantes`
  - `pontos`: Limitado pelo peso

---

## 3. `variavel` - Array de Variavel

**Usado em:** `ResumoKPI.vue` (card de variável estimada)

### Estrutura de cada item:
```typescript
{
  id?: string                  // ID do registro (opcional)
  registro_id?: string         // ID alternativo (opcional)
  funcional: string            // Funcional do colaborador
  variavel_meta: number        // Meta de variável
  variavel_real: number        // Realizado de variável
  dt_atualizacao?: string      // Data de atualização
  nome_funcional?: string      // Nome do colaborador (opcional)
  segmento?: string            // Nome do segmento (opcional)
  segmento_id?: string         // ID do segmento (opcional)
  diretoria_nome?: string      // Nome da diretoria (opcional)
  diretoria_id?: string        // ID da diretoria (opcional)
  regional_nome?: string       // Nome da regional (opcional)
  gerencia_id?: string         // ID da gerência (opcional)
  agencia_nome?: string        // Nome da agência (opcional)
  agencia_id?: string          // ID da agência (opcional)
  data?: string                // Data (opcional)
  competencia?: string         // Competência (opcional)
}
```

### Uso no ResumoKPI.vue:
- **Agregação:** Soma de todos os `variavel_meta` e `variavel_real`
- **Exibição:** Card KPI com:
  - `varPossivel`: Soma de todas as `variavel_meta`
  - `varAtingido`: Soma de todos os `variavel_real`
  - Percentual: `(varAtingido / varPossivel) * 100`
- **Condição:** Só mostra o card se houver dados (`varPossivel != null || varAtingido != null`)

---

## 4. `businessSnapshot` - BusinessSnapshot

**Usado em:** 
- `ResumoKPI.vue` (indiretamente)
- `ProdutosCards.vue` (tooltip de projeção)
- `ResumoLegacy.vue` (cálculos de referência, projeção, meta diária)

### Estrutura:
```typescript
{
  total: number                // Total de dias úteis no mês atual
  elapsed: number              // Dias úteis já decorridos no mês
  remaining: number            // Dias úteis restantes no mês
  monthStart: string           // Data de início do mês (YYYY-MM-DD)
  monthEnd: string             // Data de fim do mês (YYYY-MM-DD)
  today: string                // Data de hoje (YYYY-MM-DD)
}
```

### Uso:
- **ProdutosCards.vue (tooltip):**
  - `faltaTotal`: `meta - realizado`
  - `necessarioPorDia`: `faltaTotal / remaining`
  - `referenciaHoje`: `(meta / total) * elapsed`
  - `projecaoRitmoAtual`: `(realizado / elapsed) * total`

- **ResumoLegacy.vue:**
  - `referenciaHoje`: `(meta / total) * elapsed`
  - `projecao`: `((realizado / elapsed) * remaining) + realizado`
  - `metaDiariaNecessaria`: `(meta - realizado) / remaining`

---

## Resumo por Componente

### ResumoKPI.vue
**Dados usados:**
- `produtos[]` → Calcula `indicadoresAtingidos`, `indicadoresTotal`, `pontosAtingidos`, `pontosPossiveis`
- `variavel[]` → Calcula `varPossivel`, `varAtingido`

**Campos necessários:**
- `produtos[].atingido` (boolean)
- `produtos[].pontos` (number)
- `produtos[].pontos_meta` (number)
- `variavel[].variavel_meta` (number)
- `variavel[].variavel_real` (number)

---

### ProdutosCards.vue
**Dados usados:**
- `produtos[]` → Agrupados por família
- `businessSnapshot` → Para cálculos do tooltip

**Campos necessários:**
- `produtos[].id`, `id_familia`, `familia`, `id_indicador`, `indicador`
- `produtos[].id_subindicador`, `subindicador` (opcional)
- `produtos[].metrica`, `peso`
- `produtos[].meta`, `realizado`, `pontos`, `pontos_meta`
- `produtos[].ultima_atualizacao`
- `businessSnapshot.total`, `elapsed`, `remaining`

---

### ResumoLegacy.vue
**Dados usados:**
- `produtosMensais[]` → Agrupados por família e indicador
- `businessSnapshot` → Para cálculos de referência e projeção

**Campos necessários:**
- `produtosMensais[].id`, `id_familia`, `familia`, `id_indicador`, `indicador`
- `produtosMensais[].id_subindicador`, `subindicador` (para criar `children[]`)
- `produtosMensais[].metrica`, `peso`
- `produtosMensais[].meta`, `realizado`, `ating`, `atingido`
- `produtosMensais[].meses[]` → Array com dados mensais
- `produtosMensais[].ultima_atualizacao`
- `businessSnapshot.total`, `elapsed`, `remaining`

---

## Observações Importantes

1. **Agrupamento de produtos:**
   - No modo cards: produtos com mesmo `id_indicador` são agrupados (soma de meta, realizado, pontos)
   - No modo legacy: produtos com mesmo `id_indicador` são agrupados, e `id_subindicador` vira `children[]`

2. **Formato de datas:**
   - `ultima_atualizacao`: `YYYY-MM-DD` ou string formatada
   - `businessSnapshot.monthStart/End/today`: `YYYY-MM-DD`

3. **Formato de meses:**
   - `produtosMensais[].meses[].mes`: `YYYY-MM` (ex: "2024-01")

4. **Valores opcionais:**
   - Campos numéricos podem ser `undefined` ou `0`
   - Campos de string opcionais podem ser `undefined` ou `null`
   - Arrays podem ser vazios `[]`

5. **Cálculos no frontend:**
   - `ating`: Calculado como `(realizado / meta) * 100` se meta > 0, senão 0
   - `atingido`: `true` se `ating >= 100` ou se `(pontos / pontos_meta) >= 1`

