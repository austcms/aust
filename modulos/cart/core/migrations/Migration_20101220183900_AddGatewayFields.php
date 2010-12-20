<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20101220183900_AddGatewayFields extends Migrations
{
    function up(){

        $schema = array(
            'table' => "cart",
            'field' => 'gateway_analysing',
            'type' => 'int',
            'position' => 'AFTER client_id',
			'default' => '0'
        );
    	$this->addField($schema);

        $schema = array(
            'table' => "cart",
            'field' => 'gateway_waiting',
            'type' => 'int',
            'position' => 'AFTER client_id',
			'default' => '0'
        );
    	$this->addField($schema);

        $schema = array(
            'table' => "cart",
            'field' => 'gateway_complete',
            'type' => 'int',
            'position' => 'AFTER client_id',
			'default' => '0'
        );
    	$this->addField($schema);

        $schema = array(
            'table' => "cart",
            'field' => 'gateway_cancelled',
            'type' => 'int',
            'position' => 'AFTER client_id',
			'default' => '0'
        );
    	$this->addField($schema);

        return true;
    }

    function down(){

        $this->dropField('cart', 'gateway_analysing');
        $this->dropField('cart', 'gateway_waiting');
        $this->dropField('cart', 'gateway_cancelled');
        $this->dropField('cart', 'gateway_complete');
		
        return true;

    }
}
?>