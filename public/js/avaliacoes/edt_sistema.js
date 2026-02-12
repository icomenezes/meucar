$(document).ready(function(){

    var storage = window.localStorage;

    if(getParamUrl('id') != ""){

        $.ajax({

            url: "https://sistemameucar.com.br/apps/busca-avaliacao/app_avaliacoes/true",
            type: "POST",
            async: true,
            data: ({"id": getParamUrl('id')}),
            dataType: "TEXT",
            success: function(resp){

                var valor = JSON.parse(resp);

                //console.log(valor);
                $("#nome").val(valor.nome);
                $("#telefone").val(valor.telefone);
                $("#pretencao").val(valor.pretencao);
                $("#marcas").val(valor.marca);
                $("#modelos").html("<option value='"+valor.modelo+"'>"+valor.nome_modelo+"</option>");
                $("#valor_ano_modelo").html("<option value='"+valor.valor_ano_modelo+"'>"+valor.ano_modelo+"</option>");
                $("#ano_modelo").val(valor.ano_modelo);
                valorAnoModelo();
                validaCalcula();
                $("#ano_fabricacao").val(valor.ano_fabricacao);
                $("#portas").val(valor.portas);
                $("#cor").val(valor.cor);
                $("#km").val(valor.km);
                $("#data").val(valor.data);
                $("#solicitar_liberacao").val(valor.solicitar_liberacao);
                $("#placa").val(valor.placa);

 ///////////////////////////RESOLVE FOTOS//////////////////////////////////
                if(valor.doc_carro){
                    $("#doc_carro").attr("src", "https://sistemameucar.com.br/"+valor.doc_carro);
                }
                if(valor.foto_1){
                    $("#foto_1").attr("src", "https://sistemameucar.com.br/"+valor.foto_1);
                }
                if(valor.foto_2){
                    $("#foto_2").attr("src", "https://sistemameucar.com.br/"+valor.foto_2);
                }
                if(valor.foto_3){
                    $("#foto_3").attr("src", "https://sistemameucar.com.br/"+valor.foto_3);
                }
                if(valor.foto_4){
                    $("#foto_4").attr("src", "https://sistemameucar.com.br/"+valor.foto_4);
                }

 ///////////////////////////FIM RESOLVE FOTOS////////////////////////////////

                //console.log(valor.opcionais);
                if(valor.opcionais.split("_") != ""){
                    var arrOpt = valor.opcionais.split("_");
                    for(x in arrOpt){
                        $("#"+arrOpt[x]).attr("checked","checked");
                    }
                }

                externoCarro("data_lateral_dianteira_esquerda", parseInt(valor.data_lateral_dianteira_esquerda));
                externoCarro("data_lateral_traseira_esquerda", parseInt(valor.data_lateral_traseira_esquerda));
                externoCarro("data_porta_traseira_esquerda", parseInt(valor.data_porta_traseira_esquerda));
                externoCarro("data_porta_dianteira_esquerda", parseInt(valor.data_porta_dianteira_esquerda));
                externoCarro("data_vidro_dianteiro_esquerdo", parseInt(valor.data_vidro_dianteiro_esquerdo));
                externoCarro("data_vidro_traseiro_esquerdo", parseInt(valor.data_vidro_traseiro_esquerdo));
                externoCarro("data_roda_traseira_esquerda", parseInt(valor.data_roda_traseira_esquerda));
                externoCarro("data_roda_dianteira_esquerda", parseInt(valor.data_roda_dianteira_esquerda));
                
                externoCarro("data_porta_traseira_direita", parseInt(valor.data_porta_traseira_direita));
                externoCarro("data_porta_dianteira_direita", parseInt(valor.data_porta_dianteira_direita));
                externoCarro("data_vidro_dianteiro_direito", parseInt(valor.data_vidro_dianteiro_direito));
                externoCarro("data_vidro_traseiro_direito", parseInt(valor.data_vidro_traseiro_direito));
                externoCarro("data_lateral_traseira_direita", parseInt(valor.data_lateral_traseira_direita));
                externoCarro("data_lateral_dianteira_direita", parseInt(valor.data_lateral_dianteira_direita));
                externoCarro("data_roda_traseira_direita", parseInt(valor.data_roda_traseira_direita));
                externoCarro("data_roda_dianteira_direita", parseInt(valor.data_roda_dianteira_direita));

                externoCarro("data_vidro_dianteiro", parseInt(valor.data_vidro_dianteiro));
                externoCarro("data_capo", parseInt(valor.data_capo));
                externoCarro("data_parachoque_dianteiro", parseInt(valor.data_parachoque_dianteiro));
                externoCarro("data_farol_direito", parseInt(valor.data_farol_direito));
                externoCarro("data_farol_esquerdo", parseInt(valor.data_farol_esquerdo));
                externoCarro("data_retrovisor_direito", parseInt(valor.data_retrovisor_direito));
                externoCarro("data_retrovisor_esquerdo", parseInt(valor.data_retrovisor_esquerdo));
                
                externoCarro("data_parachoque_traseiro", parseInt(valor.data_parachoque_traseiro));
                externoCarro("data_tampa_porta-malas", parseInt(valor.data_tampa_porta));
                externoCarro("data_vidro_traseiro", parseInt(valor.data_vidro_traseiro));
                externoCarro("data_lanterna_esquerda_traseira", parseInt(valor.data_lanterna_esquerda_traseira));
                externoCarro("data_lanterna_direita_traseira", parseInt(valor.data_lanterna_direita_traseira));

                $("#teto").val(valor.teto);
                $("#obs_teto").val(valor.obs_teto);

                $("input[name='acidente'][value='"+valor.acidente+"']").click();
                $("#obs_acidente").val(valor.obs_acidente);
                $("input[name='acidente']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.acidente, "acidente");

                $("input[name='motor'][value='"+valor.motor+"']").click();
                $("#obs_motor").val(valor.obs_motor);
                $("input[name='motor']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.motor, "motor");

                $("input[name='caixa_direcao'][value='"+valor.caixa_direcao+"']").click();
                $("#obs_caixa_direcao").val(valor.obs_caixa_direcao);
                $("input[name='caixa_direcao']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.caixa_direcao, "caixa_direcao");

                $("input[name='tipo_cambio'][value='"+valor.tipo_cambio+"']").click();
                $("input[name='cambio'][value='"+valor.cambio+"']").click();
                $("#obs_cambio").val(valor.obs_cambio);
                $("input[name='tipo_cambio']").parent().parent().prop("disabled", true);
                $("input[name='cambio']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.cambio, "tipo_cambio");

                $("input[name='suspensao'][value='"+valor.suspensao+"']").click();
                $("#obs_suspensao").val(valor.obs_suspensao);
                $("input[name='suspensao']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.suspensao, "suspensao");

                $("input[name='embreagem'][value='"+valor.embreagem+"']").click();
                $("#obs_embreagem").val(valor.obs_embreagem);
                $("input[name='embreagem']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.embreagem, "embreagem");

                $("input[name='freios'][value='"+valor.freios+"']").click();
                $("#obs_freios").val(valor.obs_freios);
                $("input[name='freios']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.freios, "freios");

                $("input[name='escapamento'][value='"+valor.escapamento+"']").click();
                $("#obs_escapamento").val(valor.obs_escapamento);
                $("input[name='escapamento']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.escapamento, "escapamento");

                $("input[name='eletrica'][value='"+valor.eletrica+"']").click();
                $("#obs_eletrica").val(valor.obs_eletrica);
                $("input[name='eletrica']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.eletrica, "eletrica");

                $("input[name='luz_painel'][value='"+valor.luz_painel+"']").click();
                $("#obs_luz_painel").val(valor.obs_luz_painel);
                $("input[name='luz_painel']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.luz_painel, "luz_painel");

                $("input[name='ar_condicionado'][value='"+valor.ar_condicionado+"']").click();
                $("#obs_ar_condicionado").val(valor.obs_ar_condicionado);
                $("input[name='ar_condicionado']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.ar_condicionado, "ar_condicionado");

                $("input[name='qtd_pneus'][value='"+valor.qtd_pneus+"']").click();
                $("input[name='qtd_pneus']").parent().parent().prop("disabled", true);


                if(valor.qtd_pneus != "" && valor.qtd_pneus != 0){
                    $("#div_medidas_pneus").css("display", "block");
                }
                
                $("#largura").val(valor.largura);
                $("#perfil").val(valor.perfil);
                $("#aro").val(valor.aro);
                $("#obs_pneus").val(valor.obs_pneus);

                $("input[name='tapecaria'][value='"+valor.tapecaria+"']").click();
                $("#faixas").val(valor.faixas);
                $("#obs_tapecaria").val(valor.obs_tapecaria);
                $("input[name='tapecaria']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.tapecaria, "tapecaria");

                $("input[name='manutencao'][value='"+valor.manutencao+"']").click();
                $("#obs_manutencao").val(valor.obs_manutencao);
                $("input[name='manutencao']").parent().parent().prop("disabled", true);
                botaoVermelho(valor.manutencao, "manutencao");

                $("input[name='mercado'][value='"+valor.mercado+"']").click();
                $("#obs_mercado").val(valor.obs_mercado);
                $("input[name='mercado']").parent().parent().prop("disabled", true);
                
                $("#finalidade").val(valor.finalidade);
                
                $("#obs_final").val(valor.obs_final);

                $("#observacoes_gerencia").val(valor.observacoes_gerencia);
                
                $("#total").html(valor.total);
                
                $("#label_total").html("R$ "+retornaMoedaBrasil(valor.total));

                if(valor.marca_interesse != ""){
                    $("#veiculo_troca_interesse").css("display", "block");
                    $("#marca_interesse").val(valor.marca_interesse);
                    $("#modelo_interesse").html("<option value='"+valor.modelo_interesse+"'>"+valor.modelo_interesse+"</option>");
                    $("#ano_interesse").val(valor.ano_interesse);
                }

                $("#id_empresa").val(valor.id_empresa);
                $("#id_usuario").val(valor.id_usuario);
                $("#id_upload").val(valor.id);

                $("#login").val(storage.getItem('login'));
                $("#senha").val(storage.getItem('senha'));

                $("#telefone_avaliador").val(valor.telefone_avaliador);

                var despesas = '';

                if(valor.despesa_descricao_1){

                    despesas += '<div class="form-group" id="despesa_1">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_1" disabled aria-describedby="nomeHelp" value="'+valor.despesa_descricao_1+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_1" disabled aria-describedby="nomeHelp" value="'+valor.despesa_valor_1+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(valor.despesa_descricao_2){

                    despesas += '<div class="form-group" id="despesa_2">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_2" disabled aria-describedby="nomeHelp" value="'+valor.despesa_descricao_2+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_2" disabled aria-describedby="nomeHelp" value="'+valor.despesa_valor_2+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(valor.despesa_descricao_3){

                    despesas += '<div class="form-group" id="despesa_3">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_3" disabled aria-describedby="nomeHelp" value="'+valor.despesa_descricao_3+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_3" disabled aria-describedby="nomeHelp" value="'+valor.despesa_valor_3+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(valor.despesa_descricao_4){

                    despesas += '<div class="form-group" id="despesa_4">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_4" disabled aria-describedby="nomeHelp" value="'+valor.despesa_descricao_4+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_4" disabled aria-describedby="nomeHelp" value="'+valor.despesa_valor_4+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                '</div>';

                    despesaAdicional++;

                }

                if(valor.despesa_descricao_5){

                    despesas += '<div class="form-group" id="despesa_5">'+
                                    '<hr class="my-4">Descrição'+
                                    '<br>'+
                                    '<input type="text" class="form-control btn-lg" id="descricao" name="despesa_descricao_5" disabled aria-describedby="nomeHelp" value="'+valor.despesa_descricao_5+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small>'+
                                    '<br>Valor<br>'+
                                    '<input type="telefone" class="form-control btn-lg moeda" id="valor" name="despesa_valor_5" disabled aria-describedby="nomeHelp" value="'+valor.despesa_valor_5+'">'+
                                    '<small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small>'+
                                    '<br>'+
                                '</div>';

                    despesaAdicional++;

                }


                $("#outras_despesas").html(despesas);

                validaCalcula();
                
                $("body").animate({scrollTop:0},1000);

                //$("#button_sub_total").css("display", "none");

                $("#div_aguarde").remove();

            }

        });

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



/*function externoCarro(indice, valor){
    
    indice = indice.replace("data_", "");

    if(valor != ""){
        $("#"+indice).css("opacity", "1");
        $("#"+indice).next().html(valor);
        $("#data_"+indice).val(valor);
    }
}*/

function valorAnoModelo(){

    if($("#valor_ano_modelo").val() != ""){
        $("#bloco_valor_fipe").css("display","block");
        $("#valor_fipe").html("R$ "+retornaMoedaBrasil($("#valor_ano_modelo").val()));
        $("#fipe_resumo").html("R$ "+retornaMoedaBrasil($("#valor_ano_modelo").val()));
    }else{
        $("#valor_fipe").html("R$ 000000,00");
    }

    $("#ano_modelo").val($("#valor_ano_modelo :selected").text());

    finalidade();

}

function autoSalvar(){
}

function previewFoto(id){

    if($("#"+id).attr("src") != "/images/avaliacoes/doc_carro.jpg" && $("#"+id).attr("src") != "/images/avaliacoes/foto.jpg" && $("#"+id).attr("src") != ""){

        window.open("https://sistemameucar.com.br/avaliacoes/foto/path/"+$("#"+id).attr("src").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-").replace("/","-"), '640px', '800px');

        ///$("#preview").css("display", "block");
        ///$("#preview > img").attr("src",  $("#"+id).attr("src"));
       // $("#preview > img").css("margin-left","-"+$("#preview > img").width()+"px");

    }

}


function botaoVermelho(valor, name){



    if(valor == "Sim"){

       // $("input[name='eletrica'][value='"+valor+"']").parent().parent().parent().css("background-color","#ff6565");
        $("input[name='"+name+"'][value='"+valor+"']").parent().css("background-color","#FF0033");
        $("input[name='"+name+"'][value='"+valor+"']").parent().css("color","#FFFFFF");
        $("input[name='"+name+"'][value='"+valor+"']").parent().css("border-color","#CC0000");
        
    }

}


function anviaAprovacao(val){

    if(navigator.connection.type == "none"){
        alert("Não há conexão com a internet");
    }else{

        $.ajax({

            url: "https://sistemameucar.com.br/apps/aprova-avaliacao-sistema/app_avaliacoes/true",
            type: "POST",
            async: true,
            data: ({"login": storage.getItem('login'), "senha": storage.getItem('senha'), "situacao": val, "id": $("#id_upload").val(), "observacoes_gerencia": $("#observacoes_gerencia").val()}),
            dataType: "TEXT",
            success: function(resp){

                if(resp == "Erro"){
                    alert("Houve um erro inesperado, por favor tente novamente.\nCaso o erro persista informe o administrador do sistema.");
                    location.replace('index.html');
                }else{

                    if(resp == "Sucesso"){

                        var telefone = $("#telefone_avaliador").val().replace("(","").replace(")","").replace(" ","").replace("-","");

                        if(val == 1){
                            $("#link_whatsapp").attr("href","http://web.whatsapp.com://send?text=Avaliação aprovada, por favor acesse o app Avalia Car.&phone=+55"+telefone);
                        }else{
                            if(val == -1){
                                $("#link_whatsapp").attr("href","http://web.whatsapp.com://send?text=Avaliação reprovada, por favor acesse o app Avalia Car.&phone=+55"+telefone); 
                            }
                        }

                        $("#sucesso_avaliacao").css("display","block");

                    }

                }


            }

        });

    }

}
