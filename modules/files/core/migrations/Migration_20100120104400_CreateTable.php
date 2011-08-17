<?php
class Migration_20100120104400_CreateTable extends Migrations
{
	function up(){

		$schema["files"] = array(
			"id" => "int auto_increment",
			"node_id" => "int",
			"title" => "varchar(250)",
			"description" => "text",
			"local" => "varchar(80)",
			"url" => "text",
			"file_name" => "varchar(250)",
			"file_type" => "varchar(250)",
			"file_size" => "varchar(250)",
			"file_extension" => "varchar(10)",
			"reference" => "varchar(120)",
			"pageviews" => "int NOT NULL DEFAULT '0'",
			"blocked" => "varchar(120)",
			"approved" => "int",

			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
			)
		);
		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('files');
		return true;

	}
}
?>