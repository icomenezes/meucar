<?php
// Arquivo de diagnóstico - REMOVER APÓS O TESTE

function testaUrl($label, $url, $method = 'GET', $postData = null) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: https://veiculos.fipe.org.br',
            'Referer: https://veiculos.fipe.org.br/',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ],
    ]);
    if ($method === 'POST' && $postData) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time     = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    $erro     = curl_error($ch);
    curl_close($ch);

    $ok      = ($httpCode == 200 && $response && !$erro);
    $decoded = json_decode($response, true);
    $preview = $response ? substr($response, 0, 120) : '(sem resposta)';

    $status = $ok ? '✅ OK' : '❌ FALHOU';
    $cor    = $ok ? '#155724' : '#721c24';
    $bg     = $ok ? '#d4edda' : '#f8d7da';

    echo "<div style='padding:10px;margin:8px 0;background:{$bg};color:{$cor};border-radius:4px;font-family:monospace'>";
    echo "<strong>{$status} — {$label}</strong><br>";
    echo "HTTP: <b>{$httpCode}</b> | Tempo: <b>" . round($time, 2) . "s</b><br>";
    if ($erro) echo "Erro cURL: <b>{$erro}</b><br>";
    echo "Preview: " . htmlspecialchars($preview);
    echo "</div>";

    return $decoded;
}

echo "<h2 style='font-family:sans-serif'>🔍 Diagnóstico APIs FIPE</h2>";

// 1. Tabela de referência FIPE
$tabelas = testaUrl(
    'FIPE — ConsultarTabelaDeReferencia',
    'https://veiculos.fipe.org.br/api/veiculos/ConsultarTabelaDeReferencia',
    'POST',
    []
);

$tabelaRef = ($tabelas && isset($tabelas[0]['Codigo'])) ? $tabelas[0]['Codigo'] : null;
echo "<p style='font-family:monospace'>Tabela de referência atual: <b>" . ($tabelaRef ?: 'NÃO OBTIDA') . "</b></p>";

// 2. Marcas FIPE (carros)
if ($tabelaRef) {
    testaUrl(
        'FIPE — ConsultarMarcas (carros)',
        'https://veiculos.fipe.org.br/api/veiculos/ConsultarMarcas',
        'POST',
        ['codigoTabelaReferencia' => $tabelaRef, 'codigoTipoVeiculo' => 1]
    );
} else {
    echo "<div style='padding:10px;margin:8px 0;background:#fff3cd;color:#856404;border-radius:4px;font-family:monospace'>⚠️ Pulando ConsultarMarcas — sem tabela de referência</div>";
}

// 3. Parallelum v2 (fallback)
testaUrl(
    'Parallelum v2 — brands (carros)',
    'https://parallelum.com.br/fipe/api/v2/cars/brands'
);

// 4. BrasilAPI (usado para atualizar preços)
testaUrl(
    'BrasilAPI — FIPE preço (teste com 001004-1)',
    'https://brasilapi.com.br/api/fipe/preco/v1/001004-1'
);

echo "<hr><p style='font-family:monospace;font-size:12px;color:#666'>IP deste servidor: <b>" . $_SERVER['SERVER_ADDR'] . "</b> | " . date('d/m/Y H:i:s') . "</p>";
echo "<p style='font-family:monospace;font-size:11px;color:#999'>⚠️ Remova este arquivo após o diagnóstico.</p>";
