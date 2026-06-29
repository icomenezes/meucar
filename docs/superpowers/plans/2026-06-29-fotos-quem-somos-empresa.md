# Fotos "Quem Somos" no cadastro de empresa — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Permitir o upload de 4 fotos da loja ("Quem Somos") no cadastro/edição de empresa do ERP, salvando-as com nomes fixos na pasta da empresa e registrando os paths em 4 colunas novas.

**Architecture:** App legado Zend Framework 1 (PHP). Não há framework de testes no repositório, então a verificação é via `php -l` (lint de sintaxe) + checagem manual no navegador. As fotos são gravadas em `frente_loja_empresas/{idEmpresa}/loja-N-4.jpeg` (nomes que o site `site_padrao_meucar` consome por convenção). O código replica fielmente o padrão dos uploads `frente_loja`/`frente_loja_2` já existentes no `EmpresasController`.

**Tech Stack:** PHP 8.3, Zend Framework 1, GD (ImageCreate*/ImageJPEG), MySQL, jQuery (front).

## Global Constraints

- **Sistema em PRODUÇÃO** — toda a implementação vai em **um único commit** (revert fácil). NÃO commitar por task; só no final (Task 6).
- Nomes de arquivo **fixos** exatamente: `loja-1-4.jpeg`, `loja-2-4.jpeg`, `loja-3-4.jpeg`, `loja-4-4.jpeg` (sufixo `-4` fixo para toda empresa). Sempre `.jpeg`.
- Pasta destino: `frente_loja_empresas/{idEmpresa}/` (relativa ao public, igual aos uploads existentes).
- Colunas no banco: `path_quem_somos_1`, `path_quem_somos_2`, `path_quem_somos_3`, `path_quem_somos_4`.
- Campos do form / `$_FILES`: `quem_somos_1`, `quem_somos_2`, `quem_somos_3`, `quem_somos_4`.
- Largura de redimensionamento: **800px** (mantendo proporção).
- Replicar o padrão existente — não "melhorar" o código herdado (ex.: `end(explode(...))`, validação de `type`), para não introduzir comportamento divergente.
- A model `Empresas.php` **não muda** (usa `SELECT *`).
- O projeto `site_padrao_meucar` **não muda**.

---

### Task 1: Migration SQL das 4 colunas

**Files:**
- Create: `scripts/migration_quem_somos.sql`

**Interfaces:**
- Produces: colunas `path_quem_somos_1..4` (VARCHAR(255) NULL) na tabela `empresas`, consumidas pelo controller e pela view.

- [ ] **Step 1: Criar o arquivo de migration**

Conteúdo de `scripts/migration_quem_somos.sql`:

```sql
-- Migration: 4 colunas para fotos "Quem Somos" no cadastro de empresa
-- Rodar no banco de produção do sistemameucar.
-- Revert no rodapé (comentado).

ALTER TABLE empresas
  ADD COLUMN path_quem_somos_1 VARCHAR(255) NULL DEFAULT NULL AFTER path_frente_loja_2,
  ADD COLUMN path_quem_somos_2 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_1,
  ADD COLUMN path_quem_somos_3 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_2,
  ADD COLUMN path_quem_somos_4 VARCHAR(255) NULL DEFAULT NULL AFTER path_quem_somos_3;

-- ===== REVERT (se necessário) =====
-- ALTER TABLE empresas
--   DROP COLUMN path_quem_somos_1,
--   DROP COLUMN path_quem_somos_2,
--   DROP COLUMN path_quem_somos_3,
--   DROP COLUMN path_quem_somos_4;
```

> Nota: se a coluna `path_frente_loja_2` não existir com esse nome exato no banco
> de produção, o `AFTER path_frente_loja_2` falha. Nesse caso, remover os `AFTER`
> (as colunas são adicionadas no fim da tabela — sem impacto funcional). O usuário
> roda este script manualmente e ajusta se preciso.

- [ ] **Step 2: Validar a sintaxe do SQL visualmente**

Conferir: 4 colunas, todas `VARCHAR(255) NULL`, nomes exatos `path_quem_somos_1..4`. Sem ponto-e-vírgula faltando. (Sem commit aqui — ver Global Constraints.)

---

### Task 2: Handlers de delete no `ajaxAction()`

