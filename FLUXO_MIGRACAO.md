# 🚀 Fluxo de Migração - LocalWeb → Hetzner VPS

## 📅 Timeline

### **Antes das 22h (Preparação)**

#### ✅ 1. Upload do Script ZIP
- [ ] Faça upload do `zipar_public.php` para a raiz da aplicação na LocalWeb via FTP
- [ ] Acesse via navegador: `http://meucar.com.br/zipar_public.php`
- [ ] Aguarde completar (pode levar alguns minutos)
- [ ] Anote o nome exato do arquivo ZIP gerado

#### ✅ 2. Preparar Página de Manutenção
- [ ] Faça upload do `manutencao.html` para a raiz via FTP
- [ ] Renomeie para `index.html` (ou crie um `.htaccess` para redirecionar - ver opção abaixo)
- [ ] **Não faça isso ainda**, apenas deixe pronto!

#### ✅ 3. Teste na Nova VPS
- [ ] Verifique acesso SSH à Hetzner
- [ ] Teste acesso FTP à Hetzner
- [ ] Confirme que a nova VPS tem espaço em disco disponível

---

### **22h00 - INÍCIO DA MIGRAÇÃO**

#### ✅ 4. Baixar Banco de Dados Final
```bash
# Na sua máquina local ou na VPS
mysqldump -h localhost -u [usuario] -p [senha] [banco] > backup_$(date +%Y-%m-%d_%H%M%S).sql
```
- [ ] Banco de dados baixado e testado
- [ ] Arquivo salvo com segurança

#### ✅ 5. Desativar Acesso ao Sistema Antigo (LocalWeb)
**Opção A - Usando .htaccess:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !manutencao.html
    RewriteRule ^(.*)$ manutencao.html [L]
</IfModule>
```

**Opção B - Renomear index.php:**
```bash
# Via FTP: Renomeie index.php para index.php.bak
# E coloque manutencao.html como index.html
```

- [ ] Página de manutenção ativada na LocalWeb
- [ ] Verifique acessando `http://meucar.com.br` (deve ver página de manutenção)

#### ✅ 6. Download do Arquivo ZIP
- [ ] Conecte ao FTP da LocalWeb
- [ ] Download do arquivo `public_backup_*.zip` para sua máquina
  - **Dica:** Use cliente FTP com retomada (Filezilla, WinSCP) para arquivos grandes
- [ ] Arquivo totalmente baixado
- [ ] Verifique integridade: `unzip -t public_backup_*.zip`

#### ✅ 7. Upload para Nova VPS
- [ ] Conecte ao FTP da Hetzner (meucar.trsystem.com.br)
- [ ] Faça upload do ZIP para `/var/www/` ou local apropriado
- [ ] Arquivo totalmente upado
- [ ] Verifique integridade na nova VPS: `unzip -t public_backup_*.zip`

#### ✅ 8. Extrair na Nova VPS
```bash
# Via SSH na Hetzner
cd /var/www/meucar
unzip -o ~/public_backup_*.zip

# Copiar pasta public
cp -r public/* ./public_old/

# Limpar
rm ~/public_backup_*.zip
```
- [ ] Arquivos extraídos com sucesso
- [ ] Permissões verificadas (`ls -la public/`)

#### ✅ 9. Importar Banco de Dados
```bash
# Na nova VPS
mysql -u [usuario] -p [senha] [banco] < backup_*.sql
```
- [ ] Banco importado com sucesso
- [ ] Teste de conexão realizado

#### ✅ 10. Testar Sistema na Nova VPS
- [ ] Acesse `https://meucar.trsystem.com.br`
- [ ] Faça login (se houver autenticação)
- [ ] Navegue pelos recursos principais
- [ ] Verifique se imagens/assets carregam corretamente
- [ ] Teste formulários (se houver)

#### ✅ 11. Atualizar DNS (Importante!)
Na sua zona DNS (seu registrador de domínio):
- [ ] Atualize `meucar.com.br` para apontar para IP da Hetzner
- [ ] **Espere propagação DNS (15-30 minutos)**
  ```bash
  # Teste propagação
  nslookup meucar.com.br
  dig meucar.com.br
  ```

#### ✅ 12. Verificação Final
- [ ] Acesse `http://meucar.com.br` (agora deve ir para a nova VPS)
- [ ] Verifique em navegador anônimo (sem cache)
- [ ] Teste em um segundo computador/smartphone
- [ ] Inspecione performance

---

## 🚨 Plano B - Se Algo Dar Errado

```bash
# Voltar para LocalWeb temporariamente
# Na LocalWeb, remova o .htaccess ou renomeie index.html de volta para manutencao.html
```

---

## 📝 Dicas Importantes

### Para Arquivos Grandes
- Use **WinSCP** ou **Filezilla** (suportam retomada)
- Para ZIP muito grande, considere split:
  ```bash
  zip -s 500m -r public_backup_split.zip public/
  ```

### Verificação de Integridade
```bash
# Verificar se ZIP não está corrompido
unzip -t public_backup_*.zip | tail -1

# Deve mostrar: "No errors detected in compressed data of archive."
```

### Permissões de Arquivo na Nova VPS
```bash
# Se houver problemas de permissão
cd /var/www/meucar
chmod -R 755 public/
chmod -R 644 public/*.*

# Se aplicação grava arquivos em public
chown -R www-data:www-data public/
```

### Limpar Cache
```bash
# Se tiver cache de BD
rm -rf var/cache/*
# ou
php artisan cache:clear  # se Laravel
```

---

## ✨ Checklist Final

- [ ] Página de manutenção visível aos clientes
- [ ] Novo sistema testado e funcionando
- [ ] DNS atualizado e propagado
- [ ] Todos os arquivos em place
- [ ] Banco de dados sincronizado
- [ ] Verificação de logs (sem erros)
- [ ] Performance satisfatória
- [ ] Backup antigo mantido por segurança

---

## 💾 Backup de Segurança

Mantenha durante 7 dias:
- Arquivo ZIP do public original
- Backup do BD antigo
- Arquivo `.htaccess` original da LocalWeb

Depois de 7 dias sem problemas, você pode deletar.

---

## 🎯 Estimativa de Tempo

| Tarefa | Tempo |
|--------|-------|
| Preparação | 15 min |
| Download ZIP | 10-30 min (depende tamanho) |
| Upload para nova VPS | 10-30 min |
| Extrair e testar | 10 min |
| Propagação DNS | 15-30 min |
| **Total** | **~1-2 horas** |

---

## 📞 Suporte Rápido

**Se não conseguir acessar via SSH na LocalWeb:**
```
✓ ZIP via PHP (já feito)
✓ Download via FTP
✓ Upload via FTP para Hetzner
✓ SSH Hetzner (você tem acesso)
```

**Se arquivo ZIP ficar muito grande:**
```bash
# Comprimir mais
zip -r -9 -q public_backup.zip public/

# Ou dividir
zip -s 300m -r public_backup.zip public/
```
