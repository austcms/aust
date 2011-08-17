<?php
class Migration_20100120105600_CreateTable extends Migrations
{
	function up(){

		$schema['images'] = array(
			'id' => 'int NOT NULL auto_increment',
			'node_id' => "int",
			"title" => "text",
			"title_encoded" => "text",
			"subtitle" => "text",
			"summary" => "text",
			"description" => "text",
			"link" => "blob",
			'order_nr' => 'int',
			'file_bytes' => 'mediumint',
			'file_binary_data' => 'longblob',
			'file_name' => 'varchar(150)',
			'file_type' => 'varchar(120)',
			'reference' => 'varchar(50)',
			'reference_id' => 'int',
			'local' => "varchar(60)",
			'class' => 'varchar(60)',
			'specie' => 'varchar(60)',
			'expire_on' => 'date not null',
			'created_on' => 'date not null',
			'updated_on' => 'date not null',
			'pageviews' => 'int',
			'admin_id' => 'int',
			'dbSchemaTableProperties' => array(
				'PRIMARY KEY' => '(id)',
				'UNIQUE' => 'id (id)',
			)
		);

		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('images');
		return true;
	}
}
?>