<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulo
 * 
 */
class Migration_20101224023400_CreateLocationField extends Migrations
{
	function up(){
		$schema = array(
			'table' => 'st_agenda',
			'field' => 'place',
			'type' => 'text',
			'position' => 'AFTER description'
		);
		$this->addField($schema);

		return true;
	}
	
	function down(){
		$this->dropField('st_agenda', 'place');
		return true;
	}
}
?>