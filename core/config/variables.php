<?php
/*
 * VARIABLES
 *
 * Paths to directories and files. THIS_TO_BASEURL is the relative path
 * to root.
 */

if( !defined('THIS_TO_BASEURL') ){
	define('THIS_TO_BASEURL', '');
}

/*
 * Root folders
 */

define("CORE_DIR", 						THIS_TO_BASEURL."core/");

define("VENDOR_DIR", 					THIS_TO_BASEURL."vendor/");
define("TMP_DIR", 						THIS_TO_BASEURL."tpm/");

/*
 * Core directories
 */
	define("APP_DIR", 					CORE_DIR."app/");
	
	define("CONTROLLERS_DIR", 			APP_DIR."controllers/");
	define("MODELS_DIR", 				APP_DIR."models/");
	define("VIEWS_DIR", 				APP_DIR."views/");
	define("VIEW_FILE_STANDARD_EXTENSION", ".php");

	/*
	 * Core's config
	 */
	 define("CORE_CONFIG_DIR", 			CORE_DIR."config/");
	 	define("VERSION_FILE", 			CORE_CONFIG_DIR."version.php");
	 	define("PERMISSIONS_FILE", 		CORE_CONFIG_DIR."permissions.php");

 	define("LOAD_CORE", 				CORE_DIR."load_core.php");

	/*
	 * Installation
	 */
	define("INSTALLATION_DIR", 			CORE_CONFIG_DIR."installation/");
		define("DBSCHEMA_FILE_PATH", 	CORE_DIR.INSTALLATION_DIR."dbschema.php");

	/*
	 * GUI
	 */
	define("UI_PATH", 					CORE_DIR."user_interface/");
		define("CSS_PATH", 				UI_PATH."css/");
		define("IMG_DIR", 				UI_PATH."img/");
	define("UI_STANDARD_FILE", 			UI_PATH."ui.php");
	define("THEMES_DIR", 				UI_PATH."themes/");
	define("THEMES_SCREENSHOT_FILE", 	"screenshot");
	define("LOGIN_PATH", 				CORE_DIR."login/");

	/*
	 * Libraries
	 */
	define("LIB_DIR", 					CORE_DIR."libs/"); define("LIBS_DIR", LIB_DIR);
	define("LIB_DATA_TYPES", 			CORE_DIR."libs/functions/data_types.php");
	define('IMAGE_VIEWER_DIR', 			LIB_DIR.'imageviewer/');
	define("BASECODE_JS", 				LIB_DIR."js/");

	/*
	 * Diretório dos módu1os
	 */

	define('MODULES_DIR', 				THIS_TO_BASEURL."modules/");
	define('MODULOS_DIR', 				MODULES_DIR);
	define('INC_DIR', 					CORE_DIR.'inc/');
	
	/*
	 * TRIGGER
	 */

 	define('CONTENT_DISPATCHER', 		'content');
	define('MODULES', 					'content'); # alias for content_dispatcher
 	define('CONTROL_PANEL_DISPATCHER', 	'control_panel');

	
	define('CONTENT_TRIGGERS_DIR', 		VIEWS_DIR.'content/');
		define('CREATE_ACTION', 		'create');
		define('EDIT_ACTION', 			'edit');
		define('LISTING_ACTION', 		'listing');
		define('DELETE_ACTION', 		'delete');
		define('ACTIONS_ACTION', 		'actions');
		define('SAVE_ACTION', 			'save');


	/*
	 * Standard Messages
	 */
	define('MSG_VIEW_DIR', 				CORE_CONFIG_DIR.'messages/');
	define('MSG_ERROR_VIEW_DIR', 		MSG_VIEW_DIR.'error/');
	define('MSG_DENIED_ACCESS', 		MSG_ERROR_VIEW_DIR.'access_denied.php');
	define('MSG_CONTROLLER', 			'messages');
	define('MSG_DENIED_ACCESS_ACTION', 	'access_denied');

/*
 * General Config
 */

	define('CONFIG_DIR', 				THIS_TO_BASEURL.'config/');
	if(!defined('CONFIG_DATABASE_FILE')){
		define('CONFIG_DATABASE_FILE', 	CONFIG_DIR.'database.php');
	}
	define('CONFIG_CORE_FILE', 			CONFIG_DIR.'core.php');

	define('EXPORTED_DIR', 				CONFIG_DIR.'export/');
	define('EXPORTED_FILE', 			EXPORTED_DIR.'exported_data.php');
 	define("NAVIGATION_PERMISSIONS_FILE", CONFIG_DIR."nav_permissions.php");

/*
 * Classes
 */
	define("CLASS_DIR", 				CORE_DIR.'class/');
	define("API_CLASS_DIR", 			CLASS_DIR.'api/');
	define("CLASS_LOADER", 				CLASS_DIR.'_autoload.php');
	define("CLASS_FILE_SUFIX", 			".class");

	define("HELPERS_DIR", 				CLASS_DIR."helpers/");
	define("HELPER_CLASSNAME_SUFIX", 	"Helper");


/*
 * Modules
 */
	define('MOD_ACTIONS_FILE', 			CORE_DIR.'actions.php');
	define('MOD_DBSCHEMA', 				CORE_CONFIG_DIR.'db_schema.php');
	/*
	 * Caution: must be relative to MODULES_DIR
	 */
	define('MOD_CONFIG', 				'core/config/config.php');

	define('MOD_SETUP_CONTROLLER', 		'controller/setup_controller.php');

	define('MOD_CONTROLLER_DIR', 		'controller/');
	define('MOD_CONTROLLER', 			MOD_CONTROLLER_DIR.'mod_controller.php');
	define('MOD_CONTROLLER_NAME', 		'ModController');
	define('MOD_VIEW_DIR', 				'view/');

	define('MOD_MODELS_DIR', 			'models/');

/*
 * MIGRATIONS
 */
	define('MIGRATION_MOD_DIR', 		CORE_DIR.'migrations/');

/*
 * WIDGETS
 */
	define('WIDGETS_DIR', 				'widgets/');

/*
 * CACHE
 */
	define('CACHE_DIR', 				TMP_DIR.'cache/');
	define('CACHE_PUBLIC_DIR', 			TMP_DIR.'cache/');

	define('UPLOADS_DIR', 				'uploads/');

	define('CACHE_CSS_CONTENT', 		CACHE_PUBLIC_DIR.'style.css');
	define('CACHE_JS_CONTENT', 			CACHE_PUBLIC_DIR.'javascript.js');
	define('CACHE_CSS_FILES', 			CACHE_DIR.'CLIENTSIDE_CSS_FILES');
	define('CACHE_JS_FILES', 			CACHE_DIR.'CLIENTSIDE_JS_FILES');
?>