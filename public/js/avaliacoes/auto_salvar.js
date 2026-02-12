var storage = window.localStorage;

function autoSalvar(){

	if($("#id").val() == ""){

		if($("#nome").val() != "" && $("#marcas").val() != "" && $("#modelos").val() != "" && $("#valor_ano_modelo").val() != "" && $("#ano_modelo").val() != "" && $("#ano_fabricacao").val() != ""){
			var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
	        db.transaction(insereAvaliacaoAutoSalvar, errorAvaliacoesAutoSalvar, sucessoAvaliacoesAutoSalvar);
	    }

	}else{

		var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
        db.transaction(editaAvaliacaoAutoSalvar, errorEdtAvaliacoesAutoSalvar, sucessoEdtAvaliacoesAutoSalvar);

	}

}




function editaAvaliacaoAutoSalvar(tx){

//////////////////Pega Opcionais////////////////////////////////////////////////
    var opt = "";
    $("#table_opcionais input[type='checkbox']").each(function(){
        if($(this).is(":checked")){
            opt += $(this).attr("id")+"_";
        }
    });

    opt = opt.substring(0, opt.length-1);
    $("#opcionais").val(opt);
///////////////Fim pega opcionais//////////////////////////////////////////////

////////////////OBTEM DESPESAS ADICIONAIS///////////////////////////////////
    var strDespesas = "";
    var countDespesas = 1;
    $("#outras_despesas input[type='telefone']").each(function(){
         //console.log("Entrou "+$(this).val());
        if($(this).val()){
            strDespesas += "despesa_descricao_"+countDespesas+" = '"+$(this).prev().prev().prev().prev().val()+"', ";
            strDespesas += "despesa_valor_"+countDespesas+" = '"+$(this).val()+"', ";
            countDespesas++
        }

    });

   // console.log(strDespesas);

    //strDespesas = strDespesas.substring(0, strDespesas.length-2);

////////////////|FIM OBTEM DESPESAS ADICIONAIS|///////////////////////////////////


    strSet = "id_empresa = '"+storage.getItem('id_empresa')+"', "+
              "id_usuario = '"+storage.getItem('id_usuario')+"', "+
              "solicitar_liberacao = '"+solicitarLiberacao+"', "+
              "nome = '"+$("#nome").val()+"', "+
              "telefone = '"+$("#telefone").val()+"', "+
              "pretencao = '"+$("#pretencao").val()+"', "+
              "marca = '"+$("#marcas").val()+"', "+
              "modelo = '"+$("#modelos").val()+"', "+
              "valor_ano_modelo = '"+$("#valor_ano_modelo").val()+"', "+
              "ano_modelo = '"+$("#ano_modelo").val()+"', "+
              "ano_fabricacao = '"+$("#ano_fabricacao").val()+"', "+
              "portas = '"+$("#portas").val()+"', "+
              "cor = '"+$("#cor").val()+"', "+
              "km = '"+$("#km").val()+"', "+
              "placa = '"+$("#placa").val()+"', "+
              "doc_carro = '"+$("#doc_carro").attr("src")+"', "+
              "foto_1 = '"+$("#foto_1").attr("src")+"', "+
              "foto_2 = '"+$("#foto_2").attr("src")+"', "+
              "foto_3 = '"+$("#foto_3").attr("src")+"', "+
              "foto_4 = '"+$("#foto_4").attr("src")+"', "+
              "opcionais = '"+opt+"', "+
              "data_lateral_dianteira_esquerda = '"+$("#data_lateral_dianteira_esquerda").val()+"', "+
              "data_lateral_traseira_esquerda = '"+$("#data_lateral_traseira_esquerda").val()+"', "+
              "data_porta_traseira_esquerda = '"+$("#data_porta_traseira_esquerda").val()+"', "+
              "data_porta_dianteira_esquerda = '"+$("#data_porta_dianteira_esquerda").val()+"', "+
              "data_vidro_dianteiro_esquerdo = '"+$("#data_vidro_dianteiro_esquerdo").val()+"', "+
              "data_vidro_traseiro_esquerdo = '"+$("#data_vidro_traseiro_esquerdo").val()+"', "+
              "data_roda_traseira_esquerda = '"+$("#data_roda_traseira_esquerda").val()+"', "+
              "data_roda_dianteira_esquerda = '"+$("#data_roda_dianteira_esquerda").val()+"', "+
              "data_porta_traseira_direita = '"+$("#data_porta_traseira_direita").val()+"', "+
              "data_porta_dianteira_direita = '"+$("#data_porta_dianteira_direita").val()+"', "+
              "data_vidro_dianteiro_direito = '"+$("#data_vidro_dianteiro_direito").val()+"', "+
              "data_vidro_traseiro_direito = '"+$("#data_vidro_traseiro_direito").val()+"', "+
              "data_lateral_traseira_direita = '"+$("#data_lateral_traseira_direita").val()+"', "+
              "data_lateral_dianteira_direita = '"+$("#data_lateral_dianteira_direita").val()+"', "+
              "data_roda_traseira_direita = '"+$("#data_roda_traseira_direita").val()+"', "+
              "data_roda_dianteira_direita = '"+$("#data_roda_dianteira_direita").val()+"', "+
              "data_vidro_dianteiro = '"+$("#data_vidro_dianteiro").val()+"', "+
              "data_capo = '"+$("#data_capo").val()+"', "+
              "data_parachoque_dianteiro = '"+$("#data_parachoque_dianteiro").val()+"', "+
              "data_farol_direito = '"+$("#data_farol_direito").val()+"', "+
              "data_farol_esquerdo = '"+$("#data_farol_esquerdo").val()+"', "+
              "data_retrovisor_direito = '"+$("#data_retrovisor_direito").val()+"', "+
              "data_retrovisor_esquerdo = '"+$("#data_retrovisor_esquerdo").val()+"', "+
              "data_parachoque_traseiro = '"+$("#data_parachoque_traseiro").val()+"', "+
              "data_tampa_porta = '"+$("#data_tampa_porta-malas").val()+"', "+
              "data_vidro_traseiro = '"+$("#data_vidro_traseiro").val()+"', "+
              "data_lanterna_esquerda_traseira = '"+$("#data_lanterna_esquerda_traseira").val()+"', "+
              "data_lanterna_direita_traseira = '"+$("#data_lanterna_direita_traseira").val()+"', "+
              "teto = '"+$("#teto").val()+"', "+
              "obs_teto = '"+$("#obs_teto").val()+"', "+
              "acidente = '"+$("input[name='acidente']:checked").val()+"', "+
              "obs_acidente = '"+$("#obs_acidente").val()+"', "+
              "motor = '"+$("input[name='motor']:checked").val()+"', "+
              "obs_motor = '"+$("#obs_motor").val()+"', "+
              "caixa_direcao = '"+$("input[name='caixa_direcao']:checked").val()+"', "+
              "obs_caixa_direcao = '"+$("#obs_caixa_direcao").val()+"', "+
              "tipo_cambio = '"+$("input[name='tipo_cambio']:checked").val()+"', "+
              "cambio = '"+$("input[name='cambio']:checked").val()+"', "+
              "obs_cambio = '"+$("#obs_cambio").val()+"', "+
              "suspensao = '"+$("input[name='suspensao']:checked").val()+"', "+
              "obs_suspensao = '"+$("#obs_suspensao").val()+"', "+
              "embreagem = '"+$("input[name='embreagem']:checked").val()+"', "+
              "obs_embreagem = '"+$("#obs_embreagem").val()+"', "+
              "freios = '"+$("input[name='freios']:checked").val()+"', "+
              "obs_freios = '"+$("#obs_freios").val()+"', "+
              "escapamento = '"+$("input[name='escapamento']:checked").val()+"', "+
              "obs_escapamento = '"+$("#obs_escapamento").val()+"', "+
              "eletrica = '"+$("input[name='eletrica']:checked").val()+"', "+
              "obs_eletrica = '"+$("#obs_eletrica").val()+"', "+
              "luz_painel = '"+$("input[name='luz_painel']:checked").val()+"', "+
              "obs_luz_painel = '"+$("#obs_luz_painel").val()+"', "+
              "ar_condicionado = '"+$("input[name='ar_condicionado']:checked").val()+"', "+
              "obs_ar_condicionado = '"+$("#obs_ar_condicionado").val()+"', "+
              "qtd_pneus = '"+$("input[name='qtd_pneus']:checked").val()+"', "+
              "largura = '"+$("#largura").val()+"', "+
              "perfil = '"+$("#perfil").val()+"', "+
              "aro = '"+$("#aro").val()+"', "+
              "obs_pneus = '"+$("#obs_pneus").val()+"', "+
              "tapecaria = '"+$("input[name='tapecaria']:checked").val()+"', "+
              "faixas = '"+$("#faixas").val()+"', "+
              "obs_tapecaria = '"+$("#obs_tapecaria").val()+"', "+
              "manutencao = '"+$("input[name='manutencao']:checked").val()+"', "+
              "obs_manutencao = '"+$("#obs_manutencao").val()+"', "+
              "mercado = '"+$("input[name='mercado']:checked").val()+"', "+
              "obs_mercado = '"+$("#obs_mercado").val()+"', "+
              "finalidade = '"+$("#finalidade").val()+"', "+
              "obs_final = '"+$("#obs_final").val()+"', "+
              "total = '"+$("#total").val()+"', "+
              "marca_interesse = '"+$("#marca_interesse").val()+"', "+
              "modelo_interesse = '"+$("#modelo_interesse").val()+"', "+
              "ano_interesse = '"+$("#ano_interesse").val()+"', "+
              strDespesas;


    strSet = strSet.substring(0, strSet.length-2);

    tx.executeSql("UPDATE avaliacoes SET "+strSet+" WHERE id = "+$("#id").val()+";" );
   
}

