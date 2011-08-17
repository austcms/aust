/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */

var imageHasDescription = new Array();
var imageHasLink = new Array();
var imageHasSecondaryImage = new Array();
var imageHasLightbox = new Array();


/*
 * RELACIONAL UM-PARA-UM
 */
// Função que mostra os cadastros no formato <select>
function SetupCampoRelacionalTabelas(este, id, inc){
	// se for relacional um-para-um
	if( este.value == 'relational_onetoone' || este.value == 'relational_onetomany' ){
		
		
		
		$('#'+id+'_tabela').html('<p>Selecione o cadastro existente:</p>');
		$('#'+id).slideDown();
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

	} else {
		$('#'+id).slideUp();
	}
}

// função para mostrar campos do cadastro
function SetupCampoRelacionalCampos(tabela, id, inc){
	$.post(include_baseurl+"/js/ajax.php", {
					action: "LeCampos",
					tabela: tabela
				}, function(txt){
					//alert(txt);
					$('#'+id+'_campo').html('Campo:<br /><select name="relacionado_campo_'+inc+'"><optgroup label="Selecione um campo">'+txt+'</optgroup></select>');
					$('#'+id+'_campo').slideDown();
				})
				
}

// ao clicar em um campo de imagem, pega a imagem e bota no lightbox
function editImageInLightbox(este, imageId, field){
	var id = $( este ).attr('id');
	var src =$("img[name="+id+"]").attr('src');
	$("#lb_image").attr('src', src+"&minxsize=&maxxsize=250&maxysize=250");
	$("#lightbox-panel input[name=image_id]").val(imageId);
	$("#lightbox-panel input[name=image_field]").val(field);
	
	// imagem tem descrição?
	if( imageHasDescription[id] == "1" )
		$("#lightbox-panel div.description").show();
	else
		$("#lightbox-panel div.description").hide();
	
	// imagem tem link?
	if( imageHasLink[id] == "1" )
		$("#lightbox-panel div.link").show();
	else
		$("#lightbox-panel div.link").hide();
	
	// imagem tem imagem secundária?
	if( imageHasSecondaryImage[id] == "1" )
		$("#lightbox-panel div.secondary_image").show();
	else
		$("#lightbox-panel div.secondary_image").hide();
			
	/*
	 * ajusta valores dos inputs lightbox
	 */
	// descrição
	var description = $("input[name=image_description_"+imageId+"]").val();
	$("#lightbox-panel #image_description").val( description );
	
	// link
	var link = $("input[name=image_link_"+imageId+"]").val();
	$("#lightbox-panel #image_link").val( link );
	
	// secondaryId
	var secondaryId = $("input[name=image_secondaryid_"+imageId+"]").val();
	if( secondaryId > 0 ){ // tem imagem secundaria
		alert( secondaryId );
		$("div#secondary_image_form").show();
		$("img[name=secondary_image]").attr('src', imagesPath+secondaryId+"&maxxsize=90&maxysize=90");
		$("a#del_secondary_image").attr('data-secondaryid', secondaryId);
		$("a#del_secondary_image").show();
		$("p#missing_secondary_image").hide();
	} else {
		$("div#secondary_image_form").show();
		$("div#secondary_image_actual").hide();
		$("img[name=secondary_image]").attr('src', '');
		$("p#missing_secondary_image").show();
	}
	
}

/* search1n */
function search1n(_this){
	var field = $(_this).attr('data-field');
	var austNode = $(_this).attr('data-austnode');
	var checked_boxes = $('#search1n_'+field+'_result input[type=checkbox]:checked').serialize().replace(/%5B/g, '[').replace(/%5D/g, ']');
	
	$.post(
		include_baseurl+"/js/ajax.php?"+checked_boxes,
		{
			query: $(_this).val(),
			field: field,
			austNode: austNode,
			w: $(_this).attr('data-w'),
//			checked_boxes: ,
			relational_table: $(_this).attr('data-relational_table'),
			table: $(_this).attr('data-table'),
			ref_table: $(_this).attr('data-ref_table'),
			ref_field: $(_this).attr('data-ref_field'),
			inputName: $(_this).attr('data-input_name'),
			childField: $(_this).attr('data-child_field'),
			parentField: $(_this).attr('data-parent_field'),
			action: 'search1n'
		},
		function(response){
			$('#search1n_'+field+'_result input[type=checkbox]')
				.not(':checked, .original').parent().parent().remove();
				
			$('#search1n_'+field+'_result p.explanation').remove();
				
			if( response == '' ){
				$('#search1n_'+field+'_result').append('<p class="explanation">Termo não encontrado</p>');
			} else {
			
				$('#search1n_'+field+'_result').append(response);
			}
				
		}
	);
}
