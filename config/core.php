<?php
/**
 * Configurações do Core do sistema vão aqui. A classe estática CoreConfig é
 * responsável por guardar todas as variáveis de configuração.
 *
 * @since v0.1.5 24/06/2009
 */
/**
 * Opções de Collate
 * 
 * @global array $GLOBALS['aust_charset']
 */

$aust_charset['view'] = 'utf-8';
$aust_charset['db'] = 'utf8';
$aust_charset['db_collate'] = 'utf8_general_ci';

date_default_timezone_set('America/Sao_Paulo');

/**
 * CONFIGURAÇÕES GERAIS
 *
 * Todas as configurações do sistema
 */

	Registry::write('austTable', 'taxonomy');
	
	/*
	 * DEBUG LEVEL
	 *
	 *	  2: todos os debugs, entretanto sem detalhamentos
	 *		 completos;
	 *	  3: debug completo;
	 *
	 */
	Registry::write('debugLevel', 3);
	/*
	 * Tipo de configuração padrão, visível a todos os usuários.
	 */

	Registry::write('configStandardType','general');
	Registry::write('defaultTheme','classic_blue');

	/*
	 * Configurações que devem ser instaladas automaticamente.
	 */
		$neededConfig = array(
			array(
				'type' => 'general',
				'local' => '',
				'name' => 'Nome do site',
				'property' => 'site_name',
				'value' => 'Nome do site',
				'explanation' => 'Este nome aparecerá no título do gerenciador',
			),
			array(
				'type' => 'private',
				'local' => '',
				'name' => 'Usuário tem imagem secundária?',
				'property' => 'user_has_secondary_image',
				'value' => '0',
				'explanation' => 'Em alguns casos, pode ser necessário que administradores tenham uma imagem secundária.',
			),
			array(
				'type' => 'private',
				'local' => '',
				'name' => 'Mostrar debug SQL',
				'property' => 'show_sql_debug_messages',
				'value' => '0',
				'explanation' => 'Mostra mensagens SQL no rodapé da página.',
			),
		);
		Registry::write('neededConfig', $neededConfig);

?>