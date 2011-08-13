function editGroup(id){
	$('#edit_group input[name=id]').val(id);
	
	$('#edit_group input[name=name]').val(groups[id].name);
	$('#edit_group textarea[name=description]').val(groups[id].description);
	$('#edit_group').show();
	
}
