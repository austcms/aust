<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @package Modulos
 * @name Config
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

$modInfo = array(
    /**
     * 'nome': Nome humano do módulo
     */
 	'name' => 'Photo Gallery',
    'relationalName' => 'Photo Gallery',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'PhotoGallery',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Crie listas de galerias de fotos',

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
	    'resumo' => array(
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
	    'descricao' => array(
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
     * Se não há valor, substitui campo vazio na listagem
     * pelos valores abaixo
     */
    'replaceFieldsValueIfEmpty' => array(
        'title' => '[Sem título]',
    ),


    /**
     * '': 
     */
    '' => '',

    /**
     * CABEÇALHOS DE LISTAGEM
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
