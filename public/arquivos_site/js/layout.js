
$(document).ready(function(){

		width = $(window).width();
		$(".principal").css("margin-left",(width - $(".principal").width())/2+"px");
		$(".top_centro").css("margin-left",(width - $(".top_centro").width())/2+"px");
		$(".menu_centro").css("margin-left",(width - $(".menu_centro").width())/2+"px");
		$(".usuario_centro").css("margin-left",(width - $(".usuario_centro").width())/2+"px");
		$(".rodape_centro").css("margin-left",(width - $(".usuario_centro").width())/2+"px");


	$(window).scroll(function(){

		posicaoTop = $(this).scrollTop();
		
		
		
		if(posicaoTop > 65){

			//$( ".logo_img_pequeno" ).fadeIn("slow");
			
			
		
		}else{
			
			//$( ".logo_img_pequeno" ).fadeOut("slow");
		
		}
		
		if(posicaoTop > 150){
			
			$("#vendas-top").fadeOut("fast");
			$("#links-top").fadeIn("slow");

		}else{

			$("#vendas-top").fadeIn("slow");
			$("#links-top").fadeOut("slow");
		
		}

	});
	

});