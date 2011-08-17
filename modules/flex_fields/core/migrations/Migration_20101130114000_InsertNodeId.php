<?php
class Migration_20101130114000_InsertNodeId extends Migrations
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
				'field' => 'node_id',
				'type' => 'int',
				'position' => 'AFTER id'
			);
			$this->addField($schema);
		}
		
		return true;
	}

	function down(){

		$sql = "SELECT node_id, valor, id
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
			$this->dropField($field['value'], 'node_id');
		}

		return true;
	}

}
?>