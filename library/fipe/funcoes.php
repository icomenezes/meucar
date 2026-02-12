<?php
function _get($type,$host,$port='80',$path='/',$data='') {
    $_err = 'lib sockets::'.__FUNCTION__.'(): ';
    switch($type) { case 'http': $type = ''; case 'ssl': continue; default: die($_err.'bad $type'); } if(!ctype_digit($port)) die($_err.'bad port');
    if(!empty($data)) foreach($data AS $k => $v) $str .= urlencode($k).'='.urlencode($v).'&'; $str = substr($str,0,-1);
   
    $fp = fsockopen($host,$port,$errno,$errstr,$timeout=30);
    if(!$fp) die($_err.$errstr.$errno); else {
        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ".strlen($str)."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $str."\r\n\r\n");
       
        while(!feof($fp)) $d .= fgets($fp,4096);
        fclose($fp);
    } return $d;
}

function pegaViewstate($linhas) {
	foreach($linhas as $linha) {
		if(strpos($linha, "id=\"__VIEWSTATE\"")) {
			eregi('(value=")(.*)(")', $linha, $v);
			$viewstate = substr(str_replace("value=\"", "", $v[0]), 0, -1);
			break;
		}
	}
	return $viewstate;
}

function pegaEventValidation($linhas) {
	foreach($linhas as $linha) {
		if(strpos($linha, "id=\"__EVENTVALIDATION\"")) {
			eregi('(value=")(.*)(")', $linha, $v);
			$event_validation = substr(str_replace("value=\"", "", $v[0]), 0, -1);
			break;
		}
	}
	return $event_validation;
}

function eregHTMLTag($tagname) {
	return "^(.*)(<[ \\n\\r\\t]*$tagname(>|[^>]*>))(.*)(<[ \\n\\r\\t]*/[ \\n\\r\\t]*$tagname(>|[^>]*>))(.*)$";
}

function extraiMarcas($html) {
	$retorno = array();
	$marcas = explode("\r\n", substr($html, strpos($html, "Selecione uma marca"), -1));
	foreach($marcas as $marca) {
		if(eregi(eregHTMLTag("option"), trim($marca))) {
			eregi('(")(.*)(")', $marca, $c);
			eregi('(>)(.*)(</)', $marca, $n);
			$codigo = str_replace("\"", "", $c[0]);
			$nome = str_replace("</", "", str_replace(">", "", $n[0]));
			$retorno[] = array("codigo"=>$codigo, "nome"=>$nome);
		}
		if(strpos($marca, "</select>")) {
			break;
		}
	}
	return $retorno;
}

function extraiModelos($html) {
	$retorno = array();
	$modelos = explode("\r\n", substr($html, strpos($html, "Selecione um modelo"), -1));
	foreach($modelos as $modelo) {
		if(eregi(eregHTMLTag("option"), trim($modelo))) {
			eregi('(")(.*)(")', $modelo, $c);
			eregi('(>)(.*)(</)', $modelo, $n);
			$codigo = str_replace("\"", "", $c[0]);
			$nome = str_replace("</", "", str_replace(">", "", $n[0]));
			$retorno[] = array("codigo"=>$codigo, "nome"=>$nome);
		}
		if(strpos($modelo, "</select>")) {
			break;
		}
	}
	return $retorno;
}

function extraiAnoModelos($html) {
	$retorno = array();
	$modelos = explode("\r\n", substr($html, strpos($html, "Selecione um ano modelo"), -1));
	foreach($modelos as $modelo) {
		if(eregi(eregHTMLTag("option"), trim($modelo))) {
			eregi('(")(.*)(")', $modelo, $c);
			eregi('(>)(.*)(</)', $modelo, $n);
			$codigo = str_replace("\"", "", $c[0]);
			$nome = str_replace("</", "", str_replace(">", "", $n[0]));
			$retorno[] = array("codigo"=>$codigo, "nome"=>$nome);
		}
		if(strpos($modelo, "</select>")) {
			break;
		}
	}
	return $retorno;
}

function extraiValor($html) {
	$valor = 0;
	$informacoes = explode("\r\n", substr($html, strpos($html, "Ano Modelo"), -1));
	foreach($informacoes as $info) {
		if(strpos($info, "id=\"lblValor\"")) {
			eregi('(R\$)(.*)(</span>)', $info, $v);
			$valor = trim(str_replace("R\$", "", str_replace("</span>", "", $v[0])));
			$valor = str_replace(".", "", $valor);
			$valor = str_replace(",", ".", $valor);
			break;
		}
	}
	return trim($valor);
}
?>