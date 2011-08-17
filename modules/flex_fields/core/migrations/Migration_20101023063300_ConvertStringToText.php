<?php
class Migration_20101023063300_ConvertStringToText extends Migrations
{
	function up(){

		$sql = "SELECT node_id, property, id
				FROM flex_fields_config
				WHERE 
					type='field' AND
					(
						specie='string'
					)
				";
		
		$fields = Connection::getInstance()->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
			$sql = "SELECT value
					FROM flex_fields_config
					WHERE 
						type='structure' AND
						(
							property='table'
						)
					";
			$query = Connection::getInstance()->query($sql);
			$tableName = reset( $query );
			$tableName = $tableName['value'];
			
			$describe = Connection::getInstance()->describeTable($tableName, true);
			
			if( $describe[$field['key']]['Type'] == 'text' ){
				$sqls[] = "UPDATE flex_fields_config SET specie='text' WHERE id='".$field['id']."'";
			}
			
		}

		foreach( $sqls as $sql ){
			Connection::getInstance()->exec($sql);
		}
		
		return true;
	}

	function down(){

		$sql = "SELECT node_id, property, id
				FROM flex_fields_config
				WHERE 
					type='field' AND
					(
						specie='text'
					)
				";
		
		$fields = Connection::getInstance()->query($sql);
		
		$sqls = array();
		foreach( $fields as $field ){
			$sql = "SELECT value
					FROM flex_fields_config
					WHERE 
						type='structure' AND
						(
							property='table'
						)
					";
			$tableName = reset( Connection::getInstance()->query($sql) );
			$tableName = $tableName['value'];
			
			$describe = Connection::getInstance()->describeTable($tableName, true);
			
			if( $describe[$field['key']]['Type'] == 'varchar(250)' ){
				$sqls[] = "UPDATE flex_fields_config SET specie='string' WHERE id='".$field['id']."'";
			}
			
		}
		
		foreach( $sqls as $sql ){
			Connection::getInstance()->exec($sql);
		}
		return true;
	}

}
?>