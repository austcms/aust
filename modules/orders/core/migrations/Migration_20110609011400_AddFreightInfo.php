<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20110609011400_AddFreightInfo extends Migrations
{
	function up(){

		$schema = array(
			'table' => "st_orders",
			'field' => 'freight_service',
			'type' => 'varchar(100)',
			'position' => 'AFTER gateway_analysing',
						'default' => '0'
		);
			$this->addField($schema);

		$schema = array(
			'table' => "st_orders",
			'field' => 'freight_value',
			'type' => 'decimal(13,2)',
			'position' => 'AFTER freight_service',
						'default' => '0'
		);
			$this->addField($schema);

		return true;
	}

	function down(){
		$this->dropField('st_orders', 'freight_service');
		$this->dropField('st_orders', 'freight_value');
		return true;
	}
}
?>