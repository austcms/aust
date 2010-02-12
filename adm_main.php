<?php

/**
 * Este é o Bootstrap do sistema, carregando configurações, classes e as interfaces de usuário
 *
 * @package Interface
 * @name adm_main.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1.5
 * @since 25/07/2009
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
    define('THIS_TO_BASEURL', '');
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
include(CONFIG_DIR."database.php");

include(LIB_DIR."aust/aust_func.php");
/**
 * Conexão principal
 */
$conexao = new Conexao($dbConn);
$model = new Model($conexao);

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

/**
 * Se este arquivo NÃO está sendo carregado pelo responser
 */
$isResponser = (empty($isResponser)) ? false : $isResponser;
if(!$isResponser){

    /**
     * Verifica se a conexão ou tabelas existém
     */
    if(!$conexao->DBExiste OR ($dbSchema->verificaSchema() != 1)){
        /**
         * Se não tem DB ou tabelas
         */
        echo 'Erro no sistema: 002.';
    }
    
    /**
     * CONEXÃO EXISTE, TUDO ESTÁ OK
     *
     * Está tudo certinho, segue adiante para mostrar a UI
     */
    else {

        /**
         * Instancia objetos necessários ao funcionamento do sistema
         */
        $aust = new Aust($conexao);
        $administrador = new Administrador($conexao);
        $administrador->verifySession();
        $administrador->redirectForbiddenSession();
        $modulos = new Modulos( array('conexao'=>$conexao) );
        $config = new Config(
                array(
                    'conexao' => $conexao,
                    'permissions' => $configPermissoes,
                    'userType' => $administrador->tipo,
                    'rootType' => 'Webmaster'
                    )
            );

        /**
         * Carrega todas as estruturas que não tem categorias.
         */
        $aust->EstruturasSemCategorias();
        /**
         * Ações quanto a conteúdos e categorias
         */
        include("core/config/permissions.php");

        /**
         * @todo - excluir a linha a seguir em todo o código. Esta linha está
         * na classe CoreConfig
         */
        $aust_table = 'categorias';
        include_once(INC_DIR.'inc_categorias_functions.php');
        

        $envParams = array(
            'aust' => $aust,
            'conexao' => $conexao,
            'administrador' => $administrador,
            'permissoes' => $permissoes,
        );
        /**
         * USER INTERFACE (UI)
         *
         * Instancia a classe UI responsável pela interface do usuário.
         *
         * Permissões por usuário e por seção em config/permissions.php.
         */
        /**
         * Instancia o objeto $ui (UserInterface)
         */
        $uI = new UI;

        $_GET['action'] = (empty($_GET['action'])) ? '' : $_GET['action'];
        $_GET['section'] = (empty($_GET['section'])) ? 'index' : $_GET['section'];

        /**
         * Verifica se o usuário tem permissão para acessar a página requsitada
         */
        $permitted = $uI->verificaPermissoes();

        /**
         * Com permissão, o usuário vai até a página requisitada.
         *
         * Caso contrario, recebe uma mensagem de erro.
         */
        $param = array(
            'permitted' => $uI->verificaPermissoes(),
        );
        $filename = $uI->correctUIPage($param);

        /**
         * Salva em $content_for_layout todo o conteúdo gerado na view
         */
        ob_start();
        include($filename);
        $content_for_layout = ob_get_contents();
        ob_end_clean();
        
        /**
         * Mostra a Interface de usuário no browser
         */
        include(UI_STANDARD_FILE);

    }
}
?>