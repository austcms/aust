<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120105200_CreateTable extends Migrations
{
	function up(){

		$schema['photo_gallery'] = array(
			'id' => 'int NOT NULL auto_increment',
			'node_id' => "int",
			"title" => "text",
			"title_encoded" => "text",
			"subtitle" => "text",
			"summary" => "text",
			"text" => "text",
			"link" => "blob",
			'order_nr' => 'int',
			'ref' => 'varchar(50)',
			'ref_id' => 'int',
			'local' => "varchar(60)",
			'class' => 'varchar(60)',
			'specie' => 'varchar(60)',
			'created_on' => 'datetime',
			'updated_on' => 'datetime',
			'expire_on' => 'datetime',
			'pageviews' => 'int',
			'admin_id' => 'int',
			'dbSchemaTableProperties' => array(
				'PRIMARY KEY' => '(id)',
				'UNIQUE' => 'id (id)',
				'INDEX' => '(ref_id)',
				
			)
		);

		/*
		 * Imagens criadas são relacionadas a uma galeria
		 */
		$schema['photo_gallery_images'] = array(
			'id' => 'int NOT NULL auto_increment',
			'gallery_id' => "int",
			'order_nr' => "int default '1'",
			"title" => "text",
			"title_encoded" => "text",
			"subtitle" => "text",
			"summary" => "text",
			"text" => "text",
			"link" => "blob",
			'file_bytes' => 'mediumint',
			'file_binary_data' => 'longblob',
			'file_name' => 'varchar(150)',
			'file_type' => 'varchar(120)',
			'ref' => 'varchar(50)',
			'ref_id' => 'int',
			'is_cover' => "tinyint",
			'local' => "varchar(60)",
			'class' => 'varchar(60)',
			'specie' => 'varchar(60)',
			'created_on' => 'datetime',
			'updated_on' => 'datetime',
			'expire_on' => 'datetime',
			'pageviews' => 'int',
			'admin_id' => 'int',
			'dbSchemaTableProperties' => array(
				'PRIMARY KEY' => '(id)',
				'UNIQUE' => 'id (id)',
				'INDEX' => '(gallery_id)',
				'INDEX' => '(ref_id)',
			)
		);
		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('photo_gallery');
		$this->dropTable('photo_gallery_images');
		return true;

	}
}
?>