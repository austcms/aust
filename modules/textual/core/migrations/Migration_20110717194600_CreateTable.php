<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20110717194600_CreateTable extends Migrations
{
	function up(){

		$schema['textual'] = array(
			"id" => "int auto_increment",
			'order_nr' => "int DEFAULT '5'",
			"node_id" => "int",
			"title" => "text",
			"title_encoded" => "text COMMENT 'same title, only alpha_numeric and underlines.'",
			"subtitle" => "text",
			"summary" => "text",
			"text" => "text",
			"local" => "varchar(200)",
			"url" => "varchar(200)",
			"pageviews" => "int DEFAULT '0' ",
			"public" => "int",
			"blocked" => "int",
			"approved" => "int",
			"created_on" => "datetime",
			"admin_id" => "int",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
				//'foreign key' => '(categoria) references categorias(id)'
			)
		);
		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('textual');
		return true;

	}
}
?>