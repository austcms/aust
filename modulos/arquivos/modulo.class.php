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
	
	function __construct(){
		// pega a string global que diz qual é o charset do projeto
		global $aust_charset;
		if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
			$charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
		}
		// define qual é a tabela principal
		$this->tabela_criar = "arquivos";
		
		// sql das tabelas
		$this->db_tabelas[] = "arquivos";
		$this->sql_das_tabelas[] = "
			CREATE TABLE arquivos (
				id int NOT NULL auto_increment,
				titulo varchar(120) {$charset},
				titulo_encoded varchar(120) {$charset},
				resumo text {$charset},
				descricao text {$charset},
				local varchar(80) {$charset},
				url text {$charset},
                arquivo_nome varchar(250) {$charset},
                arquivo_tipo varchar(250) {$charset},
                arquivo_tamanho varchar(250) {$charset},
                arquivo_extensao varchar(10) {$charset},
				tipo varchar(80) {$charset},
				referencia varchar(120) {$charset},
				categorias_id int,
				visitantes int NOT NULL DEFAULT '0',
				restrito varchar(120) {$charset},
				publico varchar(120) {$charset},
				bloqueado varchar(120) {$charset},
				aprovado int,
				adddate datetime,
				autor int,
				PRIMARY KEY (id),
				UNIQUE id (id)
			) {$charset}
			";
		$this->sql_registros[] = "";
		//echo $this->sql_das_tabelas[0];

	
	}

    /*
     * funções de verificação ou leitura automática do DB
     */
    public function SQLParaListagem($param){
        // configura e ajusta as variáveis
        $categorias = $param;
        // se $categorias estiver vazio (nunca deverá acontecer)
		if(!empty($categorias)){
			$order = ' ORDER BY adddate DESC';
			$where = ' WHERE ';
			$c = 0;
			foreach($categorias as $key=>$valor){
				if($c == 0)
					$where = $where . 'categorias_id=\''.$key.'\'';
				else
					$where = $where . ' OR categorias_id=\''.$key.'\'';
				$c++;
			}
		}
        // SQL para verificar na tabela CADASTRO_CONF quais campos existem
		$sql = "SELECT
					*, DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as data, categorias_id AS cat,
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
    public function LeTabelaDaEstrutura(){
        return $this->tabela_criar;
    }

    /*
     * Função para retonar a tabela de dados de uma estrutra da cadastro
     */
    public function LeTabelaDeDados($param){
        if(is_int($param) or $param > 0){
            $estrutura = "categorias.id='".$param."'";
        } elseif(is_string($param)){
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