**Files:**
- Modify: `application/controllers/EmpresasController.php` (dentro de `ajaxAction()`, após o bloco `deleta_frente_loja_2`, ~linha 95)

**Interfaces:**
- Consumes: `Application_Model_DbTable_Empresas::getEmpresa($id)` (retorna array; índice `[0]['path_quem_somos_N']`), `->update($dados, $where)`.
- Produces: rotas `/empresas/ajax/fn/deleta_quem_somos_N/id/{id}` que ecoam `"Sucesso"` ou mensagem de erro, consumidas pelo JS da view (Task 4).

- [ ] **Step 1: Adicionar os 4 handlers**

No `EmpresasController.php`, localizar o fim do bloco `elseif($this->_getParam('fn') == 'deleta_frente_loja_2'){ ... }` (a chave de fechamento logo antes do `elseif($this->_getParam('fn') == 'busca_cidade'){`). Inserir, ANTES do `elseif`/`busca_cidade`, os 4 blocos abaixo. Eles seguem exatamente o padrão de `deleta_frente_loja`:

```php
		}elseif($this->_getParam('fn') == 'deleta_quem_somos_1'){

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));

			$path = $arrEmpresas[0]['path_quem_somos_1'];

			$dadosUp['path_quem_somos_1'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){

				unlink($path);

				echo "Sucesso";

			}else{

				echo "Erro ao deletar imagem";

			}

		}elseif($this->_getParam('fn') == 'deleta_quem_somos_2'){

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));

			$path = $arrEmpresas[0]['path_quem_somos_2'];

			$dadosUp['path_quem_somos_2'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){

				unlink($path);

				echo "Sucesso";

			}else{

				echo "Erro ao deletar imagem";

			}

		}elseif($this->_getParam('fn') == 'deleta_quem_somos_3'){

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));

			$path = $arrEmpresas[0]['path_quem_somos_3'];

			$dadosUp['path_quem_somos_3'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){

				unlink($path);

				echo "Sucesso";

			}else{

				echo "Erro ao deletar imagem";

			}

		}elseif($this->_getParam('fn') == 'deleta_quem_somos_4'){

			$dbEmpresa = new Application_Model_DbTable_Empresas();

			$arrEmpresas = $dbEmpresa->getEmpresa($this->_getParam('id'));

			$path = $arrEmpresas[0]['path_quem_somos_4'];

			$dadosUp['path_quem_somos_4'] = null;
			if($dbEmpresa->update($dadosUp,"id = ".$this->_getParam('id'))){

				unlink($path);

				echo "Sucesso";

			}else{

				echo "Erro ao deletar imagem";

			}

```

> A linha seguinte no arquivo já é `		}elseif($this->_getParam('fn') == 'busca_cidade'){` — não duplicar essa chave; os blocos acima terminam SEM a chave de fechamento final, pois ela é a abertura do `elseif` do `busca_cidade`. Conferir o balanceamento de chaves após colar.

- [ ] **Step 2: Lint**

Run: `php -l application/controllers/EmpresasController.php`
Expected: `No syntax errors detected in application/controllers/EmpresasController.php`

(Sem commit — ver Global Constraints.)

---

### Task 3: Upload no `edtAction()`

**Files:**
- Modify: `application/controllers/EmpresasController.php` (dentro de `edtAction()`, após o bloco de upload `frente_loja_2`, antes de `$dbEmpresas->update($_POST,"id = ".$idEmpresa);`)

**Interfaces:**
- Consumes: `$_FILES['quem_somos_N']`, `$_POST['nome_fantasia']`, `$idEmpresa` (= `$this->_getParam('id')`, já definido no método).
- Produces: arquivos `frente_loja_empresas/{id}/loja-N-4.jpeg`; chaves `$_POST['path_quem_somos_N']` (persistidas pelo `update($_POST,...)` que já existe ao final do método).

- [ ] **Step 1: Adicionar os 4 blocos de upload**

No `edtAction()`, localizar o fim do bloco `if(in_array($_FILES['frente_loja_2']['type'], ...)){ ... }else{ ... }` (o `else` cuja mensagem fala em "frente de loja 2"). Logo após o fechamento desse `else`, e ANTES da linha `$dbEmpresas->update($_POST,"id = ".$idEmpresa);`, inserir:

