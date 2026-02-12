//GERAL

function manageTabs(destino, atual){

	destino = "#" + destino;
	atual = "#" + atual;

	var estado = $(destino).css("display");

	if(estado == 'none'){

		$(atual).addClass('nav-1-active');
		$(destino).attr("style","display:inline");

	}else{

		$(atual).removeClass('nav-1-active');
		$(destino).attr("style","display:none");

	}

}

function float2moeda(num) {

	x = 0;

	if(num<0) {
	
		num = Math.abs(num);
		x = 1;
		
	}
   
	if(isNaN(num)){
	 
		num = "0";
		
	}
	
	cents = Math.floor((num*100+0.5)%100);
	num = Math.floor((num*100+0.5)/100).toString();

	if(cents < 10){
	 
		cents = "0" + cents;
	
	}
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++){
	
		num = num.substring(0,num.length-(4*i+3)) + '.' +num.substring(num.length-(4*i+3));
	}
	
	ret = num + ',' + cents;
	
	if (x == 1){
	 
		ret = ' - ' + ret;
	
	}	
     
	return ret;

}

function moeda2float(moeda){
	
	if(moeda != "" && !!moeda){

		moeda = moeda.replace('.',"");
		moeda = moeda.replace('.',"");
		moeda = moeda.replace('.',"");
		moeda = moeda.replace(",",".");
		moeda = parseFloat(moeda);
	
	}else{
		
		moeda = 0.00;
		
	}
	
	if(isNaN(moeda)){
		
		moeda = 0.00;
		
	}
	
	return moeda;

}

function validaNumeros(idCampo){

	var campo = $("#"+idCampo);
	var stringValida = "0123456789"
	var stringInicial = campo.val();
	var stringFinal = "";

	for(x in stringInicial){

		if(stringValida.indexOf(stringInicial[x]) != -1){

			stringFinal += stringInicial[x];

		}

	}

	campo.val(stringFinal);

}

function validaMoedas(idCampo){

	var campo = $("#"+idCampo);
	var stringValida = "0123456789."
	var stringInicial = campo.val();
	var stringFinal = "";

	for(x in stringInicial){

		if(stringValida.indexOf(stringInicial[x]) != -1){

			stringFinal += stringInicial[x];

		}

	}

	campo.val(stringFinal);

}

function validaMoedaBR(idCampo){

	var campo = $("#"+idCampo);
	var stringValida = "0123456789,-"
	var stringInicial = campo.val();
	var stringFinal = "";

	for(x in stringInicial){

		if(stringValida.indexOf(stringInicial[x]) != -1){

			stringFinal += stringInicial[x];

		}

	}

	campo.val(stringFinal);

}

function validaAjuste(idCampo){

	var campo = $("#"+idCampo);
	var stringValida = "0123456789.-"
	var stringInicial = campo.val();
	var stringFinal = "";

	for(x in stringInicial){

		if(stringValida.indexOf(stringInicial[x]) != -1){

			stringFinal += stringInicial[x];

		}

	}

	campo.val(stringFinal);

}
