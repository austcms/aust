<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100924144200_CreatePathFields extends Migrations
{
	function up(){

		$schema = array(
			'table' => 'images',
			'field' => 'file_path',
			'type' => 'text',
			'position' => 'AFTER file_type'
		);
		$this->addField($schema);
		
		$schema = array(
			'table' => 'images',
			'field' => 'file_systempath',
			'type' => 'text',
			'position' => 'AFTER file_type'
		);
		$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('images', 'file_path');
		$this->dropField('images', 'file_systempath');
		return true;
	}
}
?>