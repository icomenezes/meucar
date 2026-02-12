var app = {

    initialize: function() {
        this.bindEvents();
    },
    
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
   
    onDeviceReady: function() {
        //console.log(navigator.camera);
    },
   
};

app.initialize();

var opacity = 1;
var despesaAdicional = 1;
var imageId = "";
var img = "";


function getImage(id){

    imageId = id;
	img = $("#"+imageId).attr("alt");

   	if($("#"+imageId).attr("src") == "img/doc_carro.PNG" || $("#"+imageId).attr("src") == "img/foto.jpg"|| $("#"+imageId).attr("src") == ""){

	    navigator.camera.getPicture(cameraSuccess, cameraError, {
	        quality:100,
	        targetWidth:640,
	        destinationType: Camera.DestinationType.FILE_URI,
	        saveToPhotoAlbum:false,
	        correctOrientation: true
	    });

	}else{

		$("#preview").css("display", "block");
	    $("#preview > img").attr("src",  $("#"+id).attr("src"));
	    $("#preview > img").css("margin-left","-"+$("#preview > img").width()+"px");

	    $("#excluir_foto").css("margin-left", "-"+$("#preview > img").width()/2+"px");

	    if($("#preview > img").height() < $("#preview > img").width()){
     		$("#excluir_foto").css("margin-top", $("#preview > img").height()-60+"px");
	    }else{
	    	//$("#excluir_foto").css("margin-top", $("#preview > img").width()-60+"px");
	    	//$("#excluir_foto").css("margin-top", +60+"px");
	    }


	}

}

$("#excluir_foto").click(function(){

	if(confirm("Deseja realmente excluir esta foto?")){
		$("#"+imageId).attr("src", img);
		$("#preview").css("display", "none");
		autoSalvar();
	}

});

function cameraSuccess(imageData){
	//console.log(imageData);
	
	$("#"+imageId).attr("src", imageData);
	$("#"+imageId).ready(function(){

		if($("#"+imageId).height() < $("#"+imageId).width()){
			insereCanvas(imageId, imageData);
			autoSalvar();
		}else{
			$("#"+imageId).attr("src", img);
			alert("Por favor, capture a imagem com o dispositivo na horizontal.");
		}

	});

}

function cameraError(message) {
	//$("#"+imageId).attr("src", img);
	//alert('Failed because: ' + message);
}

