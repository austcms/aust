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

$modDbSchema["arquivos"] = array(
    "id" => "int auto_increment",

    "categoria_id" => "int",
    "titulo" => "varchar(120)",
    "titulo_encoded" => "varchar(120)",
    "descricao" => "text",
    "local" => "varchar(80)",
    "url" => "text",
    "systemurl" => "text",
    "arquivo_nome" => "varchar(250)",
    "arquivo_tipo" => "varchar(250)",
    "arquivo_tamanho" => "varchar(250)",
    "arquivo_extensao" => "varchar(10)",
    "referencia" => "varchar(120)",
    "visitantes" => "int NOT NULL DEFAULT '0'",
    "bloqueado" => "varchar(120)",
    "aprovado" => "int",
    "created_on" => "datetime",
    "update_on" => "datetime",
    "admin_id" => "int",

    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
        'foreign key' => '(categoria_id) references categorias(id)'
    )
);


?>