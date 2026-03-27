# 📦 Alternativas para Copiar a Pasta Public

Como você só tem acesso FTP à LocalWeb, aqui estão opções:

---

## ✅ **OPÇÃO 1: ZIP via PHP (RECOMENDADO)**

### Por quê?
- Funciona sem SSH
- Uma única transferência
- Mais rápido

### Como fazer:
```php
<?php
// zipar_public.php na raiz
set_time_limit(3600);
$zip = new ZipArchive();
$zip->open('public_' . time() . '.zip', ZipArchive::CREATE);
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./public'));
foreach ($files as $file) {
    if (!$file->isDir()) {
        $zip->addFile($file, str_replace('./', '', $file));
    }
}
$zip->close();
echo "ZIP criado!";
?>
```

**Passos:**
1. Upload via FTP
2. Acessa no navegador: `meucar.com.br/zipar_public.php`
3. Download do arquivo ZIP
4. Extrai na nova VPS

---

## ⚠️ **OPÇÃO 2: TAR via PHP (se ZIP não funcionar)**

```php
<?php
$tar_file = 'public_backup.tar.gz';
$command = 'tar -czf ' . escapeshellarg($tar_file) . ' public/';
exec($command, $output, $return_var);
echo ($return_var === 0) ? 'TAR criado!' : 'Erro ao criar TAR';
?>
```

**Vantagens:**
- Compressão melhor (arquivo menor)
- Nativo em Linux

---

## 😅 **OPÇÃO 3: Cópia Seletiva (Manual, Árduо)**

Se ZIP/TAR não funcionarem, copie apenas o essencial:

```
public/
├── css/ (importante)
├── js/ (importante)
├── images/ (importante)
└── uploads/ (se houver)
```

**Scripts para listar arquivos por tipo:**

```php
<?php
// Lista todos os arquivos
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator('./public')
);
$list = [];
foreach ($files as $file) {
    if (!$file->isDir()) {
        $list[] = substr($file, 2); // Remove ./
    }
}
echo '<pre>';
echo count($list) . " arquivos encontrados:\n\n";
foreach ($list as $f) {
    echo $f . "\n";
}
echo '</pre>';
?>
```

Salve isso em um arquivo .txt e use um cliente FTP com seleção em massa.

---

## 🔥 **OPÇÃO 4: Script FTP Automático**

Se tiver bastante experiência com FTP:

```bash
#!/bin/bash
# sync_ftp.sh - Sincronizar via FTP

HOST="seu-ftp.com"
USER="usuario"
PASS="senha"

lftp -u $USER,$PASS $HOST << EOF
set ftp:list-options -la
cd /public_html/public
mirror -R . /var/www/meucar/public/
quit
EOF
```

**⚠️ Cuidado:** não recomendo para iniciantes

---

## 🆘 **OPÇÃO 5: Pedido ao Suporte LocalWeb**

Se nada der certo, peça:
> "Preciso de um arquivo ZIP da pasta /public de meucar.com.br"

Eles podem fazer isso para você via suporte (pode levar algumas horas).

---

## 📊 Comparação

| Método | Facilidade | Velocidade | Confiabilidade |
|--------|-----------|-----------|-----------------|
| ZIP PHP | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| TAR PHP | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ |
| Manual FTP | ⭐⭐ | ⭐ | ⭐⭐ |
| Script FTP | ⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐ |
| Suporte | ⭐ | ⭐ | ⭐⭐⭐⭐ |

---

## 🎯 Minha Recomendação

1. **Tente ZIP PHP primeiro** (fornecido acima) ✅
2. Se der timeout, tente TAR PHP
3. Se problemas persistirem, peça suporte à LocalWeb

---

## ⚡ Otimizações para Arquivo Grande

### Se public/ > 500MB:

**Aumentar limites PHP na LocalWeb:**
```php
set_time_limit(7200);        // 2 horas
ini_set('memory_limit', '1G');
ini_set('max_execution_time', 7200);
```

**Comprimir mais agressivamente:**
```php
// Usar nível 9 de compressão
for ($i = 0; $i < 10; $i++) {
    $zip->setCompressionIndex($i, ZipArchive::CM_DEFLATE, 9);
}
```

**Ou dividir em múltiplos ZIPs:**
```bash
# Depois criar script PHP que faz isso automaticamente
zip -s 250m public_part.zip public/
```

---

## 🚨 Troubleshooting

### "Fatal error: Maximum execution time exceeded"
→ Aumentar `set_time_limit()`

### "Allowed memory size exceeded"
→ Aumentar `memory_limit`

### "The file cannot be zipped because it is encrypted"
→ Alguns arquivos são protegidos, normal

### ZIP corrompido ao baixar
→ Usar cliente FTP com **resumption support** (WinSCP, FileZilla)

### Arquivo muito grande, tá lento
→ Usar TAR com compressão ou split

---

## 💡 Dica Final

Antes de fazer tudo pela primeira vez:
```bash
# Teste com pasta pequena
mkdir test_public
cp -r public/css test_public/
# Zipee e teste o download
# Depois libera para tudo
```

Assim você identifica problemas em escala menor!
