<?php

header("Content-Type: text/html; charset=UTF-8",true);

class ValeReciboController extends Zend_Controller_Action
{

	public function init(){

		$this->view->titulo = "Recibos";

		Zend_Session::start();
		

	}

	public function validaAcesso($require){

		if(!in_array($require,$_SESSION['sessionUser']['permissoes'])){$this->_helper->redirector->gotoUrl(URL."/index/bad-access");}

	}
	
	public function ajaxAction(){

		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');

		if($this->_getParam('fn') == 'getValorExtenso'){
			
			$dbVeiculo = new Application_Model_DbTable_Veiculos();
			
			$arr['id_empresa']=$_SESSION['sessionUser']['id_empresa'];
			$arr['placa']=$this->_getParam('f');
			$arr['modelo']=$this->_getParam('f');
			$arr['parcial']=true;
			$arr['vendido']=0;
			
			if($this->_getParam('temp_troca')){
			
				$arr['temp_troca'] = 1;
					
				$arrV = $dbVeiculo->_get($arr);

				foreach($arrV as $v){
				
					echo "<li> <a href=\"#\" onclick=\"populaCamposTroca(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
				
				}
				
			}else{
				
				$arr['temp_troca'] = 0;
				
				$arrV = $dbVeiculo->_get($arr);

				foreach($arrV as $v){
				
					echo "<li> <a href=\"#\" onclick=\"populaCamposVeiculo(".$v['id'].");esconde($(this).parent().parent().parent())\">".$v['modelo']." - ".$v['placa']."</a></li>";
				
				}
				
			}
			
			
		}elseif($this->_getParam('fn') == 'getClientes'){

			$dbClientes = new Application_Model_DbTable_Clientes();
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$empresaNome = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			$arrClientes = $dbClientes->_get($arr);
			
			$options = "<option value='-1'>Selecione</option>";
			$options .= "<option value=''>".$empresaNome[0]['razao_social']."</option>";

			foreach($arrClientes as $cliente){
			
				$options .= "<option value=".$cliente['id'].">".$cliente['nome']."</option>";
			
			}
			
			echo $options;
		
		}
		
	}
	
	public function reciboValeAction(){
	
		$this->validaAcesso('emissao_recibo');
		
	}
	
	public function imprimirReciboAction(){

		if(empty($_SESSION['sessionUser']['id'])){
			return $this->_helper->redirector->gotoUrl(URL."/index/bad-access");
		}

		$dbRecibos = new Application_Model_DbTable_Recibos();



		$layout = $this->_helper->layout();
	  	$layout->setLayout('no-layout');
		
		$nomeUsuario = $this->_getParam('usuario');
		//var_export($nomeUsuario);exit;
		$valor = $this->_getParam('valor');
		$referente = $this->_getParam('referente');
		$data = $this->_getParam('data');
		$obs = $this->_getParam('obs');
		
		
		//SALVAR RECIBO
		$idUsuario = $_SESSION['sessionUser']['id'];
		$arrRecibos['id_usuario_emitiu'] = $idUsuario;
		$arrRecibos['data_hora']=@date ('y-m-d H:i:s');		
		$arrRecibos['obs'] = $obs; 
		$arrRecibos['data'] = $data;
		$dataTmp = explode("/",$arrRecibos['data']);
		$arrRecibos['data'] = implode("-",array_reverse($dataTmp));
		//var_export($valor);exit;
		$arrRecibos['referente'] = $referente;
		$arrRecibos['valor'] = $valor;
		$arrRecibos['valor'] = money_format("%i",$valor);
		
		if($nomeUsuario == ""){
		
			$arrRecibos['id_cliente'] = null;
			$arrRecibos['id_concessionaria'] = $_SESSION['sessionUser']['id_empresa'];
		
		}else{
	
			$arrRecibos['id_cliente'] = $nomeUsuario;
			$arrRecibos['id_concessionaria'] = null;
		
		}
		
		$arrRecibos['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];

		
		$dbRecibos->insert($arrRecibos);
		
		if($nomeUsuario != ""){
			
			$dbClientes = new Application_Model_DbTable_Clientes();
			$nameUsuario = $dbClientes->buscaNome($nomeUsuario);
			$nomeCliente = $nameUsuario[0]['nome'];
		
		}elseif($arrRecibos['id_empresa'] != ""){
		
			$dbEmpresas = new Application_Model_DbTable_Empresas();
			$empresaNome = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
			$nomeCliente = $empresaNome[0]['razao_social'];
		
		}
		
		
		
		$datas = explode("/",$data);
		
		$mesExtenso = $this->mesExtenso($datas[1]);
		
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		$empresaNome = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$valorExtenso = $this->valor_extenso($valor);
		//var_export($empresaNome);exit;
		$this->view->path = "/".$empresaNome[0]['path'];
		$this->view->infos = "<b>".$empresaNome[0]['razao_social']."</b><br>".$empresaNome[0]['endereco']."<br>".$empresaNome[0]['cidade']."-".$empresaNome[0]['estado']."<br>".$empresaNome[0]['tel1'];
		$this->view->strRecibo = "Recebi(emos) de <b>".$nomeCliente."</b> a import&acirc;ncia de <b>".$valorExtenso."</b> referente a <b>".$referente."</b>.<br><br>".$obs.".";
		$this->view->strData = $empresaNome[0]['cidade'].", ".$datas[0]." de ".$mesExtenso." de ".$datas[2];
		$this->view->valor = number_format($valor, 2, ",", ".");
		//var_export($this->view->valor);exit;
		$this->view->numero = @date("His");
		$this->view->text = "<br> E para maior clareza firmo(amos) o presente.";

	}
	
	private function valor_extenso($valor=0, $maiusculas=false){
	
		// verifica se tem virgula decimal
		if (strpos($valor,",") > 0){
		  // retira o ponto de milhar, se tiver
		  $valor = str_replace(".","",$valor);
	 
		  // troca a virgula decimal por ponto decimal
		  $valor = str_replace(",",".",$valor);
		}
		$singular = array("centavo", "real", "mil", "milh&atilde;o", "bilh&atilde;o", "trilh&atilde;o", "quatrilh&atilde;o");
		$plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es","quatrilh&otilde;es");
 
		$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
		$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
		$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
		$u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis","sete", "oito", "nove");
 
        $z=0;
 
        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".",$valor);
		
		$cont=count($inteiro);
		
		for($i=0;$i<$cont;$i++)
            for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                $inteiro[$i] = "0".$inteiro[$i];
 
				$fim = $cont - ($inteiro[$cont-1] > 0 ? 1 : 2);
        for ($i=0;$i<$cont;$i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
 
            $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&$ru) ? " e " : "").$ru;
            $t = $cont-1-$i;
            $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")$z++; elseif ($z > 0) $z--;
            if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
            if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
 
