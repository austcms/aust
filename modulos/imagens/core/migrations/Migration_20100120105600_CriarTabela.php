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

        $schema['imagens'] = array(
            'id' => 'int NOT NULL auto_increment',
            'categoria' => "int",
            "titulo" => "text COMMENT 'O título do registro que será mostrado para humanos.'",
            "titulo_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
            "subtitulo" => "text",
            "resumo" => "text",
            "texto" => "text COMMENT 'Texto ou descrição.'",
            "link" => "blob",
            'ordem' => 'int',
            'bytes' => 'mediumint',
            'dados' => 'longblob',
            'nome' => 'varchar(150)',
            'tipo' => 'varchar(120)',
            'ref' => 'varchar(50)',
            'ref_id' => 'int',
            'local' => "varchar(60) COMMENT 'Caso seja necessário especificar um local, seja para amostragem ou não.'",
            'classe' => 'varchar(60)',
            'especie' => 'varchar(60)',
            'adddate' => 'date not null',
            'expiredate' => 'date not null',
            'visitantes' => 'int',
            'autor' => 'int',
            'dbSchemaTableProperties' => array(
                'PRIMARY KEY' => '(id)',
                'UNIQUE' => 'id (id)',
            )
        );

        $this->createTable( $schema );

        return true;
    }

    function down(){
        $this->dropTable('imagens');
        return true;
    }
}
?>