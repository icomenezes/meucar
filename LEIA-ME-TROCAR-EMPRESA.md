# IMPLEMENTAÇÃO: Sistema de Trocar Empresa

## Resumo
Um novo atalho foi adicionado à `layout.phtml` que permite aos usuários administrativos (perfil 1) ou da empresa 239 trocar rapidamente de empresa sem fazer logout.

## Arquivos Modificados

### 1. `/application/layouts/scripts/layout.phtml`
**O que foi adicionado:**
- Um novo atalho visual na barra de ícones (ícone de empresa)
- Um modal HTML para seleção de empresa
- Código JavaScript para gerenciar o modal e fazer as chamadas AJAX

**Exibição do atalho:**
- Apenas para usuários com perfil 1 (Admin) ou empresa_id 239
- Aparece na barra de ícones entre o header e os demais atalhos

## Arquivos Criados

### 1. `/application/views/scripts/usuarios/ajax/listar-empresas.phtml`
View que retorna JSON com a lista de empresas disponíveis.

### 2. `/application/views/scripts/usuarios/ajax/trocar-empresa.phtml`
View que retorna JSON com o resultado da operação de troca de empresa.

## PRÓXIMOS PASSOS - IMPLEMENTAÇÃO NO CONTROLLER

Você precisa adicionar as seguintes ações ao seu `UsuariosController.php`:

### 1. Ação: `ajaxListarempresas`

```php
public function ajaxListarempresesAction()
{
    // Desabilita layout para retornar apenas JSON
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(false);
    
    // Valida se o usuário está logado
    if(!isset($_SESSION['sessionUser']) || empty($_SESSION['sessionUser'])) {
        $this->view->dadosEmpresasJson = ['success' => false, 'message' => 'Não autenticado'];
        return;
    }
    
    // Valida permissão
    $perfil = $_SESSION['sessionUser']['id_perfil'];
    $empresa = $_SESSION['sessionUser']['id_empresa'];
    
    if($perfil != 1 && $empresa != 239) {
        $this->view->dadosEmpresasJson = ['success' => false, 'message' => 'Permissão negada'];
        return;
    }
    
    try {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Se for admin (perfil 1), lista todas as empresas
        if($perfil == 1) {
            $sql = "SELECT id_empresa, nome_fantasia FROM empresas ORDER BY nome_fantasia";
        } else {
            // Para empresa 239: liste empresas que tem acesso
            $sql = "SELECT id_empresa, nome_fantasia FROM empresas WHERE id_empresa = " . (int)$empresa . " ORDER BY nome_fantasia";
        }
        
        $result = $db->query($sql)->fetchAll();
        
        $this->view->dadosEmpresasJson = [
            'success' => true,
            'empresas' => $result
        ];
        
    } catch(Exception $e) {
        $this->view->dadosEmpresasJson = [
            'success' => false,
            'message' => 'Erro ao listar empresas'
        ];
    }
}
```

### 2. Ação: `ajaxTrocarempresa`

```php
public function ajaxTrocarempresaAction()
{
    // Desabilita layout para retornar apenas JSON
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(false);
    
    $resposta = ['success' => false, 'message' => 'Erro desconhecido'];
    
    // Valida se o usuário está logado
    if(!isset($_SESSION['sessionUser']) || empty($_SESSION['sessionUser'])) {
        $resposta = ['success' => false, 'message' => 'Não autenticado'];
        $this->view->resposta = $resposta;
        return;
    }
    
    // Valida permissão
    $perfil = $_SESSION['sessionUser']['id_perfil'];
    $empresa_atual = $_SESSION['sessionUser']['id_empresa'];
    
    if($perfil != 1 && $empresa_atual != 239) {
        $resposta = ['success' => false, 'message' => 'Permissão negada'];
        $this->view->resposta = $resposta;
        return;
    }
    
    // Obtém ID da empresa do POST
    $id_empresa = isset($_POST['id_empresa']) ? (int)$_POST['id_empresa'] : 0;
    
    if(!$id_empresa) {
        $resposta = ['success' => false, 'message' => 'ID da empresa inválido'];
        $this->view->resposta = $resposta;
        return;
    }
    
    try {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        // Valida se a empresa existe
        $sql = "SELECT id_empresa FROM empresas WHERE id_empresa = " . $id_empresa;
        $empresa = $db->query($sql)->fetch();
        
        if(!$empresa) {
            $resposta = ['success' => false, 'message' => 'Empresa não encontrada'];
            $this->view->resposta = $resposta;
            return;
        Arquivo de documentacao removido durante rollback. Mantido apenas para registro.
        
