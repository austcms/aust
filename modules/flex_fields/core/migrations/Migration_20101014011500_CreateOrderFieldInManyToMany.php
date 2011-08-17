<?php
class Migration_20101014011500_CreateOrderFieldInManyToMany extends Migrations
{
	function up(){

		$sql = "SELECT *
				FROM flex_fields_config
				WHERE 
					type='field' AND
					(
						specie='relacional_umparamuitos' OR
						specie='relational_onetomany'
					)
				";
		
		$tables = Connection::getInstance()->query($sql);
		
		foreach( $tables as $table ){
			$schema = array(
				'table' => $table['reference'],
				'field' => 'order_nr',
				'type' => 'int',
				'position' => 'AFTER updated_on'
			);
			$this->addField($schema);
		}

		return true;
	}

	function down(){

		$sql = "SELECT *
				FROM flex_fields_config
				WHERE 
					type='field' AND
					(
						especie='relacional_umparamuitos' OR
						especie='relational_onetomany'
					)
				";
		
		$tables = Connection::getInstance()->query($sql);
		
		foreach( $tables as $table ){
			$this->dropField($table['reference'], 'order_nr');
		}

		return true;
	}

}
?>