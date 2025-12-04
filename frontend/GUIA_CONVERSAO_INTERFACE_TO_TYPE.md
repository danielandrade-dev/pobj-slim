# Guia de Conversão: Interface → Type

## Por que converter?

Segundo as regras do projeto, devemos **preferir `type` sobre `interface`** em TypeScript.

## Exemplos de Conversão

### 1. Props de Componentes

**Antes (interface):**
```typescript
interface Props {
  variant?: 'primary' | 'secondary' | 'link' | 'info'
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  icon?: string
  loading?: boolean
}

const props = defineProps<Props>()
```

**Depois (type):**
```typescript
type Props = {
  variant?: 'primary' | 'secondary' | 'link' | 'info'
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  icon?: string
  loading?: boolean
}

const props = defineProps<Props>()
```

### 2. Emits

**Antes (interface):**
```typescript
interface Emits {
  'update:modelValue': [value: string]
  'change': [value: string]
}

const emit = defineEmits<Emits>()
```

**Depois (type):**
```typescript
const emit = defineEmits<{
  'update:modelValue': [value: string]
  'change': [value: string]
}>()
```

Ou, se preferir definir separadamente:
```typescript
type Emits = {
  'update:modelValue': [value: string]
  'change': [value: string]
}

const emit = defineEmits<Emits>()
```

### 3. Tipos de Dados

**Antes (interface):**
```typescript
interface FilterOption {
  id: string
  nome: string
  id_segmento?: string
}
```

**Depois (type):**
```typescript
type FilterOption = {
  id: string
  nome: string
  id_segmento?: string
}
```

### 4. Union Types

**Antes (interface - não funciona bem):**
```typescript
interface Status {
  type: 'success' | 'error' | 'warning'
}
```

**Depois (type - melhor para unions):**
```typescript
type Status = {
  type: 'success' | 'error' | 'warning'
}

// Ou ainda melhor:
type StatusType = 'success' | 'error' | 'warning'
type Status = {
  type: StatusType
}
```

### 5. Intersection Types

**Antes (interface):**
```typescript
interface Base {
  id: string
}

interface Extended extends Base {
  name: string
}
```

**Depois (type - mais flexível):**
```typescript
type Base = {
  id: string
}

type Extended = Base & {
  name: string
}
```

## Quando usar `interface` vs `type`

### Use `type` quando:
- ✅ Props de componentes
- ✅ Union types
- ✅ Intersection types
- ✅ Tipos utilitários (Pick, Omit, etc.)
- ✅ Tipos simples de dados

### Use `interface` quando:
- ⚠️ Precisar de declaration merging (raro)
- ⚠️ Trabalhar com bibliotecas que esperam interfaces

## Arquivos Prioritários para Conversão

1. `src/types/index.ts` - Todas as definições principais
2. `src/components/Button.vue` - Props
3. `src/components/SelectInput.vue` - Props
4. `src/components/SelectSearch.vue` - Props
5. `src/components/exec/ExecChart.vue` - Tipos internos
6. `src/components/ChatWidget.vue` - Message interface

## Comando para Buscar Interfaces

```bash
# Encontrar todas as interfaces
grep -r "interface " src/ --include="*.ts" --include="*.vue"
```

## Nota Importante

A conversão de `interface` para `type` é **100% compatível** - não quebra código existente. É apenas uma preferência de estilo seguindo as regras do projeto.

