<?php
/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Conteúdos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
class Conteudo extends Modulos
{

    // TABELA
    protected $db_tabelas;
    protected $sql_das_tabelas;
    protected $sql_registros;
    public $tabela_criar;

    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    public $conexao;
    /**
     *
     * @var class Configurações do módulo
     */
    public $config;
    /**
     * @todo - Comentar certo esta classe
     *
     *
     * @global string $aust_charset Contém o charset das tabelas
     * @param Conexao $conexao Objeto que contém as configurações com o DB
     */
    function __construct($param = ''){

        $this->tabela_criar = "textos";
        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);
	
    }

    /**
     * getSQLForListing()
     *
     * Retorna um SQL para uma listagem genérica dos dados deste módulo.
     *
     * @param <array> $options
     * @return <string>
     */
    public function getSQLForListing($options = array()) {
        /*
         * SET DEFAULT OPTIONS
         */
        require_once(LIB_DATA_TYPES);
        /*
         * Default options
         */
        $categorias = getDataInArray($options, 'categorias');
        $pagina = getDataInArray($options, 'pagina');
        $itens_por_pagina = getDataInArray($options, 'resultadosPorPagina');
        $limit = '';

        $order = ' ORDER BY id DESC';
        /*
         * Gera condições para sql
         */
        if(!empty($categorias)) {
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor) {
                if($c == 0)
                    $where = $where . 'categoria=\''.$key.'\'';
                else
                    $where = $where . ' OR categoria=\''.$key.'\'';
                $c++;
            }
        }

        /*
         * Paginação?
         */
        if(!empty($pagina)) {
            $item_atual = ($pagina * $itens_por_pagina) - $itens_por_pagina;
            $limit = " LIMIT ".$item_atual.",".$itens_por_pagina;
        }

        /*
         * Sql para listagem
         */
        $sql = "SELECT
                    id, titulo, visitantes,
                    categoria AS cat,
                    DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as adddate,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node
                FROM
                    ".$this->tabela_criar.$where.$order.
                $limit;
        return $sql;

    } // fim getSQLForListing()
}
?>