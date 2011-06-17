<?php
/**
 * Ajax do Módulo
 *
 * @package ModCadastro
 * @name adm_main.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 25/07/2009
 */
/**
 * Cria SESSION
 */
session_name("aust");
session_start();

/**
 * Se não está definido o endereço deste arquivo até o root
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '../../../');
}

/**
 * Variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");


/**
 * Classes do sistema
 */
include(CLASS_DIR."_carrega_classes.inc.php");
/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Configurações de conexão do banco de dados
 */
include(CONFIG_DATABASE_FILE);

include(LIB_DIR."aust/aust_func.php");
/**
 * Conexão principal
 */
$conexao = Connection::getInstance();

/**
 * Configurações do core do sistema
 */
    include(CONFIG_DIR."core.php");
/**
 * Permissões de tipos de usuários relacionados à navegação
 */
    include(CONFIG_DIR."nav_permissions.php");
/**
 * Carrega o CORE
 */
    include(CORE_DIR.'load_core.php');


include('../index.php');

//$modulo = new Cadastro;


/*
 * Função para retornar os cadastros do sistema no formato para <select>
 */
header("Content-Type: text/html; charset=".$aust_charset['view'],true);

   // pr($_POST);

if($_POST['action'] == 'altera_relacionamentos'){

   /**
     * Cria relacionamento
     */
    if($_POST['value'] == 'true'){

        /**
         * Cria SQL
         */
        $sql = "INSERT INTO
                    modulos_conf
                    (categoria_id,tipo,propriedade,valor)
                VALUES
                    ('".$_POST['categoria']."','relacionamentos','id','".$_POST['target']."')
                ";

    /**
     * Deleta Permissão
     */
    } else {
        /**
         * Se for para uma categoria de usuários (ex.: Administradores, Moderadores, etc)
         */
        if($_POST['tipo'] == 'userTipo'){
            $agenteTipo = 'admins_tipos_id';
        /**
         * Permissão relacionada a um usuário
         */
        } elseif($_POST['tipo'] == 'user'){
            $agenteTipo = 'admins_id';
        }

        $sql = "DELETE FROM
                    modulos_conf
                WHERE
                    categoria_id='".$_POST['categoria']."' AND
                    tipo='relacionamentos' AND
                    valor='".$_POST['target']."'
                ";
    }

    if($conexao->exec($sql)){
        echo '1';
    } else {
        echo '0';
    }

    
} elseif($_POST['action'] == 'LeCampos'){
    $sql = "SELECT
                *
            FROM
                ".$_POST['tabela']."
            LIMIT 0,1
        ";
    $result = mysql_query($sql);
    $fields = mysql_num_fields($result);
    for ($i=0; $i < $fields; $i++) {
        echo '<option value="'.mysql_field_name($result, $i).'">'.mysql_field_name($result, $i).'</option>';
    }

}



?>

