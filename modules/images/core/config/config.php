<?php
/**
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.5, 30/05/2009
 */
$modInfo = array(
    'name' => 'Images',
    'className' => 'Images',
    'description' => 'Módulo gerenciador de imagens e banners',

    'actions' => array(
        'create' => 'Inserir',
        'listing' => 'Listar',
    ),
	
	'viewmodes' => array('thumbs', 'list'),
	
	'configurations' => array(
	    /*
	     * Salva arquivo no DB (por padrão, salva arquivos fisicamente)
	     */
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
	     * Tem Descrição?
	     */
	    'description' => array(
	        "value" => "",
	        "label" => "Tem descrição?",
	        "inputType" => "checkbox",
	    ),
		    /*
		     * Tem editor rico de texto em descrição?
		     */
		    'description_has_rich_editor' => array(
		        "value" => "",
		        "label" => "Descrição tem editor de texto rico?",
		        "inputType" => "checkbox",
		    ),
	    /*
	     * Tem Link?
	     */
	    'link' => array(
	        "value" => "",
	        "label" => "Tem link?",
	        "inputType" => "checkbox",
	    ),		
	    /*
	     * Tem seleção categoria?
	     */
	    'category_selection' => array(
	        "value" => "",
	        "label" => "Seleção de categoria?",
	        "inputType" => "checkbox",
	    ),		
	    /*
	     * Pode criar categorias?
	     */
	    'category_creation' => array(
	        "value" => "",
	        "label" => "Botão criar categoria?",
	        "inputType" => "checkbox",
			'help' => 'Só funcionará se puder selecionar a categoria.'
		),
	    /*
	     * Permite SWF?
	     */
	    'allow_flash_upload' => array(
	        "value" => "",
	        "label" => "Permite upload de Flash (.swf)?",
	        "inputType" => "checkbox",
			'help' => 'Arquivos Flash têm a extensão swf.'
		)
	),

    /*
     * Se não há valor, substitui campo vazio na listagem
     * pelos valores abaixo
     */
    'replaceFieldsValueIfEmpty' => array(
        'title' => '[Sem título]',
    ),

    /**
     * CABEÇALHOS DE LISTAGEM
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
