
var idUploadSistema = "";
var storage = window.localStorage;
var fd;

function upload(idForm){

    $("body").prepend('<div id="div_aguarde" style="z-index: 10; text-align:center; opacity:0.8; background-color:#000;width:100%; height:1000px; position:fixed; font-size: 20px; color:#fff;"><div style="margin-top:200px;">Enviando para o servidor...</div></div>');


    var form = document.getElementById(idForm);
    fd = new FormData(form);

    //////////////////////Resolve Fotos////////////////////
    if($("#doc_carro").attr("src") != "img/doc_carro.PNG" && $("#doc_carro").attr("src") != "img/foto.jpg"){
        resolveFotos("doc_carro");
    }

    if($("#foto_1").attr("src") != "img/foto.jpg"){
        resolveFotos("foto_1");
    }

    if($("#foto_2").attr("src") != "img/foto.jpg"){
        resolveFotos("foto_2");
    }

    if($("#foto_3").attr("src") != "img/foto.jpg"){
        resolveFotos("foto_3");
    }

    if($("#foto_4").attr("src") != "img/foto.jpg"){
        resolveFotos("foto_4");
    }
    /////////////////////////////////////////////////////////

   // /*
    $.ajax({

        url: "https://sistemameucar.com.br/apps/add-avaliacao",
        type: "POST",
        data: fd,
        async: true,
        processData: false,
        contentType: false,
        success: function(resp){

            if(resp == "Erro"){
                alert("Houve um erro inesperado, por favor tente novamente.\nCaso o erro persista informe o administrador do sistema.");
                location.replace('index.html');
            }else{

                if(resp == "Sucesso"){


                    $("#link_whatsapp_upload").attr("href", "whatsapp://send?text=Avaliação enviada por: "+storage.getItem('nome')+", por favor acesse o app Avalia Car.&phone=+55"+storage.getItem('celular_superior').replace("(","").replace(")","").replace(" ","").replace("-",""));
                    $("#sucesso_upload").css("display","block");
                    $("#div_aguarde").remove();
                    //location.replace('list.html');

                }else{

                    idUploadSistema = resp;
                    var db = window.openDatabase("avaliacar_db", "1.0", "avaliacar", 200000);
                    db.transaction(editaIdUpload, errorIdUpload, sucessoIdUpload);
                    
                }

            }
        }

    });
   // */

}

function editaIdUpload(tx){

    if($("#id").val() && idUploadSistema != ""){
        tx.executeSql("UPDATE avaliacoes SET id_upload = '"+idUploadSistema+"' WHERE id = "+$("#id").val()+";" );
    }
   
}

function errorIdUpload(tx, err) {
    $("#div_aguarde").remove();
    alert("Houve um erro inesperado, por favor tente novamente.\nCaso o erro persista informe o administrador do sistema. "+err);
    location.replace('index.html');
}

function sucessoIdUpload(){
    $("#div_aguarde").remove();
    $("#link_whatsapp_upload").attr("href", "whatsapp://send?text=Avaliação enviada por: "+storage.getItem('nome')+", por favor acesse o app Avalia Car.&phone=+55"+storage.getItem('celular_superior').replace("(","").replace(")","").replace(" ","").replace("-",""));
    $("#sucesso_upload").css("display","block");
    //location.replace('list.html');
}

function b64toBlob(b64Data, contentType, sliceSize) {
        
    contentType = contentType || '';
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {

        var slice = byteCharacters.slice(offset, offset + sliceSize);
        var byteNumbers = new Array(slice.length);

        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    
    }

    var blob = new Blob(byteArrays, {type: contentType});

    return blob;

}


function resolveFotos(id){

    var tela = document.getElementById(id+"_canvas");

    tela = tela.toDataURL('image/jpeg', 1.0);

    var block = tela.split(";");
    var contentType = block[0].split(":")[1];
    var realData = block[1].split(",")[1];
    var blob = b64toBlob(realData, contentType);
    fd.append(id.toString(), blob, id.toString());

}

