<?php
/**
 * Classe dbSchema.
 *
 * Contém propriedades das tabelas da base de dados
 *
 * @since v0.1.5, 15/05/2009
 */
/**
 * COMO INSTALAR UM NOVO DBSCHEMA
 *	  1) Instancie um objeto usando a classe dbSchema, enviando como
 *		 parâmetros:
 *		 a) variável contendo o DBSCHEMA;
 *		 b) a classe de conexão;
 *		 ex. $dbSchema = new dbSchema($dbSchema, $conexao);
 * 
 *		 O novo schema fica guardado em $this->dbSchema;
 *
 *	  2) Agora $dbSchema já está pronta para ser usada. No momento de instalar
 *		 um novo schema, chame $dbSchema->instalarSchema();
 */
class dbSchema
{
	/**
	 *
	 * @var array Possui o schema das tabelas do DB
	 */
	public $dbSchema;
	/**
	 *
	 * @var class Classe responsável pela conexão com o banco de dados
	 */
	protected $conexao;
	/**
	 *
	 * @var array Tabelas existentes no DB
	 */
	public $tabelasAtuais;
	/**
	 *
	 * @var array Campos inexistentes na base de dados atual
	 */
	public $camposInexistentes;
	/**
	 *
	 * @var array Contém informações sobre quais tabelas estão instaladas.
	 *
	 * Cada tabela (sendo índice desta array) tem os seguintes valores
	 *	 -1: tabela existe, mas algum(ns) campo(s) não existe(ém)
	 *	  0: tabela não existe
	 *	  1: tabela existe
	 *
	 */
	public $status;
	/**
	 *
	 * @var array ?
	 */
	public $schemaStatus;
	/**
	 *
	 * @var array Contém índices com nomes das tabelas instaladas recentemente, com valores:
	 *	  0: não instalado por falha.
	 *	  1: instalado normalmente.
	 */
	public $tabelasInstaladas;
	/**
	 *
	 * @var array Possui todos os nomes especiais de $dbSchema
	 */
	protected $camposEspeciais = array(
		'dbSchemaTableProperties',
		'dbSchemaSQLQuery',
	);


	/**
	 * Ajusta $this->tabelasExistente com as tabelas e campos atuais
	 *
	 * @param array $dbSchema O Schema das tabelas necessário ao funcionamento do sistema
	 * @param class $conexaoClass Classe de conexão com o banco de dados
	 */
	function  __construct() {
		require(INSTALLATION_DIR.'dbschema.php');
		$this->dbSchema = $dbSchema;
		$this->conexao = Connection::getInstance();
	}

	function setSchema($schema = array()){
		if( $this->isDbSchemaFormatOk($schema) ){
			$this->schema = $schema;
			return true;
		}
		
		return false;
	}
	