function errorEdtAvaliacoesAutoSalvar(tx, err) {
    alert("Error processing SQL: "+err);
}

function sucessoEdtAvaliacoesAutoSalvar() {
    $("#div_aguarde").remove();
    //alert("Avaliação salva com sucesso!");
    //upload("form_edt");
}



function insereAvaliacaoAutoSalvar(tx){

    //tx.executeSql('DROP TABLE IF EXISTS avaliacoes');



 //////////////////Pega Opcionais////////////////////////////////////////////////
    var opt = "";
    $("#table_opcionais input[type='checkbox']").each(function(){
        if($(this).is(":checked")){
            opt += $(this).attr("id")+"_";
        }
    });

    opt = opt.substring(0, opt.length-1);
 ///////////////Fim pega opcionais//////////////////////////////////////////////

 ////////////////OBTEM DESPESAS ADICIONAIS///////////////////////////////////
    var strDespesas = "";
    var countDespesas = 0;
    $("#outras_despesas input[type='telefone']").each(function(){
        strDespesas += "'"+$(this).prev().prev().prev().prev().val()+"', ";
        strDespesas += "'"+$(this).val()+"', ";
        countDespesas++
    });

    for(var i = countDespesas; i < 5; i++){
        strDespesas += "'', ";
        strDespesas += "'', ";
    }

    strDespesas = strDespesas.substring(0, strDespesas.length-2);

 ////////////////|FIM OBTEM DESPESAS ADICIONAIS///////////////////////////////////

 /////////////////////DATA////////////////////////////////////////////////////////
    var d = new Date();

    var mes = parseInt(d.getMonth())+1;
    var dia = parseInt(d.getDate());
    var horas = parseInt(d.getHours());
    var minutos = parseInt(d.getMinutes());
    var segundos = parseInt(d.getSeconds());

    if(mes < 10){mes = "0"+mes;}
    if(dia < 10){dia = "0"+dia;}
    if(horas < 10){horas = "0"+horas;}
    if(minutos < 10){minutos = "0"+minutos;}
    if(segundos < 10){segundos = "0"+segundos;}

    var data = d.getFullYear()+"-"+mes+"-"+dia+" "+horas+":"+minutos+":"+segundos;

    $("#data").val(data);

 ///////////////////////////////FIM DATA/////////////////////////////////////////////

    tx.executeSql("CREATE TABLE IF NOT EXISTS avaliacoes (id INTEGER PRIMARY KEY AUTOINCREMENT,"+
                                                         "id_empresa int(11), "+
                                                         "id_usuario int(11), "+
                                                         "id_upload int(11), "+
                                                         "data DATETIME, "+
                                                         "solicitar_liberacao BOOLEAN DEFAULT false, "+
                                                         "nome varchar(100), "+
                                                         "telefone varchar(20), "+
                                                         "pretencao varchar(20), "+
                                                         "marca varchar(100), "+
                                                          "modelo varchar(100), "+
                                                          "valor_ano_modelo varchar(20), "+
                                                          "ano_modelo varchar(100), "+
                                                          "ano_fabricacao varchar(10), "+
                                                          "portas varchar(10), "+
                                                          "cor varchar(10), "+
                                                          "km varchar(20), "+
                                                          "placa varchar(10), "+
                                                          "doc_carro VARCHAR(255), "+
                                                          "foto_1 VARCHAR(255), "+
                                                          "foto_2 VARCHAR(255), "+
                                                          "foto_3 VARCHAR(255), "+
                                                          "foto_4 VARCHAR(255), "+
                                                          "opcionais VARCHAR(255), "+
                                                          "data_lateral_dianteira_esquerda int(2), "+
                                                          "data_lateral_traseira_esquerda int(2), "+
                                                          "data_porta_traseira_esquerda int(2), "+
                                                          "data_porta_dianteira_esquerda int(2), "+
                                                          "data_vidro_dianteiro_esquerdo int(2), "+
                                                          "data_vidro_traseiro_esquerdo int(2), "+
                                                          "data_roda_traseira_esquerda int(2), "+
                                                          "data_roda_dianteira_esquerda int(2), "+
                                                          "data_porta_traseira_direita int(2), "+
                                                          "data_porta_dianteira_direita int(2), "+
                                                          "data_vidro_dianteiro_direito int(2), "+
                                                          "data_vidro_traseiro_direito int(2), "+
                                                          "data_lateral_traseira_direita int(2), "+
                                                          "data_lateral_dianteira_direita int(2), "+
                                                          "data_roda_traseira_direita int(2), "+
                                                          "data_roda_dianteira_direita int(2), "+
                                                          "data_vidro_dianteiro int(2), "+
                                                          "data_capo int(2), "+
                                                          "data_parachoque_dianteiro int(2), "+
                                                          "data_farol_direito int(2), "+
                                                          "data_farol_esquerdo int(2), "+
                                                          "data_retrovisor_direito int(2), "+
                                                          "data_retrovisor_esquerdo int(2), "+
                                                          "data_parachoque_traseiro int(2), "+
                                                          "data_tampa_porta int(2), "+
                                                          "data_vidro_traseiro int(2), "+
                                                          "data_lanterna_esquerda_traseira int(2), "+
                                                          "data_lanterna_direita_traseira int(2), "+
                                                          "teto int(2), "+
                                                          "obs_teto TEXT, "+
                                                          "acidente VARCHAR(5), "+
                                                          "obs_acidente TEXT, "+
                                                          "motor VARCHAR(5), "+
                                                          "obs_motor TEXT, "+
                                                          "caixa_direcao VARCHAR(5), "+
                                                          "obs_caixa_direcao TEXT, "+
                                                          "tipo_cambio VARCHAR(15), "+
                                                          "cambio VARCHAR(5), "+
                                                          "obs_cambio TEXT, "+
                                                          "suspensao VARCHAR(5), "+
                                                          "obs_suspensao TEXT, "+
                                                          "embreagem VARCHAR(5), "+
                                                          "obs_embreagem TEXT, "+
                                                          "freios VARCHAR(5), "+
                                                          "obs_freios TEXT, "+
                                                          "escapamento VARCHAR(5), "+
                                                          "obs_escapamento TEXT, "+
                                                          "eletrica VARCHAR(5), "+
                                                          "obs_eletrica TEXT, "+
                                                          "luz_painel VARCHAR(5), "+
                                                          "obs_luz_painel TEXT, "+
                                                          "ar_condicionado VARCHAR(5), "+
                                                          "obs_ar_condicionado TEXT, "+
                                                          "qtd_pneus int(2), "+
                                                          "largura VARCHAR(5), "+
                                                          "perfil VARCHAR(5), "+
                                                          "aro VARCHAR(10), "+
                                                          "obs_pneus TEXT, "+
                                                          "tapecaria VARCHAR(10), "+
                                                          "faixas int(3), "+
                                                          "obs_tapecaria TEXT, "+
                                                          "manutencao VARCHAR(5), "+
                                                          "obs_manutencao TEXT, "+
                                                          "mercado VARCHAR(5), "+
                                                          "obs_mercado TEXT, "+
                                                          "finalidade VARCHAR(50), "+
                                                          "obs_final TEXT, "+
                                                          "total FLOAT, "+
                                                          "marca_interesse VARCHAR(100), "+
                                                          "modelo_interesse VARCHAR(100), "+
                                                          "ano_interesse VARCHAR(50), "+
                                                          "despesa_descricao_1 VARCHAR(100), "+
                                                          "despesa_valor_1 FLOAT, "+
                                                          "despesa_descricao_2 VARCHAR(100), "+
                                                          "despesa_valor_2 FLOAT, "+
                                                          "despesa_descricao_3 VARCHAR(100), "+
                                                          "despesa_valor_3 FLOAT, "+
                                                          "despesa_descricao_4 VARCHAR(100), "+
                                                          "despesa_valor_4 FLOAT, "+
                                                          "despesa_descricao_5 VARCHAR(100), "+
                                                          "despesa_valor_5 FLOAT);");
   

 var strCampos = "id_empresa, "+
              "id_usuario, "+
              "id_upload, "+
              "data, "+
              "solicitar_liberacao, "+
              "nome, "+
              "telefone, "+
              "pretencao, "+
              "marca, "+
              "modelo, "+
              "valor_ano_modelo, "+
              "ano_modelo, "+
              "ano_fabricacao, "+
              "portas, "+
              "cor, "+
              "km, "+
              "placa, "+
              "doc_carro, "+
              "foto_1, "+
              "foto_2, "+
              "foto_3, "+
              "foto_4, "+
              "opcionais, "+
              "data_lateral_dianteira_esquerda, "+
              "data_lateral_traseira_esquerda, "+
              "data_porta_traseira_esquerda, "+
              "data_porta_dianteira_esquerda, "+
              "data_vidro_dianteiro_esquerdo, "+
              "data_vidro_traseiro_esquerdo, "+
              "data_roda_traseira_esquerda, "+
              "data_roda_dianteira_esquerda, "+
              "data_porta_traseira_direita, "+
              "data_porta_dianteira_direita, "+
              "data_vidro_dianteiro_direito, "+
              "data_vidro_traseiro_direito, "+
              "data_lateral_traseira_direita, "+
              "data_lateral_dianteira_direita, "+
              "data_roda_traseira_direita, "+
              "data_roda_dianteira_direita, "+
              "data_vidro_dianteiro, "+
              "data_capo, "+
              "data_parachoque_dianteiro, "+
              "data_farol_direito, "+
              "data_farol_esquerdo, "+
              "data_retrovisor_direito, "+
              "data_retrovisor_esquerdo, "+
              "data_parachoque_traseiro, "+
              "data_tampa_porta, "+
              "data_vidro_traseiro, "+
              "data_lanterna_esquerda_traseira, "+
              "data_lanterna_direita_traseira, "+
              "teto, "+
              "obs_teto, "+
              "acidente, "+
              "obs_acidente, "+
              "motor, "+
              "obs_motor, "+
              "caixa_direcao, "+
              "obs_caixa_direcao, "+
              "tipo_cambio, "+
              "cambio, "+
              "obs_cambio, "+
              "suspensao, "+
              "obs_suspensao, "+
              "embreagem, "+
              "obs_embreagem, "+
              "freios, "+
              "obs_freios, "+
              "escapamento, "+
              "obs_escapamento, "+
              "eletrica, "+
              "obs_eletrica, "+
              "luz_painel, "+
              "obs_luz_painel, "+
              "ar_condicionado, "+
              "obs_ar_condicionado, "+
              "qtd_pneus, "+
              "largura, "+
              "perfil, "+
              "aro, "+
              "obs_pneus, "+
              "tapecaria, "+
              "faixas, "+
              "obs_tapecaria, "+
              "manutencao, "+
              "obs_manutencao, "+
              "mercado, "+
              "obs_mercado, "+
              "finalidade, "+
              "obs_final, "+
              "total, "+
              "marca_interesse, "+
              "modelo_interesse, "+
              "ano_interesse, "+
              "despesa_descricao_1, "+
              "despesa_valor_1, "+
              "despesa_descricao_2, "+
              "despesa_valor_2, "+
              "despesa_descricao_3, "+
              "despesa_valor_3, "+
              "despesa_descricao_4, "+
              "despesa_valor_4, "+
              "despesa_descricao_5, "+
              "despesa_valor_5";


    strValues = "'"+storage.getItem('id_empresa')+"', "+
              "'"+storage.getItem('id_usuario')+"', "+
              "'0', "+
              "'"+data+"', "+
              "'"+solicitarLiberacao+"', "+
              "'"+$("#nome").val()+"', "+
              "'"+$("#telefone").val()+"', "+
              "'"+$("#pretencao").val()+"', "+
              "'"+$("#marcas").val()+"', "+
              "'"+$("#modelos").val()+"', "+
              "'"+$("#valor_ano_modelo").val()+"', "+
              "'"+$("#ano_modelo").val()+"', "+
              "'"+$("#ano_fabricacao").val()+"', "+
              "'"+$("#portas").val()+"', "+
              "'"+$("#cor").val()+"', "+
              "'"+$("#km").val()+"', "+
              "'"+$("#placa").val()+"', "+
              "'"+$("#doc_carro").attr("src")+"', "+
              "'"+$("#foto_1").attr("src")+"', "+
              "'"+$("#foto_2").attr("src")+"', "+
              "'"+$("#foto_3").attr("src")+"', "+
              "'"+$("#foto_4").attr("src")+"', "+
              "'"+opt+"', "+
              "'"+$("#data_lateral_dianteira_esquerda").val()+"', "+
              "'"+$("#data_lateral_traseira_esquerda").val()+"', "+
              "'"+$("#data_porta_traseira_esquerda").val()+"', "+
              "'"+$("#data_porta_dianteira_esquerda").val()+"', "+
              "'"+$("#data_vidro_dianteiro_esquerdo").val()+"', "+
              "'"+$("#data_vidro_traseiro_esquerdo").val()+"', "+
              "'"+$("#data_roda_traseira_esquerda").val()+"', "+
              "'"+$("#data_roda_dianteira_esquerda").val()+"', "+
              "'"+$("#data_porta_traseira_direita").val()+"', "+
              "'"+$("#data_porta_dianteira_direita").val()+"', "+
              "'"+$("#data_vidro_dianteiro_direito").val()+"', "+
              "'"+$("#data_vidro_traseiro_direito").val()+"', "+
              "'"+$("#data_lateral_traseira_direita").val()+"', "+
              "'"+$("#data_lateral_dianteira_direita").val()+"', "+
              "'"+$("#data_roda_traseira_direita").val()+"', "+
              "'"+$("#data_roda_dianteira_direita").val()+"', "+
              "'"+$("#data_vidro_dianteiro").val()+"', "+
              "'"+$("#data_capo").val()+"', "+
              "'"+$("#data_parachoque_dianteiro").val()+"', "+
              "'"+$("#data_farol_direito").val()+"', "+
              "'"+$("#data_farol_esquerdo").val()+"', "+
              "'"+$("#data_retrovisor_direito").val()+"', "+
              "'"+$("#data_retrovisor_esquerdo").val()+"', "+
              "'"+$("#data_parachoque_traseiro").val()+"', "+
              "'"+$("#data_tampa_porta-malas").val()+"', "+
              "'"+$("#data_vidro_traseiro").val()+"', "+
              "'"+$("#data_lanterna_esquerda_traseira").val()+"', "+
              "'"+$("#data_lanterna_direita_traseira").val()+"', "+
              "'"+$("#teto").val()+"', "+
              "'"+$("#obs_teto").val()+"', "+
              "'"+$("input[name='acidente']:checked").val()+"', "+
              "'"+$("#obs_acidente").val()+"', "+
              "'"+$("input[name='motor']:checked").val()+"', "+
              "'"+$("#obs_motor").val()+"', "+
              "'"+$("input[name='caixa_direcao']:checked").val()+"', "+
              "'"+$("#obs_caixa_direcao").val()+"', "+
              "'"+$("input[name='tipo_cambio']:checked").val()+"', "+
              "'"+$("input[name='cambio']:checked").val()+"', "+
              "'"+$("#obs_cambio").val()+"', "+
              "'"+$("input[name='suspensao']:checked").val()+"', "+
              "'"+$("#obs_suspensao").val()+"', "+
              "'"+$("input[name='embreagem']:checked").val()+"', "+
              "'"+$("#obs_embreagem").val()+"', "+
              "'"+$("input[name='freios']:checked").val()+"', "+
              "'"+$("#obs_freios").val()+"', "+
              "'"+$("input[name='escapamento']:checked").val()+"', "+
              "'"+$("#obs_escapamento").val()+"', "+
              "'"+$("input[name='eletrica']:checked").val()+"', "+
              "'"+$("#obs_eletrica").val()+"', "+
              "'"+$("input[name='luz_painel']:checked").val()+"', "+
              "'"+$("#obs_luz_painel").val()+"', "+
              "'"+$("input[name='ar_condicionado']:checked").val()+"', "+
              "'"+$("#obs_ar_condicionado").val()+"', "+
              "'"+$("input[name='qtd_pneus']:checked").val()+"', "+
              "'"+$("#largura").val()+"', "+
              "'"+$("#perfil").val()+"', "+
              "'"+$("#aro").val()+"', "+
              "'"+$("#obs_pneus").val()+"', "+
              "'"+$("input[name='tapecaria']:checked").val()+"', "+
              "'"+$("#faixas").val()+"', "+
              "'"+$("#obs_tapecaria").val()+"', "+
              "'"+$("input[name='manutencao']:checked").val()+"', "+
              "'"+$("#obs_manutencao").val()+"', "+
              "'"+$("input[name='mercado']:checked").val()+"', "+
              "'"+$("#obs_mercado").val()+"', "+
              "'"+$("#finalidade").val()+"', "+
              "'"+$("#obs_final").val()+"', "+
              "'"+$("#total").val()+"', "+
              "'"+$("#marca_interesse").val()+"', "+
              "'"+$("#modelo_interesse").val()+"', "+
              "'"+$("#ano_interesse").val()+"', "+
              strDespesas;

	 
	    tx.executeSql("INSERT INTO avaliacoes("+strCampos+") VALUES("+strValues+")");  

}

function errorAvaliacoesAutoSalvar(tx, err) {
    alert("ErrorAdd processing SQL: "+err);
}

function sucessoAvaliacoesAutoSalvar() {
    $("#div_aguarde").remove();
    //alert("Avaliação salva com sucesso!");
    //location.replace('index.html');

    insereIdAvaliacao();

}


function insereIdAvaliacao(){

	var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

    db.transaction(function(tx){

        tx.executeSql("SELECT id "+
                      "FROM avaliacoes "+
                      "order by id DESC LIMIT(1);",
        [], 
        function(tx, res) {

        	if(res.rows.item(0).id){
        		$("#id").val(res.rows.item(0).id);
        	}

        }, 
        function(tx, erro){
        
        	console.log("Error processing SQL: "+erro);

	    }, 
	    function(){
	        
	    });

	});

}
