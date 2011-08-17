<?php
/**
 * @since v0.1.5, 30/05/2009
 */
$modInfo = array(
	'state' => 'stable',
 	'name' => 'Photo Gallery',
	'relationalName' => 'Photo Gallery',
	/**
	 * 'className': Model's class name
	 */
	'className' => 'PhotoGallery',
	/**
	 * 'description'
	 */
	'description' => 'Crie galerias com várias fotos.',

	/*
	 * Actions/views available
	 */
	'actions' => array(
		'create' => 'Inserir',
		'listing' => 'Listar',
	),

	'configurations' => array(
		
		'save_into_db' => array(
			"value" => "0",
			"label" => "Salvar arquivos no DB",
			"inputType" => "checkbox",
		),
		'ordenate' => array(
			"value" => "",
			"label" => "Ordenado",
			"inputType" => "checkbox",
		),
		'has_title' => array(
			"value" => "1",
			"label" => "Tem título?",
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
		 * Tem data de expiração (quando deixará de aparecer a imagem).
		 */
		'expireTime' => array(
			"value" => "",
			"label" => "Tem expireTime?",
			"inputType" => "checkbox",
		),
		/*
		 * Descrição
		 */
		'description' => array(
			"value" => "",
			"label" => "Tem descrição?",
			"inputType" => "checkbox",
		),
		/*
		 * Descrição
		 */
		'commented_images' => array(
			"value" => "",
			"label" => "Cada foto tem descrição",
			"inputType" => "checkbox",
		),


		'related_to_aust_content' => array(
			"value" => "",
			"label" => "Deseja relacionar como escravo a alguma estrutura?",
			"inputType" => "aust_selection",
			'help' => 'Em caso da estrutura Notícias, por exemplo, esta galeria '.
						'relacionar-se-ia a ela e serviria como local para fotos.'
		),
		'related_and_visible' => array(
			"value" => "1",
			"label" => "Se relacionado, visível na listagem de estruturas? ",
			"inputType" => "checkbox",
			'help' => 'Algumas vezes, não deseja-se que apareça na listagem.'
		),


	),

	/*
	 * These fields, if empty, are filled with text during the listing.
	 */
	'replaceFieldsValueIfEmpty' => array(
		'title' => '[Sem título]',
	),

	/*
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