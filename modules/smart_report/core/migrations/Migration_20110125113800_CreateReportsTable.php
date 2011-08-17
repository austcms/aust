<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulo
 * 
 */
class Migration_20110125113800_CreateReportsTable extends Migrations
{
	function up(){

		$schema['smart_reports_filters'] = array(
			"id" => "int auto_increment",
			"node_id" => "int",
			"sql_filter" => "text COMMENT 'SQL filter used to data mine other tables.'",
			"actions" => "text COMMENT 'comma divided actions.'",
			"actions_subtract" => "varchar(200) COMMENT 'format: field;quantity.'",
			"title" => "text",
			"description" => "text",
			"order_nr" => "int",

			"root_restrict" => "int DEFAULT '0' COMMENT 'Only root can access.'",
			"public" => "int DEFAULT '1'",
			"created_on" => "datetime",
			"updated_on" => "datetime",
			"admin_id" => "int",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"INDEX" => "(node_id)"
			)
		);
		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('smart_reports_filters');
		return true;

	}
}
?>