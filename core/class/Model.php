<?php
/**
 * Arquivo que representa a estrutura controller de um MV
 *
 * @since v0.1.5, 22/06/2009
 */
class Model extends Aust
{

	public function save($data){

		if( is_array($data) ){
			/**
			 * Loop por cada tabela com valores enviados
			 */
			foreach($data as $tabela=>$campos){

				unset($camposStr);
				unset($valueStr);
				/**
				 * INSERT
				 */
				if( empty($data[$tabela]["id"]) ){
					/**
					 * Loop por cada campo e seus valores
					 */
					foreach( $campos as $campo=>$value ){

						/*
						 * Múltiplos Inserts
						 */
						if( is_int($campo) ){
							
							foreach( $value as $multipleInsertsCampo=>$multipleInsertsValor ){
								//pr($value);
								$camposStrMultiplo[] = $multipleInsertsCampo;
								$valueStrMultiplo[] = $multipleInsertsValor;
								$tempSql = "INSERT INTO
												".$tabela."
													(".implode(",", $camposStrMultiplo).")
											VALUES
												('".implode("','", $valueStrMultiplo)."')
											";
								/**
								 * SQL deste campo
								 */
								$sql[] = $tempSql;

								unset($valueStrMultiplo);
								unset($camposStrMultiplo);
								unset($tempSql);
							}

						}
						/*
						 * Inclusão normal única
						 */
						else if( is_string($campo) ){
							$camposStr[] = $campo;
							$valueStr[] = addslashes( $value);
						}
					}

					if( !empty($camposStr)
						AND !empty($valueStr) )
					{

						$tempSql = "INSERT INTO
										".$tabela."
											(".implode(",", $camposStr).")
									VALUES
										('".implode("','", $valueStr)."')
									";
						/**
						 * SQL deste campo
						 */
						$sql[] = $tempSql;
						unset($tempSql);
					}
				}
				/**
				 * UPDATE
				 */
				else {

					$w = $data[$tabela]["id"];
					unset($data[$tabela]["id"]);
					/**
					 * Loop por cada campo e seus valores
					 */
					foreach( $campos as $campo=>$value ){
						if( $campo != "id" ){
							$camposStr[] = $campo."='".addslashes($value)."'";
						}
						
					}

					$tempSql = "UPDATE
									".$tabela."
								SET
									".implode(",", $camposStr)."
								WHERE
									id='".$w."'
								";
					/**
					 * SQL deste campo
					 */
					$sql[] = $tempSql;
				}
			}
			
			if( count($sql) > 0 ){
				foreach( $sql as $instrucao ){

					Connection::getInstance()->exec($instrucao);
				}
				
				return true;
			}
		}

		return false;
	}

	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new Model;
		}

		return $instance[0];
	}
	
}

?>