#!/bin/bash

###############################################################################
# SCRIPTS PARA MIGRAÇÃO NA HETZNER
# Use os comandos abaixo via SSH na sua VPS Hetzner
###############################################################################

# ============================================================================
# 1. PREPARAR ESTRUTURA NA NOVA VPS
# ============================================================================

echo "=== 1. Preparando estrutura ==="

# Criar backup da pasta public antiga (para segurança)
cd /var/www/meucar
mkdir -p public_old_backup
cp -r public/* public_old_backup/ 2>/dev/null || true

# Criar pasta temporária para extrair
mkdir -p /tmp/public_extract
cd /tmp/public_extract

echo "✓ Estrutura preparada"

# ============================================================================
# 2. EXTRAIR ARQUIVO ZIP (quando receber)
# ============================================================================

echo "=== 2. Extraindo arquivo ZIP ==="

# Depois de fazer upload do ZIP para /tmp
unzip -q public_backup_*.zip

# Verifique se extraiu corretamente
if [ -d "public" ]; then
    echo "✓ ZIP extraído com sucesso"
else
    echo "❌ Erro ao extrair ZIP"
    exit 1
fi

# ============================================================================
# 3. COPIAR ARQUIVOS PARA LOCAL CORRETO
# ============================================================================

echo "=== 3. Copiando arquivos para destino ==="

# Backup da pasta atual (antes de sobrescrever)
cd /var/www/meucar
mv public public_backup_$(date +%Y%m%d_%H%M%S)

# Copiar pasta extraída
cp -r /tmp/public_extract/public .

# Limpar temporário
rm -rf /tmp/public_extract

echo "✓ Arquivos copiados"

# ============================================================================
# 4. AJUSTAR PERMISSÕES
# ============================================================================

echo "=== 4. Ajustando permissões ==="

cd /var/www/meucar/public

# Permissões padrão para arquivos estáticos
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Se aplicação precisa escrever em uploads/cache
if [ -d "uploads" ]; then
    chmod 777 uploads
    echo "✓ Permissão de escrita em uploads/"
fi

# Se houver cache
if [ -d "cache" ]; then
    chmod 777 cache
    echo "✓ Permissão de escrita em cache/"
fi

# Dono correto (ajuste www-data conforme seu servidor)
chown -R www-data:www-data /var/www/meucar/public

echo "✓ Permissões ajustadas"

# ============================================================================
# 5. IMPORTAR BANCO DE DADOS
# ============================================================================

echo "=== 5. Importando banco de dados ==="

# EDITE ESTAS VARIÁVEIS COM SEUS DADOS
DB_USER="seu_usuario"
DB_PASS="sua_senha"
DB_NAME="seu_banco"
DB_FILE="backup_$(date +%Y-%m-%d).sql"

# Se arquivo está em /tmp
if [ -f "/tmp/$DB_FILE" ]; then
    echo "Importando $DB_FILE..."
    mysql -u$DB_USER -p$DB_PASS $DB_NAME < /tmp/$DB_FILE

    if [ $? -eq 0 ]; then
        echo "✓ Banco de dados importado com sucesso"
        rm /tmp/$DB_FILE
    else
        echo "❌ Erro ao importar banco"
        exit 1
    fi
else
    echo "⚠️ Arquivo $DB_FILE não encontrado em /tmp"
    echo "Se desejar importar depois, execute:"
    echo "mysql -u$DB_USER -p$DB_PASS $DB_NAME < caminho/do/arquivo.sql"
fi

# ============================================================================
# 6. VERIFICAR INTEGRIDADE
# ============================================================================

echo "=== 6. Verificando integridade ==="

# Contar arquivos
TOTAL_FILES=$(find /var/www/meucar/public -type f | wc -l)
TOTAL_DIRS=$(find /var/www/meucar/public -type d | wc -l)

echo "✓ Arquivos: $TOTAL_FILES"
echo "✓ Diretórios: $TOTAL_DIRS"

# Verificar espaço em disco
DISK_USAGE=$(du -sh /var/www/meucar/public | cut -f1)
echo "✓ Espaço usado: $DISK_USAGE"

# ============================================================================
# 7. LIMPAR CACHE (se Laravel/Framework)
# ============================================================================

echo "=== 7. Limpando cache ==="

cd /var/www/meucar

# Laravel
if [ -f "artisan" ]; then
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    echo "✓ Cache Laravel limpo"
fi

# PHP genérico
if [ -d "var/cache" ]; then
    rm -rf var/cache/*
    echo "✓ Cache genérico limpo"
fi

# ============================================================================
# 8. TESTE DO SISTEMA
# ============================================================================

echo "=== 8. Teste do sistema ==="

# Verificar se aplicação inicia
if [ -f "index.php" ]; then
    php -l index.php > /dev/null
    if [ $? -eq 0 ]; then
        echo "✓ PHP syntax OK"
    else
        echo "❌ Erro de sintaxe PHP"
    fi
fi

# Testar conexão MySQL
if [ -f ".env" ]; then
    # Extrai credenciais do .env (ajuste se necessário)
    php -r "
    \$env = parse_ini_file('.env');
    \$link = @mysqli_connect(\$env['DB_HOST'], \$env['DB_USER'], \$env['DB_PASS'], \$env['DB_NAME']);
    if (\$link) {
        echo '✓ Conexão MySQL OK';
        mysqli_close(\$link);
    } else {
        echo '❌ Erro ao conectar MySQL';
    }
    " 2>/dev/null || echo "⚠️ Não foi possível verificar MySQL"
fi

echo ""
echo "=== ✨ MIGRAÇÃO CONCLUÍDA ==="
echo ""
echo "Próximos passos:"
echo "1. Acesse https://meucar.trsystem.com.br para testar"
echo "2. Verifique os logs: tail -f /var/log/apache2/error.log"
echo "3. Se houver problemas, restaure de public_backup_*"
echo ""

# ============================================================================
# BÔNUS: Ver os backups criados
# ============================================================================

echo "Backups criados:"
ls -lh /var/www/meucar/ | grep public_backup

###############################################################################
# FIM DOS SCRIPTS
###############################################################################
