<?php
/**
 * Importação da tabela FIPE via CSV — uso exclusivo em linha de comando
 *
 * Uso:
 *   php importar-fipe.php /caminho/para/fipe.csv
 *
 * Comportamento: detecta automaticamente os segmentos presentes no CSV,
 * apaga APENAS esses segmentos da tabela e insere os novos registros.
 * Segmentos não presentes no arquivo não são afetados.
 */

if (php_sapi_name() !== 'cli') {
    die("Este script só pode ser executado via linha de comando.\n");
}

if ($argc < 2) {
    fwrite(STDERR, "Uso: php importar-fipe.php /caminho/para/fipe.csv\n");
    exit(1);
}

$csvPath = $argv[1];

if (!file_exists($csvPath)) {
    fwrite(STDERR, "Arquivo não encontrado: {$csvPath}\n");
    exit(1);
}

$ext = strtolower(pathinfo($csvPath, PATHINFO_EXTENSION));
if (!in_array($ext, array('csv', 'txt'))) {
    fwrite(STDERR, "Extensão inválida. Use um arquivo .csv ou .txt\n");
    exit(1);
}

$segmentoMap = array(
    'CAR'        => 'carros',
    'TRUCK'      => 'caminhoes',
    'MOTO'       => 'motos',
    'MOTORCYCLE' => 'motos',
);

// Conexão direta PDO — sem dependência do ZF1
$host   = 'localhost';
$dbname = 'meucar';
$user   = 'meucar';
$pass   = 'LhF75SypNTLdedpt';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        )
    );
} catch (PDOException $e) {
    fwrite(STDERR, "Erro ao conectar ao banco: " . $e->getMessage() . "\n");
    exit(1);
}

// --- Primeira passagem: detecta segmentos presentes no arquivo ---
echo "Detectando segmentos no arquivo...\n";

$handle = fopen($csvPath, 'r');
if (!$handle) {
    fwrite(STDERR, "Não foi possível abrir o arquivo.\n");
    exit(1);
}

fgetcsv($handle); // pula cabeçalho
$segmentosEncontrados = array();

while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 1) continue;
    $key = strtoupper(trim($row[0]));
    $seg = isset($segmentoMap[$key]) ? $segmentoMap[$key] : 'carros';
    $segmentosEncontrados[$seg] = true;
}

fclose($handle);

if (empty($segmentosEncontrados)) {
    fwrite(STDERR, "Nenhum registro válido encontrado no arquivo.\n");
    exit(1);
}

$segmentosList = array_keys($segmentosEncontrados);
echo "Segmentos detectados: " . implode(', ', $segmentosList) . "\n";

// --- Segunda passagem: deleta segmentos do arquivo e importa ---
$handle = fopen($csvPath, 'r');
if (!$handle) {
    fwrite(STDERR, "Não foi possível reabrir o arquivo.\n");
    exit(1);
}

fgetcsv($handle); // pula cabeçalho

$batchSize  = 500;
$batch      = array();
$total      = 0;
$linhaAtual = 0;

$pdo->beginTransaction();
try {
    // Apaga apenas os segmentos presentes no CSV
    $inPlaceholders = implode(',', array_fill(0, count($segmentosList), '?'));
    $pdo->prepare("DELETE FROM fipe WHERE segmento IN ({$inPlaceholders})")
        ->execute($segmentosList);
    echo "Registros anteriores dos segmentos removidos.\n";

    while (($row = fgetcsv($handle)) !== false) {
        $linhaAtual++;
        if (count($row) < 12) {
            echo "  Linha {$linhaAtual} ignorada (colunas insuficientes).\n";
            continue;
        }

        $type       = $row[0];
        $brandCode  = $row[1];
        $brandValue = $row[2];
        $modelCode  = $row[3];
        $modelValue = $row[4];
        $yearCode   = $row[5];
        $yearValue  = $row[6];
        $fipeCode   = $row[7];
        $fuelLetter = $row[8];
        $fuelType   = $row[9];
        $price      = $row[10];
        $month      = $row[11];

        $key      = strtoupper(trim($type));
        $segmento = isset($segmentoMap[$key]) ? $segmentoMap[$key] : 'carros';

        $preco = trim($price);
        $preco = preg_replace('/[^0-9,]/', '', $preco);
        $preco = str_replace(',', '.', str_replace('.', '', $preco));
        $preco = is_numeric($preco) ? (float)$preco : null;

        $anoPartes = explode(' ', trim($yearValue));
        $anoModelo = trim($anoPartes[0]);

        $combustivelLetra = trim($fuelLetter);
        $combustivelTipo  = trim($fuelType);

        $batch[] = array(
            'segmento'          => $segmento,
            'marca_id'          => (int)trim($brandCode),
            'marca'             => trim($brandValue),
            'modelo_id'         => (int)trim($modelCode),
            'modelo'            => trim($modelValue),
            'codigo'            => trim($yearCode),
            'ano_label'         => trim($yearValue),
            'ano_modelo'        => $anoModelo,
            'cod_fipe'          => trim($fipeCode),
            'combustivel_letra' => $combustivelLetra !== '' ? $combustivelLetra : null,
            'combustivel'       => $combustivelTipo  !== '' ? $combustivelTipo  : null,
            'preco'             => $preco,
            'mes_referencia'    => trim($month),
        );

        if (count($batch) >= $batchSize) {
            insertBatch($pdo, $batch);
            $total += count($batch);
            $batch  = array();
            echo "  {$total} registros inseridos...\n";
        }
    }

    if ($batch) {
        insertBatch($pdo, $batch);
        $total += count($batch);
    }

    $pdo->commit();
    fclose($handle);

    echo "Importação concluída! {$total} registros inseridos.\n";
    exit(0);

} catch (Exception $e) {
    $pdo->rollBack();
    fclose($handle);
    fwrite(STDERR, "Erro na importação: " . $e->getMessage() . "\n");
    exit(1);
}

function insertBatch(PDO $pdo, array $rows)
{
    if (!$rows) return;
    $cols            = array_keys($rows[0]);
    $placeholder     = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
    $allPlaceholders = implode(',', array_fill(0, count($rows), $placeholder));
    $sql             = 'INSERT INTO fipe (' . implode(',', $cols) . ') VALUES ' . $allPlaceholders;
    $values          = array();
    foreach ($rows as $row) {
        foreach ($row as $v) {
            $values[] = $v;
        }
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
}
