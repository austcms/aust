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
 * Schema da tabela principal
 */

$modDbSchema["cadastros_conf"] = array(
    "id" => "int auto_increment",
    "tipo" => "varchar(80)",
    "chave" => "varchar(120)",
    "valor" => "varchar(120)",
    "nome" => "varchar(120)",
    "comentario" => "text",
    "descricao" => "text",
    "ref_tabela" => "varchar(120)",
    "ref_campo" => "varchar(120)",
    "ordem" => "int",
    "referencia" => "varchar(120) COMMENT 'Tabela que liga ou relaciona duas tabelas'",
    "especie" => "varchar(120)",
    "classe" => "varchar(120)",
    "necessario" => "bool",
    "restrito" => "bool",
    "publico" => "bool",
    "desativado" => "bool",
    "listagem" => "int",
    "desabilitado" => "bool",
    "bloqueado" => "bool",
    "aprovado" => "int",
    "categorias_id" => "int",
    "adddate" => "datetime",
    "autor" => "int",
    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
    )
);

?>