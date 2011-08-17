/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */
var searchDelay;
function cadastroSearch(este, austNode){

	clearTimeout(searchDelay);
	$('.content_search #loading_image').show();
	searchDelay = window.setTimeout(function(){
		cadastroSearchAfterDelay(este, austNode);
	}, 350);
}

function cadastroSearchAfterDelay(este, austNode){
	if( searchDelay ){
		$.post(
			include_baseurl+"/js/ajax.php",
			{
				action: "search",
				query: $(este).val(),
				austNode: austNode,
				field: $("#search_field").val()
			},
			function(txt){
				//alert(txt);
				$('#listing_table').html(txt);
				$('.content_search #loading_image').hide();
				//SetupCampoRelacionalCampos($('#campooptions_tabela option:selected').val(), id, inc);
			});
	}
}


