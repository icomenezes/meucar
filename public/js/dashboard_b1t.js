function desenhaGraficoRelogio(titulo, meta, atual, projecao, deveria, normal){

	if(normal){

		maior = 0;

		maior = (meta > maior) ? meta : maior;
		maior = (atual > maior) ? atual : maior;
		maior = (projecao > maior) ? projecao : maior;
		maior = (deveria > maior) ? deveria : maior;

		var meter1 = new RGraph.Meter('relogio', 0, maior+(maior*15/100), deveria);

	}else{

		var meter1 = new RGraph.Meter('relogio', 0, 15, deveria);

	}

	var grad1 = meter1.context.createRadialGradient(meter1.canvas.width / 2,meter1.canvas.height - 25,0,meter1.canvas.width / 2,meter1.canvas.height - 25,200);
	grad1.addColorStop(0, 'green');
	grad1.addColorStop(1, 'white');

	var grad2 = meter1.context.createRadialGradient(meter1.canvas.width / 2, meter1.canvas.height - 25,0,meter1.canvas.width / 2, meter1.canvas.height - 25,200);
	grad2.addColorStop(0, 'yellow');
	grad2.addColorStop(1, 'white');
            
	var grad3 = meter1.context.createRadialGradient(meter1.canvas.width / 2, meter1.canvas.height - 25,0,meter1.canvas.width / 2, meter1.canvas.height - 25,200);
	grad3.addColorStop(0, 'red');
	grad3.addColorStop(1, 'white')

	meter1.Set('chart.labels.position', 'inside');
	meter1.Set('chart.title', titulo);
	meter1.Set('chart.title.vpos', 0.5);
	meter1.Set('chart.title.color', 'black');
	meter1.Set('chart.red.color', grad3);
	meter1.Set('chart.red.end', meta*80/100);
	meter1.Set('chart.yellow.color', grad2);
	meter1.Set('chart.yellow.end', meta);
	meter1.Set('chart.green.color', grad1);
	meter1.Set('chart.border', false);
	meter1.Set('chart.needle.linewidth', 15);
	meter1.Set('chart.needle.tail', true);
	meter1.Set('chart.tickmarks.big.num', 0);
	meter1.Set('chart.tickmarks.small.num', 0);
	meter1.Set('chart.segment.radius.start', 100);
	meter1.Set('chart.needle.radius', 80);
	meter1.Set('chart.needle.linewidth', 2);
	meter1.Set('chart.linewidth.segments', 5);
	meter1.Set('chart.strokestyle', 'white');
	meter1.Set('chart.text.size', 8);
	meter1.Set('chart.needle.extra', [[atual, 'black'], [projecao, 'grey']]);

	meter1.Draw();

}

