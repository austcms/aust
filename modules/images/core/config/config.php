<?php
/**
 * @since v0.1.5, 30/05/2009
 */
$modInfo = array(
	'name' => 'Images',
	'className' => 'Images',
	'description' => 'Módulo gerenciador de imagens e banners.',

	/*
	 * Actions/views available
	 */
	'actions' => array(
		'create' => 'Inserir',
		'listing' => 'Listar',
	),
	
	'viewmodes' => array('thumbs', 'list'),
	
	'configurations' => array(
		'save_files_to_db' => array(
			"value" => "",
			"label" => "Salvar arquivo no Banco de Dados?",
			"inputType" => "checkbox",
			'help' => 'Por padrão, os arquivos são salvos fisicamente. Arquivos Flash '.
			 		  'são obrigatoriamente salvos fisicamente, mesmo que esta opção '.
					  'esteja selecionada.',
		),
	
   		'ordenate' => array(
			"value" => "",
			"label" => "Ordenado",
			"inputType" => "checkbox",
		),
		'summary' => array(
			"value" => "",
			"label" => "Tem resumo?",
			"inputType" => "checkbox",
			"help" => "Este é um campo de texto chamado Resumo."
		),
		'expireTime' => array(
			"value" => "",
			"label" => "Tem expireTime?",
			"inputType" => "checkbox",
			"help" => "Uma data de expiração (quando deixará de aparecer a imagem). ".
					  " Ideal para banners com data limite."
		),
		'description' => array(
			"value" => "",
			"label" => "Tem descrição?",
			"inputType" => "checkbox",
		),
			'description_has_rich_editor' => array(
				"value" => "",
				"label" => "Descrição tem editor de texto rico?",
				"inputType" => "checkbox",
			),
		'link' => array(
			"value" => "",
			"label" => "Tem link?",
			"inputType" => "checkbox",
		),		
		'category_selection' => array(
			"value" => "",
			"label" => "Seleção de categoria?",
			"inputType" => "checkbox",
		),		
		'category_creation' => array(
			"value" => "",
			"label" => "Botão criar categoria?",
			"inputType" => "checkbox",
			'help' => 'Só funcionará se puder selecionar a categoria.'
		),
		/*
		 * SWF files?
		 */
		'allow_flash_upload' => array(
			"value" => "",
			"label" => "Permite upload de Flash (.swf)?",
			"inputType" => "checkbox",
			'help' => 'Arquivos Flash têm a extensão swf.'
		)
	),

	/*
	 * The following fields are filled if empty
	 */
	'replaceFieldsValueIfEmpty' => array(
		'title' => '[Sem título]',
	),

	/**
	 * Listing header
	 */
	'contentHeader' => array(
		'campos' => array(
			'created_on', 'title', 'node'
		),
		'camposNome' => array(
			'Data','Título','Categoria'
		),
	)
);
?>
