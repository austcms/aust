<?php
/*
 * TABLE SCHEMA
 */
/**
 * Contém o schema das tabelas necessárias ao módulo
 *
/**
 * Palavras reservadas
 *      - dbSchemaTableProperties: contém informações sobre Keys serem criadas
 *      - dbSchemaSQLQuery: SQLs que devem ser executados na criação da tabela
 *
 * @global array $GLOBALS["modDbSchema"]
 * @name $modDbSchema
 */
/**
 * 
 */

$modDbSchema["galeriaimagens"] = array(
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

?>