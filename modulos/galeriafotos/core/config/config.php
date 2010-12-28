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
/**
 * Variável contendo as configurações deste módulo
 *
 * @global array $GLOBALS['modInfo']
 * @name $modInfo
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
 	'nome' => 'Galeria de Fotos',
    'relationalName' => 'Galeria de Fotos',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'GaleriaFotos',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'descricao' => 'Crie listas de galerias de fotos',
    /**
     * 'estrutura': Se pode ser instalada como estrutura (Textos podem)
     */
    'estrutura' => true,
    /**
     * 'somenteestrutura': É uma estrutura somente, sem categorias? (cadastros,
     * por exemplo)
     */
    'somenteestrutura' => true,
    /**
     * 'embed': É do tipo embed?
     */
    'embed' => false,
    /**
     * 'embedownform': É do tipo embed que tem seu próprio formulário?
     */
    'embedownform' => false,



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
    'opcoes' => array(
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
    /*
     * Se não há valor, substitui campo vazio na listagem
     * pelos valores abaixo
     */
    'replaceFieldsValueIfEmpty' => array(
        'titulo' => '[Sem título]',
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
            'adddate','titulo','node'
        ),
        'camposNome' => array(
            'Data','Título','Categoria'
        ),
    )
);
?>