function desenhaGraficoLinha(valores_real, valores_ano_anterior, valores_orcado,maior_valor){
       	  
	   //VALORES PARA TESTAR GRAFICO
	      
	   //valores_real = [12,32,34,27,32,45,45,57,'null','null','null','null',total_acumulado,total_orcado];
	   //valores_ano_anterior = [12,32,34,45,32,45,45,57,23,44,54,23,'null','null'];
	   //valores_orcado = [17,34,34,28,32,22,21,58,'null','null','null','null','null','null'];
		  
	
	titulo = "BSC - Grafico de Acompanhamento de Resultado";
	/*
	if(total_acumulado > total_orcado){
		
			maximo_valor = total_acumulado;
	   
	   }else{
		
			maximo_valor = total_orcado;
	   
	   }
*/
	var bar = new RGraph.Bar('line1', valores_real);
	
		
      // bar.Set('chart.title', 'A bar/line/pie combination (tooltips)');
		bar.Set('chart.ymax', maior_valor);
       bar.Set('chart.units.pre', '-');
       bar.Set('chart.gutter.left', 100);
       //bar.Set('chart.units', false);
       bar.Set('chart.scale.decimals', 0);
       //bar.Set('chart.noaxes', true);     
      
		//bar.Set('real_acumulado', total_acumulado);
		//bar.Set('total_orcado', total_orcado);
		
        bar.Set('chart.colors', ['#ccc', 'red', 'green', 'yellow']);
		//line.Set('chart.colors.sequential',true);
		//bar.Set('chart.scale.decimals', 2);
		bar.Set('chart.orcado',valores_orcado);

        bar.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']);

        //bar.Set('chart.tooltips', ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']);

        bar.Set('chart.background.grid.autofit', true);

        //bar.Set('chart.key', ['John', 'Jane', 'Fred']);

        bar.Set('chart.key.shadow', true);

       bar.Set('chart.key.shadow.offsetx', 0);

       bar.Set('chart.key.shadow.offsety', 0);

        bar.Set('chart.key.shadow.blur', 10);

        bar.Set('chart.key.shadow.color', 'rgba(128,128,128,0.5)');

        bar.Set('chart.key.background', 'white');

        bar.Set('chart.key.rounded', false);

        bar.Set('chart.background.grid.autofit', true);	

        bar.Set('chart.background.grid.autofit.numvlines', 14);

        bar.Set('chart.background.grid.autofit.numhlines', 10);

        bar.Set('chart.gutter.bottom', 30);

        bar.Set('chart.gutter.right', 5);
		
		
		
	
        // Define the line first so that it can be added to the bar chart
		
        var line = new RGraph.Line('line1', valores_ano_anterior, valores_orcado);
		
		//line.Set('chart.xaxispos', 'center');
		line.Set('chart.title', titulo);
        line.Set('chart.background.grid', false);

        line.Set('chart.linewidth', 3);

        line.Set('chart.colors', ['black', 'blue']);

        line.Set('chart.tickmarks', 'circle');
		
		
		// line.Set('chart.scale', false);
		
        //line.Set('chart.labels.ingraph', ['Janeiro',11,,'Fevereiro']);

        //line.Set('chart.highlight.fill', 'black');

        line.Set('chart.noaxes', true);

        //line.Set('chart.ylabels', true);

        line.Set('chart.tooltips', valores_ano_anterior, valores_orcado);
			
       //line.Set('chart.ymax', maior_valor);
	   //line.Set('chart.scale.specific', maior_valor);
        //line.Set('chart.animation.unfold.x', false);
		 //line.Set('chart.tickdirection', 1);
		bar.Set('chart.line', line);

       

        // Now use effects

       bar.Draw();		
		line.Draw();
	
	/*
	   var line3 = new RGraph.Line('line1', [-4,-7,-12,-7,-5,-4,-0]);
            line3.Set('chart.linewidth', 2);
            line3.Set('chart.hmargin', 5);
            line3.Set('chart.tickmarks', 'endcircle');
            line3.Set('chart.xaxispos', 'center');
            line3.Set('chart.shadow', true);
            line3.Set('chart.shadow.offsetx', 3);
            line3.Set('chart.shadow.offsety', 3);
            line3.Set('chart.colors', ['blue']);
            line3.Set('chart.background.grid.autofit.numhlines', 10);
            line3.Draw();
	*/

}

