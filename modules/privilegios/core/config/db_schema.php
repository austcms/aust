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
$modDbSchemaVersion = 0.1;

$modDbSchema["privilegios"] = array(
    "id" => "int auto_increment",
    "categoria_id" => "int",
    "titulo" => "text COMMENT 'O título do privilégio.'",
    "titulo_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
    "descricao" => "text",
    "local" => "varchar(200)",
    "url" => "varchar(200)",
    "type" => "varchar(120)",
    "created_on" => "date",
    "updated_on" => "date",
    "admin_id" => "int",
    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
        'foreign key' => '(categoria_id) references categorias(id)',
        'foreign key' => '(admin_id) references admins(id)'
    )
);

$modDbSchema["privilegio_agent"] = array(
    "id" => "int auto_increment",
    "agent_id" => "int",
    "privilegio_id" => "int",
    "table" => "varchar(50) COMMENT 'Nome da tabela de usuários.'",
    "observacao" => "text",
    "type" => "varchar(120)",
    "expires_on" => "date",
    "created_on" => "date",
    "updated_on" => "date",
    "admin_id" => "int",
    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
        'foreign key' => '(privilegio_id) references privilegios(id)',
        'foreign key' => '(admin_id) references admins(id)'
    )
);

$modDbSchema["privilegio_target"] = array(
    "id" => "int auto_increment",
    "target_id" => "int COMMENT 'id do conteúdo alvo'",
    "privilegio_id" => "int COMMENT 'id do privilegio alvo'",
    "target_table" => "varchar(50) COMMENT 'Nome da tabela de usuários.'",
    "type" => "varchar(30) COMMENT 'pode ser conteúdo único, categoria do site ou site'",
    "expires_on" => "date",
    "created_on" => "date",
    "updated_on" => "date",
    "admin_id" => "int",
    "dbSchemaTableProperties" => array(
        "PRIMARY KEY" => "(id)",
        "UNIQUE" => "id (id)",
        'foreign key' => '(privilegio_id) references privilegios(id)',
        'foreign key' => '(admin_id) references admins(id)'
    )
);

        //$this->tabela_criar = "privilegios_conf";
        /*
        // sql das tabelas
        $this->db_tabelas[] = "privilegios_conf";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_conf (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				chave varchar(120) {$charset},
				valor text {$charset},
				nome varchar(120) {$charset},
				comentario text {$charset},
				descricao text {$charset},
				ref_tabela varchar(120) {$charset},
				ref_campo varchar(120) {$charset},
				referencia varchar(120) {$charset},
				especie varchar(120) {$charset} COMMENT 'Se é específico de um módulo ou não (ex: privilégio do módulo texto)',
				classe varchar(120) {$charset} COMMENT 'Se é padrão do sistema ou não',
				necessario bool,
				restrito bool,
				publico bool,
				desativado bool,
				desabilitado bool,
				bloqueado bool,
				aprovado int,
				categorias_id int,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";




        $this->db_tabelas[] = "privilegios_de_conteudos";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_de_conteudos (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				privilegios_conf_id varchar(80) {$charset},
                conteudo_tabela varchar(120) {$charset},
                conteudo_id int,
                expiradate datetime,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";



        $this->db_tabelas[] = "privilegios_de_usuarios";
        $this->sql_das_tabelas[] = "
			CREATE TABLE privilegios_de_usuarios (
				id int NOT NULL auto_increment,
				tipo varchar(80) {$charset},
				privilegios_conf_id varchar(80) {$charset},
                usuario_tabela varchar(120) {$charset},
                usuario_id int,
                expiradate datetime,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
            ";

        $this->sql_registros[] = "INSERT INTO privilegios_conf(tipo,chave,valor,classe) VALUES ('grupo','nome','Cadastrados','padrão')";
        */





?>