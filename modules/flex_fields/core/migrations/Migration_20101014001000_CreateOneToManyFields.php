<?php
class Migration_20101014001000_CreateOneToManyFields extends Migrations
{
	function up(){

		$schema = array(
			'table' => 'flex_fields_config',
			'field' => 'ref_child_field',
			'type' => 'VARCHAR(60)',
			'position' => 'AFTER ref_field'
		);
		$this->addField($schema);

		$schema = array(
			'table' => 'flex_fields_config',
			'field' => 'ref_parent_field',
			'type' => 'VARCHAR(60)',
			'position' => 'AFTER ref_field'
		);
		$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('flex_fields_config', 'ref_parent_field');
		$this->dropField('flex_fields_config', 'ref_child_field');
		return true;
	}

}
?>