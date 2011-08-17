<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20110615121200_AddObservationPaidOnField extends Migrations
{
	function up(){

		$schema = array(
			'table' => "st_orders",
			'field' => 'paid_on',
			'type' => 'datetime',
			'position' => 'AFTER scheduled_on'
		);
			$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('st_orders', 'paid_on');
		return true;
	}
}
?>