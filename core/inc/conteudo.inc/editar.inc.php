<?php
	/*********************************
	*
	*	código de edição dos módulos
	*
	*********************************/

	// se existe um arquivo específico para edição no módulo, abre ele, senão abre o arquivo form.php
	if(is_file('modulos/'.$aust->LeModuloDaEstrutura($_GET[aust_node]).'/edit.php')){
		include('modulos/'.$aust->LeModuloDaEstrutura($_GET[aust_node]).'/edit.php');
	} else {
		include('modulos/'.$aust->LeModuloDaEstrutura($_GET[aust_node]).'/form.php');
	}


?>