# Migração do Omega para Vue

Este documento descreve a estrutura Vue criada para o módulo Omega. O arquivo legado `Frontend/public/legacy/omega.js` foi completamente removido e substituído por componentes Vue.

## Estrutura Criada

### 1. Types/Interfaces (`frontend/src/types/omega.ts`)

Define todas as interfaces TypeScript para:
- `OmegaUser` - Usuários do sistema
- `OmegaTicket` - Chamados/tickets
- `OmegaStatus` - Status dos chamados
- `OmegaStructure` - Estrutura de departamentos e tipos
- `OmegaMesuRecord` - Dados MESU
- `OmegaInitData` - Dados de inicialização
- E outros tipos relacionados

### 2. Services (`frontend/src/services/omegaService.ts`)

Services para chamadas de API:
- `getOmegaInit()` - Carrega dados de inicialização
- `getOmegaUsers()` - Lista usuários
- `getOmegaTickets()` - Lista tickets
- `getOmegaStatuses()` - Lista status
- `getOmegaStructure()` - Estrutura de departamentos
- `getOmegaMesu()` - Dados MESU
- `createOmegaTicket()` - Cria novo ticket
- `updateOmegaTicket()` - Atualiza ticket existente

### 3. Composable (`frontend/src/composables/useOmega.ts`)

Composable Vue que encapsula toda a lógica do Omega:

```typescript
import { useOmega } from '@/composables/useOmega'

const {
  users,
  tickets,
  statuses,
  isLoading,
  currentUser,
  loadInit,
  createTicket,
  updateTicket,
  // ... outros métodos
} = useOmega()
```

## Uso em Componentes Vue

### Exemplo básico:

```vue
<script setup lang="ts">
import { onMounted } from 'vue'
import { useOmega } from '@/composables/useOmega'

const omega = useOmega()

onMounted(async () => {
  await omega.loadInit()
})
</script>

<template>
  <div v-if="omega.isLoading.value">
    Carregando...
  </div>
  <div v-else>
    <p>Usuários: {{ omega.users.value.length }}</p>
    <p>Tickets: {{ omega.tickets.value.length }}</p>
  </div>
</template>
```

## Migração Completa

O arquivo `Frontend/public/legacy/omega.js` foi completamente removido. Toda a funcionalidade foi migrada para:

1. **Componente Vue**: `OmegaModal.vue` - Componente principal do modal
2. **Composable**: `useOmega()` - Lógica reutilizável e estado
3. **Services**: `omegaService.ts` - Chamadas de API
4. **Types**: `omega.ts` - Interfaces TypeScript

## Interface Global

Para compatibilidade com código que ainda usa a interface global, o componente `OmegaModal.vue` registra:

- `window.__openOmegaFromVue` - Função para abrir o modal do Vue
- `window.openOmegaModule` - Função para abrir o modal
- `window.openOmega` - Alias para `openOmegaModule`

## Endpoints da API

Todos os endpoints seguem o padrão `/api/omega/*`:
- `/api/omega/init` - Inicialização
- `/api/omega/users` - Usuários
- `/api/omega/tickets` - Tickets
- `/api/omega/statuses` - Status
- `/api/omega/structure` - Estrutura
- `/api/omega/mesu` - MESU

## Status da Migração

✅ **Concluído:**
1. Arquivo `omega.js` removido completamente
2. Componente Vue `OmegaModal.vue` criado
3. Composable `useOmega()` implementado
4. Services `omegaService.ts` criados
5. Types/interfaces TypeScript definidos
6. Interface global mantida para compatibilidade

## Próximos Passos

1. Implementar funcionalidades avançadas do modal (filtros, busca, etc.)
2. Adicionar testes unitários para services e composables
3. Otimizar performance do componente

