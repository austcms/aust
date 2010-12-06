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
 * @since v0.1.6, 06/07/2009
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
    'nome' => 'Arquivos',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Arquivos',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'descricao' => 'Este módulo é o responsável pelo upload e gerenciamento de arquivos.',
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
		'descricao' => array(
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
     * RESPONSER
     *
     * A seguir, as configurações do módulo para que este possa apresentar um
     * resumo a qualquer requisitante (chamado responser).
     */
    'arquitetura' => array(
        'table' => 'arquivos',
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
            'created_on','titulo','node'
        ),
        'camposNome' => array(
            'Data','Título','Categoria'
        ),
    )
);
?>
