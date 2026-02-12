<?php
### EXTRAI VALORES FIPE
### Autor: Ronaldo Moreira Junior
### E-mail: elj0k3r@gmail.com
### Twitter: twitter.com/elj0k3r
### Facebook: http://www.facebook.com/ronaldojoker
### 
### Detalhes consulte http://phpbrasil.com/profile/FTwaZFd_LPtL/ronaldo-moreira-junior ou me envie um e-mail

include("Bd.php");
include("funcoes.php");

ini_set(max_execution_time, 0);

$Bd = new Bd();
$Bd->conecta("mysql23.8bitstecnologia.com", "w8bitstecnolog22", "j10r18", "w8bitstecnolog22");

$_P = 51; // carros
#$_P = 52; // motos
#$_P = 53; // caminhões

if($_P == 51) {
	$tipo = 'carro';
	$extra = '';
}
elseif($_P == 52) {
	$tipo = 'moto';
	$extra = 'v=m&';
}
elseif($_P == 53) {
	$tipo = 'caminhao';
	$extra = 'v=c&';
}

$marcador_inicial = microtime(1);

$inicial = file_get_contents("http://www.fipe.org.br/web/indices/veiculos/default.aspx?".$extra."p=".$_P);

$vsMarca = pegaViewstate(explode("\r\n", $inicial));
$evMarca = pegaEventValidation(explode("\r\n", $inicial));

//$marcas = $Bd->resultados("SELECT * FROM marca ORDER BY nome ASC");
$marcas_extraido = extraiMarcas($inicial);

foreach($marcas_extraido as $marca) {
	if(!$Bd->resultado("SELECT id FROM marca WHERE id = '".$marca['codigo']."'")) {
		$Bd->inserir("marca", array("id"=>$marca['codigo'], "nome"=>$marca['nome'], "tipo"=>$tipo));
	}
	//print "<h1>".$marca['nome']."</h1>";

	$modelo_post = _get("http", "fipe.org.br", "80", "/web/indices/veiculos/default.aspx?".$extra."p=".$_P, array("ScriptManager1"=>"ScriptManager1|ddlMarca", "__EVENTTARGET"=>"ddlMarca", "__EVENTVALIDATION"=>$evMarca, "__VIEWSTATE"=>$vsMarca, "ddlMarca"=>$marca['codigo'], "ddlAnoValor"=>0, "ddlModelo"=>0));

	$vsModelo = pegaViewstate(explode("\r\n", $modelo_post));
	$evModelo = pegaEventValidation(explode("\r\n", $modelo_post));

	$modelos_extraido = extraiModelos($modelo_post);

	foreach($modelos_extraido as $modelo) {
		if(!$Bd->resultado("SELECT id FROM modelo WHERE id = '".$modelo['codigo']."'")) {
			$Bd->inserir("modelo", array("id"=>$modelo['codigo'], "nome"=>$modelo['nome'], "marca"=>$marca['codigo'], "tipo"=>$tipo));
		}
		//print "<h2>".$modelo['nome']."</h2>";

		$ano_modelo_post = _get("http", "fipe.org.br", "80", "/web/indices/veiculos/default.aspx?".$extra."p=".$_P, array("ScriptManager1"=>"updModelo|ddlModelo", "__EVENTTARGET"=>"ddlModelo", "__EVENTVALIDATION"=>$evModelo, "__VIEWSTATE"=>$vsModelo, "ddlMarca"=>$marca['codigo'], "ddlAnoValor"=>0, "ddlModelo"=>$modelo['codigo']));

		$vsAnoModelo = pegaViewstate(explode("\r\n", $ano_modelo_post));
		$evAnoModelo = pegaEventValidation(explode("\r\n", $ano_modelo_post));

		$ano_modelos_extraido = extraiAnoModelos($ano_modelo_post);

		foreach($ano_modelos_extraido as $ano_modelo) {
			$valor_post = _get("http", "fipe.org.br", "80", "/web/indices/veiculos/default.aspx?".$extra."p=".$_P, array("ScriptManager1"=>"updAnoValor|ddlAnoValor", "__EVENTTARGET"=>"ddlAnoValor", "__EVENTVALIDATION"=>$evAnoModelo, "__VIEWSTATE"=>$vsAnoModelo, "ddlMarca"=>$marca['codigo'], "ddlAnoValor"=>$ano_modelo['codigo'], "ddlModelo"=>$modelo['codigo']));

			$valor = extraiValor($valor_post);

			if(!$Bd->resultado("SELECT id FROM ano_modelo WHERE id = '".$ano_modelo['codigo']."'")) {
				$Bd->inserir("ano_modelo", array("id"=>$ano_modelo['codigo'], "nome"=>$ano_modelo['nome'], "modelo"=>$modelo['codigo'], "valor"=>$valor, "tipo"=>$tipo));
			}
			else {
				$Bd->atualizar("ano_modelo", array("nome"=>$ano_modelo['nome'], "valor"=>$valor, "tipo"=>$tipo), "id = '".$ano_modelo['codigo']."'");
			}
			//print $ano_modelo['nome']." <u>(".number_format($valor, 2, ",", ".").")</u><br />";
		}
		//flush();
		//sleep(1);
	}
}

$marcador_final= microtime(1);
$tempo_execucao = $marcador_final - $marcador_inicial;
print "<h1>FIM: ".sprintf("%02.3f", $tempo_execucao)."</h1>";
?>
