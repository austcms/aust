<?php
/*
 * VARIABLES
 *
 * Aqui estão as variáveis globais, como endereços, configurações, etc.
 * Caminhos são relativos ao diretório raiz ("/").
 */

/**
 * Variáveis de sistema, sem endereços
 */
define('DS', '/');

/**
 * DIRETIVAS DO CORE
 */
/**
 * Define o diretório do CORE
 */
define("CORE_DIR", "core/");

    /**
     * Define diretórios relativos ao CORE
     */
    /**
     * Diretório do MVC do Core
     */
    define("CONTROLLER_DIR", CORE_DIR."controller/");
    define("MODEL_DIR", CORE_DIR."model/");
    define("VIEW_DIR", CORE_DIR."view/");
    define("VIEW_FILE_STANDARD_EXTENSION", ".php");

    /**
     * Define o diretório onde estão os arquivos de configuração do core
     */
    define("CORE_CONFIG_DIR", CORE_DIR."config/");
    /**
     * Define o caminho para a pasta de instalação
     */
    define("RELATIVE_PATH_TO_INSTALLATION", CORE_CONFIG_DIR."installation/");
    /**
     * DBSCHEMA: arquivo
     */
    define("DBSCHEMA_FILE_PATH", CORE_DIR.RELATIVE_PATH_TO_INSTALLATION."dbschema.php");
    /**
     * USER INTERFACE
     *
     * Define o diretório onde estão as interfaces de usuário do painel do sistema
     */
    define("UI_PATH", CORE_DIR."user_interface/");
        define("CSS_PATH", UI_PATH."css/");
        define("IMG_DIR", "core/user_interface/img/");
    define("UI_STANDARD_FILE", UI_PATH."ui.php");
    define("THEMES_DIR", UI_PATH."themes/");
    define("THEMES_SCREENSHOT_FILE", "screenshot");
    /**
     * Define o diretório onde estão os arquivos de login e autenticação
     */
    define("LOGIN_PATH", CORE_DIR."login/");
    /**
     * Libs
     */
    define("LIB_DIR", CORE_DIR."libs/"); define("LIBS_DIR", LIB_DIR);
    define("LIB_DATA_TYPES", CORE_DIR."libs/functions/data_types.php");

    /**
     * GDImageViewer
     */
    define('IMAGE_VIEWER_DIR', LIB_DIR.'imageviewer/');
    /**
     * Javascript:
     *
     * Define o diretório onde estão os arquivos Javascript
     */
    define("BASECODE_JS", CORE_DIR."basecode/js/");
    /**
     * Diretório dos módu1os
     */
    define('MODULOS_DIR', 'modulos/');
    /**
     * Diretório de inclusão de arquivos ('inc')
     */
    define('INC_DIR', CORE_DIR.'inc/');
    /**
     * TRIGGER
     *
     * São os arquivos que podem desempenhar funções de módulos quando estes
     * estão ausentes.
     */
    define('CONTENT_TRIGGERS_DIR', INC_DIR.'conteudo.inc/');
        define('CREATE_ACTION', 'create');
        define('EDIT_ACTION', 'edit');
        define('LISTING_ACTION', 'listing');
        define('DELETE_ACTION', 'delete');


    /**
     * MENSAGENS
     * 
     * View com Mensagens do sistema
     */
    /**
     * Diretório com as views que contém as mensagens
     */
    define('MSG_VIEW_DIR', VIEW_DIR.'mensagens/');
    define('MSG_ERROR_VIEW_DIR', MSG_VIEW_DIR.'error/');
    /**
     * msg: acesso negado
     */
    define('MSG_DENIED_ACCESS', MSG_ERROR_VIEW_DIR.'acesso_negado.inc.php');


/**
 * CONFIGURAÇÕES GERAIS
 */
/**
 * Configurações gerais 
 */
define('CONFIG_DIR', 'config/');
/**
 * Endereço do arquivo de configurações da base de dados
 */
define('CONFIG_DATABASE_FILE', CONFIG_DIR.'database.php');
/**
 * Configurações do Core
 */
define('CONFIG_CORE_FILE', CONFIG_DIR.'core.php');


/**
 * CLASSES
 */
/**
 * Diretório das classes e configurações extras
 */
define("CLASS_DIR", CORE_DIR.'class/');
define("CLASS_LOADER", CLASS_DIR.'_carrega_classes.inc.php');
define("CLASS_FILE_SUFIX", ".class");

        /*
         * HELPERS
         */
        define("HELPERS_DIR", CLASS_DIR."helpers/");
        define("HELPER_CLASSNAME_SUFIX", "Helper");


/**
 * MÓDULOS
 *
 * Configurações de Módulos. Os paths são relativos ao diretório do módulo
 */
/**
 * Arquivos de actions
 */
define('MOD_ACTIONS_FILE', 'core/actions.php');
/**
 * Arquivo que contém o DbSchema do módulo
 */
define('MOD_DBSCHEMA', 'core/config/db_schema.php');
/**
 * Configurações do Módulo
 *
 *      A partir do diretório do módulo...
 */
define('MOD_CONFIG', 'core/config/config.php');
/**
 * Controller de setup de novas estruturas
 */
define('MOD_SETUP_CONTROLLER', 'controller/setup_controller.php');
/**
 * Definição dos diretórios da arquitetura MVC (se disponível)
 *
 * 1: Diretório do controller
 * 2: Arquivo controller principal
 * 3: Nome do controller principal
 * 4: Diretório do view
 */
define('MOD_CONTROLLER_DIR', 'controller/'); // Diretório do Controller
define('MOD_CONTROLLER', MOD_CONTROLLER_DIR.'mod_controller.php');
define('MOD_CONTROLLER_NAME', 'ModController');
define('MOD_VIEW_DIR', 'view/');


/*
 * MIGRATIONS
 */
    define('MIGRATION_MOD_DIR', 'core/migrations/');

/*
 * WIDGETS
 */
    define('WIDGETS_DIR', 'widgets/');

/*
 * CACHE
 */
    define('CACHE_DIR', CORE_DIR.'cache/');
    define('CACHE_PUBLIC_DIR', UI_PATH.'cache/');

    define('CACHE_CSS_CONTENT', CACHE_PUBLIC_DIR.'style.css');
    define('CACHE_JS_CONTENT', CACHE_PUBLIC_DIR.'javascript.js');
    define('CACHE_CSS_FILES', CACHE_DIR.'CLIENTSIDE_CSS_FILES');
    define('CACHE_JS_FILES', CACHE_DIR.'CLIENTSIDE_JS_FILES');

?>