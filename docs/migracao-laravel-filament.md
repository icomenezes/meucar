# Documentação do Sistema MeuCar para Migração Laravel + Filament

## 1. Visão Geral do Sistema

O **Sistema MeuCar** é uma aplicação Zend Framework 1 completa para gerenciamento de concessionárias de carros usados. Trata-se de um sistema B2B/B2C que gerencia:

- **Inventário de Veículos**: Registro completo de carros (novos e usados) com especificações técnicas, fotos, avaliações e status de venda
- **Gestão de Clientes**: Cadastro de clientes, histórico de compras, negociações, acompanhamento
- **Negociações e Vendas**: Fluxo completo de venda (abertura, aprovação, concretização) com suporte a trocas e financiamentos
- **Financeiro**: Integração com financeiras/despachantes, cálculo de comissões, recebimentos
- **Recursos Humanos**: Gestão de usuários, vendedores, perfis, permissões
- **Multi-empresa**: Suporte a múltiplas concessionárias com separação de dados
- **Operacional**: Agenda, contratos, garantias, preparação de veículos, despesas
- **Integração Externa**: iCarros, WebMotors, FIPE (tabela de preços)

---

## 2. Módulos do Sistema

| Módulo | Controller | Descrição |
|--------|-----------|-----------|
| **Clientes** | ClientesController | Cadastro, busca, filtros de clientes com integração a fluxo de clientes |
| **Veículos** | VeiculosController, CarrosUsadosController | Cadastro, estoque, preparação, fotos, despesas de manutenção |
| **Negociações** | NegociacoesController | Abertura, aprovação, concretização de vendas com suporte a trocas |
| **Financeiras** | FinanceirasController | Cadastro de instituições financeiras e despachantes |
| **Usuários** | UsuariosController | Cadastro de usuários, autenticação, gestão de empresas |
| **Empresas** | EmpresasController | Cadastro de concessionárias, logos, dados corporativos |
| **Agenda** | AgendaController | Agendamentos, lembretes, tarefas dos usuários |
| **Contratos** | ContratosController | Templates e geração de contratos de venda |
| **Garantias** | GarantiasController | Registro de garantias (entrada, saída, cancelamento) |
| **Relatórios** | EstatisticasController | Relatórios de vendas, comissões, performance |

---

## 3. Estrutura do Banco de Dados

### Tabela: clientes
- **Descrição**: Armazena dados de clientes (pessoas físicas e jurídicas)
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `nome`, `cpf`, `cnpj`, `email`, `tel1`, `tel2`, `celular`, `sexo`, `data_nascimento`, `cidade`, `bairro`, `estado`, `endereco`, `cep`, `cargo`, `empresa`, `origem` (FK), `ativo`, `excluido`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas
  - FK: `id_usuario_alteracao` → usuarios
  - FK: `origem` → origem_clientes
  - Referenciado por: negociacoes, fluxo_clientes, contratos_recibos

---

### Tabela: veiculos
- **Descrição**: Inventário completo de veículos com especificações técnicas
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `id_modelo` (FK), `placa`, `renavam`, `chassis`, `km`, `ano_fabricacao`, `combustivel`, `cor`, `valor_aquisicao`, `valor_venda`, `valor_aquisicao_comprador`, `descricao_site`, `origem`, `consignado`, `vendido`, `ativo`, `excluido`, `novo_usado`, `exibir_site_estoque`, `exibir_valor_site`, `temp_troca`, `data_aquisicao`, `data_termino_revisao`, `id_negociacao` (FK), `id_negociacao_compra` (FK), `id_negociacao_troca`, `id_negociacao_troca2`, `id_usuario_alteracao` (FK), `icarros`, `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas
  - FK: `id_modelo` → modelos_11
  - FK: `id_negociacao` → negociacoes
  - FK: `id_usuario_alteracao` → usuarios
  - Referenciado por: fotos_veiculos, despesas_veiculos, garantias, checklist_veiculos, opcionais_veiculos

---

### Tabela: negociacoes
- **Descrição**: Registro de todas as transações (vendas e compras)
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `id_cliente` (FK), `id_veiculo` (FK), `id_vendedor` (FK), `id_supervisor` (FK), `id_gerente` (FK), `id_financeira` (FK), `id_despachante` (FK), `id_usuario` (FK), `data_abertura`, `data_concretizacao`, `valor_venda`, `valor_compra_vendedor`, `comissao_vendedor`, `comissao_supervisor`, `comissao_gerente`, `valor_financeiro`, `compra` (0=venda, 1=compra), `aprovada` (-1=rejeitada, 0=pendente, 1=aprovada), `hora_alteracao`, `id_usuario_alteracao` (FK)
- **Relacionamentos**:
  - FK: `id_cliente` → clientes
  - FK: `id_veiculo` → veiculos
  - FK: `id_vendedor`, `id_supervisor`, `id_gerente`, `id_usuario` → usuarios
  - FK: `id_empresa` → empresas
  - FK: `id_financeira`, `id_despachante` → financeiras_despachantes
  - Referenciado por: contratos_recibos, recebimentos_negociacoes, trocas_negociacoes

---

### Tabela: financeiras_despachantes
- **Descrição**: Instituições financeiras e despachantes de documentos
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `nome`, `cnpj`, `tipo` (0=financeira, 1=despachante), `imposto`, `ativo`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas
  - FK: `id_usuario_alteracao` → usuarios
  - Referenciado por: negociacoes

---

### Tabela: usuarios
- **Descrição**: Usuários do sistema (vendedores, gerentes, administradores)
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `login`, `senha`, `nome`, `email`, `id_perfil` (FK), `telefone`, `celular`, `cidade`, `bairro`, `estado`, `endereco`, `cep`, `ativo`, `excluido`, `data_contratacao`, `data_demissao`, `receber_emails`, `relatorio_projetado`, `id_usuario_facebook`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas
  - FK: `id_perfil` → perfis
  - FK: `id_usuario_alteracao` → usuarios (self-reference)
  - Referenciado por: vendedores, negociacoes, clientes, veiculos, agenda, etc.

---

### Tabela: empresas
- **Descrição**: Concessionárias cadastradas no sistema
- **Campos identificados**:
  - `id` (PK), `razao_social`, `nome_fantasia`, `cnpj`, `email`, `tel1`, `tel2`, `cep`, `endereco`, `bairro`, `cidade`, `estado`, `path` (logo), `path_frente_loja`, `path_frente_loja_2`, `ativo`, `sistema_site` (0=interno, 1=site próprio), `novo_lojista`, `vend_nao_edit_valor`, `logado`, `login_icarros`, `senha_icarros`, `url_site`, `excluido`, `tipo_empresa`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_usuario_alteracao` → usuarios
  - Referenciado por: usuarios, veiculos, clientes, negociacoes, etc.

