	var ateHoje;
	var ateFimMes;

	var table = "";
	var respVendas;
	var idEmpresa;
	
	var arrVendasTotais = new Array();
	var arrVendasMeta = new Array();
	var arrRepasseTotais = new Array();
	var arrRepasseMeta = new Array();
	var arrAtendimentoTotais = new Array();
	var arrAtendimentoMeta = new Array();
	var arrRetornoTotais = new Array();
	var arrRetornoMeta = new Array();
	var arrCursoTotais = new Array();
	var arrCursoMeta = new Array();

///////////////GRÁFICOS/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function somaTotalGeral(){

		var totalVendas = 0;
		var metasVendas = 0;
		var totalRepasses = 0;
		var metaRepasses = 0;
		var totalAtendimentos = 0;
		var metaAtendimentos = 0;
		var totalRetornos = 0;
		var metaRetornos = 0;
		var totalCursos = 0;
		var metaCursos = 0;

		for(x in arrVendasTotais){

			totalVendas += arrVendasTotais[x];
			metasVendas += arrVendasMeta[x];
			totalRepasses += arrRepasseTotais[x];
			metaRepasses += arrRepasseMeta[x];
			totalAtendimentos += arrAtendimentoTotais[x];
			metaAtendimentos += arrAtendimentoMeta[x];
			totalRetornos += arrRetornoTotais[x];
			metaRetornos += arrRetornoMeta[x];
			totalCursos += arrCursoTotais[x];
			metaCursos += arrCursoMeta[x];

		}

		geralTotalGauge(totalVendas, metasVendas, "gauge_total", "vendas_geral", "VENDAS");
		geralTotalGauge(totalRepasses, metaRepasses, "vendas_geral", "repasses_geral", "REPASSES");
		geralTotalGauge(totalAtendimentos, metaAtendimentos, "repasses_geral", "atendimentos_geral", "ATENDIMENTOS");
		geralTotalGauge(totalRetornos, metaRetornos, "atendimentos_geral", "retornos_geral", "RETORNOS");
		geralTotalGauge(totalCursos, metaCursos, "retornos_geral", "cursos_geral", "CURSOS");

		getPreparacoes();

	}


	function tabela(resp, idPai, idFilho, label){

		var first = true;
		var subTotalRealizado = 0;
		var subTotalProjetado = 0;
		var totalRealizado = 0;
		var totalProjetado = 0;
		var idEmpresaAnterior = 0;

		if(idFilho == "vendas_repasses"){
			for(y in respVendas){
				resp[y]['qtd'] = parseInt(resp[y]['qtd'])+parseInt(respVendas[y]['qtd']);
				resp[y]['qtd_meta'] = parseInt(resp[y]['qtd_meta'])+parseInt(respVendas[y]['qtd_meta']);
			}
		}

		if(idFilho == "vendas"){

			for(x in resp.reverse()){

				var proj = (resp[x]['qtd']/ateHoje)*ateFimMes;

				if(first){

					table += "<tr><th></th><th colspan='2' id='tabela_"+idFilho+"'>"+label+"</th></tr>";
					table += "<tr><th></th><th id='tabela_"+idFilho+"_realizado'>Realizado</th><th id='tabela_"+idFilho+"_projetado'>Projetado</th></tr>";

					first = false;
				}

				if(x > 0 && idEmpresaAnterior != resp[x]['id_empresa']){
					table += "<tr class='table-warning'><td><strong>"+loja+"</strong></td><td id='sub_"+idFilho+"_realizado_"+idLoja+"'>"+subTotalRealizado+"</td><td id='sub_"+idFilho+"_projetado_"+idLoja+"'>"+subTotalProjetado.toFixed(1)+"</td></tr>";
					subTotalRealizado = 0;
					subTotalProjetado = 0;	
				}

				if(resp[x]['id_empresa'] == 3){
					var loja = "LOJA 1";
					var idLoja = "loja_1";
				}else{
					var loja = "LOJA 2";
					var idLoja = "loja_2";
				}

				subTotalRealizado += parseInt(resp[x]['qtd']);
				subTotalProjetado += proj;
				totalRealizado += parseInt(resp[x]['qtd']);
				totalProjetado += proj;

				table += "<tr><td><strong>"+resp[x]['nome_vendedor']+"</strong></td><td id='tabela_"+idFilho+"_realizado_"+resp[x]['id_usuario']+"'>"+resp[x]['qtd']+"</td><td id='tabela_"+idFilho+"projetado"+resp[x]['id_usuario']+"'>"+proj.toFixed(1)+"</td></tr>";
				idEmpresaAnterior = resp[x]['id_empresa'];

				if(x == resp.length-1){
					table += "<tr class='table-warning'><td><strong>"+loja+"</strong></td><td id='sub_"+idFilho+"_realizado_"+idLoja+"'>"+subTotalRealizado+"</td><td id='sub_"+idFilho+"_projetado_"+idLoja+"'>"+subTotalProjetado.toFixed(1)+"</td></tr>";
					table += "<tr class='table-primary'><td><strong>TOTAL LOJAS</strong></td><td id='tabela_realizado_total_"+idFilho+"'>"+totalRealizado+"</td><td id='tabela_projetado_total_"+idFilho+"'>"+totalProjetado.toFixed(1)+"</td></tr>";
				}

			}

			$("#"+idPai).html(table);

		}else{

			for(x in resp.reverse()){

				var proj = (resp[x]['qtd']/ateHoje)*ateFimMes;

				if(first){

					$("#tabela_"+idPai).after("<th colspan='2' id='tabela_"+idFilho+"'>"+label+"</th>");
					$("#tabela_"+idPai+"_projetado").after("<th id='tabela_"+idFilho+"_realizado'>Realizado</th><th id='tabela_"+idFilho+"_projetado'>Projetado</th>");

					first = false;
				}

				if(x > 0 && idEmpresaAnterior != resp[x]['id_empresa']){
					$("#sub_"+idPai+"_projetado_"+idLoja).after("<td id='sub_"+idFilho+"_realizado_"+idLoja+"'>"+subTotalRealizado+"</td><td id='sub_"+idFilho+"_projetado_"+idLoja+"'>"+subTotalProjetado.toFixed(1)+"</td>");
					subTotalRealizado = 0;
					subTotalProjetado = 0;	
				}

				if(resp[x]['id_empresa'] == 3){
					var loja = "LOJA 1";
					var idLoja = "loja_1";
				}else{
					var loja = "LOJA 2";
					var idLoja = "loja_2";
				}

				subTotalRealizado += parseInt(resp[x]['qtd']);
				subTotalProjetado += proj;
				totalRealizado += parseInt(resp[x]['qtd']);
				totalProjetado += proj;

				$("#tabela_"+idPai+"projetado"+resp[x]['id_usuario']).after("<td id='tabela_"+idFilho+"_realizado_"+resp[x]['id_usuario']+"'>"+resp[x]['qtd']+"</td><td id='tabela_"+idFilho+"projetado"+resp[x]['id_usuario']+"'>"+proj.toFixed(1)+"</td>");
				idEmpresaAnterior = resp[x]['id_empresa'];

				if(x == resp.length-1){
					$("#sub_"+idPai+"_projetado_"+idLoja).after("<td id='sub_"+idFilho+"_realizado_"+idLoja+"'>"+subTotalRealizado+"</td><td id='sub_"+idFilho+"_projetado_"+idLoja+"'>"+subTotalProjetado.toFixed(1)+"</td>");
					$("#tabela_projetado_total_"+idPai).after("<td id='tabela_realizado_total_"+idFilho+"'>"+totalRealizado+"</td><td id='tabela_projetado_total_"+idFilho+"'>"+totalProjetado.toFixed(1)+"</td>");
				}

			}

		}

	}

	function geralTotalGauge(total, meta, idPai, idFilho, label) {

		if(idPai == "gauge_total"){

		$("#"+idPai).append("<tr class='jumbotron' id='geral'>"+
				"<td colspan='5' style='text-align:center;'>"+
					"<br><h4>Total lojas</h4><br>"+
				"</td>"+
			"</tr>"+
			"<tr class='jumbotron'>"+
				"<td id='"+idFilho+"'>"+
					"<div style='text-align:center;'>"+
						"<span class='alert alert-secondary'> "+label+" <span style='font-size:10px;'>(Meta "+meta+")</span></span>"+
						"<div id='"+idFilho+"_realizado'></div>"+
						"<div id='"+idFilho+"_projetado'></div>"+
					"</div>"+
				"</td>"+
			"</tr>"+
			"<tr>"+
				"<td colspan='5' style='text-align:center;'>"+
					"<hr>"+
				"</td>"+
		"</tr>");

		}else{
			$("#"+idPai).after("<td id='"+idFilho+"' style='text-align:center; width:20%;'>"+
				"<span class='alert alert-secondary'> "+label+" <span style='font-size:10px;'>(Meta "+meta+")</span></span>"+
				"<div style='width:100%;' id='"+idFilho+"_realizado'></div>"+
				"<div style='width:100%;' id='"+idFilho+"_projetado'></div>"+
			"</td>");
		}

		var proj = (total/ateHoje)*ateFimMes;

		if(label == "ATENDIMENTOS" || label == "RETORNOS"){
			if(proj > meta){
				var maximoProj = parseInt(proj+(proj/5));
			}else{
				var maximoProj = parseInt(meta+(meta/5));
			}
			var divideTicks = 80;
		}else{
			if(proj > meta){
				var maximoProj = parseInt(proj)+2;
			}else{
				var maximoProj = parseInt(meta)+2;
			}
			var divideTicks = 2;
		}

		var gaugeOptionsVendas = {
			redFrom: 0, redTo: meta,
			greenFrom:meta, greenTo: maximoProj,
			max: maximoProj,
			minorTicks: parseInt((maximoProj/divideTicks))
		};

		gaugeDataVendas = new google.visualization.DataTable();
		gaugeDataVendas.addColumn('number', 'Projetado');
		gaugeDataVendas.addRows(1);
		gaugeDataVendas.setCell(0, 0, parseInt(proj));

		var gaugeVenda = new google.visualization.Gauge(document.getElementById(idFilho+"_projetado"));
		gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
		///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

		var deveria = (meta/ateFimMes)*ateHoje;

		if(label == "ATENDIMENTOS" || label == "RETORNOS"){
			if(deveria > parseInt(total)){
				var maximoDev = parseInt(deveria+(deveria/5));
			}else{
				var maximoDev = parseInt(total+(total/5));
			}
			divideTicks = 80;
		}else{
			if(deveria > parseInt(total)){
				var maximoDev = parseInt(deveria)+2;
			}else{
				var maximoDev = parseInt(total)+2;
			}
			divideTicks = 2;
		}

		var optionsTotalVendas = {
			redFrom: 0, redTo: deveria,
			greenFrom:deveria, greenTo: maximoDev,
			max: maximoDev,
			minorTicks: parseInt((maximoDev/divideTicks))
		};

		gaugeDataTotalVendas = new google.visualization.DataTable();
		gaugeDataTotalVendas.addColumn('number', 'Realizado');
		gaugeDataTotalVendas.addRows(2);
		gaugeDataTotalVendas.setCell(0, 0, total);

		var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById(idFilho+"_realizado"));
		gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

	}


	function totalCursoLoja() {

		var porcent = Object.keys(arrVendasTotais).length;

		for(x in arrCursoTotais){

			var cursoMeta = arrCursoMeta[x];

			if(cursoMeta == 0){

				$("#gauge_lojas").append("<div class='jumbotron' style='width:"+(94/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
												"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
												"<hr>"+
												"<span class='alert alert-info'> Não há meta de vendas! <span style='font-size:10px;'>(Meta "+cursoMeta+")</span></span>"+
												"<div class='row' id='vendas_"+x+"'>"+
													
												"</div>"+
										"</div>");

			}else{

				///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				// $("#gauge_lojas").append("<div class='jumbotron' style='width:"+(50/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
				// 									"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
				// 									"<hr>"+
				// 									"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+cursoMeta+")</span></span>"+
				// 									"<div class='row' id='vendas_"+x+"'>"+
				// 										"<div style='width:50%;' id='loja_vendas_"+x+"'></div>"+
				// 										"<div style='width:50%;' id='loja_vendas_projetado_"+x+"'></div>"+
				// 									"</div>"+
				// 							"</div>");

				$("#retorno_lojas_"+x).after("<td id='curso_lojas_"+x+"' style='text-align:center; width:20%;'>"+
							"<span class='alert alert-secondary'> CURSOS <span style='font-size:10px;'>(Meta "+cursoMeta+")</span></span>"+
								"<div style='width:100%;' id='loja_curso_total_"+x+"'></div>"+
								"<div style='width:100%;' id='loja_curso_projetado_"+x+"'></div>"+
							"</td>");
				var proj = (arrCursoTotais[x]/ateHoje)*ateFimMes;


				if(proj > cursoMeta){
					var maximoProj = parseInt(proj)+2;
				}else{
					var maximoProj = parseInt(cursoMeta)+2;
				}

				var gaugeOptionsVendas = {
					redFrom: 0, redTo: cursoMeta,
					greenFrom:cursoMeta, greenTo: maximoProj,
					max: maximoProj,
					minorTicks: (maximoProj/4)
				};

				gaugeDataVendas = new google.visualization.DataTable();
				gaugeDataVendas.addColumn('number', 'Projetado');
				gaugeDataVendas.addRows(1);
				gaugeDataVendas.setCell(0, 0, parseInt(proj));

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("loja_curso_projetado_"+x));
				gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
				///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

				var deveria = (cursoMeta/ateFimMes)*ateHoje;

				if(deveria > parseInt(arrCursoTotais[x])){
					var maximoDev = parseInt(deveria+2);
				}else{
					var maximoDev = parseInt(arrCursoTotais[x]+2);
				}

				var optionsTotalVendas = {
					redFrom: 0, redTo: deveria,
					greenFrom:deveria, greenTo: maximoDev,
					max: maximoDev,
					minorTicks: (maximoDev/4)
				};

				gaugeDataTotalVendas = new google.visualization.DataTable();
				gaugeDataTotalVendas.addColumn('number', 'Realizado');
				gaugeDataTotalVendas.addRows(2);
				gaugeDataTotalVendas.setCell(0, 0, arrCursoTotais[x]);

				var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById("loja_curso_total_"+x));
				gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

			}

		}

		somaTotalGeral();

	}


	function totalRetornoLoja() {

		var porcent = Object.keys(arrVendasTotais).length;

		for(x in arrRetornoTotais){

			var retornoMeta = arrRetornoMeta[x];

			if(retornoMeta == 0){

				$("#gauge_lojas").append("<div class='jumbotron' style='width:"+(94/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
												"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
												"<hr>"+
												"<span class='alert alert-info'> Não há meta de vendas! <span style='font-size:10px;'>(Meta "+retornoMeta+")</span></span>"+
												"<div class='row' id='vendas_"+x+"'>"+
													
												"</div>"+
										"</div>");

			}else{

				///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				// $("#gauge_lojas").append("<div class='jumbotron' style='width:"+(50/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
				// 									"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
				// 									"<hr>"+
				// 									"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+retornoMeta+")</span></span>"+
				// 									"<div class='row' id='vendas_"+x+"'>"+
				// 										"<div style='width:50%;' id='loja_vendas_"+x+"'></div>"+
				// 										"<div style='width:50%;' id='loja_vendas_projetado_"+x+"'></div>"+
				// 									"</div>"+
				// 							"</div>");

				$("#atendimento_lojas_"+x).after("<td id='retorno_lojas_"+x+"' style='text-align:center; width:20%;'>"+
							"<span class='alert alert-secondary'> RETORNOS <span style='font-size:10px;'>(Meta "+retornoMeta+")</span></span>"+
								"<div style='width:100%;' id='loja_retorno_total_"+x+"'></div>"+
								"<div style='width:100%;' id='loja_retorno_projetado_"+x+"'></div>"+
							"</td>");
				var proj = (arrRetornoTotais[x]/ateHoje)*ateFimMes;


				if(proj > retornoMeta){
					var maximoProj = parseInt(proj)+parseInt(proj)/5;
				}else{
					var maximoProj = parseInt(retornoMeta)+parseInt(retornoMeta)/5;
				}

				var gaugeOptionsVendas = {
					redFrom: 0, redTo: retornoMeta,
					greenFrom:retornoMeta, greenTo: maximoProj,
					max: maximoProj,
					minorTicks: (maximoProj/(maximoProj/10))
				};

				gaugeDataVendas = new google.visualization.DataTable();
				gaugeDataVendas.addColumn('number', 'Projetado');
				gaugeDataVendas.addRows(1);
				gaugeDataVendas.setCell(0, 0, parseInt(proj));

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("loja_retorno_projetado_"+x));
				gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
				///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

				var deveria = (retornoMeta/ateFimMes)*ateHoje;

				if(deveria > parseInt(arrRetornoTotais[x])){
					var maximoDev = parseInt(deveria+(deveria/10));
				}else{
					var maximoDev = parseInt(arrRetornoTotais[x]+(arrRetornoTotais[x]/10));
				}

				var optionsTotalVendas = {
					redFrom: 0, redTo: deveria,
					greenFrom:deveria, greenTo: maximoDev,
					max: maximoDev,
					minorTicks: (maximoDev/(maximoDev/10))
				};

				gaugeDataTotalVendas = new google.visualization.DataTable();
				gaugeDataTotalVendas.addColumn('number', 'Realizado');
				gaugeDataTotalVendas.addRows(2);
				gaugeDataTotalVendas.setCell(0, 0, arrRetornoTotais[x]);

				var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById("loja_retorno_total_"+x));
				gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

			}

		}

		totalCursoLoja();

	}


	function totalAtendimentoLoja() {

		var porcent = Object.keys(arrVendasTotais).length;

		for(x in arrAtendimentoTotais){

			var atendimentoMeta = arrAtendimentoMeta[x];

			if(atendimentoMeta == 0){

				$("#gauge_lojas").append("<div class='jumbotron' style='width:"+(94/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
												"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
												"<hr>"+
												"<span class='alert alert-info'> Não há meta de vendas! <span style='font-size:10px;'>(Meta "+atendimentoMeta+")</span></span>"+
												"<div class='row' id='vendas_"+x+"'>"+
													
												"</div>"+
										"</div>");

			}else{

				///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				// $("#gauge_lojas").append("<div class='jumbotron' style='width:"+(50/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
				// 									"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
				// 									"<hr>"+
				// 									"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+atendimentoMeta+")</span></span>"+
				// 									"<div class='row' id='vendas_"+x+"'>"+
				// 										"<div style='width:50%;' id='loja_vendas_"+x+"'></div>"+
				// 										"<div style='width:50%;' id='loja_vendas_projetado_"+x+"'></div>"+
				// 									"</div>"+
				// 							"</div>");

				$("#repasse_lojas_"+x).after("<td id='atendimento_lojas_"+x+"' style='text-align:center; width:20%;'>"+
							"<span class='alert alert-secondary'> ATENDIMENTOS <span style='font-size:10px;'>(Meta "+atendimentoMeta+")</span></span>"+
								"<div style='width:100%;' id='loja_atendimento_total_"+x+"'></div>"+
								"<div style='width:100%;' id='loja_atendimento_projetado_"+x+"'></div>"+
							"</td>");
				var proj = (arrAtendimentoTotais[x]/ateHoje)*ateFimMes;


				if(proj > atendimentoMeta){
					var maximoProj = parseInt(proj)+parseInt(proj)/5;
				}else{
					var maximoProj = parseInt(atendimentoMeta)+parseInt(atendimentoMeta)/5;
				}

				var gaugeOptionsVendas = {
					redFrom: 0, redTo: atendimentoMeta,
					greenFrom:atendimentoMeta, greenTo: maximoProj,
					max: maximoProj,
					minorTicks: (maximoProj/(maximoProj/10))
				};

				gaugeDataVendas = new google.visualization.DataTable();
				gaugeDataVendas.addColumn('number', 'Projetado');
				gaugeDataVendas.addRows(1);
				gaugeDataVendas.setCell(0, 0, parseInt(proj));

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("loja_atendimento_projetado_"+x));
				gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
				///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

				var deveria = (atendimentoMeta/ateFimMes)*ateHoje;

				if(deveria > parseInt(arrAtendimentoTotais[x])){
					var maximoDev = parseInt(deveria+(deveria/10));
				}else{
					var maximoDev = parseInt(arrAtendimentoTotais[x]+(arrAtendimentoTotais[x]/10));
				}

				var optionsTotalVendas = {
					redFrom: 0, redTo: deveria,
					greenFrom:deveria, greenTo: maximoDev,
					max: maximoDev,
					minorTicks: (maximoDev/(maximoDev/10))
				};

				gaugeDataTotalVendas = new google.visualization.DataTable();
				gaugeDataTotalVendas.addColumn('number', 'Realizado');
				gaugeDataTotalVendas.addRows(2);
				gaugeDataTotalVendas.setCell(0, 0, arrAtendimentoTotais[x]);

				var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById("loja_atendimento_total_"+x));
				gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

			}

		}

		totalRetornoLoja();

	}


	function totalRepasseLoja() {

		var porcent = Object.keys(arrVendasTotais).length;

		for(x in arrRepasseTotais){

			var repasseMeta = arrRepasseMeta[x];

			if(repasseMeta == 0){

				$("#gauge_lojas").append("<div class='jumbotron' style='width:"+(94/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
												"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
												"<hr>"+
												"<span class='alert alert-info'> Não há meta de vendas! <span style='font-size:10px;'>(Meta "+repasseMeta+")</span></span>"+
												"<div class='row' id='vendas_"+x+"'>"+
													
												"</div>"+
										"</div>");

			}else{

				///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				// $("#gauge_lojas").append("<div class='jumbotron' style='width:"+(50/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
				// 									"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
				// 									"<hr>"+
				// 									"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+repasseMeta+")</span></span>"+
				// 									"<div class='row' id='vendas_"+x+"'>"+
				// 										"<div style='width:50%;' id='loja_vendas_"+x+"'></div>"+
				// 										"<div style='width:50%;' id='loja_vendas_projetado_"+x+"'></div>"+
				// 									"</div>"+
				// 							"</div>");

				$("#vendas_lojas_"+x).after("<td id='repasse_lojas_"+x+"' style='text-align:center; width:20%;'>"+
							"<span class='alert alert-secondary'> REPASSE <span style='font-size:10px;'>(Meta "+repasseMeta+")</span></span>"+
								"<div style='width:100%;' id='loja_repasse_total_"+x+"'></div>"+
								"<div style='width:100%;' id='loja_repasse_projetado_"+x+"'></div>"+
							"</td>");
				var proj = (arrRepasseTotais[x]/ateHoje)*ateFimMes;


				if(proj > repasseMeta){
					var maximoProj = parseInt(proj)+2;
				}else{
					var maximoProj = parseInt(repasseMeta)+2;
				}

				var gaugeOptionsVendas = {
					redFrom: 0, redTo: repasseMeta,
					greenFrom:repasseMeta, greenTo: maximoProj,
					max: maximoProj,
					minorTicks: (maximoProj/4)
				};

				gaugeDataVendas = new google.visualization.DataTable();
				gaugeDataVendas.addColumn('number', 'Projetado');
				gaugeDataVendas.addRows(1);
				gaugeDataVendas.setCell(0, 0, parseInt(proj));

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("loja_repasse_projetado_"+x));
				gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
				///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

				var deveria = (repasseMeta/ateFimMes)*ateHoje;

				if(deveria > parseInt(arrRepasseTotais[x])){
					var maximoDev = parseInt(deveria)+2;
				}else{
					var maximoDev = parseInt(arrRepasseTotais[x])+2;
				}

				var optionsTotalVendas = {
					redFrom: 0, redTo: deveria,
					greenFrom:deveria, greenTo: maximoDev,
					max: maximoDev,
					minorTicks: (maximoDev/4)
				};

				gaugeDataTotalVendas = new google.visualization.DataTable();
				gaugeDataTotalVendas.addColumn('number', 'Realizado');
				gaugeDataTotalVendas.addRows(2);
				gaugeDataTotalVendas.setCell(0, 0, arrRepasseTotais[x]);

				var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById("loja_repasse_total_"+x));
				gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

			}

		}

		totalAtendimentoLoja();

	}

	function totalVendasLoja() {

		var porcent = Object.keys(arrVendasTotais).length;

		for(x in arrVendasTotais){

			if(x == 3){
				var loja = "Loja 1";
			}else{
				var loja = "Loja 2";
			}

			var vendasMeta = arrVendasMeta[x];

			if(vendasMeta == 0){

				$("#gauge_lojas").append("<div class='jumbotron' style='width:"+(94/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
												"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
												"<hr>"+
												"<span class='alert alert-info'> Não há meta de vendas! <span style='font-size:10px;'>(Meta "+vendasMeta+")</span></span>"+
												"<div class='row' id='vendas_"+x+"'>"+
													
												"</div>"+
										"</div>");

			}else{

				///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				// $("#gauge_lojas").append("<div class='jumbotron' style='width:"+(50/porcent)+"%; text-align:center; margin-left:10px;' id='loja_"+x+"'>"+
				// 									"<h5 class='display-5' style='margin-top:-30px;'>"+loja+"</h5>"+
				// 									"<hr>"+
				// 									"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+vendasMeta+")</span></span>"+
				// 									"<div class='row' id='vendas_"+x+"'>"+
				// 										"<div style='width:50%;' id='loja_vendas_"+x+"'></div>"+
				// 										"<div style='width:50%;' id='loja_vendas_projetado_"+x+"'></div>"+
				// 									"</div>"+
				// 							"</div>");


				$("#gauge_lojas").append("<tr class='jumbotron' id='loja_"+x+"'>"+
						"<td colspan='5' style='text-align:center;'>"+
							// "<div class='row' style='width:100%;'>"+
								"<br><h4>"+loja+"</h4><br>"+
							// "</div>"+
						"</td>"+
					"</tr>"+
					"<tr class='jumbotron'>"+
						"<td id='vendas_lojas_"+x+"' >"+
							"<div style='text-align:center;'>"+
								"<span class='alert alert-secondary'> VENDAS <span style='font-size:10px;'>(Meta "+vendasMeta+")</span></span>"+
								"<div id='loja_vendas_"+x+"'></div>"+
								"<div id='loja_vendas_projetado_"+x+"'></div>"+
							"</div>"+
						"</td>"+
					"</tr>"+
					"<tr>"+
						"<td colspan='5' style='text-align:center;'>"+
							"<hr>"+
						"</td>"+
				"</tr>");

				var proj = (arrVendasTotais[x]/ateHoje)*ateFimMes;

				if(proj > vendasMeta){
					var maximoProj = parseInt(proj)+2;
				}else{
					var maximoProj = parseInt(vendasMeta)+2;
				}

				var gaugeOptionsVendas = {
					redFrom: 0, redTo: vendasMeta,
					greenFrom:vendasMeta, greenTo: maximoProj,
					max: maximoProj,
					minorTicks: (maximoProj/4)
				};

				gaugeDataVendas = new google.visualization.DataTable();
				gaugeDataVendas.addColumn('number', 'Projetado');
				gaugeDataVendas.addRows(1);
				gaugeDataVendas.setCell(0, 0, parseInt(proj));

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("loja_vendas_projetado_"+x));
				gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
				///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

				////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

				var deveria = (vendasMeta/ateFimMes)*ateHoje;

				if(deveria > parseInt(arrVendasTotais[x])){
					var maximoDev = parseInt(deveria)+2;
				}else{
					var maximoDev = parseInt(arrVendasTotais[x])+2;
				}

				var optionsTotalVendas = {
					redFrom: 0, redTo: deveria,
					greenFrom:deveria, greenTo: maximoDev,
					max: maximoDev,
					minorTicks: (maximoDev/4)
				};

				gaugeDataTotalVendas = new google.visualization.DataTable();
				gaugeDataTotalVendas.addColumn('number', 'Realizado');
				gaugeDataTotalVendas.addRows(2);
				gaugeDataTotalVendas.setCell(0, 0, arrVendasTotais[x]);

				var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById("loja_vendas_"+x));
				gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

			}

		}

		totalRepasseLoja();

	}

	function cursosProjecao() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_cursos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "retornos", "cursos", "CURSOS");
				tabela(resp, "retornos", "cursos", "CURSOS");
				totalVendasLoja();
			}

	    });

	}

	function retornosProjecao() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_retornos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "atendimentos", "retornos", "RETORNOS");
				tabela(resp, "atendimentos", "retornos", "RETORNOS");
				cursosProjecao();
	        }

	    });

    }

	function atendimentosProjecao() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_atendimentos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "repasses", "atendimentos", "ATENDIMENTOS");
				tabela(resp, "vendas_repasses", "atendimentos", "ATENDIMENTOS");
				retornosProjecao();
	        }

	    });

    }



	function repasseProjecao() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_repasse",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "vendas", "repasses", "REPASSES");
				tabela(resp, "vendas", "repasses", "REPASSES");
				tabela(resp, "repasses", "vendas_repasses", "VENDAS+REPASSES");
				atendimentosProjecao();
	        }

	    });

    }


    function vendasProjecao() {

    	$("#gauge_vendedores").html("");

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_vendas",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){

				respVendas = resp;

				graficoIndividual(resp, "gauge_vendedores", "vendas", "VENDAS");
				tabela(resp, "tabela", "vendas", "VENDAS");
				repasseProjecao();
	        }

	    });

    }

	function graficoIndividual(resp, idPai, idFilho, label) {

		$("#"+idPai).html("");

		for(x in resp){

			ateHoje = parseInt(resp[x]['qtd_dias']);
			ateFimMes = parseInt(resp[x]['qtd_dias_mes']);

			if(idFilho == "vendas"){
				if(arrVendasTotais[parseInt(resp[x]['id_empresa'])] == null && arrVendasMeta[parseInt(resp[x]['id_empresa'])] == null){
					arrVendasTotais[parseInt(resp[x]['id_empresa'])] = 0;
					arrVendasMeta[parseInt(resp[x]['id_empresa'])] = 0;
				}
				arrVendasTotais[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd']);
				arrVendasMeta[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd_meta']);
			}

			if(idFilho == "repasses"){
				if(arrRepasseTotais[parseInt(resp[x]['id_empresa'])] == null && arrRepasseMeta[parseInt(resp[x]['id_empresa'])] == null){
					arrRepasseTotais[parseInt(resp[x]['id_empresa'])] = 0;
					arrRepasseMeta[parseInt(resp[x]['id_empresa'])] = 0;
				}
				arrRepasseTotais[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd']);
				arrRepasseMeta[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd_meta']);
			}

			if(idFilho == "atendimentos"){
				if(arrAtendimentoTotais[parseInt(resp[x]['id_empresa'])] == null && arrAtendimentoMeta[parseInt(resp[x]['id_empresa'])] == null){
					arrAtendimentoTotais[parseInt(resp[x]['id_empresa'])] = 0;
					arrAtendimentoMeta[parseInt(resp[x]['id_empresa'])] = 0;
				}
				arrAtendimentoTotais[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd']);
				arrAtendimentoMeta[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd_meta']);
			}

			if(idFilho == "retornos"){
				if(arrRetornoTotais[parseInt(resp[x]['id_empresa'])] == null && arrRetornoMeta[parseInt(resp[x]['id_empresa'])] == null){
					arrRetornoTotais[parseInt(resp[x]['id_empresa'])] = 0;
					arrRetornoMeta[parseInt(resp[x]['id_empresa'])] = 0;
				}
				arrRetornoTotais[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd']);
				arrRetornoMeta[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd_meta']);
			}

			if(idFilho == "cursos"){
				if(arrCursoTotais[parseInt(resp[x]['id_empresa'])] == null && arrCursoMeta[parseInt(resp[x]['id_empresa'])] == null){
					arrCursoTotais[parseInt(resp[x]['id_empresa'])] = 0;
					arrCursoMeta[parseInt(resp[x]['id_empresa'])] = 0;
				}
				arrCursoTotais[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd']);
				arrCursoMeta[parseInt(resp[x]['id_empresa'])] += parseInt(resp[x]['qtd_meta']);
			}

			var meta = resp[x]['qtd_meta'];

			///////////////////////////////////////GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

			if(idPai == "gauge_vendedores"){

				$("#"+idPai).prepend("<tr class='jumbotron' id='vendedor_"+resp[x]['id_usuario']+"'>"+
					"<td colspan='5' style='text-align:center;'>"+
						"<br><h4>"+resp[x]['nome_vendedor']+"</h4><br>"+
					"</td>"+
				"</tr>"+
				"<tr class='jumbotron'>"+
					"<td id='"+idFilho+"_"+resp[x]['id_usuario']+"' style='text-align:center;'>"+
						"<div style='text-align:center;'>"+
							"<span class='alert alert-secondary'> "+label+" <span style='font-size:10px;'>(Meta "+meta+")</span></span>"+
							"<div id='"+idFilho+"_realizado_"+resp[x]['id_usuario']+"'></div>"+
							"<div id='"+idFilho+"_projetado_"+resp[x]['id_usuario']+"'></div>"+
						"</div>"+
					"</td>"+
				"</tr>"+
				"<tr>"+
					"<td colspan='5' style='text-align:center;'>"+
						"<hr>"+
					"</td>"+
				"</tr>");

			}else{

				$("#"+idPai+"_"+resp[x]['id_usuario']).after("<td id='"+idFilho+"_"+resp[x]['id_usuario']+"' style='text-align:center; width:20%;'>"+
					"<span class='alert alert-secondary'> "+label+" <span style='font-size:10px;'>(Meta "+meta+")</span></span>"+
					"<div id='"+idFilho+"_realizado_"+resp[x]['id_usuario']+"'></div>"+
					"<div id='"+idFilho+"_projetado_"+resp[x]['id_usuario']+"'></div>"+
				"</td>");

			}

			var proj = (resp[x]['qtd']/resp[x]['qtd_dias'])*resp[x]['qtd_dias_mes'];

			if(proj > meta){
				var maximoProj = parseInt(proj)+2;
			}else{
				var maximoProj = parseInt(meta)+2;
			}

			var gaugeOptionsVendas = {
				redFrom: 0, redTo: meta,
				greenFrom:meta, greenTo: maximoProj,
				max: maximoProj,
				minorTicks: (maximoProj/4)
			};

			gaugeDataVendas = new google.visualization.DataTable();
			gaugeDataVendas.addColumn('number', 'Projetado');
			gaugeDataVendas.addRows(1);
			gaugeDataVendas.setCell(0, 0, parseInt(proj));

			var gaugeVenda = new google.visualization.Gauge(document.getElementById(idFilho+"_projetado_"+resp[x]['id_usuario']));
			gaugeVenda.draw(gaugeDataVendas, gaugeOptionsVendas);
			///////////////////////////////////////FIM GRÁFICO VENDAS PROJEÇÃO///////////////////////////////////////////////////////////////////////////

			////////////////////////////////////////GRÁFICO VENDAS TOTAL/////////////////////////////////////////////////////////////////////////////

			var deveria = (meta/resp[x]['qtd_dias_mes'])*resp[x]['qtd_dias'];

			if(deveria > parseInt(resp[x]['qtd'])){
				var maximoDev = parseInt(deveria)+2;
			}else{
				var maximoDev = parseInt(resp[x]['qtd'])+2;
			}

			var optionsTotalVendas = {
				redFrom: 0, redTo: deveria,
				greenFrom:deveria, greenTo: maximoDev,
				max: maximoDev,
				minorTicks: (maximoDev/4)
			};

			gaugeDataTotalVendas = new google.visualization.DataTable();
			gaugeDataTotalVendas.addColumn('number', 'Realizado');
			gaugeDataTotalVendas.addRows(2);
			gaugeDataTotalVendas.setCell(0, 0, resp[x]['qtd']);

			var gaugeVendaTotal = new google.visualization.Gauge(document.getElementById(idFilho+"_realizado_"+resp[x]['id_usuario']));
			gaugeVendaTotal.draw(gaugeDataTotalVendas, optionsTotalVendas);

		}

	}


	function atualiza(id){

		idEmpresa = id;

		google.charts.load('current', {'packages':['gauge']});
	   	google.charts.setOnLoadCallback(vendasProjecao);

	}

////////////////////////////////////////////////////////////////////////INICIO INDIVIDUAL VENDEDORES/////////////////////////////////////////////////////////////////////////////////////////


var idVendedor;

function cursosProjecaoVendedor() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_cursos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "retornos", "cursos", "CURSOS");
			}

	    });

	}

	function retornosProjecaoVendedor() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_retornos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "atendimentos", "retornos", "RETORNOS");
				cursosProjecaoVendedor();
	        }

	    });

    }

	function atendimentosProjecaoVendedor() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_atendimentos",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "repasses", "atendimentos", "ATENDIMENTOS");
				retornosProjecaoVendedor();
	        }

	    });

    }



	function repasseProjecaoVendedor() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_repasse",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){
				graficoIndividual(resp, "vendas", "repasses", "REPASSES");
				atendimentosProjecaoVendedor();
	        }

	    });

    }


    function vendasProjecaoVendedor() {

    	$("#gauge_vendedores").html("");

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_data_vendas",
	        type: "POST",
	        async: false,
	        data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){

	        	console.log(resp);
				graficoIndividual(resp, "gauge_vendedores", "vendas", "VENDAS");
				repasseProjecaoVendedor();
	        }

	    });

    }


    function atualizaVendedor(id){

    	idVendedor = id;

		google.charts.load('current', {'packages':['gauge']});
	   	google.charts.setOnLoadCallback(vendasProjecaoVendedor);

	}
