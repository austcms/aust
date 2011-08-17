<?php

/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */

class YouTube extends Module
{

	public $mainTable = "youtube_videos";
	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'adddate',
		'updated_on' => 'addate'
	);
	
	public $authorField = "autor";
	
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
		$where = "";
		$order = "";
		$limit = "";
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
					id, titulo, visitantes, categoria AS cat, DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as adddate,
					(	SELECT
							nome
						FROM
							categorias AS c
						WHERE
							id=cat
					) AS node
				FROM
					".$this->useThisTable().$where.$order.
				$limit
				;
					
		return $sql;
	
	}
	 
	 

	
}

?>