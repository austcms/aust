/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */

// Função que mostra os cadastros no formato <select>
function addCommentInImage(id){
	//alert($("#image_comment_button_"+id).val());

	var buttonText = $("#image_comment_button_"+id).attr('value');
	$("#image_comment_button_"+id).attr('disabled', 'disabled');
	$("#image_comment_button_"+id).attr('value', 'Salvando...');
	
	var comment = $('#comment_'+id).val();
	$('#comment_'+id).attr('disabled', 'disabled');
	
	$.post(
		include_baseurl+"/js/ajax.php",
		{ action: "saveImageComment", comment: comment, id: id },
		function(txt){
			if(txt == 0){
				alert('Comentário não foi salvo. Tente novamente.');
			}
				$("#image_comment_button_"+id).attr('value', buttonText);
				$("#image_comment_button_"+id).attr('disabled', '');
				$('#image_comment_input_'+id).hide();
				$('#image_comment_text_'+id).html(comment);
				$('#comment_'+id).attr('disabled', '');
				$('#comment_'+id).val( comment )
				$('#image_comment_input_'+id).hide();
				$('#image_comment_icon_'+id).show();

			//$('#agenda_start_datetime').html(txt);
			//SetupCampoRelacionalCampos($('#campooptions_tabela option:selected').val(), id, inc);
		})

}

