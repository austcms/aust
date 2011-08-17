<?php
/**
 * @since v0.1.6, 06/07/2009
 */

$modInfo = array(

	'state' => 'stable',
	
	'name' => 'Flexible Fields',
	/*
	 * The name of the model class
	 */
	'className' => 'FlexFields',

	'description' => 'Campos flexíveis.',

	/*
	 * actions available on the controller. The links to views are created accordingly.
	 */
	'actions' => array(
		'create' => 'Novo',
		'listing' => 'Listar',
	),

	/*
	 * Possible configurations
	 */
	'configurations' => array(
		/*
		 * Tem sistema de pesquisa?
		 */
		'image_save_path' => array(
			"value" => "",
			"label" => "URL para salvar imagens?",
			"inputType" => "text",
			'help' => 'Por padrão, sempre serão salvar dentro do diretório '.
					  'uploads/. Se você escrever ../, então as imagens serão '.
					  'salvas dentro de ../uploads/, o que provavelmente será '.
					  'a raiz do site principal.'
		),
		/*
		 * Tem sistema de pesquisa?
		 */
		'files_save_path' => array(
			"value" => "",
			"label" => "URL para salvar arquivos?",
			"inputType" => "text",
			'help' => 'Por padrão, sempre serão salvar dentro do diretório '.
					  'uploads/. Se você escrever ../, então as imagens serão '.
					  'salvas dentro de ../uploads/, o que provavelmente será '.
					  'a raiz do site principal.'
		),
		/*
		 * Tem sistema de pesquisa?
		 */
		'has_search' => array(
			"value" => "",
			"label" => "Tem pesquisa?",
			"inputType" => "checkbox",
		),
		/*
		 * Tem sistema de pesquisa?
		 */
		'has_printing_version' => array(
			"value" => "",
			"label" => "Tem versão de impressão?",
			"inputType" => "checkbox",
		),

		/*
		 * Categoria selecionável?
		 */
		'category_selectable' => array(
			"value" => "",
			"label" => "Categoria selecionável?",
			"inputType" => "checkbox",
		),
		'category_creatable' => array(
			"value" => "",
			"label" => "Categoria criável?",
			"inputType" => "checkbox",
		),
	),

	/*
	 * Specific fields configurations
	 */
	'field_configurations' => array(
	
		/*
		 * IMAGE FIELDS
		 */
			/*
			 * Multiple images allowed or only one?
			 */
			'image_field_limit_quantity' => array(
				'field_type' => 'images',
				"value" => "1",
				"label" => "Limite de imagens?",
				"inputType" => "text",
				"size" => "small",
				'help' => 'Por padrão, apenas uma imagem pode ser '.
						  'inserida. Se definir 0, então não há limites.'
			),
			/*
			 * Image has description
			 */
			'image_field_has_description' => array(
				'field_type' => 'images',
				"value" => "",
				"label" => "Tem descrição?",
				"inputType" => "checkbox",
				'help' => 'Este campo de imagem tem descrição? ',
			),
			'image_automatic_cache_sizes' => array(
				'field_type' => 'images',
				"value" => "",
				"label" => "Cache das imagens",
				"inputType" => "text",
				'help' => 'Defina quais os tamanhos devem ser cacheados automaticamente '.
						  'inserida. Use ; para definir os tamanhos, e.g. 150x100; 100x75. '.
						  'Neste exemplo, duas imagens serão cacheadas.'
			),
			/*
			 * Has secondary image?
			 */
			'image_field_has_secondary_image' => array(
				'field_type' => 'images',
				"value" => "",
				"label" => "Tem imagem secundária?",
				"inputType" => "checkbox",
				'help' => 'Por padrão, apenas uma imagem pode ser '.
						  'inserida. Se marcar este checkbox, múltiplas poderão '.
						  'ser inseridas.'
			),
			/*
			 * Has link?
			 */
			'image_field_has_link' => array(
				'field_type' => 'images',
				"value" => "",
				"label" => "Tem link?",
				"inputType" => "checkbox",
				'help' => 'Por padrão, imagens não têm link. ',
			),
		
		/*
		 * FILES
		 */
			/*
			 * Multiple files?
			 */
			'files_field_limit_quantity' => array(
				'field_type' => 'files',
				"value" => "1",
				"label" => "Limite de arquivos?",
				"inputType" => "text",
				"size" => "small",
				'help' => 'Por padrão, apenas um arquivo pode ser '.
						  'inserido. Se definir 0, então não há limites.'
			),

		/*
		 * RELATIONAL ONE-TO-MANY
		 */
			'1n_has_dragdrop' => array(
				'field_type' => 'relacional_umparamuitos',
				"value" => "",
				"label" => "Permite drag&drop",
				"inputType" => "checkbox",
				'help' => 'Com drag&drop ativado, é possível alterar a ordem '.
						  'dos itens selecionados.'
			),
		/*
		 * TEXTAREAS
		 */
			'text_has_editor' => array(
				'field_type' => 'text',
				"value" => "",
				"label" => "Ativar editor de texto rico",
				"inputType" => "checkbox",
				'help' => 'Inserir um editor de texto (negrito/itálico/etc) neste campo. ',
			),

			'text_has_images' => array(
				'field_type' => 'text',
				"value" => "",
				"label" => "Ativar imagens em editor rico",
				"inputType" => "checkbox",
				'help' => 'Selecionar este item permitirá inserir imagens entre o texto.',
			),
		/*
		 * STRING FIELDS
		 */
			/*
			 * Boolean?
			 */
			'boolean_field' => array(
				'field_type' => 'string',
				"value" => "",
				"label" => "Campo booleano",
				"inputType" => "checkbox",
				'help' => 'Campo aceita somente 0 ou 1, portanto mostra select com opções "Sim" e "Não".',
			),
			/*
			 * Currency
			 */
			'currency_mask' => array(
				'field_type' => 'string',
				"value" => "",
				"label" => "Máscara para valor monetário",
				"inputType" => "text",
				"size" => "small",
				'help' => 'Campo aceita R$. Vazio é nenhum valor.',
			),
	),

	/*
	 * Listing header
	 */
	'contentHeader' => array(
		'campos' => array(
			'adddate','titulo','node'
		),
		'camposNome' => array(
			'Data','Título','Categoria'
		),
	)
);
?>