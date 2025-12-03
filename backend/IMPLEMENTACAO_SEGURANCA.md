# ✅ Implementação - Segurança e Autenticação

## 📦 O que foi implementado

### 1. Sistema de API Key Única ✅

**Arquivos criados:**
- `src/Security/ApiKeyAuthenticator.php` - Authenticator para API Key única
- `src/Security/ApiKeyUser.php` - User representation simples
- `src/Security/ApiKeyUserProvider.php` - User Provider para Security

**Funcionalidades:**
- API Key única do projeto configurada via variável de ambiente
- Validação simples e eficiente
- Sem necessidade de banco de dados
- Role fixa: ROLE_API

**Configuração:**
Adicione no arquivo `.env`:
```
API_KEY=sua-chave-secreta-aqui
```

**Uso:**
```bash
# Enviar no header
X-API-Key: sua-api-key-aqui
```

---

### 2. Rate Limiting ✅

**Arquivo criado:**
- `src/EventSubscriber/RateLimitSubscriber.php`

**Funcionalidades:**
- Rate limiting baseado apenas em IP
- Diferentes limites por tipo de endpoint:
  - **Auth endpoints**: 5 requisições/minuto
  - **API endpoints**: 1000 requisições/hora
  - **Default**: 100 requisições/minuto
- Headers de resposta:
  - `X-RateLimit-Limit`: Limite total
  - `X-RateLimit-Remaining`: Requisições restantes
  - `Retry-After`: Segundos até poder tentar novamente

**Resposta quando excedido (429):**
```json
{
  "success": false,
  "data": {
    "error": "Muitas requisições. Tente novamente mais tarde.",
    "code": "RATE_LIMIT_EXCEEDED",
    "details": {
      "limit": 100,
      "window": 60,
      "retry_after": 45
    }
  }
}
```

---

### 3. CSRF Protection ✅

**Arquivo criado:**
- `src/EventSubscriber/CsrfProtectionSubscriber.php`

**Funcionalidades:**
- Valida tokens CSRF para POST, PUT, DELETE, PATCH
- Exclui paths públicos (login, register)
- Suporta token no header `X-CSRF-Token` ou no body/query `_token`

**Uso:**
```javascript
// Obter token CSRF (endpoint a ser criado)
const csrfToken = await fetch('/api/csrf-token').then(r => r.json());

// Enviar em requisições
fetch('/api/endpoint', {
  method: 'POST',
  headers: {
    'X-CSRF-Token': csrfToken.token
  }
});
```

---

### 4. Input Sanitization ✅

**Arquivo criado:**
- `src/Security/InputSanitizer.php`

**Funcionalidades:**
- Sanitização de strings (remove HTML, XSS)
- Sanitização de arrays recursiva
- Validação e sanitização de emails
- Validação de números (int/float) com min/max
- Validação de URLs
- Prevenção de SQL Injection (remove padrões perigosos)

**Uso:**
```php
use App\Security\InputSanitizer;

// Sanitizar string
$clean = InputSanitizer::sanitizeString($input);

// Sanitizar array
$clean = InputSanitizer::sanitizeArray($data);

// Sanitizar dados de requisição
$clean = InputSanitizer::sanitizeRequestData($requestData);

// Validações específicas
$email = InputSanitizer::sanitizeEmail($email);
$int = InputSanitizer::sanitizeInt($number, 0, 100);
```

---

### 5. Security Configuration ✅

**Arquivo criado:**
- `config/packages/security.yaml`

**Configurações:**
- Firewall para `/api` com autenticação stateless
- Guard authenticator para API Keys
- Access control por roles
- Rotas públicas (login, register, health)

---

## 🚀 Como Instalar

### 1. Instalar Dependências

```bash
cd Backend
composer require lexik/jwt-authentication-bundle symfony/security-bundle symfony/security-csrf
composer update
```

**Nota:** `symfony/rate-limiter` pode não estar disponível no Symfony 4.4. O RateLimitSubscriber implementa uma solução customizada.

### 2. Executar Migration