```php
			$quemSomosCampos = array(
				'quem_somos_1' => 'loja-1-4',
				'quem_somos_2' => 'loja-2-4',
				'quem_somos_3' => 'loja-3-4',
				'quem_somos_4' => 'loja-4-4'
			);

			foreach($quemSomosCampos as $campo => $nomeArquivo){

				if(in_array($_FILES[$campo]['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

					if(!file_exists("frente_loja_empresas/".$idEmpresa)){
						mkdir("frente_loja_empresas/".$idEmpresa);
						chmod("frente_loja_empresas/".$idEmpresa, 0755);
					}

					$novoNome = "frente_loja_empresas/".$idEmpresa."/".$nomeArquivo.".jpeg";

					$copied = false;

					if($_FILES[$campo]['tmp_name'] != ""){

						/////////////////REDIMENCIONA IMAGEM///////////////////////
						$input_image = $_FILES[$campo]['tmp_name'];

						$size = getimagesize( $input_image );

						$thumb_width = "800";

						$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );

						$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );

						$src_img = ImageCreateFromJPEG( $input_image );

						ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );

						$copied = ImageJPEG( $thumbnail, $novoNome);
						chmod($novoNome, 0755);

						ImageDestroy( $thumbnail );

					}

					if($copied){

						$coluna = "path_".$campo; // path_quem_somos_N
						$_POST[$coluna] = $novoNome;

					}

				}

			}

```

> O nome da coluna é `path_` + nome do campo: `path_quem_somos_1..4`. Isso bate com as colunas da Task 1. O `update($_POST, ...)` que já existe ao final do `edtAction()` persiste essas chaves.

- [ ] **Step 2: Lint**

Run: `php -l application/controllers/EmpresasController.php`
Expected: `No syntax errors detected`

---

### Task 4: Upload no `addAction()`

**Files:**
- Modify: `application/controllers/EmpresasController.php` (dentro de `addAction()`, após o bloco de upload `frente_loja_2`, ainda dentro do `if($idEmpresa){ ... }`)

**Interfaces:**
- Consumes: `$_FILES['quem_somos_N']`, `$idEmpresa` (retorno do `insert`), `$dbEmpresa` (já instanciado no método).
- Produces: arquivos `loja-N-4.jpeg`; persiste via `$dbEmpresa->update($dadosUp, "id = ".$idEmpresa)` (no add não há `update($_POST)` final, então cada path é gravado na hora — igual ao padrão `frente_loja_2` do add).

- [ ] **Step 1: Adicionar os 4 blocos de upload**

No `addAction()`, localizar o fim do bloco `if(in_array($_FILES['frente_loja_2']['type'], ...)){ ... if($copied){ $dadosUp['path_frente_loja_2'] = $novoNome; $dbEmpresa->update(...); } }`. Logo após o fechamento desse `if` (e antes do comentário `///chama a função que cria os fornecedores ...` / do `}else{` do `if($idEmpresa)`), inserir:

```php
				$quemSomosCampos = array(
					'quem_somos_1' => 'loja-1-4',
					'quem_somos_2' => 'loja-2-4',
					'quem_somos_3' => 'loja-3-4',
					'quem_somos_4' => 'loja-4-4'
				);

				foreach($quemSomosCampos as $campo => $nomeArquivo){

					if(in_array($_FILES[$campo]['type'], array("image/pjpeg", "image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp"))){

						if(!file_exists("frente_loja_empresas/".$idEmpresa)){
							mkdir("frente_loja_empresas/".$idEmpresa);
							chmod("frente_loja_empresas/".$idEmpresa, 0755);
						}

						$novoNome = "frente_loja_empresas/".$idEmpresa."/".$nomeArquivo.".jpeg";

						$copied = false;

						if($_FILES[$campo]['tmp_name'] != ""){

							/////////////////REDIMENCIONA IMAGEM///////////////////////
							$input_image = $_FILES[$campo]['tmp_name'];

							$size = getimagesize( $input_image );

							$thumb_width = "800";

							$thumb_height = ( int )(( $thumb_width/$size[0] )*$size[1] );

							$thumbnail = ImageCreateTrueColor( $thumb_width, $thumb_height );

							$src_img = ImageCreateFromJPEG( $input_image );

							ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1] );

							$copied = ImageJPEG( $thumbnail, $novoNome);
							chmod($novoNome, 0755);

							ImageDestroy( $thumbnail );

						}

						if($copied){

							$colunaUp = array();
							$colunaUp["path_".$campo] = $novoNome;
							$dbEmpresa->update($colunaUp,"id = ".$idEmpresa);

						}

					}

				}

```

