$(document).ready(function(){
	
	/* for adding new fields */
	$('#installation #add_new_field').click(function(){
		addNewField();
	});
	
	
	
	$('#installation .fields_setup_container .field input:last').live( 'keypress', function(e){
		if( e.keyCode == 9 ){
			if( $(this).parents('div.field').find('div.field_name input').val() == '' )
				return true;
			
			addNewField();
			$('.fields_setup_container div.field:last div.field_name input:first').focus();
			return false;
		}
	});
	
});

function addNewField(){
	$('.fields_setup_container').append( $('.field_template').html() );
	$('.fields_setup_container .field:last .field_number').html( $('.fields_setup_container .field').length );
}