---

### Tabela: agenda
- **Descrição**: Agendamentos, tarefas e lembretes dos usuários
- **Campos identificados**:
  - `id` (PK), `id_usuario` (FK), `data`, `hora`, `descricao`, `obs`, `baixado`, `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_usuario` → usuarios

---

### Tabela: garantias
- **Descrição**: Registro de garantias concedidas aos veículos vendidos
- **Campos identificados**:
  - `id` (PK), `id_veiculo` (FK), `id_fornecedor` (FK), `data_entrada`, `data_saida`, `data_cancelamento`, `dias_garantia`, `km_garantia`, `custo`, `observacao`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_veiculo` → veiculos
  - FK: `id_fornecedor` → fornecedores
  - FK: `id_usuario_alteracao` → usuarios

---

### Tabela: fotos_veiculos
- **Descrição**: Galeria de fotos dos veículos
- **Campos identificados**:
  - `id` (PK), `id_veiculo` (FK), `path` (caminho arquivo), `capa` (0/1 = foto destaque), `icarros`, `webmotors`
- **Relacionamentos**:
  - FK: `id_veiculo` → veiculos

---

### Tabela: despesas_veiculos
- **Descrição**: Custos de manutenção e preparação dos veículos
- **Campos identificados**:
  - `id` (PK), `id_veiculo` (FK), `id_fornecedor` (FK), `data`, `despesa` (descrição), `valor`, `ramo_atividade`
- **Relacionamentos**:
  - FK: `id_veiculo` → veiculos
  - FK: `id_fornecedor` → fornecedores

---

### Tabela: contratos_recibos
- **Descrição**: Templates de contratos e recibos de venda
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `nome`, `conteudo` (HTML), `marca`, `modelo`, `ano_modelo`, `id_root`, `id_usuario_alteracao` (FK), `hora_alteracao`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas

---

### Tabela: modelos_11
- **Descrição**: Catálogo de modelos de veículos (integrado com FIPE)
- **Campos identificados**:
  - `id` (PK), `marca`, `modelo`, `ano_modelo`, `preco` (FIPE), `cod_fipe`, `segmento`, `combustivel`
- **Relacionamentos**:
  - Referenciado por: veiculos

---

### Tabela: perfis
- **Descrição**: Papéis/funções no sistema
- **Campos identificados**:
  - `id` (PK), `perfil` (nome), `descricao`
- **Relacionamentos**:
  - Referenciado por: usuarios

---

### Tabela: vendedores
- **Descrição**: Informações de comissão dos vendedores
- **Campos identificados**:
  - `id` (PK), `id_usuario` (FK), `valor_fixo`, `percentual_venda`, `percentual_retorno_financeiro`, `manual`
- **Relacionamentos**:
  - FK: `id_usuario` → usuarios

---

### Tabela: fluxo_clientes
- **Descrição**: Rastreamento do cliente desde o contato até a venda
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `id_usuario` (FK), `nome`, `email`, `telefone`, `origem` (FK), `resultado`, `motivo_pre`, `data`, `data_agendamento`, `gerado_negociacao` (FK), `imap_origem`, `hash`
- **Relacionamentos**:
  - FK: `id_usuario` → usuarios
  - FK: `id_empresa` → empresas

---

### Tabela: fornecedores
- **Descrição**: Fornecedores de peças, serviços e garantias
- **Campos identificados**:
  - `id` (PK), `id_empresa` (FK), `razao_social`, `cnpj`, `ramo_atividade`, `email`, `telefone`, `ativo`
- **Relacionamentos**:
  - FK: `id_empresa` → empresas

---

### Tabela: opcionais
- **Descrição**: Lista de opcionais/acessórios dos veículos
- **Campos identificados**:
  - `id` (PK), `opcional` (nome), `id_opcionais_icarros`, `id_opcionais_webmotors`

---

### Tabela: veiculos_opcionais
- **Descrição**: Relacionamento entre veículos e seus opcionais (pivot)
- **Campos identificados**:
  - `id` (PK), `id_veiculo` (FK), `id_opcional` (FK)
- **Relacionamentos**:
  - FK: `id_veiculo` → veiculos
  - FK: `id_opcional` → opcionais

---

### Tabela: origem_clientes
- **Descrição**: Origem/fonte do cliente (telefone, indicação, site, etc.)
- **Campos identificados**:
  - `id` (PK), `descricao`, `ativo`

---

### Tabela: recebimentos_negociacoes
- **Descrição**: Parcelas e pagamentos das negociações
- **Campos identificados**:
  - `id` (PK), `id_negociacao` (FK), `valor`, `forma_pagamento`, `data_pagamento`, `baixado`
- **Relacionamentos**:
  - FK: `id_negociacao` → negociacoes

---

## 4. Regras de Negócio Principais

### Módulo Clientes
- **Busca Flexível**: Suporta busca parcial por nome/CPF/cidade com validação de empresa
- **Aniversariantes**: Sistema automático de identificação de clientes com aniversário em períodos específicos
- **Auditoria**: Rastreamento de alterações com `id_usuario_alteracao`
- **Integração Fluxo**: Clientes vêm de fluxo_clientes ou cadastro direto
- **Validação Unicidade**: CPF único por empresa

### Módulo Veículos
- **Estados do Veículo**: ativo, vendido, excluído, consignado (tipo 0/1/2), troca temporária
- **Múltiplas Negociações**: Um veículo pode estar associado a negociação de venda, compra, ou trocas
- **Status Preparação**: Controle de fase de preparação (preparado=0, em preparação=1, pronto=2, pendente=3)
- **Exibição Site**: 0=não exibe, 1=apenas logado, 2=anúncio próprio, 3=múltiplos anúncios
- **Integração iCarros/WebMotors**: Sincronização de anúncios e fotos
- **Avaliação IA**: Integração com sistema de avaliação externo

### Módulo Negociações
- **Ciclo de Vida**: Abertura → Pendência → Aprovação (gerente) → Concretização
- **Status Aprovação**: -1 (rejeitada), 0 (pendente), 1 (aprovada)
- **Trocas**: Suporte a 1 ou 2 veículos de troca na mesma negociação
- **Comissões**: Cálculo automático para vendedor, supervisor e gerente
- **Financiamento**: Integração com financeiras, cálculo de imposto/taxa
- **Validações**:
  - Verificação de cliente válido
  - Aprovação de gerente obrigatória
  - Data concretização > data abertura
  - Valor venda >= valor aquisição (exceto compra)

### Módulo Financeiro
- **Financeiras/Despachantes**: Separação entre instituições financeiras e despachantes
- **Imposto Variável**: Cada instituição tem taxa/imposto diferente
- **Recebimentos**: Rastreamento de pagamentos com status "baixado"
- **Relatórios**: Agrupamento por financeira, período, vendedor

### Módulo Usuários
- **Autenticação**: MD5 ou SHA1 para senhas (legado — migrar para bcrypt no Laravel)
- **Perfis**: 1=Admin, 2=Gerente, 3=Vendedor, 4=Supervisor, 9=Vendedor Especial, 11=Preparador
- **Permissões**: Sistema granular de ACL (access control list)
- **Multi-empresa**: Um usuário pode atuar em múltiplas empresas
- **Vendedores**: Vendedor é um usuário com relacionamento em tabela separada (valor_fixo, percentual_venda)

### Módulo Agenda
- **Rastreamento**: Agenda por usuário com data, hora e descrição
- **Status**: Campo "baixado" (0=pendente, 1=concluído)
- **Filtros**: Por usuário, período, status

### Módulo Garantias
- **Período**: Dias ou KM (o que vencer primeiro)
- **Entrada/Saída**: Rastreamento de quando a garantia inicia e termina
- **Cancelamento**: Possibilidade de cancelamento com data
- **Custo**: Valor da garantia por veículo/fornecedor

### Módulo Contratos
- **Templates**: Contratos por empresa, modelo e marca
- **Dinâmicos**: Preenchimento automático de dados (cliente, veículo, valores)
- **Versionamento**: Múltiplos contratos por empresa (id_root para padrão)

### Módulo Empresas
- **Isolamento de Dados**: Cada empresa enxerga apenas seus dados
- **Branding**: Logos, fotos de fachada customizáveis

---

## 5. Models Laravel Completos

### Cliente

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'id_empresa', 'nome', 'cpf', 'cnpj', 'email', 'tel1', 'tel2',
        'celular', 'sexo', 'data_nascimento', 'cidade', 'bairro', 'estado',
        'endereco', 'cep', 'cargo', 'empresa', 'origem', 'ativo',
        'excluido', 'id_usuario_alteracao', 'hora_alteracao'
    ];

    protected $dates = ['data_nascimento', 'hora_alteracao', 'deleted_at'];

    protected $casts = [
        'ativo' => 'boolean',
        'excluido' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function usuarioAlteracao()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_alteracao');
    }

    public function origemCliente()
    {
        return $this->belongsTo(OrigemCliente::class, 'origem');
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'id_cliente');
    }

    public function fluxos()
    {
        return $this->hasMany(FluxoCliente::class, 'id_cliente');
    }

    public function contratos()
    {
        return $this->hasMany(ContratoRecibo::class, 'id_cliente');
    }
}
```

