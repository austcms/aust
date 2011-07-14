<?php
/*
 * TABLE SCHEMA
 */
/**
 * Contém o schema das tabelas necessárias ao módulo
 *
 *
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
$modDbSchemaVersion = "1";


$modDbSchema['imagens'] = array(
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
        'foreign key' => '(categoria) references categorias(id)'
    )
);


?>