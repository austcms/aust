<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120105000_CreatesTable extends Migrations
{
	function up(){
		$schema["flex_fields_config"] = array(
			"id" => "int auto_increment",
			"node_id" => "int",
			"type" => "varchar(80)",
			"property" => "varchar(120)",
			"value" => "varchar(120)",
			"name" => "varchar(120)",
			"commentary" => "text",
			"description" => "text",
			"ref_table" => "varchar(120)",
			"ref_field" => "varchar(120)",
			"order_nr" => "int",
			"reference" => "varchar(120) COMMENT 'Table which links two other tables'",
			"specie" => "varchar(120)",
			"class" => "varchar(120)",
			"needed" => "bool",
			"restricted" => "bool",
			"public" => "bool",
			"deactivated" => "bool",
			"disabled" => "bool",
			"blocked" => "bool",
			"listing" => "bool",
			"approved" => "int",
			"created_on" => "datetime",
			"admin_id" => "int",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
			)
		);
		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('flex_fields_config');
		return true;

	}
}
?>