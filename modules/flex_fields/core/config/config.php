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
    'name' => 'Flexible Fields',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'FlexFields',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'description' => 'Campos flexíveis.',
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
     * CONFIGURAÇÕES
     */
    'configurations' => array(
        /*
         * Tem sistema de pesquisa?
         */
        'image_save_path' => array(
            "value" => "",
            "label" => "URL para salvar imagens?",
            "inputType" => "text",
			'help' => 'Por padrão, sempre serão salvar dentro do diretório '.
					  'uploads/. Se você escrever ../, então as imagens serão '.
					  'salvas dentro de ../uploads/, o que provavelmente será '.
					  'a raiz do site principal.'
        ),
        /*
         * Tem sistema de pesquisa?
         */
        'files_save_path' => array(
            "value" => "",
            "label" => "URL para salvar arquivos?",
            "inputType" => "text",
			'help' => 'Por padrão, sempre serão salvar dentro do diretório '.
					  'uploads/. Se você escrever ../, então as imagens serão '.
					  'salvas dentro de ../uploads/, o que provavelmente será '.
					  'a raiz do site principal.'
        ),
        /*
         * Tem sistema de pesquisa?
         */
        'has_search' => array(
            "value" => "",
            "label" => "Tem pesquisa?",
            "inputType" => "checkbox",
        ),
        /*
         * Tem sistema de pesquisa?
         */
        'has_printing_version' => array(
            "value" => "",
            "label" => "Tem versão de impressão?",
            "inputType" => "checkbox",
        ),

        /*
         * Categoria selecionável?
         */
        'category_selectable' => array(
            "value" => "",
            "label" => "Categoria selecionável?",
            "inputType" => "checkbox",
        ),
	    'category_creatable' => array(
	        "value" => "",
	        "label" => "Categoria criável?",
	        "inputType" => "checkbox",
	    ),
    ),

    /*
     * CONFIGURAÇÕES POR CAMPO INDIVIDUAL
     */
    'field_configurations' => array(
	
		/*
		 * CAMPOS IMAGES
		 */
	        /*
	         * Múltiplas imagens ou apenas uma?
	         */
	        'image_field_limit_quantity' => array(
				'field_type' => 'images',
	            "value" => "1",
	            "label" => "Limite de imagens?",
	            "inputType" => "text",
	            "size" => "small",
				'help' => 'Por padrão, apenas uma imagem pode ser '.
						  'inserida. Se definir 0, então não há limites.'
	        ),
	        /*
	         * Imagem tem descrição
	         */
	        'image_field_has_description' => array(
				'field_type' => 'images',
	            "value" => "",
	            "label" => "Tem descrição?",
	            "inputType" => "checkbox",
				'help' => 'Este campo de imagem tem descrição? ',
	        ),
	        /*
	         * Tem imagem secundária?
	         */
	        'image_field_has_secondary_image' => array(
				'field_type' => 'images',
	            "value" => "",
	            "label" => "Tem imagem secundária?",
	            "inputType" => "checkbox",
				'help' => 'Por padrão, apenas uma imagem pode ser '.
						  'inserida. Se marcar este checkbox, múltiplas poderão '.
						  'ser inseridas.'
	        ),
	        /*
	         * Tem link?
	         */
	        'image_field_has_link' => array(
				'field_type' => 'images',
	            "value" => "",
	            "label" => "Tem link?",
	            "inputType" => "checkbox",
				'help' => 'Por padrão, imagens não têm link. ',
	        ),
		
		/*
		 * FILES
		 */
		    /*
		     * Múltiplas imagens ou apenas uma?
		     */
			'files_field_limit_quantity' => array(
				'field_type' => 'files',
		        "value" => "1",
		        "label" => "Limite de arquivos?",
		        "inputType" => "text",
		        "size" => "small",
				'help' => 'Por padrão, apenas um arquivo pode ser '.
						  'inserido. Se definir 0, então não há limites.'
		    ),

		/*
		 * RELATIONAL ONE-TO-MANY
		 */
		    /*
		     * Múltiplas imagens ou apenas uma?
		     */
			'1n_has_dragdrop' => array(
				'field_type' => 'relacional_umparamuitos',
		        "value" => "",
		        "label" => "Permite drag&drop",
		        "inputType" => "checkbox",
				'help' => 'Com drag&drop ativado, é possível alterar a ordem '.
						  'dos itens selecionados.'
		    ),
		/*
		 * CAMPOS TEXTOS GRANDE (textarea)
		 */
		    /*
		     * Tem editor?
		     */
			'text_has_editor' => array(
				'field_type' => 'text',
				"value" => "",
				"label" => "Ativar editor de texto rico",
				"inputType" => "checkbox",
				'help' => 'Inserir um editor de texto (negrito/itálico/etc) neste campo. ',
		    ),
		    /*
		     * Pode inserir imagens via editor rico?
		     */
			'text_has_images' => array(
				'field_type' => 'text',
				"value" => "",
				"label" => "Ativar imagens em editor rico",
				"inputType" => "checkbox",
				'help' => 'Selecionar este item permitirá inserir imagens entre o texto.',
		    ),
		/*
		 * CAMPOS STRING CURTO (254 char)
		 */
		    /*
		     * Boolean?
		     */
			'boolean_field' => array(
				'field_type' => 'string',
				"value" => "",
				"label" => "Campo booleano",
				"inputType" => "checkbox",
				'help' => 'Campo aceita somente 0 ou 1, portanto mostra select com opções "Sim" e "Não".',
		    ),
		    /*
		     * Valor monetário
		     */
			'currency_mask' => array(
				'field_type' => 'string',
				"value" => "",
				"label" => "Máscara para valor monetário",
				"inputType" => "text",
				"size" => "small",
				'help' => 'Campo aceita R$. Vazio é nenhum valor.',
		    ),
    ),
    /**
     * RESPONSER
     *
     * A seguir, as configurações do módulo para que este possa apresentar um
     * resumo a qualquer requisitante (chamado responser).
     */
    'arquitetura' => array(
        'table' => 'cadastros_conf',
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
