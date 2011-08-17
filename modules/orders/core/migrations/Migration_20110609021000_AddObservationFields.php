<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20110609021000_AddObservationFields extends Migrations
{
	function up(){

		$schema = array(
			'table' => "st_orders",
			'field' => 'observation',
			'type' => 'text',
			'position' => 'AFTER sent'
		);
			$this->addField($schema);

		$schema = array(
			'table' => "st_order_items",
			'field' => 'observation',
			'type' => 'text',
			'position' => 'AFTER quantity_unit'
		);
			$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('st_orders', 'observation');
		$this->dropField('st_order_items', 'observation');
		return true;
	}
}
?>