---

### Veiculo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'veiculos';

    protected $fillable = [
        'id_empresa', 'id_modelo', 'placa', 'renavam', 'chassis', 'km',
        'ano_fabricacao', 'combustivel', 'cor', 'valor_aquisicao', 'valor_venda',
        'valor_aquisicao_comprador', 'descricao_site', 'origem', 'consignado',
        'vendido', 'ativo', 'excluido', 'novo_usado', 'exibir_site_estoque',
        'exibir_valor_site', 'temp_troca', 'data_aquisicao', 'data_termino_revisao',
        'id_negociacao', 'id_negociacao_compra', 'id_negociacao_troca',
        'id_negociacao_troca2', 'id_usuario_alteracao', 'icarros', 'hora_alteracao'
    ];

    protected $dates = ['data_aquisicao', 'data_termino_revisao', 'hora_alteracao', 'deleted_at'];

    protected $casts = [
        'vendido' => 'boolean',
        'ativo' => 'boolean',
        'excluido' => 'boolean',
        'novo_usado' => 'boolean',
        'temp_troca' => 'boolean',
        'consignado' => 'integer',
        'icarros' => 'integer',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo');
    }

    public function negociacaoVenda()
    {
        return $this->belongsTo(Negociacao::class, 'id_negociacao');
    }

    public function negociacaoCompra()
    {
        return $this->belongsTo(Negociacao::class, 'id_negociacao_compra');
    }

    public function fotos()
    {
        return $this->hasMany(FotoVeiculo::class, 'id_veiculo');
    }

    public function despesas()
    {
        return $this->hasMany(DespesaVeiculo::class, 'id_veiculo');
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class, 'id_veiculo');
    }

    public function opcionais()
    {
        return $this->belongsToMany(Opcional::class, 'veiculos_opcionais', 'id_veiculo', 'id_opcional');
    }

    public function usuarioAlteracao()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_alteracao');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', 1)->where('excluido', 0);
    }

    public function scopeEmEstoque($query)
    {
        return $query->where('vendido', 0)->where('excluido', 0);
    }

    public function scopeDisponivelVenda($query)
    {
        return $query->where('ativo', 1)->where('vendido', 0)->where('excluido', 0);
    }
}
```

---

### Negociacao

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Negociacao extends Model
{
    use HasFactory;

    protected $table = 'negociacoes';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'id_empresa', 'id_cliente', 'id_veiculo', 'id_vendedor', 'id_supervisor',
        'id_gerente', 'id_financeira', 'id_despachante', 'id_usuario',
        'data_abertura', 'data_concretizacao', 'valor_venda', 'valor_compra_vendedor',
        'comissao_vendedor', 'comissao_supervisor', 'comissao_gerente', 'valor_financeiro',
        'compra', 'aprovada', 'id_usuario_alteracao'
    ];

    protected $dates = ['data_abertura', 'data_concretizacao', 'hora_alteracao'];

    protected $casts = [
        'compra' => 'boolean',
        'aprovada' => 'integer', // -1, 0, 1
    ];

    const STATUS_REJEITADA = -1;
    const STATUS_PENDENTE = 0;
    const STATUS_APROVADA = 1;

    const TIPO_VENDA = 0;
    const TIPO_COMPRA = 1;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo');
    }

    public function vendedor()
    {
        return $this->belongsTo(Usuario::class, 'id_vendedor');
    }

    public function supervisor()
    {
        return $this->belongsTo(Usuario::class, 'id_supervisor');
    }

    public function gerente()
    {
        return $this->belongsTo(Usuario::class, 'id_gerente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function financeira()
    {
        return $this->belongsTo(FinanceiraDespacha::class, 'id_financeira');
    }

    public function despachante()
    {
        return $this->belongsTo(FinanceiraDespacha::class, 'id_despachante');
    }

    public function recebimentos()
    {
        return $this->hasMany(RecebimentoNegociacao::class, 'id_negociacao');
    }

    public function trocas()
    {
        return $this->hasMany(TrocaNegociacao::class, 'id_negociacao');
    }

    public function contratos()
    {
        return $this->hasMany(ContratoRecibo::class, 'id_negociacao');
    }

    public function scopeVendas($query)
    {
        return $query->where('compra', self::TIPO_VENDA);
    }

    public function scopeCompras($query)
    {
        return $query->where('compra', self::TIPO_COMPRA);
    }

    public function scopeAprovadas($query)
    {
        return $query->where('aprovada', self::STATUS_APROVADA);
    }

    public function scopePendentes($query)
    {
        return $query->where('aprovada', self::STATUS_PENDENTE);
    }

    public function scopeConcretizadas($query)
    {
        return $query->whereNotNull('data_concretizacao');
    }
}
```

