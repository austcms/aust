<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @package Modulos
 * @name Config
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.9, 15/12/2010
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
    'name' => 'Orders',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Orders',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Módulo gerenciador de pedidos e transações.',
    /**
     * 'estrutura': Se pode ser instalada como estrutura (Textos podem)
     */
    'estrutura' => true,
    /**
     * 'somenteestrutura': É uma estrutura somente, sem categorias? (cadastros,
     * por exemplo)
     */
    'somenteestrutura' => false,
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
        //'create' => 'Novo',
        'listing' => 'Ver',
    ),
    /*
     * CONFIGURAÇÕES
     */
    'configurations' => array(
        /*
         * Resumo
         */
        'resumo' => array(
            "value" => "",
            "label" => "Tem resumo?",
            "inputType" => "checkbox",
        ),
	    'aust_products' => array(
	        "value" => "",
	        "label" => "Como chama-se a estrutura de produtos?",
	        "inputType" => "aust_selection",
	    ),
        'aust_clients_id' => array(
            "value" => "",
            "label" => "Clientes: nome da estrutura",
            "inputType" => "aust_selection",
        ),
	    'aust_clients_name_field' => array(
	        "value" => "",
	        "label" => "Clientes: nome da coluna de nomes",
	        "inputType" => "text",
	    ),

    ),
    
    /*
     * Se não há valor, substitui campo vazio na listagem
     * pelos valores abaixo
     */
    'replaceFieldsValueIfEmpty' => array(
        'titulo' => '[Sem título]',
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
