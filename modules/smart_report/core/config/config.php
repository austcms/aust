<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @package Modulos
 * @name Config
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.9, 25/01/2011
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
    'name' => 'SmartReport',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'SmartReport',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Crie relatórios com filtros individuais',
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
    'actions' => array(
        'listing' => 'Ver',
    ),
    /*
     * CONFIGURAÇÕES
     */
    'configurations' => array(

        /*
         * activate_actions
         */
        'activate_actions' => array(
            "value" => "",
            "label" => "Ativar ações",
            "inputType" => "checkbox",
			"help" => "Este checkbox precisa estar ativado para alguma <em>action</em> funcionar."
        ),

        /*
         * id_field
         */
        'default_id_field' => array(
            "value" => "",
            "label" => "Campo id padrão",
            "inputType" => "text",
			"help" => "Se a tabela principal é <em>textos</em>, escreva </em>textos.id</em>. Isto serve para uso de actions."
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
