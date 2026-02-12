var idEdt = "";

function validaSubmitEdt(){

    var erro = false;
    var scroll = 0;

    idEdt = $("#id").val();

    if(idEdt == ""){
        erro = true;
        alert("Houve um erro inesperado, não há o 'id' da avaliação para realizar a edição!");
        return false;
    }

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
    if($("#finalidade").val() == "0" || $("#finalidade").val() == ""){
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


    $("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 20px; color:#fff;"><div style="margin-top:200px;">Enviando para o servidor...</div></div>');

        var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
        db.transaction(editaAvaliacao, errorEdtAvaliacoes, sucessoEdtAvaliacoes);

    }

}


function editaAvaliacao(tx){

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

        if($(this).val()){
            strDespesas += "despesa_descricao_"+countDespesas+" = '"+$(this).prev().prev().prev().prev().val()+"', ";
            strDespesas += "despesa_valor_"+countDespesas+" = '"+$(this).val()+"', ";
            countDespesas++
        }

    });

    //strDespesas = strDespesas.substring(0, strDespesas.length-2);

////////////////|FIM OBTEM DESPESAS ADICIONAIS|///////////////////////////////////

    strSet = "id_empresa = '"+$("#id_empresa").val()+"', "+
              "id_usuario = '"+$("#id_usuario").val()+"', "+
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

   // console.log(strSet);

    tx.executeSql("UPDATE avaliacoes SET "+strSet+" WHERE id = "+idEdt+";" );
   
}

function errorEdtAvaliacoes(tx, err) {
    alert("Error processing SQL: "+err);
}

function sucessoEdtAvaliacoes() {
   // $("#div_aguarde").remove();
    //alert("Avaliação salva com sucesso!");
    upload("form_edt");
}


