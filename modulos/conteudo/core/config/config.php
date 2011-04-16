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
    'nome' => 'Conteúdo',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'Conteudo',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'descricao' => 'Módulo gerenciador de textos',
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
        'actived' => true,
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
     * CONFIGURAÇÕES
     */
    'configurations' => array(
        /*
         * Has file
         */
        'has_file' => array(
            "value" => "",
            "label" => "Tem arquivo?",
            "inputType" => "checkbox",
        ),
        /*
         * Ordenate
         */
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
        'nova_categoria' => array(
            "value" => "",
            "label" => "Permite criar categoria?",
            "inputType" => "checkbox",
        ),
        /*
         * Há opção para selecionar o modo de visualização?
         */
        'modo_de_visualizacao' => array(
            "value" => "",
            "label" => "Opção Modo de Visualização?",
            "inputType" => "checkbox",
        ),
        'generate_preview_url' => array(
            "value" => "",
            "label" => "Mostrar Url do conteúdo?",
            "inputType" => "text",
            'help' => 'A seguir, os códigos especiais: <ul>'.
                      '<li>%id = id do conteúdo</li>'.
                      '<li>%title_encoded = título encoded</li>'.
                      '<li>%category = categoria</li>'.
                      '</ul>'.
                      'Exemplo: http://meusite.com.br/noticias/%id.'
        ),
        /*
         * Há opção para selecionar o modo de visualização?
         */
        'upload_inline_images' => array(
            "value" => "0",
            "label" => "Permitir upload de imagens no TinyMCE?",
            "inputType" => "checkbox",
        ),

	    'manual_date' => array(
	        "value" => "",
	        "label" => "Ajustar data manualmente?",
	        "inputType" => "checkbox",
	    ),

	    'show_visits_counter' => array(
	        "value" => "",
	        "label" => "Mostrar contador de visitas",
	        "inputType" => "checkbox",
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