//////////////////////////////////////////////////////////////////////////FIM INDIVIDUAL VENDEDORES//////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////INICIO TABELA CURSOS///////////////////////////////////////////////////////////////////////////////////

	function tabelaCursos(){

		$.ajax({

	        url: "/estatisticas/ajax/fn/get_cursos",
	        type: "POST",
	        async: false,
	       	data: {'id': idVendedor, 'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "JSON",
	        success: function(resp){

	        	var arrId = new Array();
	        	var arrCursos = new Array();
	        	var cont = 0;
	        	var table = "<tr><th></th>";

				for(h in resp){
					table += "<th>"+resp[h][0]['nome'].split(" ")[0].toUpperCase()+"</th>";
					arrId[cont] = resp[h][0]['id_usuario'];
					cont++;
				}

				table += "</tr>";

				cont = 0;

				for(h in resp){
					for(x in resp[h]){
						if(resp[h][x]['titulo']){
							arrCursos[retornaComUnderline(resp[h][x]['titulo'])] = resp[h][x]['titulo'].toUpperCase();
						}
					}
				}

				for(t in arrCursos){

					table += "<tr><td>"+arrCursos[t]+"</td>";

					for(i in arrId){
						table += "<td id='"+t+"_"+arrId[i]+"'></td>";
					}

					table += "</tr>";

				}

				$("#tabela_cursos").html(table);

				for(h in resp){
					for(x in resp[h]){
						if(resp[h][x]['titulo']){

							if(resp[h][x]['result'] == "failed"){
								var result = "<span class='badge badge-danger' style='font-size: 12px;'>("+resp[h][x]['score']+") - REPROVADO </span>";
							}else{
								var result = "<span class='badge badge-success' style='font-size: 12px;'>("+resp[h][x]['score']+") - APROVADO </span>";
							}

							$("#"+retornaComUnderline(resp[h][x]['titulo'])+"_"+resp[h][x]['id_usuario']).html(result);
						}
					}
				}

	        }

	    });

	}


	function retornaComUnderline(str){

		for (var i = 0; i < 6; i++) {
			str = str.replace("  ", " ");
			str = str.replace(" ", "_");
		}

		return str;

	}

//////////////////////////////////////////////////////////////////////;///////FIM TABELA CURSOS/////////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////INICIO TABELA RALE///////////////////////////////////////////////////////////////////////////////////

	function getRale() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_rale_vendedores",
	        type: "POST",
	        async: false,
	        data: {'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "TEXT",
	        success: function(resp){
				$("#tabela_rale_vendedores").html(resp);
				getRaleTotal();
	        }

	    });

    }

    function getRaleTotal() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_rale_total",
	        type: "POST",
	        async: false,
	        data: {'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "TEXT",
	        success: function(resp){
				$("#tabela_rale_total").html(resp);
				getRaleLojas();
	        }

	    });

    }

    function getRaleLojas() {

    	$.ajax({

	        url: "/estatisticas/ajax/fn/get_rale_lojas",
	        type: "POST",
	        async: false,
	        data: {'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
	        dataType: "TEXT",
	        success: function(resp){
				$("#tabela_rale_lojas").html(resp);
	        }

	    });

    }

/////////////////////////////////////////////////////////////////////////////FIM TABELA RALE/////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////INICIO PREPARADORES/////////////////////////////////////////////////////////////////////////////////////

function getPreparacoes() {

	$.ajax({

        url: "/estatisticas/ajax/fn/get_preparacoes",
        type: "POST",
        async: false,
        data: {'data_inicial':$("#data_inicial").val(), 'data_final':$("#data_final").val()},
        dataType: "JSON",
        success: function(resp){
			//console.log(resp);

			for(x in resp){

				if(resp[x]["id_empresa"] == 3){
					var loja = 1;
				}else{
					var loja = 2;
				}

				$("#inicio").after("<td style='text-align:center; width:20%;'>"+
							"<span class='alert alert-secondary'> "+resp[x]["nome"].split(" ")[0]+" <span style='font-size:10px;'>(Loja: "+loja+")</span></span>"+
								"<div style='width:100%;' id='preparador_realizado_"+resp[x]["id"]+"'></div>"+
								"<div style='width:100%;' id='preparador_atrasado_"+resp[x]["id"]+"'></div>"+
							"</td>");
				//////////////////REALIZADO/////////////////////////////////////////////////
				var gaugeOptionsRealizado = {
					redFrom: 0, redTo: (resp[x]["atrasado"]+resp[x]["realizado"])*0.1,
					greenFrom:(resp[x]["atrasado"]+resp[x]["realizado"])*0.1,
					greenTo: (resp[x]["atrasado"]+resp[x]["realizado"]),
					max: (resp[x]["atrasado"]+resp[x]["realizado"]),
					minorTicks: 5
				};

				gaugeDataRealizado = new google.visualization.DataTable();
				gaugeDataRealizado.addColumn('number', 'Realizado');
				gaugeDataRealizado.addRows(1);
				gaugeDataRealizado.setCell(0, 0, resp[x]["realizado"]);

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("preparador_realizado_"+resp[x]['id']));
				gaugeVenda.draw(gaugeDataRealizado, gaugeOptionsRealizado);

				//////////////////ATRASADO/////////////////////////////////////////////////
				var gaugeOptionsAtrasado = {
					greenFrom: 0, greenTo: (resp[x]["atrasado"]+resp[x]["realizado"])*0.1,
					redFrom:(resp[x]["atrasado"]+resp[x]["realizado"])*0.1,
					redTo: (resp[x]["atrasado"]+resp[x]["realizado"]),
					max: (resp[x]["atrasado"]+resp[x]["realizado"]),
					minorTicks: 5
				};

				gaugeDataAtrasado = new google.visualization.DataTable();
				gaugeDataAtrasado.addColumn('number', 'Atrasado');
				gaugeDataAtrasado.addRows(1);
				gaugeDataAtrasado.setCell(0, 0, resp[x]["atrasado"]);

				var gaugeVenda = new google.visualization.Gauge(document.getElementById("preparador_atrasado_"+resp[x]['id']));
				gaugeVenda.draw(gaugeDataAtrasado, gaugeOptionsAtrasado);

			}


        }

    });

}

/////////////////////////////////////////////////////////////////////////////FIM PREPARADORES/////////////////////////////////////////////////////////////////////////////////////
