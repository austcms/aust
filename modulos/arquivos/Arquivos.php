<?php

/*********************************
*
*	classe do módulo TEXTOS
*
*********************************/
class Arquivos extends Modulos {

    // TABELA
    protected $db_tabelas;
    protected $sql_das_tabelas;
    protected $sql_registros;
    public $tabela_criar;
    
    /*********************************
	*
	*	funções de ação
	*
	*********************************/

    function __construct($param = '') {
        // pega a string global que diz qual é o charset do projeto
        global $aust_charset;
        if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
            $charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
        }
        // define qual é a tabela principal
        $this->tabela_criar = "arquivos";

        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);


    }

    /**
     * parseUrl()
     *
     * Converte urls com ../ para o formato correto.
     *
     * @param <string> $url
     * @return <string>
     */
    public function parseUrl($url){

        $workVar = explode("/", $url);

        foreach( $workVar as $nr=>$value ){
            if( $value == '..' ){
                unset( $workVar[$nr] );
                unset( $workVar[$nr-1] );
            }
        }

        $url = implode('/', $workVar);
        $url = str_replace("./", "", $url);

        return $url;

    }

    /*
     * funções de verificação ou leitura automática do DB
    */
    public function SQLParaListagem($param) {
        // configura e ajusta as variáveis
        $categorias = $param;
        // se $categorias estiver vazio (nunca deverá acontecer)
        if(!empty($categorias)) {
            $order = ' ORDER BY created_on DESC';
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor) {
                if($c == 0)
                    $where = $where . 'categoria_id=\''.$key.'\'';
                else
                    $where = $where . ' OR categoria_id=\''.$key.'\'';
                $c++;
            }
        }
        // SQL para verificar na tabela CADASTRO_CONF quais campos existem
        $sql = "SELECT
					*, DATE_FORMAT(created_on, '%d/%m/%Y %H:%i') as data, categoria_id AS cat,
					(	SELECT
							nome
						FROM
							categorias AS c
						WHERE
							id=cat
					) AS node
				FROM
					".$this->tabela_criar." AS conf
                ".$where.$order;
        return $sql;
    }

    /*
     * Função para retonar a tabela de dados de uma estrutura
    */
    public function LeTabelaDaEstrutura() {
        return $this->tabela_criar;
    }

    /*
     * Função para retonar a tabela de dados de uma estrutra da cadastro
    */
    public function LeTabelaDeDados($param) {
        if(is_int($param) or $param > 0) {
            $estrutura = "categorias.id='".$param."'";
        } elseif(is_string($param)) {
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
        //echo $sql;
        $mysql = mysql_query($sql);
        $dados = mysql_fetch_array($mysql);
        return $dados['valor'];
    }

}

?>