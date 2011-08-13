<?php
/**
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6, 06/07/2009
 */

$modInfo = array(
    /**
     * 'nome': Nome humano do módulo
     */
    'name' => 'Files',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Files',
    /**
     * 'description'
     */
    'description' => 'Responsável pelo upload e gerenciamento de arquivos.',

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

	/*
	 * Configurations
	 */
	'configurations' => array(
		'upload_path' => array(
		    "value" => "",
		    "label" => "Qual path destino do upload?",
		    "inputType" => "input",
		),
		/*
		 * Ordenate
		 */
		'select_category' => array(
		    "value" => "",
		    "label" => "Permite selecionar categoria",
		    "inputType" => "checkbox",
		),
		/*
		 * Resumo
		 */
		'nova_categoria' => array(
		    "value" => "",
		    "label" => "Permite criar categoria?",
		 	"inputType" => "checkbox",
			'help' => 'Depende que a Seleção de Categoria esteja ligada.',
		),
		/*
		 * Resumo
		 */
		'description' => array(
		    "value" => "",
		    "label" => "Tem descrição?",
		    "inputType" => "checkbox",
		),
		/*
		 * show_path_to_link
		 */
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
