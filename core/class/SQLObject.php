<?php
/**
 *
 * @since v0.1.5, 30/05/2009
 */
class SQLObject {

	/**
	 *
	 * @var class Classe responsável pela conexão com o banco de dados
	 */
	protected $conexao;

	function __construct(){
		//$this->conexao = $conexaoClass;
	}

	function find($options, $modo = 'all'){
		/**
		 * Configurações gerais
		 *
		 * Ajusta os parâmetros passados em variáveis específicas
		 */
		/**
		 * $fields -> Campos que devem ser carregados
		 */
		$fields = (empty($options['fields'])) ? '*' : ( (is_array($options['fields'])) ? implode(',', $options['fields']) : $options['fields'] );
		/**
		 * $table -> Tabela a ser usada
		 */
		$table = (empty($options['table'])) ? '' : $options['table'];
		/**
		 * $join -> LEFT JOIN, RIGHT JOIN, INNER JOIN, etc
		 */
		$join = (empty($options['join'])) ? '' : ( (is_array($options['join'])) ? implode(' ', $options['join']) : $options['join'] );
		/**
		 * $order
		 */
		$order = (empty($options['order'])) ? '' : ( (is_array($options['order'])) ? 'ORDER BY '. implode(', ', $options['order']) : $options['order'] );

		/**
		 * Verifica condições passadas, formatando o comando SQL de acordo
		 */
		if(!empty($options['conditions'])){
			$conditions = $options['conditions'];
			/**
			 * Chama conditions que monta a estrutura de regras SQL
			 */
			foreach($conditions as $chave=>$value){
				$tempRule = $this->conditions($chave, $conditions[$chave]);
				if(is_array($tempRule)){
					$tempRule = implode(' AND ', $tempRule);
				}
				$rules[] = '('.$tempRule.')';
			}
		}

		/**
		 * Quebra as regras dentro do WHERE para SQL
		 */
		if(is_array($rules)){
			$rules = 'WHERE ' . implode(' AND ', $rules);
		}
		$sql = "SELECT
					$fields
				FROM
					$table
					$join
					$rules
					$order
				";

		/**
		 * Retornar somente SQL
		 *
		 * Se modo==sql
		 */
		if ( $modo == 'sql' ){
			return $sql;
		}

		/**
		 * Debuggar
		 *
		 * Descomente a linha abaixo para debugar
		 */
		$query = $this->query($sql);
		$return = array();
		foreach($query as $chave=>$dados){
			/**
			 * Monta estruturas de saída de acordo como o modo pedido
			 *
			 * O modo padrão é ALL conforme configurado nos parâmetros da função
			 */
			/**
			 * ALL
			 */
			if($modo == 'all'){
				array_push($return, $dados);
			/**
			 * FIRST
			 */
			} elseif($modo == 'first' and (count($fields) == 1 or is_string($fields))){
				if(is_array($fields)){
					$return[] = $dados[$fields[0]];
				} else {
					$return[] = $dados[$fields];
				}
			}
		}

		/**
		 * Descomente a linha abaixo para debugar
		 */
			//pr( $return);
		return $return;
	}

	/*
	 * CONDITIONS
	 *
	 * Monta regras SQL para cada condition
	 */

	function conditions($modo, $conditions){

		$rules = array();
		/**
		 * NOT
		 */
		if($modo == 'NOT'){
			foreach($conditions as $campo=>$value){
				/**
				 * Se for uma array com vários valores
				 */
				if(is_array($value)){
					$rules[] = $campo .' NOT IN(\''. implode('\', \'', $value) . '\')';
				} else {
					$rules[] = $campo .' NOT IN(\''. $value . '\')';
				}
			}
		/**
		 * OR
		 */
		} elseif($modo == 'OR'){
			foreach($conditions as $campo=>$value){
				/**
				 * Se for uma array com vários valores
				 */
				//pr($conditions);
				if(is_array($value)){
					//echo 'oi';
					$rules[] = $campo .' IN(\''. implode('\', \'', $value) . '\')';
				} else {
					//echo 'oi2';
					$rules[] = $campo .' IN(\''. $value . '\')';
				}

			}
			//if(is_array)
			$rules = implode(' OR ', $rules);
		/**
		 * CAMPOS COMUNS
		 */
		} else {

			/**
			 * Ajusta o nome do campo
			 */
			$campo = $modo;
			if(is_array($conditions)){
				foreach($conditions as $value){
					/**
					 * Vários valores para este campo
					 */
					if(is_array($value)){
						foreach($value as $cadaValor){
							$tempRules[] = $campo.'=\''. $cadaValor . '\'';
						}
					/**
					 * Um único valor para este campo
					 */
					} else {
						$tempRules[] = $campo.'=\''. $value . '\'';
					}
				}
				$rules[] = implode(' AND ', $tempRules);
			} else {
				//echo $conditions;

				$rules[] = $campo.'=\''. $conditions . '\'';
			}

		}
		//pr($rules);
		$return = $rules;
		return $return;
		
	}

	/**
	 * saveSql()
	 * 
	 * Crie declarações SQL com algumas opções
	 * 
	 * @param array $params 
	 * @return string SQL criado
	 */
	function saveSql($params){

		if( !empty($params["table"])
			AND !empty($params["data"])
			)
		{
			$sql = "INSERT INTO ".$params["table"];

			$data = $params["data"];
			foreach( $data as $campo=>$value ){
				$campos[] = $campo;
				$valuees[] = $value;
			}

			$sql.= " (".implode(",", $campos) .")";
			$sql.= " VALUES ('".implode("','", $valuees) ."')";

		}

		else {
			return false;
		}

		return $sql;


	}


}

?>
