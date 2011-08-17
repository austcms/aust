/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */

function mostraResultadosPerguntaAberta(id){
	$("#resultados_perguntaaberta_"+id).html("<em>Carregando, aguarde...</em>");


	$.post(include_baseurl+"/js/ajax.php", {
					action: "leResultadosAbertos",
					id: id
				}, function(txt){
					//alert(txt);
					$("#resultados_perguntaaberta_"+id).html(txt);
					
					//SetupCampoRelacionalCampos($('#campooptions_tabela option:selected').val(), id, inc);
	});


}