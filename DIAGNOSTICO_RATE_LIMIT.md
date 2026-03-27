# 🚨 Diagnóstico - Rate Limit 429 - Meu Carro

## **Problemas Identificados**

### **1. ⚠️ CRÍTICO: ClientesController.php - getByCpfNome() [Linha 1239]**
**Status:** ✅ CORRIGIDO

```php
// ANTES (SEM LIMIT)
$arrC = $dbCliente->fetchAll("(cpf LIKE  '".$this->_getParam('f')."%' OR nome LIKE  '".$this->_getParam('f')."%') AND id_empresa = ".$_SESSION['sessionUser']['id_empresa']);

// DEPOIS (COM LIMIT 50)
$arrC = $dbCliente->fetchAll("(cpf LIKE  '".$this->_getParam('f')."%' OR nome LIKE  '".$this->_getParam('f')."%') AND id_empresa = ".$_SESSION['sessionUser']['id_empresa']." LIMIT 50");
```

**Impacto:** 🔴 CRÍTICO
**Motivo:** Esta função é chamada via AJAX repetidamente e retornava **TODOS os clientes** que começam com o termo buscado. Com milhares de clientes no banco, isso causava:
- Queries gigantes
- Múltiplas requisições simultâneas
- Esgotamento do rate limit (100 requisições)
- Bloqueio HTTP 429

---

### **2. ⚠️ ClientesController.php - getById() [Linha 1251]**
**Status:** ✅ CORRIGIDO

```php
// ANTES
$arrC = $dbCliente->fetchAll("id = " . $this->_getParam('f'));

// DEPOIS
$arrC = $dbCliente->fetchAll("id = " . $this->_getParam('f')." LIMIT 1");
```

**Impacto:** 🟡 MÉDIO
**Motivo:** Melhor prática - sempre usar LIMIT 1 quando se busca por ID único

---

### **3. 🐛 Clientes.php - Erro Lógico [Linha 108]**
**Status:** ✅ CORRIGIDO

```php
// ANTES (ERRO)
if(isset($arr['cpf'])){
    $row->where("c.cpf = '" . $arr['cnpj'] . "'");  // ❌ Acessa índice errado
}

// DEPOIS
if(isset($arr['cpf'])){
    $row->where("c.cpf = '" . $arr['cpf'] . "'");  // ✅ Índice correto
}
```

**Impacto:** 🟡 MÉDIO
**Motivo:** O código estava acessando `$arr['cnpj']` quando deveria acessar `$arr['cpf']`. Isso causava queries vazias ou incorretas.

---

### **4. ⚠️ Clientes.php - _get() [Linha 170]**
**Status:** ⚠️ REQUER REVISÃO

```php
// LINHA 170 - SEM LIMIT
return $row->query()->fetchAll();
```

**Impacto:** 🟠 ALTO (se usado sem filtros)
**Motivo:** Este método pode retornar TODOS os clientes da empresa se não for chamado com filtros restritivos

**Recomendação:**
- Adicionar um LIMIT padrão (ex: 5000) para segurança
- Documentar que uso sem filtros é perigoso
- Ou forçar filtro obrigatório

---

### **5. 🔴 SQL Injection em Múltiplos Locais**
**Severidade:** 🔴 CRÍTICO (Segurança)

As queries usam string concatenation ao invés de prepared statements:
```php
// ❌ INSEGURO
$dbCliente->fetchAll("(cpf LIKE  '".$this->_getParam('f')."%'..."

// ✅ SEGURO
$dbCliente->fetchAll("(cpf LIKE  ?..."  // com prepared statements
```

**Locais encontrados:**
- ClientesController.php: Linhas 1239, 1251, 1265...
- Clientes.php: Linhas 78, 84, 90, 102, etc.

**Recomendação:** Migrar para prepared statements do Zend_Db ou usar binding de parâmetros.

---

## **O que causou os bloqueios (429)**

Baseado nos logs de erro do Apache:

1. **Cliente com IP 191.252.83.220** fez >100 requisições em poucos segundos
   - Cada chamada a `/clientes/ajax/fn/getByCpfNome` retornava centenas/milhares de registros
   - O navegador fazia múltiplas requisições simultâneas (RGraph JS, dashboard_b1t.js, etc.)
   - ModSecurity bloqueou após 100 requisições em 1 segundo

2. **Fluxo do problema:**
   ```
   Usuário digita na busca de clientes
   → JavaScript faz AJAX call (getByCpfNome)
   → Query retorna 5.000+ clientes (sem LIMIT)
   → Navegador tenta carregar JS e CSS em paralelo
   → ~15-20 requisições simultâneas
   → Cliente atinge 100 requisições
   → ModSecurity bloqueia com HTTP 429
   ```

---

## **Ações Tomadas**

✅ Adicionado `LIMIT 50` em `getByCpfNome()` - máximo de resultados da busca
✅ Adicionado `LIMIT 1` em `getById()` - segurança
✅ Corrigido erro de índice em `_get()` - `$arr['cnpj']` → `$arr['cpf']`

---

## **Próximos Passos Recomendados**

1. **Imediato:**
   - Implementar prepared statements em todas as queries
   - Adicionar LIMIT padrão nos métodos sem filtro obrigatório
   - Aumentar o rate limit no Apache se 100 requisições/s é insuficiente

2. **Curto Prazo:**
   - Implementar paginação no frontend (AJAX com offset/limit)
   - Adicionar cache para buscas de clientes
   - Considerar usar Elasticsearch ou similar para buscas grandes

3. **Médio Prazo:**
   - Refatorar camada de dados para usar ORM moderno (Doctrine, Eloquent)
   - Implementar testes de performance
   - Monitorar queries lentas com Query Profiler

---

## **Teste**

Para validar a correção:
```javascript
// Abrir Developer Tools (F12)
// Ir para Negociações > Add Compra
// Buscar um cliente
// Verificar Network tab
// Cada requisição deve retornar max 50 resultados
```

---

**Data:** 27/03/2026
**Investigado:** Claude Code