---

### Usuario

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'id_empresa', 'login', 'senha', 'nome', 'email', 'id_perfil',
        'telefone', 'celular', 'cidade', 'bairro', 'estado', 'endereco',
        'cep', 'ativo', 'excluido', 'data_contratacao', 'data_demissao',
        'receber_emails', 'relatorio_projetado', 'id_usuario_facebook', 'id_usuario_alteracao'
    ];

    protected $hidden = ['senha'];

    protected $dates = ['data_contratacao', 'data_demissao', 'hora_alteracao'];

    protected $casts = [
        'ativo' => 'boolean',
        'excluido' => 'boolean',
        'receber_emails' => 'boolean',
        'relatorio_projetado' => 'boolean',
    ];

    const PERFIL_ADMIN = 1;
    const PERFIL_GERENTE = 2;
    const PERFIL_VENDEDOR = 3;
    const PERFIL_SUPERVISOR = 4;
    const PERFIL_VENDEDOR_ESPECIAL = 9;
    const PERFIL_PREPARADOR = 11;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil');
    }

    public function vendedor()
    {
        return $this->hasOne(Vendedor::class, 'id_usuario');
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'id_usuario');
    }

    public function negociacoesVendidas()
    {
        return $this->hasMany(Negociacao::class, 'id_vendedor');
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'id_usuario');
    }

    public function fluxosClientes()
    {
        return $this->hasMany(FluxoCliente::class, 'id_usuario');
    }

    public function isVendedor(): bool
    {
        return in_array($this->id_perfil, [self::PERFIL_VENDEDOR, self::PERFIL_VENDEDOR_ESPECIAL]);
    }

    public function isGerente(): bool
    {
        return $this->id_perfil === self::PERFIL_GERENTE;
    }

    public function isAdmin(): bool
    {
        return $this->id_perfil === self::PERFIL_ADMIN;
    }
}
```

---

### Empresa

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'razao_social', 'nome_fantasia', 'cnpj', 'email', 'tel1', 'tel2',
        'cep', 'endereco', 'bairro', 'cidade', 'estado', 'path', 'path_frente_loja',
        'path_frente_loja_2', 'ativo', 'sistema_site', 'novo_lojista',
        'vend_nao_edit_valor', 'logado', 'login_icarros', 'senha_icarros',
        'url_site', 'excluido', 'tipo_empresa', 'id_usuario_alteracao'
    ];

    protected $dates = ['hora_alteracao'];

    protected $casts = [
        'ativo' => 'boolean',
        'sistema_site' => 'boolean',
        'novo_lojista' => 'boolean',
        'vend_nao_edit_valor' => 'boolean',
        'excluido' => 'boolean',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_empresa');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_empresa');
    }

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'id_empresa');
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'id_empresa');
    }

    public function financeiras()
    {
        return $this->hasMany(FinanceiraDespacha::class, 'id_empresa');
    }

    public function fornecedores()
    {
        return $this->hasMany(Fornecedor::class, 'id_empresa');
    }
}
```