$(document).ready(function(){

    var storage = window.localStorage;
    
    //$("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 30px; color:#fff;"><div style="margin-top:200px;">Aguarde...</div></div>');

    $("#id").val(getParamUrl('id'));

    if($("#id").val() != ""){

        var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);

        db.transaction(function(tx){

            tx.executeSql("SELECT av.*, mo.nome AS nome_modelo "+
                          "FROM avaliacoes as av "+
                          "LEFT JOIN modelos AS mo ON av.modelo = mo.id "+
                          "WHERE av.id="+getParamUrl("id")+";",
            [], 
            function(tx, res){

                $("#nome").val(res.rows.item(0).nome);
                $("#telefone").val(res.rows.item(0).telefone);
                $("#pretencao").val(res.rows.item(0).pretencao);
                $("#marcas").val(res.rows.item(0).marca);
                $("#modelos").html("<option value='"+res.rows.item(0).modelo+"'>"+res.rows.item(0).nome_modelo+"</option>");
                $("#valor_ano_modelo").html("<option value='"+res.rows.item(0).valor_ano_modelo+"'>"+res.rows.item(0).ano_modelo+"</option>");
                $("#ano_modelo").val(res.rows.item(0).ano_modelo);
                valorAnoModelo();
                validaCalcula();
                $("#ano_fabricacao").val(res.rows.item(0).ano_fabricacao);
                $("#portas").val(res.rows.item(0).portas);
                $("#cor").val(res.rows.item(0).cor);
                $("#km").val(res.rows.item(0).km);
                $("#data").val(res.rows.item(0).data);
                $("#solicitar_liberacao").val(res.rows.item(0).solicitar_liberacao);
                $("#placa").val(res.rows.item(0).placa);

 ///////////////////////////RESOLVE FOTOS//////////////////////////////////
                $("#doc_carro").attr("src", res.rows.item(0).doc_carro);
                insereCanvas("doc_carro", res.rows.item(0).doc_carro);

                $("#foto_1").attr("src", res.rows.item(0).foto_1);
                insereCanvas("foto_1", res.rows.item(0).foto_1);

                $("#foto_2").attr("src", res.rows.item(0).foto_2);
                insereCanvas("foto_2", res.rows.item(0).foto_2);

                $("#foto_3").attr("src", res.rows.item(0).foto_3);
                insereCanvas("foto_3", res.rows.item(0).foto_3);
                
                $("#foto_4").attr("src", res.rows.item(0).foto_4);
                insereCanvas("foto_4", res.rows.item(0).foto_4);

                //////////////////////TO BASE64///////////////////
                // imagem.onload = function() {
                //     c.drawImage(imagem,0,0);
                //  tela = tela.toDataURL('image/jpeg', 1.0);
                //     console.log(tela);
                // }
                //////////////////////////////////////////////////

 ///////////////////////////FIM RESOLVE FOTOS////////////////////////////////

                if(res.rows.item(0).opcionais.split("_") != ""){
                    var arrOpt = res.rows.item(0).opcionais.split("_");
                    for(x in arrOpt){
                        $("#"+arrOpt[x]).attr("checked","checked");
                    }
                }

                externoCarro("data_lateral_dianteira_esquerda", res.rows.item(0).data_lateral_dianteira_esquerda);
                externoCarro("data_lateral_traseira_esquerda", res.rows.item(0).data_lateral_traseira_esquerda);
                externoCarro("data_porta_traseira_esquerda", res.rows.item(0).data_porta_traseira_esquerda);
                externoCarro("data_porta_dianteira_esquerda", res.rows.item(0).data_porta_dianteira_esquerda);
                externoCarro("data_vidro_dianteiro_esquerdo", res.rows.item(0).data_vidro_dianteiro_esquerdo);
                externoCarro("data_vidro_traseiro_esquerdo", res.rows.item(0).data_vidro_traseiro_esquerdo);
                externoCarro("data_roda_traseira_esquerda", res.rows.item(0).data_roda_traseira_esquerda);
                externoCarro("data_roda_dianteira_esquerda", res.rows.item(0).data_roda_dianteira_esquerda);
                
                externoCarro("data_porta_traseira_direita", res.rows.item(0).data_porta_traseira_direita);
                externoCarro("data_porta_dianteira_direita", res.rows.item(0).data_porta_dianteira_direita);
                externoCarro("data_vidro_dianteiro_direito", res.rows.item(0).data_vidro_dianteiro_direito);
                externoCarro("data_vidro_traseiro_direito", res.rows.item(0).data_vidro_traseiro_direito);
                externoCarro("data_lateral_traseira_direita", res.rows.item(0).data_lateral_traseira_direita);
                externoCarro("data_lateral_dianteira_direita", res.rows.item(0).data_lateral_dianteira_direita);
                externoCarro("data_roda_traseira_direita", res.rows.item(0).data_roda_traseira_direita);
                externoCarro("data_roda_dianteira_direita", res.rows.item(0).data_roda_dianteira_direita);

                externoCarro("data_vidro_dianteiro", res.rows.item(0).data_vidro_dianteiro);
                externoCarro("data_capo", res.rows.item(0).data_capo);
                externoCarro("data_parachoque_dianteiro", res.rows.item(0).data_parachoque_dianteiro);
                externoCarro("data_farol_direito", res.rows.item(0).data_farol_direito);
                externoCarro("data_farol_esquerdo", res.rows.item(0).data_farol_esquerdo);
                externoCarro("data_retrovisor_direito", res.rows.item(0).data_retrovisor_direito);
                externoCarro("data_retrovisor_esquerdo", res.rows.item(0).data_retrovisor_esquerdo);
                
                externoCarro("data_parachoque_traseiro", res.rows.item(0).data_parachoque_traseiro);
                externoCarro("data_tampa_porta-malas", res.rows.item(0).data_tampa_porta);
                externoCarro("data_vidro_traseiro", res.rows.item(0).data_vidro_traseiro);
                externoCarro("data_lanterna_esquerda_traseira", res.rows.item(0).data_lanterna_esquerda_traseira);
                externoCarro("data_lanterna_direita_traseira", res.rows.item(0).data_lanterna_direita_traseira);

                $("#teto").val(res.rows.item(0).teto);
                $("#obs_teto").val(res.rows.item(0).obs_teto);

                $("input[name='acidente'][value='"+res.rows.item(0).acidente+"']").click();
                $("#obs_acidente").val(res.rows.item(0).obs_acidente);

                $("input[name='motor'][value='"+res.rows.item(0).motor+"']").click();
                $("#obs_motor").val(res.rows.item(0).obs_motor);

                $("input[name='caixa_direcao'][value='"+res.rows.item(0).caixa_direcao+"']").click();
                $("#obs_caixa_direcao").val(res.rows.item(0).obs_caixa_direcao);

                $("input[name='tipo_cambio'][value='"+res.rows.item(0).tipo_cambio+"']").click();
                $("input[name='cambio'][value='"+res.rows.item(0).cambio+"']").click();
                $("#obs_cambio").val(res.rows.item(0).obs_cambio);

                $("input[name='suspensao'][value='"+res.rows.item(0).suspensao+"']").click();
                $("#obs_suspensao").val(res.rows.item(0).obs_suspensao);

                $("input[name='embreagem'][value='"+res.rows.item(0).embreagem+"']").click();
                $("#obs_embreagem").val(res.rows.item(0).obs_embreagem);

                $("input[name='freios'][value='"+res.rows.item(0).freios+"']").click();
                $("#obs_freios").val(res.rows.item(0).obs_freios);

                $("input[name='escapamento'][value='"+res.rows.item(0).escapamento+"']").click();
                $("#obs_escapamento").val(res.rows.item(0).obs_escapamento);

                $("input[name='eletrica'][value='"+res.rows.item(0).eletrica+"']").click();
                $("#obs_eletrica").val(res.rows.item(0).obs_eletrica);

                $("input[name='luz_painel'][value='"+res.rows.item(0).luz_painel+"']").click();
                $("#obs_luz_painel").val(res.rows.item(0).obs_luz_painel);

                $("input[name='ar_condicionado'][value='"+res.rows.item(0).ar_condicionado+"']").click();
                $("#obs_ar_condicionado").val(res.rows.item(0).obs_ar_condicionado);

                $("input[name='qtd_pneus'][value='"+res.rows.item(0).qtd_pneus+"']").click();

                if(res.rows.item(0).qtd_pneus != "" && res.rows.item(0).qtd_pneus != 0){
                    $("#div_medidas_pneus").css("display", "block");
                }
                
                $("#largura").val(res.rows.item(0).largura);
                $("#perfil").val(res.rows.item(0).perfil);
                $("#aro").val(res.rows.item(0).aro);
                $("#obs_pneus").val(res.rows.item(0).obs_pneus);

                $("input[name='tapecaria'][value='"+res.rows.item(0).tapecaria+"']").click();
                $("#faixas").val(res.rows.item(0).faixas);
                $("#obs_tapecaria").val(res.rows.item(0).obs_tapecaria);

                $("input[name='manutencao'][value='"+res.rows.item(0).manutencao+"']").click();
                $("#obs_manutencao").val(res.rows.item(0).obs_manutencao);

                $("input[name='mercado'][value='"+res.rows.item(0).mercado+"']").click();
                $("#obs_mercado").val(res.rows.item(0).obs_mercado);
                
                $("#finalidade").val(res.rows.item(0).finalidade);
                
                $("#obs_final").val(res.rows.item(0).obs_final);
                
                $("#total").val(res.rows.item(0).total);

                if(res.rows.item(0).marca_interesse != ""){
                    $("#veiculo_troca_interesse").css("display", "block");
                    $("#marca_interesse").val(res.rows.item(0).marca_interesse);
                    $("#modelo_interesse").html("<option value='"+res.rows.item(0).modelo_interesse+"'>"+res.rows.item(0).modelo_interesse+"</option>");
                    $("#ano_interesse").val(res.rows.item(0).ano_interesse);
                }

                //console.log(res.rows.item(0).id_empresa);

                if(res.rows.item(0).id_empresa == ""){
                    $("#id_empresa").val(storage.getItem('id_empresa'));
                }else{
                    $("#id_empresa").val(res.rows.item(0).id_empresa);
                }

                if(res.rows.item(0).id_usuario == ""){
                    $("#id_usuario").val(storage.getItem('id_usuario'));
                }else{
                    $("#id_usuario").val(res.rows.item(0).id_usuario);
                }

                $("#id_upload").val(res.rows.item(0).id_upload);

                $("#login").val(storage.getItem('login'));
                $("#senha").val(storage.getItem('senha'));
                
                $("#telefone_avaliador").val(storage.getItem('telefone_avaliador'));

                 var despesas = '';

                if(res.rows.item(0).despesa_descricao_1 != ""){

                    despesas += '<div class="form-group" id="despesa_1">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_1" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_descricao_1+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_1" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_valor_1+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                    '<button type="button" onclick="$(this).parent().remove();" class="btn btn-danger">Excluir</button>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(res.rows.item(0).despesa_descricao_2 != ""){

                    despesas += '<div class="form-group" id="despesa_2">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_2" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_descricao_2+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_2" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_valor_2+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                    '<button type="button" onclick="$(this).parent().remove();" class="btn btn-danger">Excluir</button>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(res.rows.item(0).despesa_descricao_3 != ""){

                    despesas += '<div class="form-group" id="despesa_3">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_3" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_descricao_3+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_3" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_valor_3+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                    '<button type="button" onclick="$(this).parent().remove();" class="btn btn-danger">Excluir</button>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(res.rows.item(0).despesa_descricao_4 != ""){

                    despesas += '<div class="form-group" id="despesa_4">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_4" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_descricao_4+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_4" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_valor_4+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                    '<button type="button" onclick="$(this).parent().remove();" class="btn btn-danger">Excluir</button>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(res.rows.item(0).despesa_descricao_5 != ""){

                    despesas += '<div class="form-group" id="despesa_5">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_5" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_descricao_5+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_5" aria-describedby="nomeHelp" value="'+res.rows.item(0).despesa_valor_5+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                    '<button type="button" onclick="$(this).parent().remove();" class="btn btn-danger">Excluir</button>'+
                                '</div>';

                    despesaAdicional++;

                }


                $("#outras_despesas").html(despesas);

                validaCalcula();

                $("body").animate({scrollTop:0},1);

    	    });

        }, function(tx, erro){
            $("#div_aguarde").remove();
            alert("Error processing SQL: "+erro);
        }, function(){
            $("#div_aguarde").remove();
        });


    }else{

        $("#id_empresa").val(storage.getItem('id_empresa'));
        $("#id_usuario").val(storage.getItem('id_usuario'));

        $("#login").val(storage.getItem('login'));
        $("#senha").val(storage.getItem('senha'));
        
        $("#div_aguarde").remove();

    }

});

function externoCarro(indice, valor){

    indice = indice.replace("data_", "");

    if(valor != ""){

        var arr = indice.split("_");

        if(arr[0] == "porta" || arr[0] == "lateral" || arr[0] == "capo" || arr[0] == "parachoque" || arr[0] == "tampa"){

            $("#"+indice).css("opacity", "1");

            if(valor == 1){
                $("#"+indice).next().html("M");
            }
            if(valor == 2){
                $("#"+indice).next().html("PC");
            }
            if(valor == 3){
                $("#"+indice).next().html("P");
            }
            if(valor == 4){
                $("#"+indice).next().html("F");
            }
            if(valor == 5){
                $("#"+indice).next().html("T");
            }

        }else{

            if(valor == 1){
                $("#"+indice).next().html("R");
            }
            if(valor == 2){
                $("#"+indice).next().html("T");
            }

        }

        $("#"+indice).css("opacity", "1");        
        //$("#"+indice).next().html(valor);
        $("#data_"+indice).val(valor);

    }

}

function valorAnoModelo(){

    if($("#valor_ano_modelo").val() != ""){
        $("#bloco_valor_fipe").css("display","block");
        $("#valor_fipe").html("R$ "+retornaMoedaBrasil($("#valor_ano_modelo").val()));
    }else{
        $("#valor_fipe").html("R$ 000000,00");
    }

    $("#ano_modelo").val($("#valor_ano_modelo :selected").text());

    finalidade();

}

function insereCanvas(id, valor){
               
    var tela = document.getElementById(id+"_canvas");
    var c = tela.getContext("2d");

    var imagem = new Image();

    imagem.src = valor;

    imagem.onload = function() {

        var width = document.getElementById(id).naturalWidth;
        var height = document.getElementById(id).naturalHeight;
        tela.width = width;
        tela.height = height;
    
        c.drawImage(imagem,0,0);

    }

}