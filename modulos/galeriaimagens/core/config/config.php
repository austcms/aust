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
    'mvc' => false,
    /**
     * 'nome': Nome humano do módulo
     */
    'nome' => 'Galeria de Imagens Anexadas',
    /**
     * 'className': Classe oficial do módulo
     */
    'className' => 'GaleriaImagens',
    /**
     * 'descricao': Descrição que facilita compreender a função do módulo
     */
    'descricao' => 'Inserir imagens anexadas a um texto ou conteúdo.',
    /**
     * 'estrutura': Se pode ser instalada como estrutura (Textos podem)
     */
    'estrutura' => false,
    /**
     * 'somenteestrutura': É uma estrutura somente, sem categorias? (cadastros,
     * por exemplo)
     */
    'somenteestrutura' => false,
    /**
     * 'embed': É do tipo embed?
     */
    'embed' => true,
    /**
     * 'embedownform': É do tipo embed que tem seu próprio formulário?
     */
    'embedownform' => true,

    'liberacao' => true,

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
        'criar' => 'Novo',
        'listar' => 'Listar',
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

);

// itens que serão listados no cabeçalho da listagem
$content_header['campos'] = Array('adddate','titulo','node');
$content_header['campos_nome'] = Array('Data','Título','Categoria');

?>