---

### FotoVeiculo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FotoVeiculo extends Model
{
    use HasFactory;

    protected $table = 'fotos_veiculos';
    public $timestamps = false;

    protected $fillable = ['id_veiculo', 'path', 'capa', 'icarros', 'webmotors'];

    protected $casts = [
        'capa' => 'boolean',
        'icarros' => 'boolean',
        'webmotors' => 'boolean',
    ];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo');
    }
}
```

---

### DespesaVeiculo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DespesaVeiculo extends Model
{
    use HasFactory;

    protected $table = 'despesas_veiculos';
    public $timestamps = false;

    protected $fillable = ['id_veiculo', 'id_fornecedor', 'data', 'despesa', 'valor', 'ramo_atividade'];

    protected $dates = ['data'];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }
}
```

---

### Garantia

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Garantia extends Model
{
    use HasFactory;

    protected $table = 'garantias';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'id_veiculo', 'id_fornecedor', 'data_entrada', 'data_saida',
        'data_cancelamento', 'dias_garantia', 'km_garantia', 'custo',
        'observacao', 'id_usuario_alteracao'
    ];

    protected $dates = ['data_entrada', 'data_saida', 'data_cancelamento', 'hora_alteracao'];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'id_fornecedor');
    }
}
```

---

### Agenda

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'data', 'hora', 'descricao', 'obs', 'baixado', 'hora_alteracao'];

    protected $dates = ['data', 'hora_alteracao'];

    protected $casts = [
        'baixado' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function scopePendentes($query)
    {
        return $query->where('baixado', 0);
    }
}
```

---

### ContratoRecibo

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContratoRecibo extends Model
{
    use HasFactory;

    protected $table = 'contratos_recibos';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'id_empresa', 'nome', 'conteudo', 'marca', 'modelo', 'ano_modelo',
        'id_root', 'id_usuario_alteracao'
    ];

    protected $dates = ['hora_alteracao'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }
}
```

---

### FinanceiraDespacha

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceiraDespacha extends Model
{
    use HasFactory;

    protected $table = 'financeiras_despachantes';
    const UPDATED_AT = 'hora_alteracao';
    const CREATED_AT = null;

    protected $fillable = [
        'id_empresa', 'nome', 'cnpj', 'tipo', 'imposto', 'ativo', 'id_usuario_alteracao'
    ];

    protected $dates = ['hora_alteracao'];

    protected $casts = [
        'ativo' => 'boolean',
        'tipo' => 'integer',
    ];

    const TIPO_FINANCEIRA = 0;
    const TIPO_DESPACHANTE = 1;

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function negociacoes()
    {
        return $this->hasMany(Negociacao::class, 'id_financeira');
    }

    public function isFinanceira(): bool
    {
        return $this->tipo === self::TIPO_FINANCEIRA;
    }

    public function isDespachante(): bool
    {
        return $this->tipo === self::TIPO_DESPACHANTE;
    }
}
```

---

### Fornecedor

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fornecedor extends Model
{
    use HasFactory;

    protected $table = 'fornecedores';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa', 'razao_social', 'cnpj', 'ramo_atividade', 'email', 'telefone', 'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function despesas()
    {
        return $this->hasMany(DespesaVeiculo::class, 'id_fornecedor');
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class, 'id_fornecedor');
    }
}
```

---

### Vendedor

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendedor extends Model
{
    use HasFactory;

    protected $table = 'vendedores';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'valor_fixo', 'percentual_venda',
        'percentual_retorno_financeiro', 'manual'
    ];

    protected $casts = [
        'valor_fixo' => 'decimal:2',
        'percentual_venda' => 'decimal:2',
        'percentual_retorno_financeiro' => 'decimal:2',
        'manual' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
```

---

### Modelo (FIPE)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modelo extends Model
{
    use HasFactory;

    protected $table = 'modelos_11';
    public $timestamps = false;

    protected $fillable = [
        'marca', 'modelo', 'ano_modelo', 'preco', 'cod_fipe', 'segmento', 'combustivel'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'id_modelo');
    }
}
```

---

### FluxoCliente

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FluxoCliente extends Model
{
    use HasFactory;

    protected $table = 'fluxo_clientes';

    protected $fillable = [
        'id_empresa', 'id_usuario', 'nome', 'email', 'telefone', 'origem',
        'resultado', 'motivo_pre', 'data', 'data_agendamento', 'gerado_negociacao',
        'imap_origem', 'hash'
    ];

    protected $dates = ['data', 'data_agendamento'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function negociacao()
    {
        return $this->belongsTo(Negociacao::class, 'gerado_negociacao');
    }
}
```

---

### Opcional

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opcional extends Model
{
    use HasFactory;

    protected $table = 'opcionais';
    public $timestamps = false;

    protected $fillable = ['opcional', 'id_opcionais_icarros', 'id_opcionais_webmotors'];

    public function veiculos()
    {
        return $this->belongsToMany(Veiculo::class, 'veiculos_opcionais', 'id_opcional', 'id_veiculo');
    }
}
```

---

### Perfil

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Perfil extends Model
{
    use HasFactory;

    protected $table = 'perfis';
    public $timestamps = false;

    protected $fillable = ['perfil', 'descricao'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_perfil');
    }
}
```

