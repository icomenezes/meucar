# Upload de 4 fotos "Quem Somos" no cadastro de empresa

Data: 2026-06-29
Projeto: sistemameucar (ERP)

## Contexto

Dois sistemas distintos compartilham a mesma rede/servidor de arquivos:

- **sistemameucar** (este projeto, ERP): cadastra empresas, veículos, fotos e grava
  os arquivos no servidor em `meucar.trsystem.com.br/public/frente_loja_empresas/{id}/`.
- **site_padrao_meucar** (site público): exibe os carros e a página "Quem Somos".
  Em `quem-somos.php` ele referencia 4 fotos da loja **apenas pelo path fixo**, nunca
  lendo colunas do banco:

```php
$foto_1 = "frente_loja_empresas/".$idEmpresa."/loja-1-4.jpeg";
$foto_2 = "frente_loja_empresas/".$idEmpresa."/loja-2-4.jpeg";
$foto_3 = "frente_loja_empresas/".$idEmpresa."/loja-3-4.jpeg";
$foto_4 = "frente_loja_empresas/".$idEmpresa."/loja-4-4.jpeg";
```

Hoje essas 4 fotos são colocadas manualmente na pasta. O objetivo é permitir o
upload pela tela de cadastro/edição de empresa no ERP.

**Esta alteração é exclusivamente no projeto sistemameucar.** O site não muda.

## Objetivo

No cadastro/edição de empresa (`/empresas/edt/id/{id}` e `/empresas/add`):

1. Permitir upload de 4 fotos da loja ("Quem Somos").
2. Salvá-las na pasta `frente_loja_empresas/{idEmpresa}/` com os nomes fixos
   `loja-1-4.jpeg`, `loja-2-4.jpeg`, `loja-3-4.jpeg`, `loja-4-4.jpeg` (exatamente
   como o site espera).
3. Registrar o path de cada foto em uma coluna nova da tabela `empresas`
   (rastreabilidade/controle interno do ERP: preview + botão deletar na tela).
4. Permitir deletar cada foto individualmente.

## 1. Banco de dados (tabela `empresas`)

Adicionar 4 colunas para registrar os paths salvos:

```sql
ALTER TABLE empresas
  ADD COLUMN path_quem_somos_1 VARCHAR(255) NULL AFTER path_frente_loja_2,
  ADD COLUMN path_quem_somos_2 VARCHAR(255) NULL AFTER path_quem_somos_1,
  ADD COLUMN path_quem_somos_3 VARCHAR(255) NULL AFTER path_quem_somos_2,
  ADD COLUMN path_quem_somos_4 VARCHAR(255) NULL AFTER path_quem_somos_3;
```

A migration é entregue como arquivo `.sql` para o usuário rodar no servidor.

A model `Application_Model_DbTable_Empresas` estende `Zend_Db_Table_Abstract` e usa
`SELECT *` em todas as consultas — **não precisa ser alterada**; as colunas novas
aparecem automaticamente nos arrays de empresa.

## 2. Nomes de arquivo (fixos)

Salvos em `frente_loja_empresas/{idEmpresa}/`:

| Campo do form  | Arquivo gravado   | Coluna no banco       |
|----------------|-------------------|-----------------------|
| `quem_somos_1` | `loja-1-4.jpeg`   | `path_quem_somos_1`   |
| `quem_somos_2` | `loja-2-4.jpeg`   | `path_quem_somos_2`   |
| `quem_somos_3` | `loja-3-4.jpeg`   | `path_quem_somos_3`   |
| `quem_somos_4` | `loja-4-4.jpeg`   | `path_quem_somos_4`   |

Sempre `.jpeg` — a imagem é reconvertida para JPEG no redimensionamento,
independentemente do formato enviado. O sufixo `-4` é fixo para todas as empresas
(padrão observado nos arquivos de cliente do site).

## 3. Controller (`EmpresasController.php`)

### `edtAction()` e `addAction()`

