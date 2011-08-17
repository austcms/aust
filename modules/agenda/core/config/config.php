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
	'name' => 'Agenda',
	/**
	 * 'className': Classe oficial do módulo
	 */
	'className' => 'Agenda',
	/**
	 * 'description'
	 */
	'description' => 'Módulo para cadastro de compromissos.',
	/**
	 * 'structure_only': É uma estrutura somente, sem categorias? (cadastros,
	 * por exemplo)
	 */
	'structure_only' => false,

	/**
	 * RESPONSER
	 */
	/**
	 * Se possui método de leitura de resumo
	 */
	'responser' => array(
		'actived' => true,
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
		'listing' => 'Ver agenda',
	),

	'configurations' => array(
		'ordenate' => array(
			"value" => "",
			"label" => "Ordenado",
			"inputType" => "checkbox",
		),

		/*
		 * Resumo
		 */
		'summary' => array(
			"value" => "",
			"label" => "Tem resumo?",
			"inputType" => "checkbox",
		),

		/*
		 * Place
		 */
		'has_place' => array(
			"value" => "0",
			"label" => "Tem lugar?",
			"inputType" => "checkbox",
			"help" => "Caso seja necessário, pode ser necessário especificar
						um campo com 'lugar' do evento."
		),

		/*
		 * editor rich edit
		 */
		'description_has_rich_editor' => array(
			"value" => "0",
			"label" => "Editor rico na descrição",
			"inputType" => "checkbox",
		),

		/*
		 * Há opção para selecionar o modo de visualização?
		 */
		'description_upload_inline_images' => array(
			"value" => "0",
			"label" => "Permitir upload de imagens no campo descrição via TinyMCE",
			"inputType" => "checkbox",
		),

		/*
		 * Pessoa responsável
		 */
		'has_responsible_person' => array(
			"value" => "0",
			"label" => "Tem uma pessoa responsável?",
			"inputType" => "checkbox",
		),

		/*
		 * One day only
		 */
		'one_day_only' => array(
			"value" => "1",
			"label" => "É um dia apenas?",
			"inputType" => "checkbox",
		),

		/*
		 * Resumo
		 */
		'nova_categoria' => array(
			"value" => "",
			"label" => "Permite criar categoria?",
			"inputType" => "checkbox",
		),
		/*
		 * Resumo
		 */
		'modo_de_visualizacao' => array(
			"value" => "",
			"label" => "Opção Modo de Visualização?",
			"inputType" => "checkbox",
			'help' => ''
		),		
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
			'created_on','title','node'
		),
		'camposNome' => array(
			'Data','Título','Categoria'
		),
	)
);
?>
