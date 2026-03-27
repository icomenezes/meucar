<?php
/**
 * Script para zipar a pasta public inteira
 * Coloque este arquivo na raiz da aplicação na LocalWeb
 * Acesse: http://seu-dominio.com.br/zipar_public.php
 */

set_time_limit(3600); // 1 hora de timeout
ini_set('memory_limit', '512M');

$public_dir = __DIR__ . '/public';
$zip_file = __DIR__ . '/public_backup_' . date('Y-m-d_H-i-s') . '.zip';

// Validação
if (!is_dir($public_dir)) {
    die('❌ Pasta public não encontrada!');
}

// Criar ZIP
$zip = new ZipArchive();

if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {

    // Função recursiva para adicionar arquivos
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($public_dir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    $total_files = 0;
    foreach ($files as $file) {
        if (!$file->isDir()) {
            $file_path = $file->getRealPath();
            $relative_path = substr($file_path, strlen($public_dir) + 1);
            $zip->addFile($file_path, 'public/' . $relative_path);
            $total_files++;

            // Feedback a cada 100 arquivos
            if ($total_files % 100 === 0) {
                echo "✓ $total_files arquivos adicionados...<br>";
                flush();
            }
        }
    }

    $zip->close();

    $file_size = filesize($zip_file) / (1024 * 1024); // em MB

    echo <<<HTML
    <html>
    <head>
        <meta charset="UTF-8">
        <title>✓ Pasta Zipada com Sucesso!</title>
        <style>
            body { font-family: Arial; margin: 40px; background: #f5f5f5; }
            .success { background: #d4edda; padding: 20px; border-radius: 5px; color: #155724; }
            .info { background: #cfe2ff; padding: 15px; margin-top: 20px; border-radius: 5px; color: #084298; }
            code { background: #eee; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <div class="success">
            <h2>✓ Sucesso!</h2>
            <p><strong>Total de arquivos:</strong> $total_files</p>
            <p><strong>Tamanho do ZIP:</strong> {$file_size} MB</p>
            <p><strong>Nome do arquivo:</strong> <code>public_backup_HTML
            . date('Y-m-d_H-i-s') .
            HTML.zip</code></p>
        </div>

        <div class="info">
            <h3>📝 Próximos passos:</h3>
            <ol>
                <li>Acesse sua VPS via FTP</li>
                <li>Baixe o arquivo <code>public_backup_*.zip</code> da raiz da aplicação</li>
                <li>Na sua nova VPS, extraia o arquivo</li>
                <li>Mova a pasta <code>public</code> para o local correto</li>
            </ol>
        </div>
    </body>
    </html>
    HTML;

} else {
    die('❌ Erro ao criar arquivo ZIP!');
}
?>