	/**
	 * getInstance()
	 *
	 * Para Singleton
	 *
	 * @staticvar <object> $instance
	 * @return <Conexao object>
	 */
	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new dbSchema;
		}

		return $instance[0];

	}

	/**
	 * VERIFICAÇÕES
	 */
	/**
	 * Função verifica se o Schema está instalado
	 *
	 * @return string
	 *	 -2: Algumas tabelas não existem
	 *	 -1: Todas as tabelas existém, mas alguns campos não
	 *	  0: Nenhuma tabela existe
	 *	  1: Tudo ok
	 */
	public function verificaSchema(){
		/**
		 * Reseta propriedades do objeto
		 */
		$this->camposInexistentes = array();
		$this->tabelasInstaladas = array();
		$this->tabelasAtuais();
		/**
		 * Nomes de campos especiais
		 */
		$status = array();

		foreach($this->dbSchema as $tabela=>$value){
			/**
			 * Se tabela não existe
			 */
			if(!$this->verificaTabela($tabela)){
				$status[$tabela] = 0;
			} else {
				/**
				 * Se tabela existe, verifica campos
				 */
				$status[$tabela] = 1;
				if(is_array($value)){
					/**
					 * Loop por cada campo do Schema
					 */
					foreach($value as $campo=>$properties){
						/**
						 * Se não for um dado especial (informações sobre associação, etc),
						 * é um campo normal
						 */
						/**
						 * Se campo não existe
						 */
						if(!$this->verificaCampo($campo, $tabela)){
							$status[$tabela] = -1;
							$this->camposInexistentes[$tabela][$campo] = $properties;
						}
					}
				}
			}
		}

		/**
		 * Guarda status das tabelas
		 */
		$this->status = $status;

		/**
		 * Retorna resultado
		 *	 -2: algumas tabelas existém e outras nãos
		 *	 -1: as tabelas existém, mas alguns campos não
		 *	  0: nenhuma tabela existe
		 *	  1: todas as tabelas existém
		 */
		if(!empty($status)){
			/**
			 * Algumas tabelas existém e outras não
			 */
			if(in_array(0, $status) AND in_array(1, $status)){
				$this->schemaStatus = -2;
			}
			/**
			 * As tabelas existém, mas alguns campos não
			 */
			elseif(in_array(1, $status) AND in_array(-1, $status)) {
				$this->schemaStatus = -1;
			}
			/**
			 * Todas as tabelas precisam ser instaladas
			 */
			elseif(in_array(0, $status) AND !in_array(1, $status)) {
				$this->schemaStatus = 0;
			} else {
				$this->schemaStatus = 1;
			}
		} else {
			/**
			 * Há algum erro com o schema
			 */
			$this->schemaStatus = false;
		}

		return $this->schemaStatus;
		
	}

	/**
	 * tabelasAtuais()
	 *
	 * Verifica quais as tabelas estão instaladas atualmente
	 *
	 * @return <array>
	 */
	public function tabelasAtuais(){
		/**
		 * Carrega todas as tabelas do DB
		 */
		$mysql = Connection::getInstance()->query('SHOW TABLES');

		$this->tabelasAtuais = array();
		/**
		 * Laço por todas as tabelas do DB
		 */
		//while( $dados = mysql_fetch_array($mysql) ){
		foreach($mysql as $chave=>$dados){
			/**
			 * Carrega todos os campos das tabelas e então grava em $this->tabelasAtuais
			 */
			$describeSql = 'DESCRIBE '.reset($dados);


			foreach(Connection::getInstance()->query($describeSql) as $tabela=>$info){
				$this->tabelasAtuais[ reset($dados) ][$info['Field']] = $info;
			}
		}

		return $this->tabelasAtuais;

	}

	/**
	 * Verifica se determinada tabela existe no DB atual
	 *
	 * @param string|array $tabela Tabela a ser verifica ante
	 * @return bool Retorna se determinada tabela existe
	 */
	protected function verificaTabela($tabela){
		/**
		 * Verifica se $tabela existe DB atual
		 */
		if(is_string($tabela)){
			if(!array_key_exists($tabela, $this->tabelasAtuais)){
				return false;
			}
		}
		return true;

	}

	/**
	 * Verifica se determinado campo existe no DB atual
	 *
	 * @param string|array $campo
	 * @return bool
	 */
	protected function verificaCampo($campo, $tabela){

		//pr($this->tabelasAtuais[$tabela]);
		if(is_string($campo)){
			if(!in_array($campo, $this->camposEspeciais)){
				if(!array_key_exists($campo, $this->tabelasAtuais[$tabela])){
					//echo $campo;
					return false;
				}
			}
		}

		return true;
	}

	/**
	 *
	 * INSTALAÇÃO
	 *
	 * Métodos de instalação
	 */
	/**
	 * 
	 * Instala um Schema de DB a partir de $this->dbSchema.
	 * 
	 * Para instalação de tabelas de módulos, o módulo deve instanciar um objeto
	 * -new dbSchema- e enviar seu schema de tabelas para o objeto,
	 * posteriormente chamando a função a seguir.
	 *
	 * @return bool
	 *	  - Retorna 1 se tudo ocorreu normalmente;
	 *	  - Se não está tudo ok, retorna array com tabelas não instaladas.
	 */
	public function instalarSchema(){
		/**
		 * Verifica se as tabelas já existem. Somente se estiver tudo ok a
		 * instalação continua
		 */

		$checkedSchema = $this->verificaSchema();
		
		if( $checkedSchema == -1 ){
			foreach( $this->camposInexistentes as $table=>$field ){
				$sql = $this->sqlForMissingFields( array($table => $field) );
				Connection::getInstance()->exec( $sql, "CREATE_TABLE");
			}
			
			return true;
		}
		
		if($checkedSchema != 1 AND $this->isDbSchemaFormatOk($this->dbSchema) ){

			/**
			 * Tabela por tabela do Schema
			 */

			$sql = $this->sql();

			/**
			 * Executa Query por Query, criando cada tabela inexistente
			 */
			if(is_array($sql)){
				foreach($sql as $tabela=>$value){
					/*
					 * SubSQLQueries, por exemplo
					 */
					if( !empty($value) AND is_array($value) ){

						
						/**
						 * subqueries
						 */
						foreach( $value as $subTabelas ){

							foreach($subTabelas as $subSql){
								if(Connection::getInstance()->exec($subSql)){

								} else {

								}
							}
						}
					}
					/*
					 * SQLs simples, como TABLES
					 */
					else {
						$mysql = Connection::getInstance()->exec($value, 'CREATE_TABLE');
						if($mysql){
							$resultado[$tabela] = 1;
							/**
							 * Guarda resultado como tabela instalada
							 */
							$this->tabelasInstaladas[$tabela] = 1;

						} else {
							/**
							 * Guarda resultado como tabela não instalada
							 */
							$this->tabelasInstaladas[$tabela] = 0;

							$resultado[$tabela] = 0;
						}
					}
				}
			}

			/**
			 * Se alguma tabela não foi instalada, retorna a array contendo o resultado
			 */
			if(in_array(0, $resultado)){
				return $resultado;
			}
			/**
			 * Tudo foi ok
			 */
			return 1;
		}
		/**
		 * Se as tabelas já existêm ou o schema está com defeito
		 */
		else {
			return false;
		}
	}

	/**
	 * isDbSchemaFormatOk()
	 *
	 * Verifica se o formato do schema está correto
	 *
	 * @param <array> [opcional] $dbSchema
	 * @return <bool>
	 */
	public function isDbSchemaFormatOk($dbSchema = ''){
		if( is_array($dbSchema) )
			return true;

		return false;
	}

	public function sql($schema = array()){
		if( empty($schema) )
			$schema = $this->dbSchema;
		
		$sql = false;
		$this->tabelasAtuais();
		foreach($schema as $tabela=>$campos){

			/*
			 * Só roda SQL se tabela não existe ainda.
			 */
			foreach($campos as $nome=>$properties){

				if( !in_array( $nome, $this->camposEspeciais) ){
					$camposSchema[] = $nome.' '.$properties;
				}
				/*
				 * Campo especial (Keys)
				 */
				else {
					if(is_array($properties)){
						/**
						 * Percorre os campos especiais (Keys)
						 */
						foreach($properties as $key=>$properties2){
							/**
							 * Se for propriedades como Keys primárias, únicas, etc
							 */
							if($nome == 'dbSchemaTableProperties'){
								$camposSchema[] = $key.' '.$properties2;
							}
							/**
							 * Se for SQLs que devem ser rodados na criação da tabela
							 */
							elseif($nome == 'dbSchemaSQLQuery'){
								$sqlsubquery[$tabela][] = $properties2;
							}
						}
					}

				}

		   	}
			$sql[$tabela] = 'CREATE TABLE '.$tabela.' ('. implode(', ', $camposSchema) .')';
			unset($camposSchema);
		}

		if( !empty($sqlsubquery) )
			$sql['dbSchemaSQLQuery'] = $sqlsubquery;

		return $sql;
	}

	/**
	 * sqlForMissingFields()
	 * 
	 * Retorna o SQL necessário para criar os campos que estão
	 * faltando de acordo com $params.
	 * 
	 * @param <array> $params
	 * @param <string> $mode
	 * @return <string> 
	 */
	public function sqlForMissingFields($params, $mode = ''){

		$table = key($params);
		$fields = $params[$table];
		$addStatement = '';

		if( is_array($fields) ){
			foreach( $fields as $field=>$property ){
				$addStatement[] = 'ADD COLUMN '.$field. ' '.$property.' ';
			}
		}

		return 'ALTER TABLE '.$table.' '.implode(", ", $addStatement);
	}
}

?>
