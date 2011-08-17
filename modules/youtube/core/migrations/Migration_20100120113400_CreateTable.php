<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120113400_CreateTable extends Migrations
{
	function up(){

		$schema["youtube_videos"] = array(
			"id" => "int auto_increment",
			"categoria" => "int",
			"ordem" => "varchar(10)",
			"titulo" => "text COMMENT 'O título do conteúdo que será mostrado para humanos.'",
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
			"expiredate" => "date",
			"autor" => "int",
			"dbSchemaTableProperties" => array(
				"PRIMARY KEY" => "(id)",
				"UNIQUE" => "id (id)",
			)
		);

		$this->createTable( $schema );

		return true;
	}

	function down(){
		$this->dropTable('youtube_videos');
		return true;
	}
}
?>