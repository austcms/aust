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
    if( este.value == 'relational_onetoone' || este.value == 'relational_onetomany' ){
        
        
        
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

// ao clicar em um campo de imagem, pega a imagem e bota no lightbox
function editImageInLightbox(este, imageId, field){
	var id = $( este ).attr('id');
	var src =$("#"+id+" img").attr('src');
	$("#lb_image").attr('src', src+"&minxsize=&maxxsize=250&maxysize=250");
	$("#lightbox-panel input[name=image_id]").val(imageId);
	$("#lightbox-panel input[name=image_field]").val(field);
	
	var description = $("input[name=image_description_"+imageId+"]").val();
	$("#lightbox-panel #image_description").val( description );
	
	var secondaryId = $("input[name=image_secondaryid_"+imageId+"]").val();
	if( secondaryId > 0 ){
		$("div#secondary_image_form").show();
		$("img[name=secondary_image]").attr('src', imagesPath+secondaryId+"&maxxsize=90&maxysize=90");
		$("a#del_secondary_image").attr('data-secondaryid', secondaryId);
		$("a#del_secondary_image").show();
		$("p#missing_secondary_image").hide();
	} else {
		$("div#secondary_image_form").hide();
		$("img[name=secondary_image]").attr('src', '');
		$("a#del_secondary_image").hide();
		$("p#missing_secondary_image").show();
	}
	
}