> Usa `$colunaUp` (array local fresco) por iteração, em vez de reaproveitar `$dadosUp`, para não arrastar chaves de uploads anteriores no `update`. Isso é mais seguro que o padrão herdado (que reusa `$dadosUp`), mas não muda o comportamento das fotos anteriores.

- [ ] **Step 2: Lint**

Run: `php -l application/controllers/EmpresasController.php`
Expected: `No syntax errors detected`

---

### Task 5: View — seção de upload + JS de delete

**Files:**
- Modify: `application/views/scripts/empresas/edt.phtml` (form: após o bloco `upload_frente_loja_2` ~linha 803; JS: após `confirmaDeleteFrenteLoja2` ~linha 1635)
- Modify: `application/views/scripts/empresas/add.phtml` (form: após o input `frente_loja_2` ~linha 789)

**Interfaces:**
- Consumes: `$this->empresa['path_quem_somos_N']`, `$this->empresa['id']` (já disponíveis na view via `edtAction`); rotas de delete da Task 2.
- Produces: inputs `name="quem_somos_N"` consumidos pelas Tasks 3 e 4.

- [ ] **Step 1: Adicionar a seção de upload no `edt.phtml`**

Localizar o fechamento do bloco `<div class="inline" ... id="upload_frente_loja_2"> ... </div>` (logo após a linha do `<?php } ?>` que fecha o input `frente_loja_2`, ~linha 803). Inserir logo depois:

```php
	<div class="inline" style="clear:both;height:auto;margin-bottom:10px;margin-top:20px;">
		<label class="label">QUEM SOMOS (Fotos da Loja)</label>
	</div>

	<div class="inline" style="height:10%;clear:both;" id="upload_quem_somos_1">
		<div id="up_quem_somos_1"><label class="label">Foto Loja 1</label>
	<?php if($this->empresa['path_quem_somos_1'] !=  ""){?>
		<table class="table"><tr><td><img style="width:170px; height:110px;" src="/<?php echo $this->empresa['path_quem_somos_1']; ?>"/></td><td><input type="button" class="btn-small btn-del" value="Deletar" onclick="confirmaDeleteQuemSomos1(<?php echo $this->empresa['id'];?>);"/></td></tr></table>
	<?php }else{ ?>
		<input type="file" id="quem_somos_1" name="quem_somos_1" class="text" value=""/>
	<?php } ?>
		</div>
	</div>

	<div class="inline" style="height:10%;" id="upload_quem_somos_2">
		<div id="up_quem_somos_2"><label class="label">Foto Loja 2</label>
	<?php if($this->empresa['path_quem_somos_2'] !=  ""){?>
		<table class="table"><tr><td><img style="width:170px; height:110px;" src="/<?php echo $this->empresa['path_quem_somos_2']; ?>"/></td><td><input type="button" class="btn-small btn-del" value="Deletar" onclick="confirmaDeleteQuemSomos2(<?php echo $this->empresa['id'];?>);"/></td></tr></table>
	<?php }else{ ?>
		<input type="file" id="quem_somos_2" name="quem_somos_2" class="text" value=""/>
	<?php } ?>
		</div>
	</div>

	<div class="inline" style="height:10%;" id="upload_quem_somos_3">
		<div id="up_quem_somos_3"><label class="label">Foto Loja 3</label>
	<?php if($this->empresa['path_quem_somos_3'] !=  ""){?>
		<table class="table"><tr><td><img style="width:170px; height:110px;" src="/<?php echo $this->empresa['path_quem_somos_3']; ?>"/></td><td><input type="button" class="btn-small btn-del" value="Deletar" onclick="confirmaDeleteQuemSomos3(<?php echo $this->empresa['id'];?>);"/></td></tr></table>
	<?php }else{ ?>
		<input type="file" id="quem_somos_3" name="quem_somos_3" class="text" value=""/>
	<?php } ?>
		</div>
	</div>

	<div class="inline" style="height:10%;" id="upload_quem_somos_4">
		<div id="up_quem_somos_4"><label class="label">Foto Loja 4</label>
	<?php if($this->empresa['path_quem_somos_4'] !=  ""){?>
		<table class="table"><tr><td><img style="width:170px; height:110px;" src="/<?php echo $this->empresa['path_quem_somos_4']; ?>"/></td><td><input type="button" class="btn-small btn-del" value="Deletar" onclick="confirmaDeleteQuemSomos4(<?php echo $this->empresa['id'];?>);"/></td></tr></table>
	<?php }else{ ?>
		<input type="file" id="quem_somos_4" name="quem_somos_4" class="text" value=""/>
	<?php } ?>
		</div>
	</div>

```