Para cada um dos 4 campos `quem_somos_1..4`, replicar o padrão dos uploads
existentes (`frente_loja`/`frente_loja_2`):

1. Validar `$_FILES['quem_somos_N']['type']` contra o array de tipos de imagem já
   usado no arquivo.
2. Criar a pasta `frente_loja_empresas/{id}` se não existir
   (`mkdir` + `chmod 0755`) — mesma lógica atual.
3. Redimensionar para **largura 800px** mantendo proporção, converter para JPEG
   (`ImageCreateTrueColor` / `ImageCopyResampled` / `ImageJPEG`), gravar com o nome
   fixo `loja-N-4.jpeg`, `chmod 0755`, liberar memória (`ImageDestroy`).
4. Gravar o path em `path_quem_somos_N`:
   - No `edtAction()`: `$_POST['path_quem_somos_N'] = $novoNome;` (o `update`
     final já persiste).
   - No `addAction()`: `$dadosUp['path_quem_somos_N'] = $novoNome;` seguido de
     `$dbEmpresa->update($dadosUp, "id = ".$idEmpresa);` (mesmo padrão das outras
     fotos no add).

Cada upload é independente — se um falha, os demais e o resto do cadastro
continuam (igual ao comportamento atual). Mensagem de aviso no padrão existente.

### `ajaxAction()`

Adicionar 4 handlers, espelho de `deleta_frente_loja`:
`deleta_quem_somos_1`, `deleta_quem_somos_2`, `deleta_quem_somos_3`,
`deleta_quem_somos_4`. Cada um:

1. `getEmpresa($id)` para obter o path atual da coluna `path_quem_somos_N`.
2. `update(['path_quem_somos_N' => null], "id = ".$id)`.
3. `unlink($path)` do arquivo.
4. `echo "Sucesso"` / mensagem de erro.

## 4. View (`edt.phtml` e `add.phtml`)

Adicionar uma seção visual "Quem Somos (Fotos da Loja)" com 4 blocos de upload,
idênticos ao padrão `upload_frente_loja_2`:

- Se `$this->empresa['path_quem_somos_N']` preenchido → tabela com `<img>` preview
  + botão Deletar chamando `confirmaDeleteQuemSomosN(id)`.
- Senão → `<input type="file" id="quem_somos_N" name="quem_somos_N" />`.

Containers com ids `upload_quem_somos_N` / `up_quem_somos_N` (para o JS remover
após deletar).

No bloco `<script>`, adicionar 4 funções `confirmaDeleteQuemSomos1..4(id)`, espelho
de `confirmaDeleteFrenteLoja2`, chamando
`/empresas/ajax/fn/deleta_quem_somos_N/id/{id}`.

`add.phtml`: incluir os 4 inputs de upload na mesma seção (no add não há preview,
pois a empresa ainda não existe — apenas `<input type="file">`).

## 5. Tratamento de erro

Mantém o padrão existente do arquivo: cada upload é isolado em seu próprio `if`,
não bloqueia os demais nem o `update`/`insert` principal. Mensagens de aviso
reaproveitam o estilo das mensagens atuais.

## Arquivos afetados

- `application/controllers/EmpresasController.php` (add, edt, ajax)
- `application/views/scripts/empresas/edt.phtml` (form + JS)
- `application/views/scripts/empresas/add.phtml` (form)
- Novo: `scripts/migration_quem_somos.sql` (ALTER TABLE)

## Pontos de atenção

- **Largura 800px**: escolhida para as fotos de galeria (interiores). As fotos de
  frente de loja usam 640px; 800px dá mais qualidade para o "quem somos". Ajustável
  depois se necessário.
- **Resize assume JPEG na origem** (`ImageCreateFromJPEG`), igual ao código atual de
  `frente_loja`. Envio de PNG quebraria o resize — limitação herdada do padrão
  existente, mantida por consistência. O site espera `.jpeg`.
- **Sem alteração na model** — `SELECT *` cobre as colunas novas.
- **Sem alteração no site** — `site_padrao_meucar` só lê o path fixo.
