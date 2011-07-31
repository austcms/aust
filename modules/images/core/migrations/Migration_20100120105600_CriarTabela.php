<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120105600_CriarTabela extends Migrations
{
    function up(){

        $schema['images'] = array(
            'id' => 'int NOT NULL auto_increment',
            'node_id' => "int",
            "title" => "text COMMENT 'O título do registro que será mostrado para humanos.'",
            "title_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
            "subtitle" => "text",
            "summary" => "text",
            "description" => "text COMMENT 'Texto ou descrição.'",
            "link" => "blob",
            'order_nr' => 'int',
            'image_bytes' => 'mediumint',
            'image_binary_data' => 'longblob',
            'image_name' => 'varchar(150)',
            'image_type' => 'varchar(120)',
            'reference' => 'varchar(50)',
            'reference_id' => 'int',
            'local' => "varchar(60) COMMENT 'Caso seja necessário especificar um local, seja para amostragem ou não.'",
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