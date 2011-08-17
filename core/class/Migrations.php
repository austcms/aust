<?php
/**
 * MIGRATIONS
 *
 * Contém os métodos para executar os migrations, seja do Core ou dos módulos.
 *
 * @since v0.1.6, 18/01/2010
 */
class Migrations
{
	public $conexao; // refatorar: verificar se não está sendo usado e retirar
	public $connection;

	/**
	 * Versão do Migration atual.
	 *
	 * @var <string>
	 */
	public $version = '';
	/**
	 * Nome do módulo atual.
	 *
	 * @var <string>
	 */
	public $modName = '';

	function __construct($modName, $conexao = ''){
		$this->conexao = Connection::getInstance();
		$this->connection = $this->conexao;
		$this->modName = $modName;

		$regexp = "/([0-9]{14})/";

		if( preg_match( $regexp, get_class($this), $matches) ){
			$this->version = $matches[0];
		}
	}

	/**
	 * goUp()
	 *
	 * Executa o método para avançar a versão e salva o
	 * resultado no DB.
	 *
	 * @return <bool>
	 */
	public function goUp(){

		if( $this->up() ){
			$sql = "INSERT INTO
						migrations_mods
						(version, module_name)
					VALUES
						('".$this->version."', '".$this->modName."')
					";
			Connection::getInstance()->exec($sql);

			return true;
		}

		return false;

	}

	function up(){
		return true;
	}

	function down(){
		return true;
	}

	/**
	 * createTable()
	 *
	 * Cria uma tabela usando $schema como um Schema ou como uma string
	 * sql.
	 *
	 * @param <mixed> $schema
	 * @return <bool>
	 */
	function createTable($dbschema){

		$c = Connection::getInstance();
		if( is_array($dbschema) ){
			$schema = new dbSchema( $dbschema );

			if( $schema->isDbSchemaFormatOk($dbschema) ){

				if( is_array($schema->sql($dbschema))){
				
					foreach( $schema->sql($dbschema) as $tabela=>$sql ){
						/**
						 * @todo - deve-se fazer com que se a
						 * função a seguir não funcione ou retorne
						 * falso, mostrar uma mensagem de erro.
						 */
						$c->exec($sql);
					}
				}
			}
		} else if( is_string($dbschema) ){
			if( !$c->exec($dbschema) )
				return false;
		}

		return true;
	}

	public function dropTable($table, $field){
		$sql = "ALTER TABLE ".
					$options['table'].
				" DROP COLUMN ".
				$options['field'];

		Connection::getInstance()->exec($sql);
	}

	public function addField($options = array()){
		if( is_array($options) ){

			if( !array_key_exists('table', $options)
				OR !array_key_exists('field', $options)
				OR !array_key_exists('type', $options) )
				return false;


			$position = '';
			if( !empty($options['position']) )
				$position = $options['position'];

			$default = '';
			if( ( !empty($options['default']) ) && 
				$options['default'] !== false )
				$default = "DEFAULT '".$options['default']."'";
			

			$sql = "ALTER TABLE ".
						$options['table'].
					" ADD COLUMN ".
					$options['field'].
					" ".$options['type'].
				" ".$default.' '.$position;
			
			Connection::getInstance()->exec($sql);
		}
	}

}

?>