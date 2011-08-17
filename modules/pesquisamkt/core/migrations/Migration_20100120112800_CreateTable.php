<?php
class Migration_20100120112800_CreateTable extends Migrations
{
	function up(){

		$modDbSchema["pesqmkt"] = array(
			"id" => "int auto_increment",
			"categoria" => "int",
			"tipo" => "varchar(80) COMMENT 'O tipo pode ser _enquete_, _pesqmkt_, entre outros.'",
			"ordem" => "varchar(10)",
			"titulo" => "text COMMENT 'O título do conteúdo que será mostrado para humanos.'",
			"titulo_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
			"subtitulo" => "text",
			"resumo" => "text",
			"texto" => "text",
			"local" => "varchar(200)",
			"url" => "varchar(200)",
			"classe" => "varchar(80)",
			"especie" => "varchar(80)",
			"referencia" => "varchar(120) COMMENT 'Referência a outro conteúdo (interno ou externo). Pode servir como base para redirects.'",
			"visitantes" => "int DEFAULT '0' ",
			"restrito" => "varchar(120)",
			"publico" => "varchar(120)",
			"bloqueado" => "varchar(120)",
			"ativo" => "char(1) default '0'",
			"aprovado" => "int",
			"adddate" => "datetime",
			"expiredate" => "date",
			"autor" => "int",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
				'foreign key' => '(categoria) references categorias(id)'
			)
		);

		$modDbSchema["pesqmkt_perguntas"] = array(
			"id" => "int auto_increment",
			"pesqmkt_id" => "int",
			"ordem" => "varchar(10)",
			"resumo" => "text",
			"texto" => "text",
			"temoutro" => "int default '0' COMMENT 'É uma booleana indicando se, além de múltiplas escolhas, tem um campo de texto chamado _Outro_'",
			"tipo" => "varchar(20)",
			"classe" => "varchar(80)",
			"especie" => "varchar(80)",
			"adddate" => "datetime",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
				'foreign key' => '(pesqmkt_id) references pesqmkt(id)'
			)
		);

		$modDbSchema["pesqmkt_respostas"] = array(
			"id" => "int auto_increment",
			"pesqmkt_pergunta_id" => "int",
			"ordem" => "varchar(10)",
			"titulo" => "text COMMENT 'O título do conteúdo que será mostrado para humanos.'",
			"titulo_encoded" => "text COMMENT 'Título tratado para ser mostrado na barra de endereços.'",
			"resumo" => "varchar(130) COMMENT 'Algum tipo de resumo que possa ser necessário.'",
			"votos" => "int(10) default '0'",
			"texto" => "text",
			"votante_nome" => "varchar(50)",
			"votante_email" => "varchar(50)",
			"votante_telefone" => "varchar(50)",
			"votante_cidade" => "varchar(50)",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
				'foreign key' => '(pesqmkt_pergunta_id) references pesqmkt_perguntas(id)'
			)
		);

		$modDbSchema["pesqmkt_respostas_textos"] = array(
			"id" => "int auto_increment",
			"pesqmkt_pergunta_id" => "int",
			"resposta" => "text COMMENT 'O texto respondido pelo usuário.'",
			"votante_nome" => "varchar(50)",
			"votante_email" => "varchar(50)",
			"votante_telefone" => "varchar(50)",
			"votante_cidade" => "varchar(50)",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
				'foreign key' => '(pesqmkt_pergunta_id) references pesqmkt_perguntas(id)'
			)
		);

		$modDbSchema["pesqmkt_votantes"] = array(
			"id" => "int auto_increment",
			"pesqmkt_id" => "int",
			"ip" => "varchar(20)",
			"observacao" => "varchar(50)",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"INDEX" => "ip (pesqmkt_id, ip)",
				'foreign key' => '(pesqmkt_id) references pesqmkt(id)'
			)
		);

		$this->createTable( $modDbSchema );

		return true;
	}

	function down(){
		$this->dropTable('pesqmkt');
		$this->dropTable('pesqmkt_perguntas');
		$this->dropTable('pesqmkt_respostas');
		$this->dropTable('pesqmkt_respostas_textos');
		$this->dropTable('pesqmkt_votantes');
		return true;
	}
}
?>