<?php
class Bd {
	var $banco;
	var $conexao;
	var $debug;
/*
		switch($this->getBanco()) {
			case "mysql":
			break;
			case "pg":
			break;
		}
*/

	function __construct() {
		$this->setBanco("mysql");
		$this->setDebug = false;
	}

	function __destruct() {
		if($this->getConexao()) {
			switch($this->getBanco()) {
				case "mysql":
					if($this->getConexao()) {
						@mysql_close($this->getConexao());
					}
				break;
				case "pg":
					if($this->getConexao()) {
						@pg_close($this->getConexao());
					}
				break;
			}
		}
	}

	public function conecta($host, $usuario, $senha, $banco) {
		$conexao = null;
		switch($this->getBanco()) {
			case "mysql":
				$conexao = mysql_connect($host, $usuario, $senha) OR $this->erro();
				mysql_select_db($banco) OR $this->erro();
			break;
			case "pg":
				$conexao = pg_connect("host=".$host." user=".$usuario." password=".$senha." dbname=".$banco) OR $this->erro();
			break;
		}
		$this->setConexao($conexao);
	}

	public function consultar($query) {
		$consulta = null;
		switch($this->getBanco()) {
			case "mysql":
				$consulta = mysql_query($query, $this->getConexao());
				if($this->getDebug()) {
					print "<h3><span style=\"color: blue\">SQL DEBUG:</span> <span style=\"background-color: white; color: lightgray\">[".$query."]</span> ".(mysql_error($this->getConexao()) ? "<span style=\"color: red\">ERRO: ".mysql_error($this->getConexao())."</span>" : "<span style=\"color: green\">OK</span>")."</h3>";
				}
			break;
			case "pg":
				$consulta = pg_query($this->getConexao(), $query);
				if($this->getDebug()) {
					print "<h3><span style=\"color: blue\">SQL DEBUG:</span> <span style=\"background-color: white; color: lightgray\">[".$query."]</span> ".(pg_last_error($this->getConexao()) ? "<span style=\"color: red\">ERRO: ".pg_last_error($this->getConexao())."</span>" : "<span style=\"color: green\">OK</span>")."</h3>";
				}
			break;
		}
		return $consulta;
	}

	public function resultados($query) {
		$dados = array();
		$query = $this->consultar($query);
		switch($this->getBanco()) {
			case "mysql":
				while($linha = mysql_fetch_array($query, MYSQL_BOTH)) {
					if($linha) {
						$dados[] = $linha;
					}
				}
			break;
			case "pg":
				$contador = 0;
				while($linha = pg_fetch_array($query, $contador, PGSQL_BOTH)) {
					if($linha) {
						$dados[] = $linha;
					}
					$i++;
				}
			break;
		}
		return $dados;
	}

	public function resultado($query) {
		$dados = array();
		$query = $this->consultar($query);
		switch($this->getBanco()) {
			case "mysql":
				$dados = mysql_fetch_array($query, MYSQL_BOTH);
			break;
			case "pg":
				$dados = pg_fetch_array($query, 0, PGSQL_BOTH);
			break;
		}
		return $dados;
	}

	public function inserir($tabela, $dados = array()) {
		$campos = array();
		$valores = array();
		foreach($dados as $campo => $valor) {
			$campos[] = $campo;
			$valores[] = "'".html_entity_decode($valor)."'";
		}
		$campos = implode(", ", $campos);
		$valores = implode(", ", $valores);

		$this->consultar("INSERT INTO ".$tabela." (".$campos.") VALUES (".$valores.")");
	}

	public function atualizar($tabela, $dados = array(), $where = '') {
		$camposValores = array();
		foreach($dados as $campo => $valor) {
			$camposValores[] = $campo."='".html_entity_decode($valor)."'";
		}
		$camposValores = implode(", ", $camposValores);

		$this->consultar("UPDATE ".$tabela." SET ".$camposValores.($where ? " WHERE ".$where : ''));
	}

	public function deletar($tabela, $where = '') {
		$this->consultar("DELETE FROM ".$tabela.($where ? " WHERE ".$where : ''));
	}

	private function erro($mensagem) {
		switch($this->getBanco()) {
			case "mysql":
				die(mysql_error($this->getConexao()));
			break;
			case "pg":
				die(pg_last_error($this->getConexao()));
			break;
		}
	}

	public function setBanco($banco) {
		// $banco = mysql ou pg
		$this->banco = $banco;
	}

	public function getBanco() {
		return $this->banco;
	}

	public function setConexao($conexao) {
		$this->conexao = $conexao;
	}

	public function getConexao() {
		return $this->conexao;
	}

	public function setDebug($debug) {
		// $debug = true ou false
		$this->debug = $debug;
	}

	public function getDebug() {
		return $this->debug;
	}
}
?>