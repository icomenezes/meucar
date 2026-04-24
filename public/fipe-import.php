<?php
/**
 * Importação da tabela FIPE via CSV
 * Acesso: /fipe-import.php
 */

session_start();

if (!isset($_SESSION['sessionUser']['id']) || $_SESSION['sessionUser']['id_perfil'] != 1) {
    header('Location: /index/login-v2');
    exit;
}

define('APP_PATH', dirname(__DIR__) . '/application');

$config  = new Zend_Config_Ini(APP_PATH . '/configs/application.ini', 'production');
$db = Zend_Db::factory($config->resources->db);

$mensagem = '';
$erro     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $upload = isset($_FILES['csv']) ? $_FILES['csv'] : null;

    if (!$upload || $upload['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Nenhum arquivo enviado ou erro no upload.';
    } else {
        $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, array('csv', 'txt'))) {
            $erro = 'Arquivo inválido. Envie um .csv ou .txt';
        } else {
            $handle = fopen($upload['tmp_name'], 'r');
            if (!$handle) {
                $erro = 'Não foi possível ler o arquivo.';
            } else {
                $segmentoMap = array('CAR' => 'carros', 'TRUCK' => 'caminhoes', 'MOTO' => 'motos');
                fgetcsv($handle); // pula cabeçalho
                $db->beginTransaction();
                try {
                    $db->query('TRUNCATE TABLE fipe');
                    $batch     = array();
                    $total     = 0;
                    $batchSize = 500;

                    while (($row = fgetcsv($handle)) !== false) {
                        if (count($row) < 12) continue;

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
                            insertBatch($db, $batch);
                            $total += count($batch);
                            $batch = array();
                        }
                    }

                    if ($batch) {
                        insertBatch($db, $batch);
                        $total += count($batch);
                    }

                    $db->commit();
                    fclose($handle);
                    $mensagem = "Importacao concluida! {$total} registros inseridos.";

                } catch (Exception $e) {
                    $db->rollBack();
                    fclose($handle);
                    $erro = 'Erro: ' . $e->getMessage();
                }
            }
        }
    }
}

function insertBatch($db, array $rows)
{
    if (!$rows) return;
    $cols            = array_keys($rows[0]);
    $placeholder     = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
    $allPlaceholders = implode(',', array_fill(0, count($rows), $placeholder));
    $sql             = 'INSERT INTO fipe (' . implode(',', $cols) . ') VALUES ' . $allPlaceholders;
    $values          = array();
    foreach ($rows as $row) {
        foreach ($row as $v) { $values[] = $v; }
    }
    $db->query($sql, $values);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Importar FIPE</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:600px">
    <h4>Importar tabela FIPE (CSV)</h4>
    <p class="text-muted">O conteúdo anterior da tabela será substituído completamente.</p>
    <hr>

    <?php if ($mensagem): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Arquivo CSV da FIPE</label>
            <input type="file" name="csv" class="form-control" accept=".csv,.txt" required>
            <div class="form-text">Formato: Type, Brand Code, Brand Value, Model Code, Model Value, Year Code, Year Value, Fipe Code, Fuel Letter, Fuel Type, Price, Month</div>
        </div>
        <div class="alert alert-warning py-2">
            <strong>Atenção:</strong> todos os dados da tabela FIPE atual serão substituídos.
        </div>
        <button type="submit" class="btn btn-primary">Importar</button>
        <a href="/agenda/agenda-concessionario" class="btn btn-secondary ms-2">Voltar</a>
    </form>
</div>
</body>
</html>
