<?php
class CategoryShortcuts extends Widget
{

	/**
	 * getStructures()
	 *
	 * Retorna todas as estruturas do site.
	 *
	 * @return <array>
	 */
	function getStructures(){

		$sql = "SELECT
					*
				FROM
					categorias
				WHERE
					classe='structure'
				";
		
		$query = Connection::getInstance()->query($sql);

		$est = array();
		foreach( $query as $chave=>$value ){

			if( !$this->envParams['permissoes']->verify($value['id']) ){
				continue;
			}

			$est[$value['id']]['nome'] = $value['nome'];
			$est[$value['id']]['tipo'] = $value['tipo'];
			$est[$value['id']]['id'] = $value['id'];
		}
		return $est;
		
	}

}
?>
