var linkInputName;
var originalLinkValue;

// arquivos flash não podem ter link
function validateFile(){
	var este = $('input[name=frmarquivo]');
	var isFlash = false;
	
	var filename = $(este).val();
	
	if( filename.substring(filename.length-3) == 'swf' )
		isFlash = true;
	else if( filename == '' && fileMimeType == 'application/x-shockwave-flash' ){
		isFlash = true;
 		fileMimeType = '';
	}

	var flashAlertStr = 'Arquivo Flash não possui link.';
	
	if( $(este).attr('name') != '' )
		linkInputName = $('#link').attr('name');

	originalLinkValue = '';
	if( $('#link').val() != flashAlertStr ){
		originalLinkValue = $('#link').val();
	}
		
//	window.status = 'origin:'+originalLinkValue;

	$('p.link_explanation').hide();
	// é flash
	if( isFlash ){
		$('#link').attr('disabled', 'disabled').val( flashAlertStr );
		$('#post_link').html('<input type="hidden" name="'+linkInputName+'" value="" />');
		
		$('#explanation_link_file_is_flash').hide().show();
	} else {
		$('#explanation_link_file_is_image').hide().show();
		
		$('#post_link input[name='+linkInputName+']').remove();
		
		if( $('#link').val() == flashAlertStr )
			$('#link').val(originalLinkValue);
		
		$('#link').attr('disabled', '');
	}
//	window.status = isFlash+ ' - ' + (filename.substring(filename.length-3) == 'swf') + ' - '+mimeType;

}
