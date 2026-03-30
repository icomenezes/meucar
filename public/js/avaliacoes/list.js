var strAvaliacoesSistema = "";
var respTemp

$(document).ready(function(){

    $.ajax({

        url: "https://meucar.trsystem.com.br/apps/lista-avaliacoes/app_avaliacoes/true",
        type: "POST",
        async: true,
        dataType: "TEXT",
        success: function(resp){

            respTemp = resp;

            jQuery.each(JSON.parse(resp), function(i, val){

                var aprovada = '<span class="badge badge-warning">?</span>';

                if(val.aprovada == 1){
                    aprovada = '<span class="badge badge-success">Aprovada</span>';
                }else{
                    if(val.aprovada == -1){
                        aprovada = '<span class="badge badge-danger">Reprovada</span>';
                    }
                }

                var classe = "success";
                if(val.solicitar_liberacao == "1"){
                    classe = "danger";
                }

                strAvaliacoesSistema += '<tr style="cursor:pointer;" onclick="location.replace(\'avaliacoes/edt/id/'+val.id+'\')" class="table-'+classe+'"><td style="vertical-align:middle;">'+val.avaliador+'</td><td>'+val.nome+'</td><td>'+val.nome_modelo+'</td><td>'+val.data.split(" ")[0].split("-")[2]+"/"+val.data.split(" ")[0].split("-")[1]+"/"+val.data.split(" ")[0].split("-")[0]+'<br>'+aprovada+'</td></tr>';

            });

            $("#tbody_enviados").html(strAvaliacoesSistema);
            $("#div_aguarde").remove();

        }

    });

});




function popupBusca(tipo, local){

    var count = 0;
    var arr = new Array();
    var strOption = "<option value=''></option>";

    $("#tipo").val(tipo);
    $("#local").val(local);

    if(tipo == "avaliador" && local == "remoto"){

        $("#titulo_termo").html("Avaliador");

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var existe = false;
            for(x in arr){

                if(arr[x] == val.avaliador){
                    existe = true;
                    break;
                }

            }

            if(!existe){
                arr[count] = val.avaliador;
                strOption += "<option value='"+val.avaliador+"'>"+val.avaliador+"</option>";
                count++;
            }

        });

    }


    if(tipo == "cliente" && local == "remoto"){

        $("#titulo_termo").html("Cliente");

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var existe = false;
            for(x in arr){

                if(arr[x] == val.nome){
                    existe = true;
                    break;
                }

            }

            if(!existe){
                arr[count] = val.nome;
                strOption += "<option value='"+val.nome+"'>"+val.nome+"</option>";
                count++;
            }

        });

    }

    if(tipo == "veiculo" && local == "remoto"){

        $("#titulo_termo").html("Veículo");

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var existe = false;
            for(x in arr){

                if(arr[x] == val.nome_modelo){
                    existe = true;
                    break;
                }

            }

            if(!existe){
                arr[count] = val.nome_modelo;
                strOption += "<option value='"+val.nome_modelo+"'>"+val.nome_modelo+"</option>";
                count++;
            }

        });

    }


    $("#termo").html(strOption);

    $('#termo').select2();
    $("#popup_busca").css("display","block");

}




$("#termo").change(function(){

    $("#popup_busca").css("display","none");

    strAvaliacoesSistema = "";

    if($("#tipo").val() == "avaliador" && $("#local").val() == "remoto"){

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var aprovada = '<span class="badge badge-warning">?</span>';

            if(val.aprovada == 1){
                aprovada = '<span class="badge badge-success">Aprovada</span>';
            }else{
                if(val.aprovada == -1){
                    aprovada = '<span class="badge badge-danger">Reprovada</span>';
                }
            }

            var classe = "success";
            if(val.solicitar_liberacao == "1"){
                classe = "danger";
            }

            if(val.avaliador == $("#termo").val()){

                strAvaliacoesSistema += '<tr style="cursor:pointer;" onclick="location.replace(\'avaliacoes/edt/id/'+val.id+'\')" class="table-'+classe+'"><td style="vertical-align:middle;">'+val.avaliador+'</td><td>'+val.nome+'</td><td>'+val.nome_modelo+'</td><td>'+val.data.split(" ")[0].split("-")[2]+"/"+val.data.split(" ")[0].split("-")[1]+"/"+val.data.split(" ")[0].split("-")[0]+'<br>'+aprovada+'</td></tr>';
                
            }

        });

        $("#tbody_enviados").html(strAvaliacoesSistema);

    }

    if($("#tipo").val() == "cliente" && $("#local").val() == "remoto"){

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var aprovada = '<span class="badge badge-warning">?</span>';

            if(val.aprovada == 1){
                aprovada = '<span class="badge badge-success">Aprovada</span>';
            }else{
                if(val.aprovada == -1){
                    aprovada = '<span class="badge badge-danger">Reprovada</span>';
                }
            }

            var classe = "success";
            if(val.solicitar_liberacao == "1"){
                classe = "danger";
            }

            if(val.nome == $("#termo").val()){

                strAvaliacoesSistema += '<tr style="cursor:pointer;" onclick="location.replace(\'avaliacoes/edt/id/'+val.id+'\')" class="table-'+classe+'"><td style="vertical-align:middle;">'+val.avaliador+'</td><td>'+val.nome+'</td><td>'+val.nome_modelo+'</td><td>'+val.data.split(" ")[0].split("-")[2]+"/"+val.data.split(" ")[0].split("-")[1]+"/"+val.data.split(" ")[0].split("-")[0]+'<br>'+aprovada+'</td></tr>';
                
            }

        });

        $("#tbody_enviados").html(strAvaliacoesSistema);

    }

    if($("#tipo").val() == "veiculo" && $("#local").val() == "remoto"){

        jQuery.each(JSON.parse(respTemp), function(i, val){

            var aprovada = '<span class="badge badge-warning">?</span>';

            if(val.aprovada == 1){
                aprovada = '<span class="badge badge-success">Aprovada</span>';
            }else{
                if(val.aprovada == -1){
                    aprovada = '<span class="badge badge-danger">Reprovada</span>';
                }
            }

            var classe = "success";
            if(val.solicitar_liberacao == "1"){
                classe = "danger";
            }

            if(val.nome_modelo == $("#termo").val()){

                strAvaliacoesSistema += '<tr style="cursor:pointer;" onclick="location.replace(\'avaliacoes/edt/id/'+val.id+'\')" class="table-'+classe+'"><td style="vertical-align:middle;">'+val.avaliador+'</td><td>'+val.nome+'</td><td>'+val.nome_modelo+'</td><td>'+val.data.split(" ")[0].split("-")[2]+"/"+val.data.split(" ")[0].split("-")[1]+"/"+val.data.split(" ")[0].split("-")[0]+'<br>'+aprovada+'</td></tr>';
                
            }

        });

        $("#tbody_enviados").html(strAvaliacoesSistema);

    }

});
