# 🔑 Configuração da API Key no Frontend

## 📋 Visão Geral

O frontend foi configurado para incluir automaticamente a API Key em todas as requisições para o backend. A API Key é enviada no header `X-API-Key`.

---

## ⚙️ Como Configurar

### 1. Variável de Ambiente (Recomendado)

Crie um arquivo `.env` na raiz do projeto `frontend/`:

```env
VITE_API_KEY=sua-chave-secreta-aqui
```

**Importante:** 
- A chave deve ser a mesma configurada no backend (`API_KEY` no `.env` do backend)
- O arquivo `.env` não deve ser commitado no Git (já está no `.gitignore`)

### 2. Variável Global (Alternativa)

Você também pode injetar a API Key via JavaScript global no `index.html`:

```html
<script>
  window.API_KEY = 'sua-chave-secreta-aqui';
</script>
```

---

## 🔄 Prioridade de Configuração

A API Key é obtida na seguinte ordem de prioridade:

1. **Variável global** `window.API_KEY` (maior prioridade)
2. **Variável de ambiente** `VITE_API_KEY`

Se nenhuma for encontrada, as requisições serão feitas sem API Key (e falharão se o backend exigir autenticação).

---

## 📝 Exemplo de Uso

### Arquivo `.env`:

```env
# Backend URL
VITE_API_URL=http://localhost:8081

# API Key (deve ser a mesma do backend)
VITE_API_KEY=minha-chave-super-secreta-123456
```

### Como Funciona:

Todas as requisições feitas através das funções `apiGet()`, `apiPost()`, `apiPut()`, `apiDelete()` automaticamente incluem o header:

```
X-API-Key: minha-chave-super-secreta-123456
```

**Você não precisa fazer nada manualmente!** A API Key é adicionada automaticamente.

---

## 🔍 Verificação

### Verificar se a API Key está sendo enviada:

1. Abra o DevTools do navegador (F12)
2. Vá para a aba "Network"
3. Faça uma requisição qualquer
4. Clique na requisição e verifique os "Request Headers"
5. Deve aparecer: `X-API-Key: sua-chave-aqui`

### Testar no Console:

```javascript
// Verificar se a API Key está configurada
console.log(window.API_KEY || import.meta.env.VITE_API_KEY)
```

---

## 🚨 Troubleshooting

### Erro: "API Key não fornecida" (401)

**Causa:** A API Key não está configurada ou está incorreta.

**Solução:**
1. Verifique se o arquivo `.env` existe na raiz do `frontend/`
2. Verifique se a variável `VITE_API_KEY` está definida
3. Certifique-se de que a chave é a mesma do backend
4. Reinicie o servidor de desenvolvimento (`npm run dev`)

### Erro: "API Key inválida" (401)

**Causa:** A API Key no frontend não corresponde à do backend.

**Solução:**
1. Verifique o arquivo `.env` do backend (`Backend/.env`)
2. Copie o valor de `API_KEY` do backend
3. Cole no `.env` do frontend como `VITE_API_KEY`
4. Reinicie ambos os servidores

### A API Key não está sendo enviada

**Causa:** A variável não está sendo lida corretamente.

**Solução:**
1. Certifique-se de que o arquivo é `.env` (não `.env.local` ou outro)
2. Reinicie o servidor Vite (variáveis de ambiente são carregadas na inicialização)
3. Verifique se não há espaços extras na variável
4. Use aspas se a chave contiver caracteres especiais

---

## 🔒 Segurança

### ⚠️ Importante:

1. **Nunca commite o arquivo `.env`** com a API Key real
2. Use `.env.example` para documentar a estrutura
3. Em produção, use variáveis de ambiente do servidor
4. A API Key deve ser forte e secreta

### Para Produção:

Em produção, configure a API Key via:
- Variáveis de ambiente do servidor
- Variáveis de ambiente do Docker/Kubernetes
- Secrets do CI/CD
- Variável global injetada pelo servidor web

---

## 📚 Arquivos Modificados

- `src/config/api.ts` - Adicionada função `getApiKey()`
- `src/services/api.ts` - Adicionado header `X-API-Key` em todas as requisições
- `.env.example` - Criado arquivo de exemplo

---

## ✅ Checklist

- [ ] Criar arquivo `.env` na raiz do frontend
- [ ] Adicionar `VITE_API_KEY` com a mesma chave do backend
- [ ] Reiniciar servidor de desenvolvimento
- [ ] Verificar no DevTools se o header está sendo enviado
- [ ] Testar uma requisição para confirmar que funciona

---

**Status:** ✅ Configuração Completa
**Data:** 2024-12-03

