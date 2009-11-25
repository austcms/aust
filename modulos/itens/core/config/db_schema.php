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

$modDbSchema["textos"] = array(
    "id" => "int auto_increment",
    "categoria" => "int",
    "titulo" => "text COMMENT 'O título do texto que será mostrado para humanos.'",
    "titulo_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
    "subtitulo" => "text",
    "resumo" => "text",
    "texto" => "text",
    "local" => "varchar(200)",
    "url" => "varchar(200)",
    "tipo" => "varchar(120)",
    "tiporef" => "varchar(120)",
    "classe" => "varchar(120)",
    "especie" => "varchar(120)",
    "referencia" => "varchar(120)  COMMENT 'Referência a outro conteúdo (interno ou externo). Pode servir como base para redirects.'",
    "visitantes" => "int DEFAULT '0' ",
    "restrito" => "varchar(120)",
    "publico" => "varchar(120)",
    "bloqueado" => "varchar(120)",
    "aprovado" => "int",
    "adddate" => "datetime",
    "autor" => "int",
    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
    )
);

?>