---

### RecebimentoNegociacao

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecebimentoNegociacao extends Model
{
    use HasFactory;

    protected $table = 'recebimentos_negociacoes';
    public $timestamps = false;

    protected $fillable = ['id_negociacao', 'valor', 'forma_pagamento', 'data_pagamento', 'baixado'];

    protected $dates = ['data_pagamento'];

    protected $casts = [
        'valor' => 'decimal:2',
        'baixado' => 'boolean',
    ];

    public function negociacao()
    {
        return $this->belongsTo(Negociacao::class, 'id_negociacao');
    }
}
```

---

## 6. Estrutura Sugerida para Filament

### Resources Principais

#### ClienteResource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Cliente;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    protected static ?string $slug = 'clientes';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Dados Pessoais')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\TextInput::make('nome')
                                ->label('Nome')
                                ->required()
                                ->maxLength(150),
                            Forms\Components\TextInput::make('cpf')
                                ->label('CPF')
                                ->mask('999.999.999-99')
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('cnpj')
                                ->label('CNPJ')
                                ->mask('99.999.999/0001-99'),
                            Forms\Components\Select::make('sexo')
                                ->label('Sexo')
                                ->options(['M' => 'Masculino', 'F' => 'Feminino', 'O' => 'Outro']),
                            Forms\Components\DatePicker::make('data_nascimento')
                                ->label('Data de Nascimento'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Contato')
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email(),
                            Forms\Components\TextInput::make('tel1')
                                ->label('Telefone 1')
                                ->mask('(99) 9999-9999'),
                            Forms\Components\TextInput::make('tel2')
                                ->label('Telefone 2')
                                ->mask('(99) 9999-9999'),
                            Forms\Components\TextInput::make('celular')
                                ->label('Celular')
                                ->mask('(99) 99999-9999'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Endereço')
                        ->schema([
                            Forms\Components\TextInput::make('endereco')
                                ->label('Endereço')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('bairro')
                                ->label('Bairro'),
                            Forms\Components\TextInput::make('cidade')
                                ->label('Cidade'),
                            Forms\Components\TextInput::make('estado')
                                ->label('Estado')
                                ->maxLength(2),
                            Forms\Components\TextInput::make('cep')
                                ->label('CEP')
                                ->mask('99999-999'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Profissional')
                        ->schema([
                            Forms\Components\TextInput::make('empresa')
                                ->label('Empresa'),
                            Forms\Components\TextInput::make('cargo')
                                ->label('Cargo'),
                            Forms\Components\Select::make('origem')
                                ->label('Origem')
                                ->relationship('origemCliente', 'descricao'),
                        ]),
                ]),
            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('ativo')
                        ->label('Ativo'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->label('Celular'),
                Tables\Columns\TextColumn::make('cidade')
                    ->label('Cidade'),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('negociacoes_count')
                    ->label('Negociações')
                    ->counts('negociacoes'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ativo')
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

#### VeiculoResource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Veiculo;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class VeiculoResource extends Resource
{
    protected static ?string $model = Veiculo::class;
    protected static ?string $slug = 'veiculos';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Veículos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Veículo')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Informações Básicas')
                        ->schema([
                            Forms\Components\Select::make('id_empresa')
                                ->label('Empresa')
                                ->relationship('empresa', 'nome_fantasia')
                                ->required(),
                            Forms\Components\Select::make('id_modelo')
                                ->label('Modelo')
                                ->relationship('modelo', 'modelo')
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('placa')
                                ->label('Placa')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(8),
                            Forms\Components\TextInput::make('renavam')
                                ->label('Renavam')
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('chassis')
                                ->label('Chassis'),
                            Forms\Components\TextInput::make('km')
                                ->label('Quilometragem')
                                ->numeric(),
                        ]),
                    Forms\Components\Tabs\Tab::make('Especificações')
                        ->schema([
                            Forms\Components\TextInput::make('ano_fabricacao')
                                ->label('Ano Fabricação')
                                ->numeric(),
                            Forms\Components\Select::make('combustivel')
                                ->label('Combustível')
                                ->options(['Gasolina' => 'Gasolina', 'Diesel' => 'Diesel', 'GNV' => 'GNV', 'Flex' => 'Flex', 'Elétrico' => 'Elétrico']),
                            Forms\Components\TextInput::make('cor')
                                ->label('Cor'),
                            Forms\Components\Toggle::make('novo_usado')
                                ->label('Novo (desmarcado = Usado)'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Preços e Status')
                        ->schema([
                            Forms\Components\TextInput::make('valor_aquisicao')
                                ->label('Valor Aquisição')
                                ->numeric()
                                ->prefix('R$'),
                            Forms\Components\TextInput::make('valor_venda')
                                ->label('Valor Venda')
                                ->numeric()
                                ->prefix('R$'),
                            Forms\Components\Select::make('consignado')
                                ->label('Consignado')
                                ->options([0 => 'Não', 1 => 'Sim - Próprio', 2 => 'Sim - Repasse']),
                            Forms\Components\Toggle::make('ativo')
                                ->label('Ativo'),
                            Forms\Components\Toggle::make('vendido')
                                ->label('Vendido'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Fotos')
                        ->schema([
                            Forms\Components\FileUpload::make('fotos')
                                ->label('Fotos do Veículo')
                                ->multiple()
                                ->image()
                                ->disk('public')
                                ->directory('veiculos'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Opcionais')
                        ->schema([
                            Forms\Components\CheckboxList::make('opcionais')
                                ->label('Opcionais')
                                ->relationship('opcionais', 'opcional'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('modelo.marca')
                    ->label('Marca')
                    ->sortable(),
                Tables\Columns\TextColumn::make('modelo.modelo')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('km')
                    ->label('KM')
                    ->numeric(),
                Tables\Columns\TextColumn::make('valor_venda')
                    ->label('Valor Venda')
                    ->money('BRL'),
                Tables\Columns\IconColumn::make('vendido')
                    ->label('Vendido')
                    ->boolean(),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('consignado')
                    ->label('Consignado')
                    ->options([0 => 'Não', 1 => 'Sim - Próprio', 2 => 'Sim - Repasse']),
                Tables\Filters\TernaryFilter::make('vendido')
                    ->label('Vendido'),
                Tables\Filters\TernaryFilter::make('ativo')
                    ->label('Ativo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

#### NegociacaoResource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Negociacao;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class NegociacaoResource extends Resource
{
    protected static ?string $model = Negociacao::class;
    protected static ?string $slug = 'negociacoes';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Negociações';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Negociação')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Dados Principais')
                        ->schema([
                            Forms\Components\Select::make('id_empresa')
                                ->label('Empresa')
                                ->relationship('empresa', 'nome_fantasia')
                                ->required(),
                            Forms\Components\Select::make('id_cliente')
                                ->label('Cliente')
                                ->relationship('cliente', 'nome')
                                ->required()
                                ->searchable(),
                            Forms\Components\Select::make('id_veiculo')
                                ->label('Veículo')
                                ->relationship('veiculo', 'placa')
                                ->required()
                                ->searchable(),
                            Forms\Components\DateTimePicker::make('data_abertura')
                                ->label('Data Abertura')
                                ->required(),
                            Forms\Components\DateTimePicker::make('data_concretizacao')
                                ->label('Data Concretização'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Pessoas')
                        ->schema([
                            Forms\Components\Select::make('id_vendedor')
                                ->label('Vendedor')
                                ->relationship('vendedor', 'nome'),
                            Forms\Components\Select::make('id_supervisor')
                                ->label('Supervisor')
                                ->relationship('supervisor', 'nome'),
                            Forms\Components\Select::make('id_gerente')
                                ->label('Gerente')
                                ->relationship('gerente', 'nome'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Valores')
                        ->schema([
                            Forms\Components\TextInput::make('valor_venda')
                                ->label('Valor Venda')
                                ->numeric()
                                ->prefix('R$'),
                            Forms\Components\TextInput::make('comissao_vendedor')
                                ->label('Comissão Vendedor')
                                ->numeric()
                                ->prefix('R$'),
                            Forms\Components\TextInput::make('comissao_supervisor')
                                ->label('Comissão Supervisor')
                                ->numeric()
                                ->prefix('R$'),
                            Forms\Components\TextInput::make('comissao_gerente')
                                ->label('Comissão Gerente')
                                ->numeric()
                                ->prefix('R$'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Financeiro')
                        ->schema([
                            Forms\Components\Select::make('id_financeira')
                                ->label('Financeira')
                                ->relationship('financeira', 'nome'),
                            Forms\Components\Select::make('id_despachante')
                                ->label('Despachante')
                                ->relationship('despachante', 'nome'),
                            Forms\Components\TextInput::make('valor_financeiro')
                                ->label('Valor Financeiro')
                                ->numeric()
                                ->prefix('R$'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Status')
                        ->schema([
                            Forms\Components\Select::make('aprovada')
                                ->label('Status Aprovação')
                                ->options([
                                    -1 => 'Rejeitada',
                                    0 => 'Pendente',
                                    1 => 'Aprovada',
                                ]),
                            Forms\Components\Toggle::make('compra')
                                ->label('Compra (desmarcado = Venda)'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('veiculo.modelo.modelo')
                    ->label('Veículo'),
                Tables\Columns\TextColumn::make('valor_venda')
                    ->label('Valor')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('data_abertura')
                    ->label('Abertura')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('data_concretizacao')
                    ->label('Concretização')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\BadgeColumn::make('aprovada')
                    ->label('Status')
                    ->colors([
                        'danger' => -1,
                        'warning' => 0,
                        'success' => 1,
                    ])
                    ->formatStateUsing(fn (int $state): string => match($state) {
                        -1 => 'Rejeitada',
                        0 => 'Pendente',
                        1 => 'Aprovada',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('aprovada')
                    ->label('Status')
                    ->options([
                        -1 => 'Rejeitada',
                        0 => 'Pendente',
                        1 => 'Aprovada',
                    ]),
                Tables\Filters\TernaryFilter::make('compra')
                    ->label('Tipo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
```

