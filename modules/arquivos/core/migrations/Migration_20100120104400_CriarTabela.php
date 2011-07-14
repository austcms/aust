<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120104400_CriarTabela extends Migrations
{
    function up(){

        $schema["arquivos"] = array(
            "id" => "int auto_increment",
            "categoria_id" => "int",
            "titulo" => "varchar(250)",
            "descricao" => "text",
            "local" => "varchar(80)",
            "url" => "text",
            "arquivo_nome" => "varchar(250)",
            "arquivo_tipo" => "varchar(250)",
            "arquivo_tamanho" => "varchar(250)",
            "arquivo_extensao" => "varchar(10)",
            "referencia" => "varchar(120)",
            "visitantes" => "int NOT NULL DEFAULT '0'",
            "bloqueado" => "varchar(120)",
            "aprovado" => "int",

            "dbSchemaTableProperties" => array(
                "PRIMARY KEY" => "(id)",
                "UNIQUE" => "id (id)",
            )
        );
        $this->createTable( $schema );

        return true;
    }

    function down(){
        $this->dropTable('arquivos');
        return true;

    }
}
?>