```bash
php bin/console doctrine:migrations:migrate
```

### 3. Configurar Security

O arquivo `config/packages/security.yaml` já está configurado. Verifique se está correto para seu ambiente.

### 4. Limpar Cache

```bash
php bin/console cache:clear
```

---

## 📝 Como Usar

### Configurar API Key

A API Key é configurada via variável de ambiente `API_KEY` no arquivo `.env`:

```env
API_KEY=minha-chave-secreta-super-segura-123456
```

**Dica:** Gere uma chave forte:
```bash
# Linux/Mac
openssl rand -hex 32

# Ou use um gerador online de chaves seguras
```

### Usar API Key em Requisições

```bash
curl -H "X-API-Key: sua-api-key-aqui" \
     https://api.exemplo.com/api/endpoint
```

### Sanitizar Inputs em Controllers

```php
use App\Security\InputSanitizer;

public function create(Request $request)
{
    $data = json_decode($request->getContent(), true);
    
    // Sanitiza todos os dados
    $data = InputSanitizer::sanitizeRequestData($data);
    
    // Valida campos específicos
    $email = InputSanitizer::sanitizeEmail($data['email']);
    $age = InputSanitizer::sanitizeInt($data['age'], 0, 120);
    
    // Processa dados sanitizados...
}
```

---

## 🔒 Segurança Implementada

### ✅ Autenticação
- [x] API Key única do projeto
- [x] Validação simples e eficiente
- [x] Configuração via variável de ambiente
- [ ] JWT (pendente - requer configuração adicional)

### ✅ Autorização
- [x] Sistema de roles
- [x] Access control por rotas
- [ ] Permissões granulares (pendente)

### ✅ Proteção contra Ataques
- [x] Rate Limiting
- [x] CSRF Protection
- [x] SQL Injection Prevention
- [x] XSS Prevention (sanitização)
- [x] Input Validation

---

## 📋 Próximos Passos

### 1. Gerar API Key Segura

```bash
# Linux/Mac
openssl rand -hex 32

# Ou use um gerador online
# Depois adicione no .env:
# API_KEY=chave-gerada-aqui
```

### 2. Configurar JWT (Opcional)

Se quiser usar JWT além de API Keys:

```bash
composer require lexik/jwt-authentication-bundle
php bin/console lexik:jwt:generate-keypair
```

Depois configure em `config/packages/lexik_jwt_authentication.yaml`.

### 3. Implementar Permissões Granulares

Criar sistema de permissões mais detalhado baseado em recursos.

### 4. Adicionar Endpoint de CSRF Token

```php
/** @Route("/api/csrf-token", methods={"GET"}) */
public function getCsrfToken(CsrfTokenManagerInterface $csrfTokenManager, Request $request)
{
    $token = $csrfTokenManager->getToken($request->getPathInfo());
    return $this->success(['token' => $token->getValue()]);
}
```

### 5. Adicionar Logging de Segurança

Logar tentativas de autenticação falhadas, rate limits excedidos, etc.

---

## 🐛 Troubleshooting

### Erro: "API Key não encontrada"
**Solução:** Verifique se a API Key foi criada e está ativa no banco de dados.

### Erro: "Rate limit excedido"
**Solução:** Aguarde o tempo indicado em `Retry-After` ou ajuste os limites em `RateLimitSubscriber`.

### Erro: "Token CSRF inválido"
**Solução:** Certifique-se de enviar o token CSRF no header `X-CSRF-Token` ou no body/query como `_token`.

### Erro: "Class not found" ao usar InputSanitizer
**Solução:** Limpe o cache: `php bin/console cache:clear`

---

## 📚 Referências

- [Symfony Security](https://symfony.com/doc/4.4/security.html)
- [Lexik JWT Bundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)

---

**Status:** ✅ Implementação Básica Completa
**Data:** 2024-12-03

**Pendente:**
- [ ] Configuração completa de JWT
- [ ] Endpoint para gerenciar API Keys
- [ ] Permissões granulares
- [ ] Logging de segurança