---

#### UsuarioResource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Usuario;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;
    protected static ?string $slug = 'usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')
                ->label('Nome')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('login')
                ->label('Login')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('email')
                ->label('E-mail')
                ->email(),
            Forms\Components\TextInput::make('senha')
                ->label('Senha')
                ->password()
                ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn ($state) => filled($state)),
            Forms\Components\Select::make('id_empresa')
                ->label('Empresa')
                ->relationship('empresa', 'nome_fantasia')
                ->required(),
            Forms\Components\Select::make('id_perfil')
                ->label('Perfil')
                ->relationship('perfil', 'perfil')
                ->required(),
            Forms\Components\TextInput::make('telefone')
                ->label('Telefone')
                ->mask('(99) 9999-9999'),
            Forms\Components\TextInput::make('celular')
                ->label('Celular')
                ->mask('(99) 99999-9999'),
            Forms\Components\Toggle::make('ativo')
                ->label('Ativo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('login')
                    ->label('Login')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                Tables\Columns\TextColumn::make('empresa.nome_fantasia')
                    ->label('Empresa'),
                Tables\Columns\TextColumn::make('perfil.perfil')
                    ->label('Perfil'),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
```

---

#### EmpresaResource

```php
<?php

namespace App\Filament\Resources;

use App\Models\Empresa;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;
    protected static ?string $slug = 'empresas';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Empresas';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Empresa')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Dados Básicos')
                        ->schema([
                            Forms\Components\TextInput::make('razao_social')
                                ->label('Razão Social')
                                ->required(),
                            Forms\Components\TextInput::make('nome_fantasia')
                                ->label('Nome Fantasia')
                                ->required(),
                            Forms\Components\TextInput::make('cnpj')
                                ->label('CNPJ')
                                ->mask('99.999.999/0001-99')
                                ->unique(ignoreRecord: true),
                        ]),
                    Forms\Components\Tabs\Tab::make('Contato')
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('E-mail')
                                ->email(),
                            Forms\Components\TextInput::make('tel1')
                                ->label('Telefone 1')
                                ->mask('(99) 9999-9999'),
                            Forms\Components\TextInput::make('tel2')
                                ->label('Telefone 2')
                                ->mask('(99) 9999-9999'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Endereço')
                        ->schema([
                            Forms\Components\TextInput::make('endereco')
                                ->label('Endereço'),
                            Forms\Components\TextInput::make('bairro')
                                ->label('Bairro'),
                            Forms\Components\TextInput::make('cidade')
                                ->label('Cidade'),
                            Forms\Components\TextInput::make('estado')
                                ->label('Estado')
                                ->maxLength(2),
                            Forms\Components\TextInput::make('cep')
                                ->label('CEP')
                                ->mask('99999-999'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Branding')
                        ->schema([
                            Forms\Components\FileUpload::make('path')
                                ->label('Logo')
                                ->image()
                                ->disk('public')
                                ->directory('empresas/logos'),
                            Forms\Components\FileUpload::make('path_frente_loja')
                                ->label('Foto Fachada 1')
                                ->image()
                                ->disk('public')
                                ->directory('empresas/fachadas'),
                            Forms\Components\FileUpload::make('path_frente_loja_2')
                                ->label('Foto Fachada 2')
                                ->image()
                                ->disk('public')
                                ->directory('empresas/fachadas'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Integrações')
                        ->schema([
                            Forms\Components\TextInput::make('login_icarros')
                                ->label('Login iCarros'),
                            Forms\Components\TextInput::make('senha_icarros')
                                ->label('Senha iCarros')
                                ->password(),
                            Forms\Components\TextInput::make('url_site')
                                ->label('URL Site'),
                        ]),
                    Forms\Components\Tabs\Tab::make('Status')
                        ->schema([
                            Forms\Components\Toggle::make('ativo')
                                ->label('Ativo'),
                            Forms\Components\Toggle::make('sistema_site')
                                ->label('Sistema Site Próprio'),
                            Forms\Components\Toggle::make('novo_lojista')
                                ->label('Novo Lojista'),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_fantasia')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cnpj')
                    ->label('CNPJ'),
                Tables\Columns\TextColumn::make('cidade')
                    ->label('Cidade'),
                Tables\Columns\TextColumn::make('usuarios_count')
                    ->label('Usuários')
                    ->counts('usuarios'),
                Tables\Columns\TextColumn::make('veiculos_count')
                    ->label('Veículos')
                    ->counts('veiculos'),
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Ativo')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
```

---

### Outros Resources Recomendados

| Resource | Descrição |
|----------|-----------|
| `AgendaResource` | Gerenciar tarefas e lembretes |
| `GarantiaResource` | Gestão de garantias de veículos |
| `FornecedorResource` | Fornecedores de serviços |
| `FinanceiraResource` | Instituições financeiras |
| `ContratoResource` | Templates de contratos |
| `ModeloResource` | Catálogo de modelos FIPE (leitura) |

### Páginas Customizadas Sugeridas

1. **Dashboard** — KPIs de vendas, veículos em estoque, comissões
2. **Relatório de Vendas** — Por período, vendedor, gerente
3. **Relatório Financeiro** — Comissões, recebimentos, fluxo de caixa
4. **Preparação de Veículos** — Status de preparação, despesas
5. **Fluxo de Clientes** — Funil de vendas (leads → venda)

---

## 7. Checklist de Migração

### Fase 1 — Preparação
- [ ] Criar estrutura Laravel 11 com Filament 3
- [ ] Configurar banco de dados (apontar para o MySQL existente)
- [ ] Instalar dependências (Filament, Spatie Permissions, etc.)
- [ ] Configurar autenticação

### Fase 2 — Models e Relationships
- [ ] Criar todos os 18+ Models com relacionamentos
- [ ] Implementar Scopes úteis (ativos, emEstoque, concretizadas, etc.)
- [ ] Adicionar Casts e datas corretos

### Fase 3 — Filament Resources Básicos
- [ ] ClienteResource, VeiculoResource, NegociacaoResource
- [ ] UsuarioResource, EmpresaResource, FornecedorResource
- [ ] AgendaResource, GarantiaResource, ContratoResource

### Fase 4 — Lógica de Negócio
- [ ] Service para cálculo de comissões
- [ ] Observer para auditoria (id_usuario_alteracao, hora_alteracao)
- [ ] Validações customizadas (aprovação gerente, data concretização)

### Fase 5 — Multi-tenancy
- [ ] Implementar isolamento por id_empresa em todas as queries
- [ ] Considerar pacote `stancl/tenancy` ou scopes globais

### Fase 6 — Senhas (Segurança)
- [ ] Migrar senhas MD5/SHA1 para bcrypt no primeiro login

### Fase 7 — Relatórios e Dashboard
- [ ] Dashboard com KPIs (Filament Widgets)
- [ ] Relatórios de vendas e financeiro

### Fase 8 — Testes e Deploy
- [ ] Testes unitários dos Models
- [ ] Testes de permissões/ACL
- [ ] Backup completo do banco antes do cutover
- [ ] Migração em ambiente staging primeiro
