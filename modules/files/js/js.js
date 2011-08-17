/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */



/*
 * RELACIONAL UM-PARA-UM
 */
// Função que mostra os cadastros no formato <select>
function SetupCampoRelacionalTabelas(este, id, inc){
	// se for relacional um-para-um
	//alert(este.value);
	if( este.value == 'relacional_umparaum' || este.value == 'relacional_umparamuitos' ){
		
		
		
		$('#'+id+'_tabela').html('<p>Selecione o cadastro existente:</p>');
		// $('#'+id).append('<optgroup label="Escolhe uma tabela"></optgroup>');
		$('#'+id).slideDown();
		//$('#'+id+'_campo').html('<p>pooooooooo</p>');
		$('#'+id+'_campo').show();
		$.post(include_baseurl+"/js/ajax.php", {
						action: "LeCadastros"
					}, function(txt){
						//alert(txt);
						$('#'+id+'_tabela').append(
										'<div>Cadastro:<br />' +
										'<select onchange="javascript: SetupCampoRelacionalCampos(this.value, \''+id+'\', \''+inc+'\')" id="campooptions_tabela" name="relacionado_tabela_'+inc+'">' +
										'<optgroup label="Selecione um cadastro">' +
										txt +
										'</optgroup></select></div>');//<div id="\''+id+'_campo\'">\''+id+'_campo\'</div>');
						SetupCampoRelacionalCampos($('#campooptions_tabela option:selected').val(), id, inc);
					})

		//$('#'+id).html('Selecione o cadastro existente:');
		//$('#'+id).css({'font-size': '12px', 'padding': '10px', 'visibility': 'visible'});
		//$('#'+id).fadein();
		//$('#'+id).html('Escolhido');
	} else {
		$('#'+id).slideUp();
	}
}

// função para mostrar campos do cadastro
function SetupCampoRelacionalCampos(tabela, id, inc){
	// se for relacional um-para-um
	//alert(este + ' - ' +id);
//alert(id);

	//$('#'+id+'_campo').html('oi2')
	$.post(include_baseurl+"/js/ajax.php", {
					action: "LeCampos",
					tabela: tabela
				}, function(txt){
					//alert(txt);
					$('#'+id+'_campo').html('Campo:<br /><select name="relacionado_campo_'+inc+'"><optgroup label="Selecione um campo">'+txt+'</optgroup></select>');
					$('#'+id+'_campo').slideDown();
				})
				
}


