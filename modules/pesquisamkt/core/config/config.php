<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @since v0.1.5, 30/05/2009
 */
/**
 * Variável contendo as configurações deste módulo
 *
 * @global array $GLOBALS['modInfo']
 */

$modInfo = array(
	/**
	 * Cabeçalho
	 *
	 * Informações sobre o próprio módulo, como nome, descriçao, entre outros
	 */
	/**
	 * É arquitetura MVC
	 */
	'mvc' => true,
	/**
	 * 'nome': Nome humano do módulo
	 */
	'name' => 'Pesquisa',
	/**
	 * 'className': Classe oficial do módulo
	 */
	'className' => 'PesquisaMkt',
	/**
	 * 'description'
	 */
	'description' => 'Módulo gerenciador Pesquisas de Marketing e Enquetes, possibilitando perguntas abertas e fechadas.',
	/**
	 * 'structure': Se pode ser instalada como estrutura (Textos podem)
	 */
	'structure' => true,
	/**
	 * 'structure_only': É uma estrutura somente, sem categorias? (cadastros,
	 * por exemplo)
	 */
	'structure_only' => true,

	/**
	 * RESPONSER
	 */
	/**
	 * Se possui método de leitura de resumo
	 */
	'responser' => array(
		'actived' => false,
	),

	/**
	 * Opções de gerenciamento de conteúdo
	 *
	 * A opções a seguir dizem respeito a qualquer ação que envolva
	 * a interação do módulo com conteúdo.
	 */
	/**
	 * Opções de gerenciamento deste módulo
	 *
	 */
	'actions' => array(
		'create' => 'Novo',
		'listing' => 'Listar',
	),

	'configurations' => array(
		'enquete' => array(
			"value" => "",
			"label" => "É uma enquete? (considera ser 1 pergunta apenas)",
			"inputType" => "checkbox",
		),
		'has_no_title' => array(
			"value" => "0",
			"label" => "Não tem título",
			"inputType" => "checkbox",
		),
		'has_description' => array(
			"value" => "0",
			"label" => "Tem descrição?",
			"inputType" => "checkbox",
		),
		'has_no_visibility_option' => array(
			"value" => "0",
			"label" => "Não tem opção de visibilidade",
			"inputType" => "checkbox",
			'help' => 'O botão "Esta pesquisa está visível?" não é necessária.'
		),
		'can_not_add_alternatives' => array(
			"value" => "1",
			"label" => "Não pode inserir alternativas",
			"inputType" => "checkbox",
			'help' => 'Há um botão "+ alternativa".'
		),
		'do_not_show_result' => array(
			"value" => "",
			"label" => "Não mostrar resultados",
			"inputType" => "checkbox",
			'help' => 'No caso de enquetes/pesquisas, há resultados a serem mostrados. '.
					  'Este item desativa esta amostragem.'
		),
		'first_alternative_right' => array(
			"value" => "",
			"label" => "Primeira alternativa está certa",
			"inputType" => "checkbox",
			'help' => 'No caso de enquetes/pesquisas, não resultados corretos. Em jogos, entretanto, '.
					  'há. Esta será a primeira alternativa.'
		),
		
	),

	/**
	 * RESPONSER
	 *
	 * A seguir, as configurações do módulo para que este possa apresentar um
	 * resumo a qualquer requisitante (chamado responser).
	 */
	'arquitetura' => array(
		'table' => 'textos',
		'foreignKey' => 'categoria',
	),


	/**
	 * '': 
	 */
	'' => '',

	/**
	 * Listing header
	 */
	'contentHeader' => array(
		'campos' => array(
			'adddate','titulo'//,'node'
		),
		'camposNome' => array(
			'Data','Título'//,'Categoria'
		),
	)
);
?>
