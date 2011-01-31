<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120105500_CriarTabela extends Migrations
{
    function up(){

        $schema["galeriaimagens"] = array(
            "id" => "int auto_increment",
            "ordem" => "int",
            "bytes" => "mediumint",
            "dados" => "longblob",
            "nome" => "varchar(150)",
            "tipo" => "varchar(150)",
            "titulo" => "text",
            "ref" => "varchar(120)",
            "categoria" => "varchar(150)",
            "descricao" => "text",
            "classe" => "varchar(120)",
            "especie" => "varchar(120)",
            "visivel" => "bool",
            "adddate" => "datetime",
            "autor" => "int",
            "dbSchemaTableProperties" => array(
                "PRIMARY KEY" => "(id)",
                "UNIQUE" => "id (id)",
            )
        );
        $this->createTable( $schema );

        return true;
    }

    function down(){
        $this->dropTable('galeriaimagens');
        return true;
    }
}
?>