$(document).ready(function(){

	var widEsq = $("#carro_lateral_esquerda").width();
	var heiEsq = $("#carro_lateral_esquerda").height();

	$("#porta_dianteira_esquerda").css({"width":widEsq*0.313, "margin-left":widEsq*0.293, "margin-top":heiEsq*0.012, "opacity":0 });
	$("#porta_dianteira_esquerda").next().css({"margin-left":widEsq*0.4, "margin-top":heiEsq*0.4, "font-size":widEsq*0.08});

	$("#porta_traseira_esquerda").css({"width":widEsq*0.287, "margin-left":widEsq*0.562, "margin-top":heiEsq*0.0, "opacity":0 });
	$("#porta_traseira_esquerda").next().css({"margin-left":widEsq*0.65, "margin-top":heiEsq*0.4, "font-size":widEsq*0.08});

	$("#lateral_traseira_esquerda").css({"width":widEsq*0.211, "margin-left":widEsq*0.74, "margin-top":heiEsq*0.01, "opacity":0 });
	$("#lateral_traseira_esquerda").next().css({"margin-left":widEsq*0.87, "margin-top":heiEsq*0.2, "font-size":widEsq*0.08});

	$("#lateral_dianteira_esquerda").css({"width":widEsq*0.254, "margin-left":widEsq*0.051, "margin-top":heiEsq*0.266, "opacity":0 });
	$("#lateral_dianteira_esquerda").next().css({"margin-left":widEsq*0.18, "margin-top":heiEsq*0.3, "font-size":widEsq*0.08});

	$("#vidro_dianteiro_esquerdo").css({"width":widEsq*0.2551, "margin-left":widEsq*0.3319, "margin-top":heiEsq*0.0323, "opacity":0 });
	$("#vidro_dianteiro_esquerdo").next().css({"margin-left":widEsq*0.45, "margin-top":heiEsq*0.05, "font-size":widEsq*0.08});

	$("#vidro_traseiro_esquerdo").css({"width":widEsq*0.185, "margin-left":widEsq*0.607, "margin-top":heiEsq*0.035, "opacity":0 });
	$("#vidro_traseiro_esquerdo").next().css({"margin-left":widEsq*0.67, "margin-top":heiEsq*0.02, "font-size":widEsq*0.08});

	$("#roda_traseira_esquerda").css({"width":widEsq*0.1, "margin-left":widEsq*0.794, "margin-top":heiEsq*0.68, "opacity":0 });
	$("#roda_traseira_esquerda").next().css({"margin-left":widEsq*0.82, "margin-top":heiEsq*0.65, "font-size":widEsq*0.08});

	$("#roda_dianteira_esquerda").css({"width":widEsq*0.1, "margin-left":widEsq*0.121, "margin-top":heiEsq*0.68, "opacity":0 });
	$("#roda_dianteira_esquerda").next().css({"margin-left":widEsq*0.15, "margin-top":heiEsq*0.65, "font-size":widEsq*0.08});

	var widDir = $("#carro_lateral_direita").width();
	var heiDir = $("#carro_lateral_direita").height();

	$("#lateral_dianteira_direita").css({"width":widDir*0.254, "margin-left":widDir*0.696, "margin-top":heiDir*0.266, "opacity":0 });
	$("#lateral_dianteira_direita").next().css({"margin-left":widEsq*0.77, "margin-top":heiEsq*0.3, "font-size":widEsq*0.08});
	
	$("#porta_dianteira_direita").css({"width":widEsq*0.313, "margin-left":widEsq*0.394, "margin-top":heiEsq*0.012, "opacity":0 });
	$("#porta_dianteira_direita").next().css({"margin-left":widEsq*0.55, "margin-top":heiEsq*0.35, "font-size":widEsq*0.08});

	$("#porta_traseira_direita").css({"width":widEsq*0.287, "margin-left":widEsq*0.151, "margin-top":heiEsq*0.0, "opacity":0 });
	$("#porta_traseira_direita").next().css({"margin-left":widEsq*0.3, "margin-top":heiEsq*0.35, "font-size":widEsq*0.08});

	$("#lateral_traseira_direita").css({"width":widEsq*0.211, "margin-left":widEsq*0.05, "margin-top":heiEsq*0.01, "opacity":0 });
	$("#lateral_traseira_direita").next().css({"margin-left":widEsq*0.09, "margin-top":heiEsq*0.2, "font-size":widEsq*0.08});


	$("#vidro_dianteiro_direito").css({"width":widEsq*0.2551, "margin-left":widEsq*0.413, "margin-top":heiEsq*0.0323, "opacity":0 });
	$("#vidro_dianteiro_direito").next().css({"margin-left":widEsq*0.5, "margin-top":heiEsq*0.05, "font-size":widEsq*0.08});

	$("#vidro_traseiro_direito").css({"width":widEsq*0.185, "margin-left":widEsq*0.208, "margin-top":heiEsq*0.035, "opacity":0 });
	$("#vidro_traseiro_direito").next().css({"margin-left":widEsq*0.28, "margin-top":heiEsq*0.03, "font-size":widEsq*0.08});

	$("#roda_traseira_direita").css({"width":widEsq*0.1, "margin-left":widEsq*0.107, "margin-top":heiEsq*0.678, "opacity":0 });
	$("#roda_traseira_direita").next().css({"margin-left":widEsq*0.135, "margin-top":heiEsq*0.64, "font-size":widEsq*0.08});

	$("#roda_dianteira_direita").css({"width":widEsq*0.1, "margin-left":widEsq*0.7803, "margin-top":heiEsq*0.678, "opacity":0 });
	$("#roda_dianteira_direita").next().css({"margin-left":widEsq*0.81, "margin-top":heiEsq*0.64, "font-size":widEsq*0.08});

	var widFre = $("#carro_frente").width();
	var heiFre = $("#carro_frente").height();

	$("#vidro_dianteiro").css({"width":widFre*0.7, "margin-left":widFre*0.154, "margin-top":heiFre*0.012, "opacity":0 });
	$("#vidro_dianteiro").next().css({"margin-left":widEsq*0.48, "margin-top":heiFre*0.05, "font-size":widEsq*0.08});

	$("#capo").css({"width":widFre*0.818, "margin-left":widFre*0.086, "margin-top":heiFre*0.263, "opacity":0 });
	$("#capo").next().css({"margin-left":widEsq*0.48, "margin-top":heiFre*0.3, "font-size":widEsq*0.08});

	$("#parachoque_dianteiro").css({"width":widFre*0.956, "margin-left":widFre*0.018, "margin-top":heiFre*0.512, "opacity":0 });
	$("#parachoque_dianteiro").next().css({"margin-left":widEsq*0.48, "margin-top":heiFre*0.65, "font-size":widEsq*0.08});

	$("#farol_direito").css({"width":widFre*0.225, "margin-left":widFre*0.033, "margin-top":heiFre*0.453, "opacity":0 });
	$("#farol_direito").next().css({"margin-left":widEsq*0.1, "margin-top":heiFre*0.45, "font-size":widEsq*0.08});

	$("#farol_esquerdo").css({"width":widFre*0.233, "margin-left":widFre*0.724, "margin-top":heiFre*0.448, "opacity":0 });
	$("#farol_esquerdo").next().css({"margin-left":widEsq*0.84, "margin-top":heiFre*0.45, "font-size":widEsq*0.08});

	$("#retrovisor_direito").css({"width":widFre*0.125, "margin-left":widFre*0, "margin-top":heiFre*0.224, "opacity":0 });
	$("#retrovisor_direito").next().css({"margin-left":widEsq*0.04, "margin-top":heiFre*0.182, "font-size":widEsq*0.08});

	$("#retrovisor_esquerdo").css({"width":widFre*0.123, "margin-left":widFre*0.877, "margin-top":heiFre*0.224, "opacity":0 });
	$("#retrovisor_esquerdo").next().css({"margin-left":widEsq*0.92, "margin-top":heiFre*0.182, "font-size":widEsq*0.08});


	var widTra = $("#carro_traseira").width();
	var heiTra = $("#carro_traseira").height();

	$("#vidro_traseiro").css({"width":widTra*0.823, "margin-left":widTra*0.091, "margin-top":heiTra*0.024, "opacity":0 });
	$("#vidro_traseiro").next().css({"margin-left":widTra*0.47, "margin-top":heiTra*0.05, "font-size":widTra*0.08});

	$("#parachoque_traseiro").css({"width":widTra, "margin-left":widTra*0.0, "margin-top":heiTra*0.487, "opacity":0 });
	$("#parachoque_traseiro").next().css({"margin-left":widTra*0.47, "margin-top":heiTra*0.6, "font-size":widTra*0.08});

	$("#tampa_porta-malas").css({"width":widTra*0.834, "margin-left":widTra*0.085, "margin-top":heiTra*0.018, "opacity":0 });
	$("#tampa_porta-malas").next().css({"margin-left":widTra*0.53, "margin-top":heiTra*0.21, "font-size":widTra*0.08});

	$("#lanterna_esquerda_traseira").css({"width":widTra*0.116, "margin-left":widTra*0.006, "margin-top":heiTra*0.256, "opacity":0 });
	$("#lanterna_esquerda_traseira").next().css({"margin-left":widTra*0.04, "margin-top":heiTra*0.32, "font-size":widTra*0.08});

	$("#lanterna_direita_traseira").css({"width":widTra*0.112, "margin-left":widTra*0.872, "margin-top":heiTra*0.256, "opacity":0 });
	$("#lanterna_direita_traseira").next().css({"margin-left":widTra*0.91, "margin-top":heiTra*0.32, "font-size":widTra*0.08});

});

