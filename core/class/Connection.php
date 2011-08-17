<?php
/**
 * CONNECTION ADAPTER
 *
 * Serves as an adapter (PDO or not) between the application and its database.
 *
 * Caution:
 *	  - Regarding PDO: if you find and error, 'mysql unbuffered', error 2014,
 *		you have to free memory after each query.
 *		us PDOStatement::closeCursor() for this.
 *
 * @since v0.1.5, 30/05/2009
 */
class Connection extends SQLObject {

	/**
	 * @var Resource Where the connection handle is located
	 */
	public $conn;
	private $db;

	public $tables = array();
	public $tablesDescribed = array();

	protected $dbConfig;
	private $debugLevel = 0;
	public $encoding = 'utf8';

	function __construct(){
			
		$this->dbConfig = DATABASE_CONFIG::$dbConn;
		$this->encoding = (empty($this->dbConfig['encoding'])) ? 'utf8' : $this->dbConfig['encoding'];
		
		if( $this->PdoExtension() ){
			$this->pdoInit($this->dbConfig);
			if( $this->debugLevel == 1 ){
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			}
		}
		/*
		 * non pdo mortal connection
		 */
		else {
			$this->DbConnect($this->dbConfig);
		}

		$this->_acquireTablesList();
	}

	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new Connection;
		}

		return $instance[0];
	}

	/**
	 * pdoInit
	 * 
	 * This method is called if PDO extension is present.
	 *
	 * @param array $dbConfig
	 *	  driver: (mysql, postgresql, mssql, oracle, etc)
	 *	  database: database name
	 *	  server: host address
	 *	  username:
	 *	  password:
	 */
	protected function pdoInit($dbConfig){

		$dbConfig['driver'] = (empty($dbConfig['driver'])) ? 'mysql' : $dbonfig['driver'];

		$port = '';
		if( !empty($dbConfig['port']) )
			$port = 'port='.$dbConfig['port'].';';

		$connectionStatement = $dbConfig['driver'].':host='.$dbConfig['server'].';'
								. $port
								.   'dbname='.$dbConfig['database'];

		/**
		 * @todo - should raise exception if no connection is found.
		 */
		try {
			if( $this->conn = new PDO(
									$connectionStatement,
									$dbConfig['username'], $dbConfig['password'],
									array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true)
								)

			){
				$this->dbExists = true;
			} else {
				return false;
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			exit();
		}

		if( !empty($dbConfig["encoding"]) ){
			$this->conn->exec("SET NAMES '".$dbConfig["encoding"]."'");
			$this->conn->exec("SET character_set_connection=".$dbConfig["encoding"]);
			$this->conn->exec("SET character_set_client=".$dbConfig["encoding"]);
			$this->conn->exec("SET character_set_results=".$dbConfig["encoding"]);
		}

		$this->pdo = $this->conn;
		return true;

	}
	/**
	 * @param array $dbConfig
	 *	  driver: (mysql, postgresql, mssql, oracle, etc)
	 *	  database: database name
	 *	  server: host address
	 *	  username:
	 *	  password:
	 */
	protected function DbConnect($dbConfig){
		$conexao = $dbConfig;
		$conn = mysql_connect($conexao['server'], $conexao['username'], $conexao['password']) or die('Erro ao encontrar servidor');
		if(mysql_select_db($conexao['database'], $conn)){
			$this->dbExists = true;
			$this->db = $db;
			$this->conn = $conn;
		} else {
			$this->dbExists = false;
		}
	}

	public function dbExists(){
		return $this->dbExists;
	}

	/*
	 * CRUD
	 */
	/**
	 * Queries' integration
	 *
	 * @param string $sql
	 * @return array Query result
	 */
	public function query($sql, $type = "ASSOC"){
		/*
		 * We count how long the queries took. This is the timer init
		 */
		$sT = microtime(true);

		if($this->PdoExtension()){
			/**
			 * Roda o SQL e trás os resultados para um array
			 */

			if ( !empty( $type ) ){
				/*
				 * if "ASSOC", uses PDO::FETCH_ASSOC
				 */
				if( $type == "ASSOC" ){
					$query = $this->conn->query( $sql, PDO::FETCH_ASSOC );
				}
				/*
				 * If any type accepted by PDO
				 */
				else if (
					in_array( 
						$type,
						array(
							PDO::FETCH_ASSOC,
							PDO::FETCH_BOTH,
							PDO::FETCH_BOUND,
							PDO::FETCH_CLASS,
							PDO::FETCH_INTO,
							PDO::FETCH_LAZY,
							PDO::FETCH_NUM,
							PDO::FETCH_OBJ,
						)
					)
				){
					$query = $this->conn->query( $sql, $type );
				}
			} else {
				$query = $this->conn->query($sql);
			}

			if( $query === false){
				$errorInfo = $this->conn->errorInfo();
				$debugResult = end( $errorInfo );
			}

			if( !empty($query) ){

				foreach($query as $chave=>$value){
					$result[] = $value;
				}
				$query->closeCursor();
			}
			
		}
		/*
		 * PDOless query
		 */
		else {

			/**
			 * @todo - implement errorinfo
			 */
			$mysql = mysql_query($sql);
			
			while($dados = mysql_fetch_array($mysql)){
				$result[] = $dados;
			}
		}
		
		if(empty($result)){
			$result = array();
		}

		/*
		 * Debugs SQL
		 */
		if( Registry::read('debugLevel') > 1 ){

			$sEnd = microtime(true);

			if( !empty($debugResult) AND !is_string($debugResult) ){
				$debugResult = count($result);
			} else if( empty($debugResult) ) {
				$debugResult = count($result);
			}
			/*
			 * DESCRIBEs are hidden from view
			 */
			if( substr( $sql, 0, 8 ) !== 'DESCRIBE' ){
				$debugVars = array(
					'sql' => $sql,
					'result' => $debugResult,
					'time' => $sEnd - $sT
				);
				Registry::add('debug',$debugVars);
			}
		}
		return $result;
	}

	/**
	 * @param string $sql
	 * @return <mixed>
	 */
	public function exec($sql, $mode = ''){
		/**
		 * Timer init
		 */
		$sT = microtime(true);
		$debugResult = false;
		
		if($this->PdoExtension()){
			$result = $this->conn->exec($sql);

			if( $result === false){
				$errorInfo = $this->conn->errorInfo();
				$debugResult = end( $errorInfo );
			}

			/**
			 * If CREATE TABLE, success returns 0 and insuccess returns false.
			 * This clarifies the result
			 */
			if( in_array( $mode, array('CREATE_TABLE', 'CREATE TABLE') ) ){
				if($result == 0 AND !is_bool($result)){
					return true;
				} else {
					return false;
				}
			}
		} else {
			$result = mysql_query($sql);
		}

		/*
		 * Debugs SQL
		 */
		if( Registry::read('debugLevel') > 1 ){

			$sEnd = microtime(true);

			if( empty($debugResult)
				OR !is_string($debugResult) )
			{
				$debugResult = count($result);
			}

			/*
			 * DESCRIBEs are not shown
			 */
			if( substr( $sql, 0, 8 ) !== 'DESCRIBE' ){

				if( Registry::read('debugLevel') < 3) {
					if( strlen($sql) > 1200 ){
						$sql = substr($sql, 0, 1200).' ... <strong>Truncated message</strong>.';
					}
				}
				$debugVars = array(
					'sql' => $sql,
					'result' => $debugResult,
					'time' => $sEnd - $sT
				);
				Registry::add('debug',$debugVars);
			}
		}
		$this->debug = $debugResult;

		return $result;

	}

	/**
	 * @todo - comentar
	 *
	 * @param <type> $sql
	 * @return <type>
	 */
	public function count($sql){

		if($this->PdoExtension()){

			$mysql = $this->conn->prepare($sql);
			$mysql->execute();

			$total = $mysql->rowCount();
			$mysql->closeCursor();

			return $total;
		} else {
			$mysql = mysql_query($sql);
			return mysql_num_rows($mysql);
		}
	}

	public function lastInsertId(){
		return $this->conn->lastInsertId();
	}

	function _acquireTablesList(){
		$tables = $this->query("SHOW TABLES");
		if( empty($tables) )
			return array();
			
		$result = array();
		
		foreach( $tables as $key=>$value){
			$name = reset($value);
			$result[] = $name;
		}
		
		$this->tables = $result;
		return $result;
	}
	
	function hasTable($tableName){
		$this->_acquireTablesList();
		
		return in_array($tableName, $this->tables);
	}
	
	function tableHasField($table, $field){
		$fields = $this->query('DESCRIBE '.$table);
		
		foreach( $fields as $key=>$value ){
			if( $value['Field'] == $field )
				return true;
		}
		
		return false;
	}

	function describeTable($table, $fieldAsKey = true){

		if( !empty($this->tablesDescribed[$table]) )
			return $this->tablesDescribed[$table];

		$fields = $this->query('DESCRIBE '.$table);
		
		if( $fieldAsKey ){
			$result = array();
			foreach( $fields as $key=>$value ){
				$result[$value['Field']] = $value;
			}
		} else {
			$result = $fields;
		}

		$this->tablesDescribed[$table] = $result;
		
		return $result;
	}

	protected function PdoExtension(){
		return extension_loaded('PDO');
	}
}

?>