function desenhaGraficoRes(valores_real, valores_ano_anterior, valores_orcado, total_acumulado, total_orcado){
       
		var valores = [total_acumulado,total_orcado];
				
	   /*VALORES PARA TESTAR GRAFICO
	      
	   valores_real = [12,32,34,27,32,45,45,57,'null','null','null','null',total_acumulado,total_orcado];
	   valores_ano_anterior = [12,32,34,45,32,45,45,57,23,44,54,23,'null','null'];
	   valores_orcado = [17,34,34,28,32,22,21,58,'null','null','null','null','null','null'];
	*/	  
	
	titulo = "Real x Orcado";
	
	if(total_acumulado > total_orcado){
		
			maximo_valor = total_acumulado;
	   
	   }else{
		
			maximo_valor = total_orcado;
	   
	   }
	
	var bar = new RGraph.Bar('line2', valores);	
	
      // bar.Set('chart.title', 'A bar/line/pie combination (tooltips)');
		bar.Set('chart.title', titulo);
        bar.Set('chart.ymax', maximo_valor);
		bar.Set('real_acumulado', total_acumulado);
		bar.Set('total_orcado', total_orcado);
		bar.Set('res', true);
        bar.Set('chart.colors', ['#ccc', 'red', 'green', 'yellow']);
		bar.Set('chart.units.pre', '-');
		bar.Set('chart.gutter.right', 100);
		//line.Set('chart.colors.sequential',true);
		
		bar.Set('chart.orcado',valores_orcado);

        bar.Set('chart.labels', ['Real Ac.', 'Orcado Ac.']);
		//bar.Set('chart.labels.above',true);
		//bar.Set('chart.labels.above.size',true);

        //bar.Set('chart.tooltips', ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']);

        //bar.Set('chart.background.grid.autofit', true);

        //bar.Set('chart.key', ['John', 'Jane', 'Fred']);

        //bar.Set('chart.key.shadow', true);

       //bar.Set('chart.key.shadow.offsetx', 0);

      // bar.Set('chart.key.shadow.offsety', 0);

       // bar.Set('chart.key.shadow.blur', 10);

        //bar.Set('chart.key.shadow.color', 'rgba(128,128,128,0.5)');

        //bar.Set('chart.key.background', 'white');

        //bar.Set('chart.key.rounded', true);

       // bar.Set('chart.background.grid.autofit', true);

       bar.Set('chart.background.grid.autofit.numvlines', 2);
	   
	   bar.Set('chart.yaxispos', 'right');

        //bar.Set('chart.background.grid.autofit.numhlines', 10);

       //bar.Set('chart.gutter.bottom', 30);

        //bar.Set('chart.gutter.right', 5);
		
		
		
	
        // Define the line first so that it can be added to the bar chart

      
        bar.Draw();


}

//funcao que escreve grafico de barras e linhas. Eh necessario infomar o maior valor da barra ...
function desenhaGraficoLinhaBarra(valores_real, valores_ano_anterior, valores_orcado,maior_valor, titulo){  
	
	 
	
	//Aqui o grafico de barra, defina o array dos valores	
	var bar = new RGraph.Bar('line1', valores_real);	    
		
		//aqui entra o maior valor da barra
        bar.Set('chart.ymax', maior_valor);
		bar.Set('chart.colors', ['green', 'green', 'green', 'green']);
		
		
		//parametro criado para poder customizar as cores das barras
		bar.Set('chart.orcado',valores_orcado);
       // bar.Set('chart.labels', ['Jan', 'Feb', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']);
        bar.Set('chart.labels', ['Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']);
        bar.Set('chart.background.grid.autofit', true);
        //bar.Set('chart.key', ['John', 'Jane', 'Fred']);
        bar.Set('chart.key.shadow', true);
		bar.Set('chart.key.shadow.offsetx', 3);
		bar.Set('chart.key.shadow.offsety', 3);
        bar.Set('chart.key.shadow.blur', 10);
        bar.Set('chart.key.shadow.color', 'rgba(128,128,128,0.5)');
        bar.Set('chart.key.background', 'white');
        bar.Set('chart.key.rounded', true);
        bar.Set('chart.background.grid.autofit', true);
		bar.Set('chart.background.grid.autofit.numvlines', 12);
        bar.Set('chart.background.grid.autofit.numhlines', 30);
        bar.Set('chart.gutter.bottom', 30);
        bar.Set('chart.gutter.right', 5);		
        bar.Set('chart.gutter.left', 76);
		bar.Set('chart.tooltips', valores_real);		
	
        // Aqui começa as linhas, definir o nome e os arrays de valores de cada linha
       var line = new RGraph.Line('line1', valores_ano_anterior, valores_orcado);		
		
		line.Set('chart.title', titulo);
        line.Set('chart.background.grid', false);
        line.Set('chart.linewidth', 3);
        line.Set('chart.colors', ['orange', 'blue']);
        line.Set('chart.tickmarks', 'circle');
        //line.Set('chart.labels.ingraph', ['Janeiro',11,,'Fevereiro']);
        line.Set('chart.highlight.fill', 'black');
        line.Set('chart.noaxes', true);
        line.Set('chart.ylabels', false);
        line.Set('chart.tooltips', valores_ano_anterior, valores_orcado);
        line.Set('chart.animation.unfold.x', true);
		
		//aqui vc inclui as linhas no grafico de barras
		bar.Set('chart.line', line);
      

        bar.Draw();
		line.Draw();
		
}
