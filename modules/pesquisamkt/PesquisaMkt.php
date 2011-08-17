<?php
/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */

class PesquisaMkt extends Module
{

	public $mainTable = 'pesqmkt';
	
	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'adddate',
		'updated_on' => 'addate'
	);
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

		$this->tabela_criar = "pesqmkt";
		/**
		 * A classe Pai inicializa algumas varíaveis importantes. A linha a
		 * seguir assegura-se de que estas variáveis estarão presentes nesta
		 * classe.
		 */
		parent::__construct($param);
	
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
			foreach($categorias as $key=>$value){
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
					id, titulo, visitantes, categoria AS cat,
					DATE_FORMAT(adddate, '".$this->date['standardFormat']."') as adddate,
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
	
	/**
	 * loadFirstQuestions()
	 *
	 * Carrega a primeira questão de cada pergunta.
	 *
	 * @param $query array Resultado de uma query
	 */
	function loadFirstQuestions($query){
		if( empty($query) ) return $query;
		
		// pega ids
		foreach( $query as $key=>$value ){
			$ids[] = $value['id'];
			$questionKeys[$value['id']] = $key;
		}
		
		$sql = "SELECT
					id, pesqmkt_id, texto AS text
				FROM
					pesqmkt_perguntas
				WHERE
					pesqmkt_id IN ('".implode("','", $ids)."')
				GROUP BY
					pesqmkt_id
				ORDER BY
					id ASC
				";
		$questions = Connection::getInstance()->query($sql);

		foreach( $questions as $value ){
			$query[ $questionKeys[$value['pesqmkt_id']] ]['question'] = $value;
		}
		$result = $query;
		
		return $result;
	} // end loadFirstQuestions()

}

?>