<?php
/**
 * @since v0.1.6, 06/07/2009
 */

$modInfo = array(
	'state' => 'stable',
	'name' => 'Files',
	/*
	 * 'className': the model's class name
	 */
	'className' => 'Files',
	'description' => 'Responsável pelo upload e gerenciamento de arquivos.',

	/*
	 * Actions/views available
	 */
	'actions' => array(
		'create' => 'Novo',
		'listing' => 'Listar',
	),

	/*
	 * Configurations
	 */
	'configurations' => array(
		'upload_path' => array(
			"value" => "",
			"label" => "Qual path destino do upload?",
			"inputType" => "input",
		),
		'select_category' => array(
			"value" => "",
			"label" => "Permite selecionar categoria",
			"inputType" => "checkbox",
		),

		'new_aust_node' => array(
			"value" => "",
			"label" => "Permite criar categoria?",
		 	"inputType" => "checkbox",
			'help' => 'Depende que a Seleção de Categoria esteja ligada.',
		),
		'description' => array(
			"value" => "",
			"label" => "Tem descrição?",
			"inputType" => "checkbox",
		),
		'show_path_to_link' => array(
			"value" => "",
			"label" => "Mostrar path para link?",
			"inputType" => "checkbox",
		),
	),

	/**
	 * Listing header
	 */
	'contentHeader' => array(
		'campos' => array(
			'created_on', 'title', 'node'
		),
		'camposNome' => array(
			'Data', 'Título', 'Categoria'
		),
	)
);
?>
