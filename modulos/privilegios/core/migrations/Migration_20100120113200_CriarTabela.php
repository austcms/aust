<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20100120113200_CriarTabela extends Migrations
{
    function up(){

        $schema["privilegios"] = array(
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

        $schema["privilegio_agent"] = array(
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

        $schema["privilegio_target"] = array(
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

        $this->createTable( $schema );

        return true;
    }

    function down(){
        $this->dropTable('privilegios');
        $this->dropTable('privilegio_agent');
        $this->dropTable('privilegio_target');
        return true;
    }
}
?>