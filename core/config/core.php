<?php
/*
 * CONFIGURAÇÕES INTERNAS
 */

// PHP 5.3 needs this
date_default_timezone_set('America/Sao_Paulo');

/*
 * Diretórios com Permissão de Acesso Necessária
 */
	Registry::write('permission_needed_dirs',
			array(
//				CACHE_DIR,
//				CACHE_PUBLIC_DIR,
//				CACHE_CSS_CONTENT,
//				CACHE_JS_CONTENT,
				UPLOADS_DIR,
			));

/*
 * Actions padrão de Módulos
 */
	Registry::write('default_actions',
			array(
				'Criar' => CREATE_ACTION,
				'Listar' => LISTING_ACTION,
				'Editar' => EDIT_ACTION,
			));

/*
 * Tema padrão (quando nenhum outro está selecionado)
 */
	Registry::write('defaultTheme','classic_blue')



?>
