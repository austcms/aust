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
    'nome' => 'Imagens',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Imagens',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'descricao' => 'Módulo gerenciador de imagens e banners',
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
   		'ordenate' => array(
	        "value" => "",
	        "label" => "Ordenado",
	        "inputType" => "checkbox",
	    ),
	    /*
	     * Resumo
	     */
	    'resumo' => array(
	        "propriedade" => "resumo", // nome da propriedade
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
	    'descricao' => array(
	        "value" => "",
	        "label" => "Tem descrição?",
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
