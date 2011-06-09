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
            'type' => 'int',
            'position' => 'AFTER gateway_analysing',
						'default' => '0'
        );
    		$this->addField($schema);

        $schema = array(
            'table' => "st_orders",
            'field' => 'freight_value',
            'type' => 'int',
            'position' => 'AFTER freight_service',
						'default' => '0'
        );
    		$this->addField($schema);

        return true;
    }

    function down(){

        $this->dropField('st_orders', 'gateway_analysing');
        $this->dropField('st_orders', 'gateway_waiting');
        $this->dropField('st_orders', 'gateway_cancelled');
        $this->dropField('st_orders', 'gateway_complete');
		
        return true;

    }
}
?>