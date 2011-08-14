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
            "title" => "text COMMENT 'O título do registro que será mostrado para humanos.'",
            "title_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
            "subtitle" => "text",
            "summary" => "text",
            "text" => "text COMMENT 'Texto ou descrição.'",
            "link" => "blob",
            'order_nr' => 'int',
            'ref' => 'varchar(50)',
            'ref_id' => 'int',
            'local' => "varchar(60) COMMENT 'Caso seja necessário especificar um local, seja para amostragem ou não.'",
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
            "title" => "text COMMENT 'O título do registro que será mostrado para humanos.'",
            "title_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
            "subtitle" => "text",
            "summary" => "text",
            "text" => "text COMMENT 'Texto ou descrição.'",
            "link" => "blob",
            'image_bytes' => 'mediumint',
            'image_binary_data' => 'longblob',
            'image_name' => 'varchar(150)',
            'image_type' => 'varchar(120)',
            'ref' => 'varchar(50)',
            'ref_id' => 'int',
            'is_cover' => "tinyint COMMENT 'É uma imagem de capa da galeria atual? 1=sim, 0=não'",
            'local' => "varchar(60) COMMENT 'Caso seja necessário especificar um local, seja para amostragem ou não.'",
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