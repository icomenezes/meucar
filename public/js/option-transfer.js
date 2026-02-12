function move(origem, destino){

	$("#" + origem +" option:selected").each(function(){
		
		$("#" + destino +" ").append("<option value='"+this.value+"'>"+this.text+"</option>");
		this.style.display = 'none';

	});

	$("#" + origem +"  option:selected").remove();

	ordena(destino);

}

function ordena(objeto){

	var texto = new Array();
	var arr = new Array();

	$("#" + objeto +"  option").each(function(){

		texto.push(this.value + "|" + this.text);

	});
	
	texto.sort();
	$("#" + objeto).html("");
	
	for(i=0; i<texto.length; i++){
		
		arr = texto[i].split("|");
		$("#" + objeto).append("<option value='"+arr[0]+"'>"+arr[1]+"</option>");
		
	}

}
