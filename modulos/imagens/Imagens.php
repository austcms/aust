<?php

/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Textos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

class Imagens extends Module
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

        $this->tabela_criar = "imagens";
        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);
	
	}

    /**
     * RESPONSER
     *
     * Carrega conteúdo para leitura externa. Retorna, geralmente, em array.
     */

    public function retornaResumo(){
        return parent::retornaResumo();
    }


    /**
     * trataImagem
     *
     * Trata uma imagem
     *
     * @param array $files O mesmo $_FILE vindo de um formulário
     * @param string $width Valor padrão de largura
     * @param string $height Valor padrão de altura
     * @return array
     */
    function trataImagem($files, $width = "1024", $height = "768"){

        /*
         * Toma dados de $files
         */
        $frmarquivo = $files['frmarquivo']['tmp_name'];
        $frmarquivo_name = $files['frmarquivo']['name'];
        $frmarquivo_type = $files['frmarquivo']['type'];

        /*
         * Abre o arquivo e tomas as informações
         */
        $fppeq = fopen($frmarquivo,"rb");
        $arquivo = fread($fppeq, filesize($frmarquivo));
        fclose($fppeq);

        /*
         * Cria a imagem e toma suas proporções
         */
        $im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
        $largurao = imagesx($im);// pegar a largura da amostra
        $alturao = imagesy($im);// pegar a altura da amostra

        /*
         * Configura o tamanho da nova imagem
         */
        if($largurao > $width)
            $largurad = $width;
        else
            $largurad = $largurao; // definir a altura da miniatura em px

        $alturad = ($alturao*$largurad)/$largurao; // calcula a largura da imagem a partir da altura da miniatura
        $nova = imagecreatetruecolor($largurad,$alturad); // criar uma imagem em branco
        //imageantialias($nova,true);
        //imagecopyresized($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);
        imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

        ob_start();
        imagejpeg($nova, '', 90);
        $mynewimage = ob_get_contents();
        ob_end_clean();

        /*
         * Prepara dados resultados para retornar
         */
        imagedestroy($nova);

        $result["filesize"] = strlen($mynewimage);
        //$result["filedata"] = addslashes($mynewimage);
        $result["filedata"] = $mynewimage;
        $result["filename"] = $frmarquivo_name;
        $result["filetype"] = $frmarquivo_type;

        return $result;

    }


    /**
     * @todo - comentar
     *
     *
     * @param <type> $categorias
     * @param <type> $pagina
     * @param <type> $itens_por_pagina
     * @return <type>
     */
    
    public function SQLParaListagem($categorias = '', $pagina = '', $itens_por_pagina = ''){
        if(!empty($categorias)){
            $order = ' ORDER BY id DESC';
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor){
                if($c == 0)
                    $where = $where . 'categoria=\''.$key.'\'';
                else
                    $where = $where . ' OR categoria=\''.$key.'\'';
                $c++;
            }
        }
        $limit = '';
        if(!empty($pagina)){
            $item_atual = ($pagina * $itens_por_pagina) - $itens_por_pagina;
            $limit = " LIMIT ".$item_atual.",".$itens_por_pagina;
        }
        
        $sql = "SELECT
                id, titulo, visitantes, categoria AS cat, DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as adddate,
                (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                ) AS node
                FROM
                    ".$this->tabela_criar.$where.$order.
                    $limit
                ;
					
        return $sql;
	
    }
     
}

?>