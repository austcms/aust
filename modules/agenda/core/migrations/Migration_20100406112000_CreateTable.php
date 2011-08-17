<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulo
 * 
 */
class Migration_20100406112000_CreateTable extends Migrations
{
	function up(){

		$schema['st_agenda'] = array(
			"id" => "int auto_increment",
			"categoria_id" => "int",
			"title" => "text COMMENT 'O título do evento.'",
			"description" => "text COMMENT 'Descrição do evento.'",
			"occurs_all_day" => "smallint COMMENT '1 se dura todo o dia.'",
			"actor_is_user" => "smallint",
			"actor_admin_id" => "int",
			"actor_admin_name" => "varchar(200)",
			"start_datetime" => "datetime",
			"end_datetime" => "datetime",
			"created_on" => "datetime",
			"updated_on" => "datetime",
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
		$this->dropTable('st_agenda');
		return true;

	}
}
?>