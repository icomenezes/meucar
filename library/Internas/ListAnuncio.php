<?php

class Internas_ListAnuncio{

	//private $id;
	private $versao_id;
	private $anoFabricacao;
	private $anoModelo;
	private $portas;
	private $cor_id;
	private $km;
	private $preco;
	private $combustivel_id;
	private $placa;
	private $listaOpcionais_ids;
	private $texto;
	private $status;
	private $tipoAnuncio_id;
	private $anunciante_id;
	private $anuncio0km = false;
	
	
	public function getAnunciante_id(){
	
		return $this->anunciante_id;
	
	}
	
	public function setAnunciante_id($anunciante_id){
	
		$this->anunciante_id = $anunciante_id;
	
	}
	
	public function getAnuncio0km(){
	
		return $this->anuncio0km;
	
	}
	
	public function setAnuncio0km($anuncio0km){
	
		$this->anuncio0km = $anuncio0km;
	
	}

	public function getTipoAnuncio_id(){
	
		return $this->tipoAnuncio_id;
	
	}
	
	public function setTipoAnuncio_id($tipoAnuncio_id){
	
		$this->tipoAnuncio_id = $tipoAnuncio_id;
	
	}
	
	
	public function getStatus(){
	
		return $this->status;
	
	}
	
	public function setStatus($status){
	
		$this->status = $status;
	
	}
	
	
	public function getTexto(){
	
		return $this->texto;
	
	}
	
	public function setTexto($texto){
	
		$this->texto = $texto;
	
	}
	
	
	public function getListaOpcionais_ids(){
	
		return $this->listaOpcionais_ids;
	
	}
	
	public function setListaOpcionais_ids($listaOpcionais_ids){
	
		$this->listaOpcionais_ids = $listaOpcionais_ids;
	
	}

	
	public function getPlaca(){
	
		return $this->placa;
	
	}
	
	public function setPlaca($placa){
	
		$this->placa = $placa;
	
	}
	
	
	public function getCombustivel_id(){
	
		return $this->combustivel_id;
	
	}
	
	public function setCombustivel_id($combustivel_id){
	
		$this->combustivel_id = $combustivel_id;
	
	}
	
	
	public function getPreco(){
	
		return $this->preco;
	
	}
	
	public function setPreco($preco){
	
		$this->preco = $preco;
	
	}
	
	
	public function getKm(){
	
		return $this->km;
	
	}
	
	public function setKm($km){
	
		$this->km = $km;
	
	}
	
	
	public function getCor_id(){
	
		return $this->cor_id;
	
	}
	
	public function setCor_id($cor_id){
	
		$this->cor_id = $cor_id;
	
	}
	
	
	public function getPortas(){
	
		return $this->portas;
	
	}
	
	public function setPortas($portas){
	
		$this->portas = $portas;
	
	}
	
	
	public function getAnoModelo(){
	
		return $this->anoModelo;
	
	}
	
	public function setAnoModelo($anoModelo){
	
		$this->anoModelo = $anoModelo;
	
	}
	
	
	public function getAnoFabricacao(){
	
		return $this->anoFabricacao;
	
	}
	
	public function setAnoFabricacao($anoFabricacao){
	
		$this->anoFabricacao = $anoFabricacao;
	
	}
	
	
	public function getVersao_id(){
	
		return $this->versao_id;
	
	}
	
	public function setVersao_id($versao_id){
	
		$this->versao_id = $versao_id;
	
	}
	
	
	public function getId(){
	
		return $this->id;
	
	}
	
	public function setId($id){
	
		$this->id = $id;
	
	}

}


?>