        if(!$maiusculas){
		 
			return($rt ? $rt : "zero");
         
		}elseif($maiusculas == "2"){
		
			return (strtoupper($rt) ? strtoupper($rt) : "Zero");
        
		}else{
		
			return (ucwords($rt) ? ucwords($rt) : "Zero");
       
		}
 
	}
	
	private function mesExtenso($mes){
		
		switch($mes){
		
			case 1:
			return "Janeiro";
			break;
			
			case 2:
			return "Fevereiro";
			break;
			
			case 3:
			return "Mar&ccedil;o";
			break;
			
			case 4:
			return "Abril";
			break;
			
			case 5:
			return "Maio";
			break;
			
			case 6:
			return "Junho";
			break;
			
			case 7:
			return "Julho";
			break;
			
			case 8:
			return "Agosto";
			break;
			
			case 9:
			return "Setembro";
			break;
			
			case 10:
			return "Outubro";
			break;
			
			case 11:
			return "Novembro";
			break;
			
			case 12:
			return "Dezembro";
			break;
		
		}
	
	}
	
	public function listaAction(){
	
		$this->validaAcesso('emissao_recibo');
	
		$dbRecibos = new Application_Model_DbTable_Recibos();
		$dbClientes = new Application_Model_DbTable_Clientes();
		$dbEmpresas = new Application_Model_DbTable_Empresas();
		
		$dataInicial = $this->_getParam('data_inicial');
		$dataFinal = $this->_getParam('data_final');
		$cliente = $this->_getParam('id_cliente');

		if($dataInicial && $dataFinal){
				
				$dataTmp = explode("/",$dataInicial);
				$dataInicial = implode("-",array_reverse($dataTmp));
				
				$dataTmp = explode("/",$dataFinal);
				$dataFinal = implode("-",array_reverse($dataTmp));
				
				$arrFiltro['data_inicial'] = $dataInicial;
				$arrFiltro['data_final'] = $dataFinal;
			
			}elseif($dataInicial){
			
				$dataTmp = explode("/",$dataInicial);
				$dataInicial = implode("-",array_reverse($dataTmp));
			
				$arrFiltro['data_inicial'] = $dataInicial;
				$arrFiltro['data_final'] = @date("Y-m-d");
			
			}elseif($dataFinal){
				
				$dataTmp = explode("/",$dataFinal);
				$dataFinal = implode("-",array_reverse($dataTmp));
				
				$arrFiltro['data_final'] = $dataFinal;
				
			}
			
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
			
		if($cliente){
		
			$arrFiltro['id_cliente'] = $cliente;
			
			if($arrFiltro['id_cliente'] == -2){
			
				$arrFiltro['id_cliente'] = null;
				$arrFiltro['id_concessionaria'] = $_SESSION['sessionUser']['id_empresa'];
			
			}
		
		}else{
		
			$arrFiltro['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		
		}
		
		//var_export($arrFiltro);
			
		$empresaNome = $dbEmpresas->getEmpresa($_SESSION['sessionUser']['id_empresa']);
		
		$arrRecibos = $dbRecibos->_get($arrFiltro);
		
		foreach($arrRecibos as $key=>$valor){
		
			if($arrRecibos[$key]['cliente'] == ""){
			
				$arrRecibos[$key]['cliente'] = $empresaNome[0]['razao_social'];
			
			}
		
		}

		$arr['id_empresa'] = $_SESSION['sessionUser']['id_empresa'];
		$arrClientes = $dbClientes->_get($arr);

		//$arrClientes = $dbClientes->getClientes($_SESSION['sessionUser']['id_empresa']);
		
		//$arrClientes['valor'] = money_format("%i",$valor);
		$this->view->concessionaria = $empresaNome[0];
		$this->view->clientes = $arrClientes;
		$this->view->recibos = $arrRecibos;
		
	
	
	}
	
}

?>
