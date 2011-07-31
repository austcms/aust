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
    'admin_group_id' => 'int',
    'name' => 'varchar(120)',
    'login' => 'varchar(40)',
    'password' => 'varchar(40)',
    'email' => 'varchar(200)',
    'twitter' => 'varchar(250)',
    'facebook' => 'varchar(250)',
    'created_on' => 'datetime',
    'updated_on' => 'datetime',
    'is_blocked' => 'int default "0"',
    'is_deleted' => 'int default "0"',
    'admin_id' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

	$dbSchema['admin_photos'] = array(
	    'id' => 'int auto_increment',
	    'admin_id' => 'int',
		'image_type' => 'varchar(30)',
	    'title' => 'text',
	    'file_systempath' => 'text',
	    'file_path' => 'text',
	    'file_name' => 'text',
	    'file_type' => 'varchar(20)',
	    'file_size' => 'varchar(20)',
	    'created_on' => 'datetime',
	    'updated_on' => 'datetime',
	    'dbSchemaTableProperties' => array(
	        'PRIMARY KEY' => '(id)',
	        'INDEX' => '(admin_id)',
	    )
	);

$dbSchema['admin_permissions'] = array(
    'id' => 'int auto_increment',
    'admin_id' => 'int',
    'admin_group_id' => 'int',
    'node_id' => 'int',
    'type' => 'varchar(80) COMMENT \'kind of permission: permit, deny etc\'',
    'action' => 'varchar(80) COMMENT \'action: create, edit, listing etc\'',
    'created_on' => 'datetime',
    'author_id' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

$dbSchema['admin_groups'] = array(
    'id' => 'int auto_increment',
    'name' => 'varchar(120)',
    'name_abrev' => 'varchar(30)',
    'description' => 'text',
    'public' => 'int',
    'created_on' => 'datetime',
    'admin_in' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    ),
    'dbSchemaSQLQuery' => array(
        "INSERT INTO admin_groups(name,created_on,public) VALUES('Webmaster','".date("Y-m-d H:i:s")."',0)",
        "INSERT INTO admin_groups(name,public,name_abrev,created_on,description) VALUES('Administrador',1,'Adm','".date("Y-m-d H:i:s")."','Administradores têm total acesso ao gerenciar. Somente eles têm poder para criar novos usuários, alterar hierarquias e configurar o gerenciador.')",
        "INSERT INTO admin_groups(name,public,created_on,description) VALUES('Moderador',1,'".date("Y-m-d H:i:s")."','Moderadores têm poder de restringir o acesso ao gerenciamento de conteúdo e bloquear colaboradores. Ele que define que usuários podem escrever notícias, por exemplo.')",
        "INSERT INTO admin_groups(name,public,created_on,description) VALUES('Colaborador',1,'".date("Y-m-d H:i:s")."','Colaboradores podem somente gerenciar conteúdos. Não podem gerenciar pessoas nem modificar configurações importantes.')",
    )
);


$dbSchema['taxonomy'] = array(
    'id' => 'int NOT NULL auto_increment',
    'name' => 'varchar(240)',
    'name_encoded' => 'varchar(240)',
    'structure_name' => 'varchar(240)',
    'structure_name_encoded' => 'varchar(240)',
    'father_id' => 'int',
    'father_name_encoded' => 'varchar(240)',
    'description' => 'text',
    'class' => 'varchar(200)',
    'type' => 'varchar(200)',
    'editable' => 'varchar(200) default "0" COMMENT "by default, nothing is editable by normal users"',
    'visible' => 'int default "1"',
    'related_to' => 'int COMMENT "Galleries related to News, for example, have News\' id on this field"',
    'public' => 'bool',
    'admin_id' => 'varchar(120)',
    'order_nr' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
        'INDEX' => '(nome_encoded)',
        'INDEX' => '(patriarca_encoded)',
        'INDEX' => '(related_to)',
    )
);

$dbSchema['austnode_images'] = array(
    'id' => 'int NOT NULL auto_increment',
    'node_id' => 'varchar(240)',
    'father_id' => 'int',
    'subordinado_nome_encoded' => 'varchar(240)',
    'description' => 'text',
    'class' => 'varchar(200)',
    'type' => 'varchar(200)',
	'systempath' => 'text',
	'file_name' => 'varchar(200)',
	'original_file_name' => 'varchar(200)',
	'file_type' => 'varchar(25)',
	'file_size' => 'varchar(200)',
	'file_ext' => 'varchar(20)',
	'created_on' => 'datetime',
	'updated_on' => 'datetime',
	
    'editable' => 'bool default "0"',
    'public' => 'bool default "1"',
    'admin_id' => 'int',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
		'INDEX' => '(node_id)'
    )
);

$dbSchema['aust_relations'] = array(
    'slave_id' 					=> 'int',
    'slave_name' 				=> 'varchar(240)',
    'slave_name_encoded'	 	=> 'varchar(240)',
    'master_id' 				=> 'int',
    'master_name' 				=> 'varchar(240)',
    'master_name_encoded' 		=> 'varchar(240)',
	'created_on' 				=> 'datetime',
	'updated_on' 				=> 'datetime',
    'dbSchemaTableProperties' 	=> array(
        'INDEX' 	=> '(slave_id)',
        'INDEX' 	=> '(master_id)',
    )
);


$dbSchema['config'] = array(
    'id' => 'int NOT NULL auto_increment',
    'tipo' => 'varchar(50)',
    'local' => 'varchar(50)',
    'nome' => 'text',
    'propriedade' => 'varchar(100)',
    'valor' => 'text',
    'explanation' => 'text',
    'options_json' => 'text',
    'class' => 'varchar(100)',
    'ref_table' => 'varchar(100)',
    'ref_field' => 'varchar(100)',
    'autor' => 'int',
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
    'categoria_id' => 'int COMMENT "O id da categoria que recebe esta configuração."',
    'tipo' => 'varchar(50) COMMENT "Tipo de conf. Ex.: relacionamento"',
    'local' => 'varchar(50)',
    'nome' => 'text COMMENT "Um nome humano para esta configuração, se necessário"',
    'propriedade' => 'varchar(100) COMMENT "O nome da propriedade."',
    'valor' => 'text COMMENT "O valor da conf propriamente dita"',
    'observacao' => 'text',
    'autor' => 'varchar(120)',
    'dbSchemaTableProperties' => array(
        'PRIMARY KEY' => '(id)',
        'UNIQUE' => 'id (id)',
    )
);

/*
 * MIGRATIONS
 */
/*
 * Migrations -> Módulos
 */
    $dbSchema['migrations_mods'] = array(
        'version' => 'varchar(50) NOT NULL',
        'module_name' => 'varchar(254) COMMENT "Nome do módulo."',
    );

/*
 * WIDGETS
 */
    $dbSchema['widgets'] = array(
        'id' => 'int NOT NULL auto_increment',
        'name' => 'varchar(150) NOT NULL',
        'path' => 'varchar(250) NOT NULL',
        'column_nr' => 'int NOT NULL COMMENT "Número da coluna em que o widget estará."',
        'position_nr' => 'int NOT NULL COMMENT "Posição do widget na coluna"',
        'is_global' => 'int NOT NULL DEFAULT "0" COMMENT "1 se este widget é para todos os usuários."',
        'admin_id' => 'int NOT NULL COMMENT "Id do administrador atual"',
        'dbSchemaTableProperties' => array(
            'PRIMARY KEY' => '(id)',
            'UNIQUE' => 'id (id)',
        )
    );

?>