- [ ] **Step 2: Adicionar as 4 funções JS no `edt.phtml`**

Localizar o fim da função `function confirmaDeleteFrenteLoja2(id){ ... }` (~linha 1635, logo antes de `function validaCampo(){`). Inserir logo após o `}` que fecha `confirmaDeleteFrenteLoja2`:

```javascript
function confirmaDeleteQuemSomos1(id){

	decisao = confirm("Deseja realmente deletar a imagem?");

	if (decisao){

		$.ajax({
			type: "POST",
			url:"/empresas/ajax/fn/deleta_quem_somos_1/id/"+id,
			dataType:"html",
			success:function(retorno){

				if(retorno == "Sucesso"){

					$("#upload_quem_somos_1").html("<label class='label'>Foto Loja 1</label><input type='file' id='quem_somos_1' name='quem_somos_1' class='text' value=''/>");
					$("#up_quem_somos_1").remove();

				}else{

					alert(retorno);

				}

			}
		})

	}

}

function confirmaDeleteQuemSomos2(id){

	decisao = confirm("Deseja realmente deletar a imagem?");

	if (decisao){

		$.ajax({
			type: "POST",
			url:"/empresas/ajax/fn/deleta_quem_somos_2/id/"+id,
			dataType:"html",
			success:function(retorno){

				if(retorno == "Sucesso"){

					$("#upload_quem_somos_2").html("<label class='label'>Foto Loja 2</label><input type='file' id='quem_somos_2' name='quem_somos_2' class='text' value=''/>");
					$("#up_quem_somos_2").remove();

				}else{

					alert(retorno);

				}

			}
		})

	}

}

function confirmaDeleteQuemSomos3(id){

	decisao = confirm("Deseja realmente deletar a imagem?");

	if (decisao){

		$.ajax({
			type: "POST",
			url:"/empresas/ajax/fn/deleta_quem_somos_3/id/"+id,
			dataType:"html",
			success:function(retorno){

				if(retorno == "Sucesso"){

					$("#upload_quem_somos_3").html("<label class='label'>Foto Loja 3</label><input type='file' id='quem_somos_3' name='quem_somos_3' class='text' value=''/>");
					$("#up_quem_somos_3").remove();

				}else{

					alert(retorno);

				}

			}
		})

	}

}

function confirmaDeleteQuemSomos4(id){

	decisao = confirm("Deseja realmente deletar a imagem?");

	if (decisao){

		$.ajax({
			type: "POST",
			url:"/empresas/ajax/fn/deleta_quem_somos_4/id/"+id,
			dataType:"html",
			success:function(retorno){

				if(retorno == "Sucesso"){

					$("#upload_quem_somos_4").html("<label class='label'>Foto Loja 4</label><input type='file' id='quem_somos_4' name='quem_somos_4' class='text' value=''/>");
					$("#up_quem_somos_4").remove();

				}else{

					alert(retorno);

				}

			}
		})

	}

}

```

- [ ] **Step 3: Adicionar os inputs no `add.phtml`**

Localizar o input `<input type="file" id="frente_loja_2" name="frente_loja_2" .../>` (~linha 789) e o fechamento da sua `<div class="inline">`. Inserir logo após esse fechamento de div:

```php
	<div class="inline" style="clear:both;height:auto;margin-bottom:10px;margin-top:20px;">
		<label class="label">QUEM SOMOS (Fotos da Loja)</label>
	</div>

	<div class="inline" style="clear:both;">
		<label class="label">Foto Loja 1</label>
		<input type="file" id="quem_somos_1" name="quem_somos_1" class="text" value=""/>
	</div>

	<div class="inline">
		<label class="label">Foto Loja 2</label>
		<input type="file" id="quem_somos_2" name="quem_somos_2" class="text" value=""/>
	</div>

	<div class="inline">
		<label class="label">Foto Loja 3</label>
		<input type="file" id="quem_somos_3" name="quem_somos_3" class="text" value=""/>
	</div>

	<div class="inline">
		<label class="label">Foto Loja 4</label>
		<input type="file" id="quem_somos_4" name="quem_somos_4" class="text" value=""/>
	</div>

```

> Conferir se o `<form>` do `add.phtml` tem `enctype="multipart/form-data"` (necessário p/ upload). O `edt.phtml` já tem. Se faltar no add, adicionar ao `<form>`.

- [ ] **Step 4: Lint das views**

Run: `php -l application/views/scripts/empresas/edt.phtml`
Run: `php -l application/views/scripts/empresas/add.phtml`
Expected (ambos): `No syntax errors detected`

---

### Task 6: Verificação final + commit único

**Files:** (nenhum novo)

- [ ] **Step 1: Lint de todos os arquivos modificados**

Run:
```bash
php -l application/controllers/EmpresasController.php
php -l application/views/scripts/empresas/edt.phtml
php -l application/views/scripts/empresas/add.phtml
```
Expected (todos): `No syntax errors detected`

- [ ] **Step 2: Conferir o diff completo**

Run: `git --no-pager diff --stat` e `git --no-pager diff`
Verificar: só 3 arquivos PHP modificados + 1 SQL + 2 docs (spec/plan). Nenhuma alteração fora de escopo. Nomes de coluna/arquivo conferem com Global Constraints.

- [ ] **Step 3: Verificação manual no navegador (após o usuário rodar a migration)**

Checklist manual (o usuário executa, pois depende de produção/banco):
1. Rodar `scripts/migration_quem_somos.sql` no banco.
2. Abrir `/empresas/edt/id/{id}` de uma empresa de teste → ver a seção "QUEM SOMOS" com 4 inputs.
3. Subir 4 imagens, salvar → conferir em `frente_loja_empresas/{id}/` os arquivos `loja-1-4.jpeg`..`loja-4-4.jpeg` (largura ~800px).
4. Reabrir a edição → ver os 4 previews + botão Deletar.
5. Clicar Deletar em uma → confirma, some o preview, arquivo apagado, coluna zerada.
6. Abrir o `quem-somos.php` do site da empresa → as fotos aparecem.

- [ ] **Step 4: Commit único**

```bash
git add scripts/migration_quem_somos.sql \
        application/controllers/EmpresasController.php \
        application/views/scripts/empresas/edt.phtml \
        application/views/scripts/empresas/add.phtml \
        docs/superpowers/plans/2026-06-29-fotos-quem-somos-empresa.md
git commit -m "feat: upload de 4 fotos 'quem somos' no cadastro de empresa

- 4 colunas path_quem_somos_1..4 (migration SQL)
- upload em add/edt salvando loja-N-4.jpeg em frente_loja_empresas/{id} (800px)
- delete individual via ajax
- consumido pelo site_padrao_meucar por convencao de nome

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

Expected: 1 commit criado com os 5 arquivos (o spec da etapa de brainstorming já foi commitado antes; aqui entram código + migration + plano).

---

## Self-Review

**Spec coverage:**
- BD 4 colunas → Task 1 ✓
- Nomes fixos `loja-N-4.jpeg` → Tasks 3, 4 ✓
- Controller add/edt upload + resize 800px → Tasks 3, 4 ✓
- Controller ajax delete → Task 2 ✓
- View edt (preview+delete) + add (inputs) + JS → Task 5 ✓
- Model sem mudança → respeitado (nenhuma task toca a model) ✓
- Site sem mudança → respeitado ✓
- Commit único (produção) → Task 6 ✓

**Placeholder scan:** sem TBD/TODO; todo código está completo nos steps.

**Type/naming consistency:** colunas `path_quem_somos_N`, campos `quem_somos_N`, arquivos `loja-N-4.jpeg`, rotas `deleta_quem_somos_N`, funções `confirmaDeleteQuemSomosN`, ids `upload_quem_somos_N`/`up_quem_somos_N` — consistentes entre Tasks 1–5. ✓
