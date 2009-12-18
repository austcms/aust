<?php
	// TABELA
	$sql_das_tabelas['admins'] = "
		CREATE TABLE admins (
			id int NOT NULL auto_increment,
			tipo int, /* tipo é relacionado à tabela usuarios_tipos */
			nome varchar(120),
			login varchar(40),
			senha varchar(40),
			email varchar(200),
			telefone varchar(200),
			celular varchar(200),
			sexo varchar(40),
			biografia text,
			supervisionado int,
			adddate datetime not null,
			autor int null, /* autor é relacionado à tabela usuarios */
			PRIMARY KEY (id),
			UNIQUE id (id)
		)
		";
		
	$sql_das_tabelas['admins_permissions'] = "
		CREATE TABLE admins_permissions (
			id int NOT NULL auto_increment,
            admins_id int,
            admins_tipos_id int,
            categorias_id int,
            tipo varchar(80) COMMENT 'Tipo de permissão: permit, deny, etc',
			adddate datetime not null,
			autor int null, /* autor é relacionado à tabela admins */
			PRIMARY KEY (id),
			UNIQUE id (id)
		)
		";

	// TABELA
	$sql_das_tabelas['admins_tipos'] = "
		CREATE TABLE admins_tipos (
			id int NOT NULL auto_increment,
			nome varchar(120),
			nome_abrev varchar(30),
			descricao text,
			supervisionado bool,
            publico bool COMMENT 'Indica se este tipo é público (pode ser listado) ou não.',
			data datetime,
			autor int, /* autor é relacionado à tabela usuarios */
			PRIMARY KEY (id),
			UNIQUE id (id)
		)
		";
	// TABELA
	$sql_das_tabelas['categorias'] = "
		CREATE TABLE categorias (
			id int NOT NULL auto_increment,
			nome text,
			patriarca text,
			subordinadoid int,
			descricao text,
			classe varchar(200), 
			tipo varchar(200),
			tipo_legivel varchar(200),
			permissao varchar(200),
            publico bool,
			autor varchar(120),
			PRIMARY KEY (id),
			UNIQUE id (id)
		)";

	// TABELA
	$sql_das_tabelas['config'] = "
		CREATE TABLE config (
			id int NOT NULL auto_increment,
			tipo varchar(50),
			local varchar(50),
			nome text,
			propriedade varchar(100),
			valor text,
			autor varchar(120),
			PRIMARY KEY (id),
			UNIQUE id (id)
		)";

    $sql_das_tabelas['modulos'] = "
		CREATE TABLE modulos (
			id int NOT NULL auto_increment,
			tipo varchar(120),
			chave varchar(120),
			valor varchar(120),
			pasta varchar(120),
			nome varchar(120),
			descricao varchar(120),
			embed bool,
			embedownform bool,
            somenteestrutura bool,
            publico bool,
			autor int,
			PRIMARY KEY (id),
			UNIQUE id (id)
		)";
    $sql_das_tabelas['modulos_conf'] = "
		CREATE TABLE modulos_conf (
			id int NOT NULL auto_increment,
			tipo varchar(50),
			local varchar(50),
			nome text,
			propriedade varchar(100),
			valor text,
			observacao text,
			autor varchar(120),
			PRIMARY KEY (id),
			UNIQUE id (id)
		)";

    $sql_das_tabelas['imagens'] = "
		CREATE TABLE imagens (
            id int NOT NULL auto_increment,
            ordem int,
            bytes mediumint,
            dados longblob,
            nome varchar(150),
            tipo varchar(120),
            titulo text,
            ref varchar(120),
            categoria varchar(150),
            descricao text,
            classe varchar(120),
            especie varchar(120),
			adddate datetime not null,
            autor varchar(120),
            PRIMARY KEY (id),
            UNIQUE id (id)
            )
";

    $sql_registros['admins_tipos'][] = "INSERT INTO admins_tipos(nome,data,publico) VALUES('Webmaster','".date("Y-m-d H:i:s")."',0)";
	$sql_registros['admins_tipos'][] = "INSERT INTO admins_tipos(nome,publico,nome_abrev,data,descricao) VALUES('Administrador',1,'Adm','".date("Y-m-d H:i:s")."','O Administrador controla todo o site e gerencia moderadores e colaboradores. Somente administradores podem cadastrar outros usuários.');";
	$sql_registros['admins_tipos'][] = "INSERT INTO admins_tipos(nome,publico,data,descricao) VALUES('Moderador',1,'".date("Y-m-d H:i:s")."','O Moderador controla e gerencia todo o conteúdo. Não podem adicionar outros usuários ou configurar as opções.');";
	$sql_registros['admins_tipos'][] = "INSERT INTO admins_tipos(nome,publico,data,descricao) VALUES('Colaborador',1,'".date("Y-m-d H:i:s")."','O Colaborador pode somente inserir conteúdo.');";
?>