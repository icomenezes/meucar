//////////////BLOCO ATUALIZA///////////////////////////////////
var qtdErro = 0;

$("#atualizaFIPE").click(function(){

    $("#btn_menu").click();

    if(confirm("A atualização pode demorar alguns minutos, dependendo da sua conexão com a internet, enquanto estiver atualizando permaneça conectado a internet e próximo a fonte de internet(Roteador)\n\nClique em Ok para iniciar a atualização.")){
    
    $("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 30px; color:#fff;"><div style="margin-top:200px;">Aguarde...</div></div>');

    var strModelos = "";
    var strAnoModelos = "";
    var strCamposParametros = "";
    var strValuesParametros = "";

    var parametros = $.ajax({
        url: "https://meucar.trsystem.com.br/apps/atualiza-parametros/app_avaliacoes/true",
        async: false,
        type: 'POST',
        dataType: 'json'
    }).responseText;

    jQuery.each(JSON.parse(parametros), function(i, val) {
        strCamposParametros += i+", "; 
        strValuesParametros += "'"+val+"', ";
    });

    strCamposParametros = strCamposParametros.substring(0, strCamposParametros.length-2);
    strValuesParametros = strValuesParametros.substring(0, strValuesParametros.length-2);

    var modelos = $.ajax({
        url: "https://meucar.trsystem.com.br/fipe/modelos-fipe/app/true",
        async: false,
        type: 'POST',
        dataType: 'json'
    }).responseText;

    jQuery.each(JSON.parse(modelos), function(i, val) {
        strModelos += "('"+val.id+"', '"+val.nome+"', '"+val.marca+"', '"+val.tipo+"'),"; 
    });

    strModelos = strModelos.substring(0, strModelos.length-1)+";";

    var anoModelos = $.ajax({
        url: "https://meucar.trsystem.com.br/fipe/anos-modelos-fipe/app/true",
        async: false,
        type: 'POST',
        dataType: 'json'
    }).responseText;

    jQuery.each(JSON.parse(anoModelos), function(i, val) {
        strAnoModelos += "('"+val.id+"', '"+val.nome+"', '"+val.modelo+"', '"+val.valor+"', '"+val.tipo+"'),"; 
    });

    strAnoModelos = strAnoModelos.substring(0, strAnoModelos.length-1)+";";


    var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
    db.transaction(function(tx){

        tx.executeSql('DROP TABLE IF EXISTS marcas');
        tx.executeSql('DROP TABLE IF EXISTS modelos');
        tx.executeSql('DROP TABLE IF EXISTS ano_modelos');
        tx.executeSql('DROP TABLE IF EXISTS parametros_avaliacoes');

        tx.executeSql('CREATE TABLE IF NOT EXISTS marcas(id TEXT, nome TEXT, tipo TEXT)');
        tx.executeSql('CREATE TABLE IF NOT EXISTS modelos(id int(11), nome varchar(200), marca int(5), tipo varchar(10))');
        tx.executeSql('CREATE TABLE IF NOT EXISTS ano_modelos(id TEXT, nome TEXT, modelo TEXT, valor TEXT, tipo TEXT)');
        tx.executeSql("CREATE TABLE IF NOT EXISTS parametros_avaliacoes (id int(11) NOT NULL,id_empresa int(11) DEFAULT NULL, data_atualizacao DATETIME,checkbox_valor_tabela_limite int(1) DEFAULT '0',valor_tabela_limite float DEFAULT NULL,checkbox_margem_compra int(1) DEFAULT '0',margem_compra float DEFAULT NULL,checkbox_margem_troca_estoque int(1) DEFAULT '0',margem_troca_estoque float DEFAULT NULL,checkbox_margem_compra_acima_limite int(1) DEFAULT '0',margem_compra_acima_limite float DEFAULT NULL,checkbox_margem_troca_estoque_acima_limite int(1) DEFAULT '0',margem_troca_estoque_acima_limite float DEFAULT NULL,checkbox_valor_fipe_veiculo_repasse int(1) DEFAULT '0',valor_fipe_veiculo_repasse float DEFAULT NULL,checkbox_margem_repasse int(1) DEFAULT '0',margem_repasse float DEFAULT NULL,checkbox_limite_kilometragem int(1) DEFAULT '0',limite_kilometragem int(11) DEFAULT NULL,checkbox_kilometragem_ano int(1) DEFAULT '0',kilometragem_ano int(11) DEFAULT NULL,cores_estranhas varchar(255) DEFAULT NULL,opcionais varchar(255) DEFAULT NULL,checkbox_martelinho int(1) DEFAULT '0',martelinho float DEFAULT NULL,martelinho_tempo int(3) DEFAULT '0',checkbox_pincelar int(1) DEFAULT '0',pincelar float DEFAULT NULL,pincelar_tempo int(3) DEFAULT '0',checkbox_pintar int(1) DEFAULT '0',pintar float DEFAULT NULL,pintar_tempo int(3) DEFAULT '0',checkbox_funilaria int(1) DEFAULT '0',funilaria float DEFAULT NULL,funilaria_tempo int(3) DEFAULT '0',checkbox_trocar_pecas int(1) DEFAULT '0',trocar_pecas float DEFAULT NULL,trocar_pecas_tempo int(3) DEFAULT '0',checkbox_farol int(1) DEFAULT '0',farol float DEFAULT NULL,farol_tempo int(3) DEFAULT '0',checkbox_lanterna int(1) DEFAULT '0',lanterna float DEFAULT NULL,lanterna_tempo int(3) DEFAULT '0',checkbox_vidro int(1) DEFAULT '0',vidro float DEFAULT NULL,vidro_tempo int(3) DEFAULT '0',checkbox_retrovisor int(1) DEFAULT '0',retrovisor float DEFAULT NULL,retrovisor_tempo int(3) DEFAULT '0',checkbox_roda int(1) DEFAULT '0',roda float DEFAULT NULL,roda_tempo int(3) DEFAULT '0',checkbox_vestigio_acidente int(1) DEFAULT '0',vestigio_acidente float DEFAULT NULL,vestigio_acidente_tempo int(3) DEFAULT '0',checkbox_motor int(1) DEFAULT '0',motor float DEFAULT NULL,motor_tempo int(3) DEFAULT '0',checkbox_caixa_direcao int(1) DEFAULT '0',caixa_direcao float DEFAULT NULL,caixa_direcao_tempo int(3) DEFAULT '0',checkbox_cambio_manual int(1) DEFAULT '0',cambio_manual float DEFAULT NULL,cambio_manual_tempo int(3) DEFAULT '0',checkbox_cambio_automatico int(1) DEFAULT '0',cambio_automatico float DEFAULT NULL,cambio_automatico_tempo int(3) DEFAULT '0',checkbox_suspensao int(1) DEFAULT '0',suspensao float DEFAULT NULL,suspensao_tempo int(3) DEFAULT '0',checkbox_embreagem int(1) DEFAULT '0',embreagem float DEFAULT NULL,embreagem_tempo int(3) DEFAULT '0',checkbox_freios int(1) DEFAULT '0',freios float DEFAULT NULL,freios_tempo int(3) DEFAULT '0',checkbox_escapamento int(1) DEFAULT '0',escapamento float DEFAULT NULL,escapamento_tempo int(3) DEFAULT '0',checkbox_eletrica int(1) DEFAULT '0',eletrica float DEFAULT NULL,eletrica_tempo int(3) DEFAULT '0',checkbox_ar_condicionado int(1) DEFAULT '0',ar_condicionado float DEFAULT NULL,ar_condicionado_tempo int(3) DEFAULT '0',checkbox_luz_painel int(1) DEFAULT '0',luz_painel float DEFAULT NULL,luz_painel_tempo int(3) DEFAULT '0',checkbox_aro_13 int(1) DEFAULT '0',aro_13 float DEFAULT NULL,aro_13_tempo int(3) DEFAULT '0',checkbox_aro_14 int(1) DEFAULT '0',aro_14 float DEFAULT NULL,aro_14_tempo int(3) DEFAULT '0',checkbox_aro_15 int(1) DEFAULT '0',aro_15 float DEFAULT NULL,aro_15_tempo int(3) DEFAULT '0',checkbox_aro_16 int(1) DEFAULT '0',aro_16 float DEFAULT NULL,aro_16_tempo int(3) DEFAULT '0',checkbox_aro_17 int(1) DEFAULT '0',aro_17 float DEFAULT NULL,aro_17_tempo int(3) DEFAULT '0',checkbox_aro_18 int(1) DEFAULT '0',aro_18 float DEFAULT NULL,aro_18_tempo int(3) DEFAULT '0',checkbox_off_road int(1) DEFAULT '0',off_road float DEFAULT NULL,off_road_tempo int(3) DEFAULT '0',checkbox_faixa_couro int(1) DEFAULT '0',faixa_couro float DEFAULT NULL,faixa_couro_tempo int(3) DEFAULT '0',checkbox_faixa_tecido int(1) DEFAULT '0',faixa_tecido float DEFAULT NULL,faixa_tecido_tempo int(3) DEFAULT '0',checkbox_manutencao int(1) DEFAULT '0',manutencao float DEFAULT NULL,checkbox_mercado_normal int(1) DEFAULT '0',mercado_normal float DEFAULT NULL,checkbox_mercado_ruim int(1) DEFAULT '0',mercado_ruim float DEFAULT NULL,checkbox_mercado_pessimo int(1) DEFAULT '0',mercado_pessimo float DEFAULT NULL,checkbox_mercado_nao_sei int(1) DEFAULT '0', mercado_nao_sei float DEFAULT NULL);");
        
        tx.executeSql("INSERT INTO marcas (id, nome, tipo) VALUES ('1', 'Acura', 'carro'),('2', 'Agrale', 'carro'),('4', 'AM Gen', 'carro'),('5', 'Asia Motors', 'carro'),('189', 'ASTON MARTIN', 'carro'),('6', 'Audi', 'carro'),('7', 'BMW', 'carro'),('8', 'BRM', 'carro'),('9', 'Buggy', 'carro'),('123', 'Bugre', 'carro'),('10', 'Cadillac', 'carro'),('11', 'CBT Jipe', 'carro'),('136', 'CHANA', 'carro'),('182', 'CHANGAN', 'carro'),('161', 'CHERY', 'carro'),('12', 'Chrysler', 'carro'),('13', 'Citroën', 'carro'),('14', 'Cross Lander', 'carro'),('15', 'Daewoo', 'carro'),('16', 'Daihatsu', 'carro'),('17', 'Dodge', 'carro'),('147', 'EFFA', 'carro'),('18', 'Engesa', 'carro'),('19', 'Envemo', 'carro'),('20', 'Ferrari', 'carro'),('21', 'Fiat', 'carro'),('149', 'Fibravan', 'carro'),('22', 'Ford', 'carro'),('190', 'FOTON', 'carro'),('170', 'Fyber', 'carro'),('23', 'GM - Chevrolet', 'carro'),('153', 'GREAT WALL', 'carro'),('24', 'Gurgel', 'carro'),('152', 'HAFEI', 'carro'),('25', 'Honda', 'carro'),('26', 'Hyundai', 'carro'),('27', 'Isuzu', 'carro'),('177', 'JAC', 'carro'),('28', 'Jaguar', 'carro'),('29', 'Jeep', 'carro'),('154', 'JINBEI', 'carro'),('30', 'JPX', 'carro'),('31', 'Kia Motors', 'carro'),('32', 'Lada', 'carro'),('171', 'LAMBORGHINI', 'carro'),('33', 'Land Rover', 'carro'),('34', 'Lexus', 'carro'),('168', 'LIFAN', 'carro'),('127', 'LOBINI', 'carro'),('35', 'Lotus', 'carro'),('140', 'Mahindra', 'carro'),('36', 'Maserati', 'carro'),('37', 'Matra', 'carro'),('38', 'Mazda', 'carro'),('39', 'Mercedes-Benz', 'carro'),('40', 'Mercury', 'carro'),('167', 'MG', 'carro'),('156', 'MINI', 'carro'),('41', 'Mitsubishi', 'carro'),('42', 'Miura', 'carro'),('43', 'Nissan', 'carro'),('44', 'Peugeot', 'carro'),('45', 'Plymouth', 'carro'),('46', 'Pontiac', 'carro'),('47', 'Porsche', 'carro'),('185', 'RAM', 'carro'),('186', 'RELY', 'carro'),('48', 'Renault', 'carro'),('49', 'Rover', 'carro'),('50', 'Saab', 'carro'),('51', 'Saturn', 'carro'),('52', 'Seat', 'carro'),('183', 'SHINERAY', 'carro'),('157', 'smart', 'carro'),('125', 'SSANGYONG', 'carro'),('54', 'Subaru', 'carro'),('55', 'Suzuki', 'carro'),('165', 'TAC', 'carro'),('56', 'Toyota', 'carro'),('57', 'Troller', 'carro'),('58', 'Volvo', 'carro'),('59', 'VW - VolksWagen', 'carro'),('163', 'Wake', 'carro'),('120', 'Walk', 'carro');");
        tx.executeSql("INSERT INTO modelos (id, nome, marca, tipo) VALUES "+strModelos);
        tx.executeSql("INSERT INTO ano_modelos (id, nome, modelo, valor, tipo) VALUES "+strAnoModelos);
        tx.executeSql("INSERT INTO parametros_avaliacoes("+strCamposParametros+") VALUES("+strValuesParametros+");");

    },
    function(tx, err) {
        $("#div_aguarde").remove();
        //console.log(err);
        qtdErro++;
        if(qtdErro < 4){
            $("#atualizaFIPE").click();
        }
        if(qtdErro == 4){
            alert("Houve "+qtdErro+" tentativas de atualização e não foi possível atualizar, por favor tente novamente.\nCaso o erro persista, informe o administrador do sistema.");
        }
    },
    function(){
        $("#div_aguarde").remove();
        strModelos = "";
        strAnoModelos = "";
        strCamposParametros = "";
        strValuesParametros = "";
        var storage = window.localStorage;
        storage.setItem('atualizacao', 0);
        $(".atualizacao_disponivel").css("display", "none");
        alert("Atualização realizada com sucesso!");
        getMarcas();
    });

    }

});

//////////////////////////FIM BLOCO ATUALIZA////////////////////////////////////

function getModelosLimpos(id_marca){

    var strModelos = "<option value=''>Selecione o modelo</option>";

    var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

    db.transaction(function(tx){

        tx.executeSql("SELECT * FROM modelos WHERE marca = '"+id_marca+"' order by nome ASC;", [], function(tx, res) {
                //alert("Quantidade Resultados: " + res.rows.length);
                for (var i = 0;i<res.rows.length;i++){

                	var nomeModelo = res.rows.item(i).nome.split(" ")[0]+" "+res.rows.item(i).nome.split(" ")[1];

                    strModelos += "<option value='"+nomeModelo+"'>"+nomeModelo+"</option>";
                    // console.log("Linha "+i+": "+res.rows.item(i).nome);
                }

                $("#modelo_interesse").html(strModelos);

            });

    }, function(tx, erro){
        alert("Error processing SQL: "+erro);
    }, function(){});

}



$("#valor_ano_modelo").change(function(){

	if($(this).val() != ""){
		$("#bloco_valor_fipe").css("display","block");
		$("#valor_fipe").html("R$ "+retornaMoedaBrasil($(this).val()));  
	}

	$("#ano_modelo").val($("#valor_ano_modelo :selected").text());

});


function getAnoModelos(id_modelo){

    var strAnoModelos = "<option value=''>Selecione o ano/versão</option>";

   var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

    db.transaction(function(tx){

        tx.executeSql("SELECT * FROM ano_modelos WHERE modelo = '"+id_modelo+"' order by nome ASC;", [], function(tx, res) {

            for (var i = 0;i<res.rows.length;i++){
                strAnoModelos += "<option value='"+res.rows.item(i).valor+"'>"+res.rows.item(i).nome+"</option>";
            }

            $("#valor_ano_modelo").html(strAnoModelos);

        });

    }, function(tx, erro){
        alert("Error processing SQL: "+erro);
    }, function(){});

}


function getModelos(id_marca){

    var strModelos = "<option value=''>Selecione o modelo</option>";

    var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

    db.transaction(function(tx){

        tx.executeSql("SELECT * FROM modelos WHERE marca = '"+id_marca+"' order by nome ASC;", [], function(tx, res) {
                //alert("Quantidade Resultados: " + res.rows.length);
                for (var i = 0;i<res.rows.length;i++){
                    strModelos += "<option value='"+res.rows.item(i).id+"'>"+res.rows.item(i).nome+"</option>";
                    // console.log("Linha "+i+": "+res.rows.item(i).nome);
                }

                $("#modelos").html(strModelos);

            });

    }, function(tx, erro){
        alert("Error processing SQL: "+erro);
    }, function(){
        //$('#modelos').select2();
    });

}


function getMarcas(){

    var strMarcas = "<option value=''>Selecione a marca</option>"+
    "<option value='13'>CITROËN</option>"+
    "<option value='21'>FIAT</option>"+
    "<option value='22'>FORD</option>"+
    "<option value='23'>GM - CHEVROLET</option>"+
    "<option value='25'>HONDA</option>"+
    "<option value='26'>HYUNDAI</option>"+
    "<option value='41'>MITSUBISHI</option>"+
    "<option value='44'>PEUGEOT</option>"+
    "<option value='48'>RENAULT</option>"+
    "<option value='56'>TOYOTA</option>"+
    "<option value='59'>VW - VOLKSWAGEN</option>"+
    "<option class='divider'></option>";

    var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

    db.transaction(function(tx){

        tx.executeSql("SELECT * FROM marcas order by nome ASC;", [], function(tx, res) {
                    //alert("Quantidade Resultados: " + res.rows.length);
                    
                    for (var i = 0;i<res.rows.length;i++){
                     strMarcas += "<option value='"+res.rows.item(i).id+"'>"+res.rows.item(i).nome+"</option>";
                       // console.log("Linha "+i+": "+res.rows.item(i).nome);
                   }

                   $(".marcas").html(strMarcas);

               });

    }, function(tx, erro){


    }, function(){});

}


$(".moeda").keyup(function(){

	$(this).attr("maxlength","18");

	var valor = $(this).val();

	valor = valor.replace(",","").replace(",","").replace(",","").replace(",","").replace(".","").replace(".","").replace(".","").replace(".","");

	if(isNaN(valor)){
		$(this).val($(this).val().substring(0,$(this).val().length-1));
		return false;
	}

	$(this).val("");

	var cents = valor.substring(valor.length-2, valor.length);
	var centena = valor.substring(valor.length-2, valor.length-5);
	var milhar = valor.substring(valor.length-5, valor.length-8);
	var milhoes = valor.substring(valor.length-8, valor.length-11);
	var bilhoes = valor.substring(valor.length-11, valor.length-14);

	//console.log(bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents);

	if(bilhoes != ""){
		$(this).val(bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents);
	}else{
		if(milhoes != ""){
			$(this).val(milhoes+"."+milhar+"."+centena+","+cents);
		}else{
			if(milhar != ""){
				$(this).val(milhar+"."+centena+","+cents);
			}else{
				if(centena != ""){
					$(this).val(centena+","+cents);
				}else{
					if(cents != ""){
						$(this).val(cents);
					}
				}
			}
		}
	}

});


function retornaMoedaBrasil(valor){

    if(!valor){
        return false;
    }

    if(valor.substring(valor.length-2, valor.length-1) == "."){
        valor = valor+"0";
    }else{
        valor = valor+"00";
    }

    valor = valor.replace(",","").replace(",","").replace(",","").replace(",","").replace(".","").replace(".","").replace(".","").replace(".","");

	if(isNaN(valor)){
		return false;
	}

	var cents = valor.substring(valor.length-2, valor.length);
	var centena = valor.substring(valor.length-2, valor.length-5);
	var milhar = valor.substring(valor.length-5, valor.length-8);
	var milhoes = valor.substring(valor.length-8, valor.length-11);
	var bilhoes = valor.substring(valor.length-11, valor.length-14);

	//console.log(bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents);

	if(bilhoes != ""){
		return bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents;
	}else{
		if(milhoes != ""){
			return milhoes+"."+milhar+"."+centena+","+cents;
		}else{
			if(milhar != ""){
				return milhar+"."+centena+","+cents;
			}else{
				if(centena != ""){
					return centena+","+cents;
				}else{
					if(cents != ""){
						return cents;
					}
				}
			}
		}
	}
}


function addMoeda(){

    $(".moeda").keyup(function(){

        $(this).attr("maxlength","18");

        var valor = $(this).val();

        valor = valor.replace(",","").replace(",","").replace(",","").replace(",","").replace(".","").replace(".","").replace(".","").replace(".","");

        if(isNaN(valor)){
            $(this).val($(this).val().substring(0,$(this).val().length-1));
            return false;
        }

        $(this).val("");

        var cents = valor.substring(valor.length-2, valor.length);
        var centena = valor.substring(valor.length-2, valor.length-5);
        var milhar = valor.substring(valor.length-5, valor.length-8);
        var milhoes = valor.substring(valor.length-8, valor.length-11);
        var bilhoes = valor.substring(valor.length-11, valor.length-14);

        //console.log(bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents);

        if(bilhoes != ""){
            $(this).val(bilhoes+"."+milhoes+"."+milhar+"."+centena+","+cents);
        }else{
            if(milhoes != ""){
                $(this).val(milhoes+"."+milhar+"."+centena+","+cents);
            }else{
                if(milhar != ""){
                    $(this).val(milhar+"."+centena+","+cents);
                }else{
                    if(centena != ""){
                        $(this).val(centena+","+cents);
                    }else{
                        if(cents != ""){
                            $(this).val(cents);
                        }
                    }
                }
            }
        }

    });
    
}


$(document).ready(function(){
	//getMarcas();
});