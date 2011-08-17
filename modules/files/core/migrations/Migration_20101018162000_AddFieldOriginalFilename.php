<?php
class Migration_20101018162000_AddFieldOriginalFilename extends Migrations
{
	function up(){

		$schema = array(
			'table' => 'files',
			'field' => 'original_filename',
			'type' => 'VARCHAR(250)',
			'position' => 'AFTER url'
		);
		$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('files', 'original_filename');
		return true;
	}
}
?>