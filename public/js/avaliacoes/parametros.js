var storage = window.localStorage;
var solicitarLiberacao = false;
var arrParametros;
var outrosResumo;
var gastosResumo;



$("input").keyup(function(){validaCalcula();});
$("input").focus(function(){autoSalvar();});
$("input").focusout(function(){autoSalvar();});
$("select").change(function(){validaCalcula(); autoSalvar();});
$("textarea").change(function(){validaCalcula(); autoSalvar();});
$("input[type='radio']").focus(function(){validaCalcula(); autoSalvar();});
$("input[type='checkbox']").click(function(){validaCalcula(); autoSalvar();});
$("#btn_salvar_reparo").click(function(){validaCalcula(); autoSalvar();});

function validaCalcula(){

    outrosResumo = 0;
    gastosResumo = 0;

    solicitarLiberacao = false;

    $("#text_liberacao").html("");
    $("#button_sub_total").removeClass("btn-danger");
    $("#button_sub_total").addClass("btn-success");
   
    if($("#valor_fipe").html() != ""){
        
        var FIPE = parseFloat($("#valor_fipe").html().replace("R$ ","").replace(",00","").replace(".",""));
        
        verificaParamCor();
        verificaKm();
        verificaKmPorAno();
        verificaOpcionais();

        var subTotal = (FIPE-parseFloat(verificaExternoLataria()));

        gastosResumo += parseFloat(verificaExternoLataria());

        if($("input[name='acidente']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['vestigio_acidente']);

            outrosResumo += parseFloat(arrParametros['vestigio_acidente']);

            if(arrParametros['checkbox_vestigio_acidente'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

////////////////////////////////INICIO MECÂNICA//////////////////////////////////////////
        if($("input[name='motor']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['motor']);

            gastosResumo += parseFloat(arrParametros['motor']);

            if(arrParametros['checkbox_motor'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }


        if($("input[name='caixa_direcao']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['caixa_direcao']);

            gastosResumo += parseFloat(arrParametros['caixa_direcao']);

            if(arrParametros['checkbox_caixa_direcao'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

        if($("input[name='tipo_cambio']:checked").val() == "manual"){

            if($("input[name='cambio']:checked").val() == "Sim"){

                subTotal = subTotal - parseFloat(arrParametros['cambio_manual']);

                gastosResumo += parseFloat(arrParametros['cambio_manual']);

                if(arrParametros['checkbox_cambio_manual'] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

        }

        if($("input[name='tipo_cambio']:checked").val() == "automatico"){

            if($("input[name='cambio']:checked").val() == "Sim"){

                subTotal = subTotal - parseFloat(arrParametros['cambio_automatico']);

                gastosResumo += parseFloat(arrParametros['cambio_automatico']);

                if(arrParametros['checkbox_cambio_automatico'] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

        }

        if($("input[name='suspensao']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['suspensao']);

            gastosResumo += parseFloat(arrParametros['suspensao']);

            if(arrParametros['checkbox_suspensao'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

        if($("input[name='embreagem']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['embreagem']);

            gastosResumo += parseFloat(arrParametros['embreagem']);

            if(arrParametros['checkbox_embreagem'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

        if($("input[name='freios']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['freios']);

            gastosResumo += parseFloat(arrParametros['freios']);

            if(arrParametros['checkbox_freios'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

        if($("input[name='escapamento']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['escapamento']);

            gastosResumo += parseFloat(arrParametros['escapamento']);

            if(arrParametros['checkbox_escapamento'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }
///////////////////////////////FIM MECÂNICA/////////////////////////////////////////

/////////////////////////////INICIO ELÉTRICA////////////////////////////////////////
        if($("input[name='eletrica']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['eletrica']);

            gastosResumo += parseFloat(arrParametros['eletrica']);

            if(arrParametros['checkbox_eletrica'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }

        if($("input[name='luz_painel']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['luz_painel']);

            gastosResumo += parseFloat(arrParametros['luz_painel']);

            if(arrParametros['checkbox_luz_painel'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }


        if($("input[name='ar_condicionado']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['ar_condicionado']);

            gastosResumo += parseFloat(arrParametros['ar_condicionado']);

            if(arrParametros['checkbox_ar_condicionado'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }
///////////////////////////////////////FIM ELÉTRICA////////////////////////////////////////////

///////////////////////////////////////INICIO PNEUS////////////////////////////////////////////
        if($("input[name='qtd_pneus']:checked").val() && $("#aro").val() != ""){

            var qtd = parseInt($("input[name='qtd_pneus']:checked").val());

            subTotal = subTotal - (qtd*parseFloat(arrParametros[$("#aro").val()]));

            gastosResumo += (qtd*parseFloat(arrParametros[$("#aro").val()]));

            if(arrParametros['checkbox_'+$("#aro").val()] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }
///////////////////////////////////////FIM PNEUS////////////////////////////////////////////

///////////////////////////////////INICIO TAPEÇARIA/////////////////////////////////////////
        if(($("input[name='tapecaria']:checked").val() == "faixa_couro" || $("input[name='tapecaria']:checked").val() == "faixa_tecido") && $("#faixas").val() != ""){

            if($("#faixas").val() == "-1"){

                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;

            }else{

                if($("#faixas").val()){

                    var qtd = parseInt($("#faixas").val());

                    subTotal = subTotal - (qtd*parseFloat(arrParametros[$("input[name='tapecaria']:checked").val()]));

                    gastosResumo += (qtd*parseFloat(arrParametros[$("input[name='tapecaria']:checked").val()]));

                    if(arrParametros['checkbox_'+$("input[name='tapecaria']:checked").val()] == 1){
                        $("#text_liberacao").html("Solicitar Liberação");
                        $("#button_sub_total").removeClass("btn-success");
                        $("#button_sub_total").addClass("btn-danger");
                        solicitarLiberacao = true;
                    }

                }

            }
            
        }
///////////////////////////////////FIM TAPEÇARIA/////////////////////////////////////////
       // console.log(subTotal);

//////////////////////////////////INICIO MANUTENÇÃO/////////////////////////////////////////

        if($("input[name='manutencao']:checked").val() == "Sim"){

            subTotal = subTotal - parseFloat(arrParametros['manutencao']);

            outrosResumo += parseFloat(arrParametros['manutencao']);

            if(arrParametros['checkbox_manutencao'] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }
//////////////////////////////////FIM MANUTENÇÃO/////////////////////////////////////////

//////////////////////////////////INICIO MERCADO/////////////////////////////////////////
        if($("input[name='mercado']:checked").val() == "mercado_normal" || $("input[name='mercado']:checked").val() == "mercado_ruim" || $("input[name='mercado']:checked").val() == "mercado_pessimo" || $("input[name='mercado']:checked").val() == "mercado_nao_sei"){

            subTotal = subTotal - parseFloat(arrParametros[$("input[name='mercado']:checked").val()]);

            outrosResumo += parseFloat(arrParametros[$("input[name='mercado']:checked").val()]);

            if(arrParametros['checkbox_'+$("input[name='mercado']:checked").val()] == 1){
                $("#text_liberacao").html("Solicitar Liberação");
                $("#button_sub_total").removeClass("btn-success");
                $("#button_sub_total").addClass("btn-danger");
                solicitarLiberacao = true;
            }

        }
///////////////////////////////////////FIM MERCADO///////////////////////////////////////

////////////////////////////////////INICIO FINALIDADE////////////////////////////////////
        
        if($("#finalidade").val() != ""){
            subTotal = subTotal - parseFloat(arrParametros[$("#finalidade").val()]);
            $("#margem_resumo").html("R$ "+parseFloat(arrParametros[$("#finalidade").val()])+",00");
        }

/////////////////////////////////////FIM FINALIDADE//////////////////////////////////////

///////////////////////////////INICIO DESPESAS ADICIONAIS////////////////////////////////
        $("#outras_despesas input[type='telefone']").each(function(){
            if($(this).val() != ""){
                if($(this).prev().prev().prev().prev().val() != ""){
                    subTotal = subTotal - parseFloat($(this).val().replace(".","").replace(".","").replace(",","."));

                    gastosResumo +=  parseFloat($(this).val().replace(".","").replace(".","").replace(",","."));
                }
            }
        });
////////////////////////////////FIM DESPESAS ADICIONAIS//////////////////////////////////

        if(subTotal){
            $("#button_sub_total").css("display","block");
            $("#sub_total").html("Salvar: R$ "+mascaraMoeda(subTotal+"00"));
            $("#total").val(subTotal);
            //console.log("\n"+mascaraMoeda(subTotal+"00"));
        }

        if(solicitarLiberacao){
            $("#sub_total").css("display", "none");
            $("#liberacao").html("Liberar: ");
        }else{
            $("#sub_total").css("display", "block");
        }
        
        $("#outros_resumo").html("R$ "+outrosResumo+",00");
        $("#gastos_resumo").html("R$ "+gastosResumo+",00");

    }

}

function verificaParamCor(){
    if($("#cor").val() != ""){
        if(arrParametros['cores_estranhas'].indexOf($("#cor").val().toLowerCase()) != -1){
            $("#text_liberacao").html("Solicitar Liberação");
            $("#button_sub_total").removeClass("btn-success");
            $("#button_sub_total").addClass("btn-danger");
            solicitarLiberacao = true;
        }
    }
}

function verificaKm(){
    if(arrParametros['limite_kilometragem'] < $("#km").val()){
        $("#text_liberacao").html("Solicitar Liberação");
        $("#button_sub_total").removeClass("btn-success");
        $("#button_sub_total").addClass("btn-danger");
        solicitarLiberacao = true;
    }
}

function verificaKmPorAno(){
    var ano = parseInt(new Date().getFullYear());
    var arr = $("#ano_modelo").val().split(" ");
    var totalAno = ano-parseInt(arr[0]);

    if(parseInt($("#km").val()) / totalAno > parseInt(arrParametros['kilometragem_ano'])){
        $("#text_liberacao").html("Solicitar Liberação");
        $("#button_sub_total").removeClass("btn-success");
        $("#button_sub_total").addClass("btn-danger");
        solicitarLiberacao = true;
    }
}

function verificaOpcionais(){

    $("#table_opcionais input[type='checkbox']").each(function(){
        if($(this).is(":checked")){
            var arr = arrParametros['opcionais'].split("_");
            for(x in arr){
                if(parseInt($(this).attr("id")) == parseInt(arr[x])){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }
            }
        }
    });

}


function verificaExternoLataria(){

    valorTotal = 0;

    $("#externo_lataria input[type='hidden']").each(function(){

        if($(this).val() != ""){

            var lataria =  new Array();

            lataria[1] = "martelinho";
            lataria[2] = "pincelar";
            lataria[3] = "pintar";
            lataria[4] = "funilaria";
            lataria[5] = "trocar_pecas";

            var arr = $(this).attr("id").split("_");

            if(arr[1] == "porta" || arr[1] == "lateral" || arr[1] == "capo" || arr[1] == "parachoque" || arr[1] == "tampa"){

                valorTotal += parseFloat(arrParametros[lataria[$(this).val()]]);

                if(arrParametros["checkbox_"+lataria[$(this).val()]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

            if(arr[1] == "roda"){

                valorTotal += parseFloat(arrParametros[arr[1]]);

                if(arrParametros["checkbox_"+arr[1]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

            if(arr[1] == "vidro"){

                valorTotal += parseFloat(arrParametros[arr[1]]);

                if(arrParametros["checkbox_"+arr[1]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

            if(arr[1] == "farol"){

                valorTotal += parseFloat(arrParametros[arr[1]]);

                if(arrParametros["checkbox_"+arr[1]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }

            }

            if(arr[1] == "retrovisor"){

                valorTotal += parseFloat(arrParametros[arr[1]]);

                if(arrParametros["checkbox_"+arr[1]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }
            }

            if(arr[1] == "lanterna"){

                valorTotal += parseFloat(arrParametros[arr[1]]);

                if(arrParametros["checkbox_"+arr[1]] == 1){
                    $("#text_liberacao").html("Solicitar Liberação");
                    $("#button_sub_total").removeClass("btn-success");
                    $("#button_sub_total").addClass("btn-danger");
                    solicitarLiberacao = true;
                }
            }

        }

    });

    return valorTotal;

}





function getParametros(){


    $.ajax({

        url: "https://sistemameucar.com.br/avaliacoes/busca-parametros",
        type: "POST",
        async: false,
        dataType: "TEXT",
        success: function(resp){

            jQuery.each(JSON.parse(resp), function(i, val){

                arrParametros = JSON.parse(resp);
                
            });


        }

    });

}


function mascaraMoeda(valor){

    valor = valor.replace(",","").replace(",","").replace(",","").replace(",","").replace(".","").replace(".","").replace(".","").replace(".","");

    if(isNaN(valor)){
      $(this).val($(this).val().substring(0,$(this).val().length-1));
      return false;
    }

    var cents = valor.substring(valor.length-2, valor.length);
    var centena = valor.substring(valor.length-2, valor.length-5);
    var milhar = valor.substring(valor.length-5, valor.length-8);
    var milhoes = valor.substring(valor.length-8, valor.length-11);
    var bilhoes = valor.substring(valor.length-11, valor.length-14);

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


$("#valor_ano_modelo").change(function(){

    var FIPE = parseFloat($("#valor_fipe").html().replace("R$ ","").replace(",00","").replace(".",""));

    if(FIPE < parseFloat(arrParametros['valor_fipe_veiculo_repasse'])){

        $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                              "<option value='margem_compra'>Compra</option>"+
                              "<option value='margem_repasse'>Repasse</option>"+
                              "<option value='margem_troca_estoque'>Troca</option>"
                              );

    }else{

        if(FIPE >= parseFloat(arrParametros['valor_fipe_veiculo_repasse']) && FIPE <= parseFloat(arrParametros['valor_tabela_limite'])){

            $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                              "<option value='margem_compra'>Compra</option>"+
                              "<option value='margem_troca_estoque'>Troca</option>"
                              );

        }else{

            if(FIPE > parseFloat(arrParametros['valor_tabela_limite'])){

                $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                                  "<option value='margem_compra_acima_limite'>Compra</option>"+
                                  "<option value='margem_troca_estoque_acima_limite'>Troca</option>"
                                  );

            }

        }

    }

});


function finalidade(){

    var FIPE = parseFloat($("#valor_fipe").html().replace("R$ ","").replace(",00","").replace(".",""));

    if(FIPE < parseFloat(arrParametros['valor_fipe_veiculo_repasse'])){

        $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                              "<option value='margem_compra'>Compra</option>"+
                              "<option value='margem_repasse'>Repasse</option>"+
                              "<option value='margem_troca_estoque'>Troca</option>"
                              );

    }else{

        if(FIPE >= parseFloat(arrParametros['valor_fipe_veiculo_repasse']) && FIPE <= parseFloat(arrParametros['valor_tabela_limite'])){

            $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                              "<option value='margem_compra'>Compra</option>"+
                              "<option value='margem_troca_estoque'>Troca</option>"
                              );

        }else{

            if(FIPE > parseFloat(arrParametros['valor_tabela_limite'])){

                $("#finalidade").html("<option value=''>Selecione a finalidade</option>"+
                                  "<option value='margem_compra_acima_limite'>Compra</option>"+
                                  "<option value='margem_troca_estoque_acima_limite'>Troca</option>"
                                  );

            }

        }

    }

}


function validaSubmit(){

    var erro = false;
    var scroll = 0;

    $("#nome").removeClass("is-invalid");
    $("#nome").addClass("is-valid");
    if($("#nome").val() == 0){
        $("#nome").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#nome").offset().top; }
        erro = true;
    }

    $("#telefone").removeClass("is-invalid");
    $("#telefone").addClass("is-valid");
    if($("#telefone").val() == 0){
        $("#telefone").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#telefone").offset().top; }
        erro = true;
    }

    $("#pretencao").removeClass("is-invalid");
    $("#pretencao").addClass("is-valid");
    if($("#pretencao").val() == 0){
        $("#pretencao").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#pretencao").offset().top; }
        erro = true;
    }

    $("#marcas").removeClass("is-invalid");
    $("#marcas").addClass("is-valid");
    if($("#marcas").val() == 0){
        $("#marcas").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#marcas").offset().top; }
        erro = true;
    }

    $("#modelos").removeClass("is-invalid");
    $("#modelos").addClass("is-valid");
    if($("#modelos").val() == 0){
        $("#modelos").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#marcas").offset().top; }
        erro = true;
    }

    $("#valor_ano_modelo").removeClass("is-invalid");
    $("#valor_ano_modelo").addClass("is-valid");
    if($("#valor_ano_modelo").val() == 0){
        $("#valor_ano_modelo").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#marcas").offset().top; }
        erro = true;
    }

    $("#ano_fabricacao").removeClass("is-invalid");
    $("#ano_fabricacao").addClass("is-valid");
    if($("#ano_fabricacao").val() == 0){
        $("#ano_fabricacao").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#ano_fabricacao").offset().top; }
        erro = true;
    }

    $("#portas").removeClass("is-invalid");
    $("#portas").addClass("is-valid");
    if($("#portas").val() == 0){
        $("#portas").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#portas").offset().top; }
        erro = true;
    }

    $("#cor").removeClass("is-invalid");
    $("#cor").addClass("is-valid");
    if($("#cor").val() == 0){
        $("#cor").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#cor").offset().top; }
        erro = true;
    }

    $("#km").removeClass("is-invalid");
    $("#km").addClass("is-valid");
    if($("#km").val() == 0){
        $("#km").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#km").offset().top; }
        erro = true;
    }

    $("#placa").removeClass("is-invalid");
    $("#placa").addClass("is-valid");
    if($("#placa").val() == 0){
        $("#placa").addClass("is-invalid");
        if(scroll == 0){ scroll = $("#placa").offset().top; }
        erro = true;
    }

    $("#doc_carroHelp").css("display","none");
     $("#doc_carro").parent().prev().removeClass("invalido");
    if($("#doc_carro").attr("src") == "img/doc_carro.PNG"){
        $("#doc_carroHelp").css("display","block");
        $("#doc_carro").parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("#doc_carro").offset().top-40; }
        erro = true;
    }

    $("input[name='acidente']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='acidente']:checked").val()){
        $("input[name='acidente']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='acidente']").offset().top-60; }
        erro = true;
    }

    $("input[name='motor']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='motor']:checked").val()){
        $("input[name='motor']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='motor']").offset().top-60; }
        erro = true;
    }

    $("input[name='caixa_direcao']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='caixa_direcao']:checked").val()){
        $("input[name='caixa_direcao']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='caixa_direcao']").offset().top-60; }
        erro = true;
    }

    $("input[name='tipo_cambio']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='tipo_cambio']:checked").val()){
        $("input[name='tipo_cambio']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='tipo_cambio']").offset().top-60; }
        erro = true;
    }

    $("input[name='cambio']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='cambio']:checked").val()){
        $("input[name='cambio']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='cambio']").offset().top-60; }
        erro = true;
    }

    $("input[name='suspensao']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='suspensao']:checked").val()){
        $("input[name='suspensao']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='suspensao']").offset().top-60; }
        erro = true;
    }

    $("input[name='embreagem']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='embreagem']:checked").val()){
        $("input[name='embreagem']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='embreagem']").offset().top-60; }
        erro = true;
    }

    $("input[name='freios']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='freios']:checked").val()){
        $("input[name='freios']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='freios']").offset().top-60; }
        erro = true;
    }

    $("input[name='escapamento']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='escapamento']:checked").val()){
        $("input[name='escapamento']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='escapamento']").offset().top-60; }
        erro = true;
    }

    $("input[name='eletrica']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='eletrica']:checked").val()){
        $("input[name='eletrica']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='eletrica']").offset().top-60; }
        erro = true;
    }

    $("input[name='luz_painel']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='luz_painel']:checked").val()){
        $("input[name='luz_painel']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='luz_painel']").offset().top-60; }
        erro = true;
    }

    $("input[name='ar_condicionado']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='ar_condicionado']:checked").val()){
        $("input[name='ar_condicionado']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='ar_condicionado']").offset().top-60; }
        erro = true;
    }

    $("input[name='qtd_pneus']:checked").parent().parent().parent().prev().removeClass("invalido");
    if(!$("input[name='qtd_pneus']:checked").val()){
        $("input[name='qtd_pneus']").parent().parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='qtd_pneus']").offset().top-60; }
        erro = true;
    }

    $("#largura").parent().parent().parent().parent().parent().parent().prev().removeClass("invalido");
    if($("input[name='qtd_pneus']:checked").val() != "" && $("input[name='qtd_pneus']:checked").val() != 0){

        $("#largura").parent().parent().parent().parent().parent().parent().prev().removeClass("invalido");
        if($("#largura").val() == ""){
            $("#largura").parent().parent().parent().parent().parent().parent().prev().addClass("invalido");
            if(scroll == 0){ scroll = $("#largura").offset().top-60; }
            erro = true;
        }
        if($("#perfil").val() == ""){
            $("#perfil").parent().parent().parent().parent().parent().parent().prev().addClass("invalido");
            if(scroll == 0){ scroll = $("#perfil").offset().top-60; }
            erro = true;
        }
        if($("#aro").val() == ""){
            $("#aro").parent().parent().parent().parent().parent().parent().prev().addClass("invalido");
            if(scroll == 0){ scroll = $("#aro").offset().top-60; }
            erro = true;
        }

    }

    $("input[name='tapecaria']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='tapecaria']:checked").val()){
        $("input[name='tapecaria']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='tapecaria']").offset().top-60; }
        erro = true;
    }

    $("input[name='manutencao']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='manutencao']:checked").val()){
        $("input[name='manutencao']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='manutencao']").offset().top-60; }
        erro = true;
    }

    $("input[name='mercado']:checked").parent().parent().prev().removeClass("invalido");
    if(!$("input[name='mercado']:checked").val()){
        $("input[name='mercado']").parent().parent().prev().addClass("invalido");
        if(scroll == 0){ scroll = $("input[name='mercado']").offset().top-60; }
        erro = true;
    }

    $("#finalidade").prev().removeClass("invalido");
    if($("#finalidade").val() == "0"){
        $("#finalidade").prev().addClass("invalido");
        if(scroll == 0){ scroll = $("#finalidade").offset().top-60; }
        erro = true;
    }

    $("#outras_despesas_label").removeClass("invalido");
    $("#outras_despesas input[type='telefone']").each(function(){
        
        $(this).removeClass("is-invalid");
        $(this).addClass("is-valid");
        if($(this).val() == ""){

            $("#outras_despesas_label").addClass("invalido");
            $(this).addClass("is-invalid");

            if(scroll == 0){ scroll = $(this).offset().top-60; }
            erro = true;
        }

        $(this).prev().prev().prev().prev().removeClass("is-invalid");
        $(this).prev().prev().prev().prev().addClass("is-valid");
        if($(this).prev().prev().prev().prev().val() == ""){

            $("#outras_despesas_label").addClass("invalido");
            $(this).prev().prev().prev().prev().addClass("is-invalid");

            if(scroll == 0){ scroll = $(this).prev().prev().prev().prev().offset().top-60; }
            erro = true;   
        }

    });

    if(erro){
        $("body").animate({scrollTop:scroll-80},1000);
    }else{
        var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
        db.transaction(insereAvaliacao, errorAvaliacoes, sucessoAvaliacoes);
    }

}


function insereAvaliacao(tx){

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

function errorAvaliacoes(tx, err) {
    alert("ErrorAdd processing SQL: "+err);
}

function sucessoAvaliacoes() {
    //$("#div_aguarde").remove();
    alert("Avaliação salva com sucesso!");
    location.replace('index.html');
}

$(document).ready(function(){
    getParametros();
    getMarcas();
});