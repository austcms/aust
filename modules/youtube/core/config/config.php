<?php
/**
 * Configurações
 *
 * Arquivo contendo informações sobre este módulo
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
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
    'name' => 'Vídeos YouTube',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'YouTube',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Módulo gerenciador de vídeos do YouTube',
    /**
     * 'estrutura': Se pode ser instalada como estrutura (Textos podem)
     */
    'estrutura' => true,
    /**
     * 'structure_only': É uma estrutura somente, sem categorias? (cadastros,
     * por exemplo)
     */
    'structure_only' => true,

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
        'create' => 'Novo',
        'listing' => 'Listar',
    ),
	'configurations' => array(
		'ordenate' => array(
		    "value" => "",
		    "label" => "Ordenado",
		    "inputType" => "checkbox",
		),
		'resumo' => array(
		    "value" => "",
		    "label" => "Tem resumo?",
		    "inputType" => "checkbox",
		),
		'categorias' => array(
		    "value" => "",
		    "label" => "Tem categorias?",
		    "inputType" => "checkbox",
		),
		'descricao' => array(
		    "value" => "",
		    "label" => "Tem descrição?",
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
