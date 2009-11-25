<?php
/*
 * TABLE SCHEMA
 */
/**
 * Contém o schema das tabelas necessárias ao sistema
 *
 * @global array $GLOBALS['dbSchema']
 * @name $dbSchema
 */
/**
 * Palavras reservadas
 *      dbSchemaTableProperties: contém informações sobre Keys serem criadas
 *      dbSchemaSQLQuery: SQLs que devem ser executados na criação da tabela
 */
/**
 * admins: Onde ficam registrados os administradores do sistema
 */
$dbSchema['admins'] = array(
    'id' => 'int auto_increment',
    'tipo' => 'int',
    'nome' => 'varchar(120)',
    'login' => 'varchar(40)',
    'senha' => 'varchar(40)',
    'email' => 'varchar(200)',
    'telefone' => 'varchar(200)',
    'celular' => 'varchar(200)',
    'sexo' => 'varchar(40)',
    'biografia' => 'text',
    'supervisionado' => 'int',
    'adddate' => 'datetime',
    'autor' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

$dbSchema['admins_permissions'] = array(
    'id' => 'int auto_increment',
    'admins_id' => 'int',
    'admins_tipos_id' => 'int',
    'categorias_id' => 'int',
    'tipo' => 'varchar(80) COMMENT \'Tipo de permissão: permit, deny, etc\'',
    'adddate' => 'datetime',
    'autor' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

$dbSchema['admins_tipos'] = array(
    'id' => 'int auto_increment',
    'nome' => 'varchar(120)',
    'nome_abrev' => 'varchar(30)',
    'descricao' => 'text',
    'supervisionado' => 'bool',
    'publico' => ' bool COMMENT \'Indica se este tipo é público (pode ser listado) ou não.\'',
    'data' => 'datetime',
    'autor' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    ),
    'dbSchemaSQLQuery' => array(
        "INSERT INTO admins_tipos(nome,data,publico) VALUES('Webmaster','".date("Y-m-d H:i:s")."',0)",
        "INSERT INTO admins_tipos(nome,publico,nome_abrev,data,descricao) VALUES('Administrador',1,'Adm','".date("Y-m-d H:i:s")."','O Administrador controla todo o site e gerencia moderadores e colaboradores. Somente administradores podem cadastrar outros usuários.')",
        "INSERT INTO admins_tipos(nome,publico,data,descricao) VALUES('Moderador',1,'".date("Y-m-d H:i:s")."','O Moderador controla e gerencia todo o conteúdo. Não podem adicionar outros usuários ou configurar as opções.')",
        "INSERT INTO admins_tipos(nome,publico,data,descricao) VALUES('Colaborador',1,'".date("Y-m-d H:i:s")."','O Colaborador pode somente inserir conteúdo.')",
    )
);


$dbSchema['categorias'] = array(
    'id' => 'int NOT NULL auto_increment',
    'nome' => 'text',
    'nome_encoded' => 'text',
    'patriarca' => 'text',
    'patriarca_encoded' => 'text',
    'subordinadoid' => 'int',
    'descricao' => 'text',
    'classe' => 'varchar(200)',
    'tipo' => 'varchar(200)',
    'tipo_legivel' => 'varchar(200)',
    'permissao' => 'varchar(200)',
    'publico' => 'bool',
    'autor' => 'varchar(120)',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);




$dbSchema['config'] = array(
    'id' => 'int NOT NULL auto_increment',
    'tipo' => 'varchar(50)',
    'local' => 'varchar(50)',
    'nome' => 'text',
    'propriedade' => 'varchar(100)',
    'valor' => 'text',
    'autor' => 'varchar(120)',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

$dbSchema['modulos'] = array(
    'id' => 'int NOT NULL auto_increment',
    'tipo' => 'varchar(120)',
    'chave' => 'varchar(120)',
    'valor' => 'varchar(120)',
    'pasta' => 'varchar(120)',
    'nome' => 'varchar(120)',
    'descricao' => 'varchar(120)',
    'embed' => 'bool',
    'embedownform' => 'bool',
    'somenteestrutura' => 'bool',
    'publico' => 'bool',
    'autor' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);
$dbSchema['modulos_conf'] = array(
    'id' => 'int NOT NULL auto_increment',
    'tipo' => 'varchar(50)',
    'local' => 'varchar(50)',
    'nome' => 'text',
    'propriedade' => 'varchar(100)',
    'valor' => 'text',
    'observacao' => 'text',
    'autor' => 'varchar(120)',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);





?>