$(".defeito").click(function(){
	defeito(this);
});

$(".defeito").next().click(function(){
	defeito($(this).prev());
});


function defeito(este){

	var lataria =  new Array();
	var demais =  new Array();

	var title = $(este).attr("id").replace('_', ' ').replace('_', ' ').replace('_', ' ');

	lataria[1] = "1-Martelinho";
	lataria[2] = "2-Pincelar";
	lataria[3] = "3-Pintar";
	lataria[4] = "4-Funilaria";
	lataria[5] = "5-Trocar peças";

	demais[1] = "1-Reparar";
	demais[2] = "2-Trocar";

	$("#reparo_externo").css("display","block");
	$(".title_reparo").html(title.toUpperCase());

	var arr = $(este).attr("id").split("_");

	var data = $("#data_"+$(este).attr("id")).val();

	if(arr[0] == "porta" || arr[0] == "lateral" || arr[0] == "capo" || arr[0] == "parachoque" || arr[0] == "tampa"){
		
		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in lataria){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+lataria[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+lataria[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	if(arr[0] == "roda"){

		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in demais){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+demais[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+demais[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	if(arr[0] == "vidro"){

		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in demais){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+demais[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+demais[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	if(arr[0] == "farol"){

		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in demais){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+demais[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+demais[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	if(arr[0] == "retrovisor"){

		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in demais){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+demais[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+demais[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	if(arr[0] == "lanterna"){

		var option = "<option value=''>Nenhum reparo</option>";
		
		for(x in demais){

			if(x == data){
				option += "<option selected='selected' value='"+x+"'>"+demais[x]+"</option>";
			}else{
				option += "<option value='"+x+"'>"+demais[x]+"</option>";
			}

		}

		$("#reparo").html(option);

	}

	$("#reparo").attr("name",$(este).attr("id"));

}

$(document).ready(function(){

    var data = new Date();
    var ano = parseInt(data.getFullYear());
    var strAno = "<option value=''>Selecione o ano de fabricação</option>";
    var fim = ano-40;

    while(ano > fim){
        strAno += "<option value='"+ano+"'>"+ano+"</option>";
        ano--;
    }

    $(".ano_fabricacao").html(strAno);

    var storage = window.localStorage;
    if(storage.getItem('atualizacao') == 1){
        $(".atualizacao_disponivel").css("display", "inline");
    }


	// var tela = document.getElementById("doc_carro_canvas");
 //    var c = tela.getContext("2d");

 //    var imagem = new Image();

 //    imagem.src = "img/doc_carro.PNG";

 //    imagem.onload = function() {

 //    	c.drawImage(imagem,70,10,143,114);

 //    }


});


$("#btn_salvar_reparo").click(function(){

	if($("#reparo").val() != ""){

		$("#data_"+$("#reparo").attr("name")).val($("#reparo").val());
		$(".modal").css("display","none");
		$("#"+$("#reparo").attr("name")).css("opacity", 1);
		$("#"+$("#reparo").attr("name")).next().html($("#reparo").val());

	}else{

		$("#data_"+$("#reparo").attr("name")).val($("#reparo").val());
		$(".modal").css("display","none");
		$("#"+$("#reparo").attr("name")).css("opacity", 0);
		$("#"+$("#reparo").attr("name")).next().html("");
		
	}

});

$("button").click(function(){
	if($(this).attr("data-dismiss") == "modal"){
		$(".modal").css("display","none");
	}
});

function add(){

	$('button').click(function(){
		if($(this).html() == "Excluir"){
			//if(confirm("Deseja realmente excluir?")){
				$(this).parent().remove();
				validaCalcula();
			//}
		}
	});

	validaCalcula();
	$("input[type='telefone']").focusout(function(){
		validaCalcula();
	});

}

$("#adiciona_despesa").click(function(){

	if(parseInt(contaDespesa()) < 5){

		$("#outras_despesas").prepend('<div class="form-group" id="despesa_'+despesaAdicional+'><hr class="my-4">Descrição<br><input type="text" class="form-control btn-lg" id="descricao" aria-describedby="nomeHelp" value=""><small id="nomeHelp" class="form-text text-muted">Descreva a despesa.</small><br>Valor<br><input type="telefone" class="form-control btn-lg moeda" id="valor" aria-describedby="nomeHelp" value=""><small id="nomeHelp" class="form-text text-muted">Informe o valor da despesa.</small><br><button type="button" class="btn btn-danger">Excluir</button></div>');
		add();
		addMoeda();
		despesaAdicional++;

	}

});

$("#finalidade").change(function(){
	if($("#finalidade :selected").text() == "Troca"){
		$("#veiculo_troca_interesse").css("display","block");
	}else{
		$("#veiculo_troca_interesse").css("display","none");
	}
});

$("input[name='qtd_pneus']").change(function(){
	if($("input[name='qtd_pneus']:checked").val() != 0){
		$("#div_medidas_pneus").css("display", "block");
	}else{
		$("#div_medidas_pneus").css("display", "none");
		$("#largura").val("");
		$("#perfil").val("");
		$("#aro").val("");
	}
});

function getParamUrl(name){

	var arrUrl = window.location.href.split("/");

    if(arrUrl[parseInt(arrUrl.indexOf(name))+1]){
    	return arrUrl[parseInt(arrUrl.indexOf(name))+1];
    }else{
    	return "";
    }


}

function contaDespesa(){
	var contDespesa = 0;
	$("#outras_despesas [type='text']").each(function(){
		if($(this).attr("id") == "descricao"){
			contDespesa++;
		}
	});

	return contDespesa;

}


$("#reset_login").click(function(){
	resetLogin();
});


function resetLogin(){

	var storage = window.localStorage;
	storage.setItem('id_usuario', 0);
	storage.setItem('id_empresa', 0);
    storage.setItem('nome', 0);
    storage.setItem('login', 0);
    storage.setItem('senha', 0);
    storage.setItem('data_atualizacao', 0);
    storage.setItem('atualizacao', 0);
    storage.setItem('id_perfil', 0);
    storage.setItem('celular_superior', 0);
    storage.setItem('telefone_avaliador', 0);

    location.replace("index.html");

}


$("#close_foto").click(function(){
    $("#preview").css("display", "none");
});