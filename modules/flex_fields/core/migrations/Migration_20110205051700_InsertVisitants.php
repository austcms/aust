<?php
class Migration_20110205051700_InsertVisitants extends Migrations
{
	function up(){

		$sql = "SELECT node_id, value, id
				FROM flex_fields_config
				WHERE 
					type='structure' AND
					(
						property='table'
					)
				";

		$fields = Connection::getInstance()->query($sql);
		foreach( $fields as $field ){
			$schema = array(
				'table' => $field['value'],
				'field' => 'visitants',
				'type' => 'int',
				'position' => 'AFTER updated_on',
				'default' => '0',
			);
			$this->addField($schema);
		}
		
		return true;
	}

	function down(){

		$sql = "SELECT node_id, value, id
				FROM flex_fields_config
				WHERE 
					type='structure' AND
					(
						property='table'
					)
				";
		
		$fields = Connection::getInstance()->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
			$this->dropField($field['value'], 'visitants');
		}

		return true;
	}

}
?>