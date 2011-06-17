<?php
	/*********************************
	*
	*	código de edição dos módulos
	*
	*********************************/

	// se existe um arquivo específico para edição no módulo, abre ele, senão abre o arquivo form.php
	if(is_file(MODULES_DIR.Aust::getInstance()->LeModuloDaEstrutura($_GET[aust_node]).'/edit.php')){
		include(MODULES_DIR.Aust::getInstance()->LeModuloDaEstrutura($_GET[aust_node]).'/edit.php');
	} else {
		include(MODULES_DIR.Aust::getInstance()->LeModuloDaEstrutura($_GET[aust_node]).'/form.php');
	}


?>