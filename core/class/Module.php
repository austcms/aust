<?php
/**
 * abstract Modulo
 *
 * Superclasse dos módulos
 *
 * @since v0.1.5, 30/05/2009
 */
class Module extends ActiveModule
{

	/*
	 *
	 * CONFIGURAÇÕES ESPECÍFICAS DO MÓDULO
	 *
	 */
		/**
		 *
		 * @var <string> Tabela principal de dados
		 */
		public $mainTable;

		/**
		 *
		 * @var <array> Formatos de data
		 */
		public $date = array(
			'standardFormat' => '%d/%m/%Y',
			'created_on' => 'created_on',
			'updated_on' => 'update_on'
		);

		public $fieldsToLoad = array(
			'title', 'pageviews'
		);

		public $titleEncodedField = 'title_encoded';
		public $authorField = "admin_id";

		public $austField = 'node_id';
		public $order = 'id DESC';

		public $defaultLimit = '25';
		public $limit;
		
		public $viewModes = array();

		// instanciated structure name
		public $name;
		// instanciated structure information
		public $information = array();

	/*
	 *
	 * VARIÁVEIS DE QUERY
	 *
	 */
		/**
		 *
		 * @var <int> Id em uso
		 */
		public $w;

		/**
		 *
		 * @var <array>
		 */
		public $loadedIds;

		/**
		 *
		 * @var <string> Contém a última SQL criada.
		 */
		public $lastSql;
		/**
		 *
		 * @var <string> Contém a última SQL criada para contar quantos registros
		 * há no DB.
		 */
		public $lastCountSql;
		/**
		 *
		 * @var <int> Total de registros no DB.
		 */
		public $totalRows;
		/**
		 *
		 * @var <array> Último resultado de query executado.
		 */
		public $lastQuery;
		
		/**
		 * The result from load() will have keys incremental, i.e.
		 *
		 * 		array('0' => result_1, '1' => result_2, etc)
		 *
		 * If it is set to true, the key will be the id of the record, i.e.
		 *
		 * 		array('346' => result_1, '490' => result_2, etc)
		 */
		public $idAsKeyResult = false;
	
	/**
	 * VARIÁVEIS DO MÓDULO
	 */
		/**
		 *
		 * @var <array> Contém a tabela atual descrita
		 */
		public $describedTable;
		/**
		 *
		 * @var <string> erros e sucessos das operações
		 */
		public $status;

	/**
	 * VARIÁVEIS DE AMBIENTE
	 *
	 * Conexão com banco de dados, sistema Aust, entre outros
	 */
		/**
		 *
		 * @var class Classe responsável pela conexão com o banco de dados
		 */
		public $connection;

		/**
		 *
		 * @var <int> Contém o número do Nodo atual
		 */
		public $austNode;
		/**
		 *
		 * @var class Classe responsável pela conexão com o banco de dados
		 */
		public $aust;
		/**
		 *
		 * @var array Configurações estáticas do módulo
		 */
		public $config;
		/**
		 *
		 * @var array Configurações estáticas do módulo
		 */
		public $structureConfig;
		/**
		 *
		 * @var array Configurações estáticas do módulo
		 */
		public $structureFieldsConfig;

		public $params;

	/**
	 *
	 * @var <bool> Indica se este é um teste. O sendo, não realiza
	 * alguns procedimentos impossíveis de serem realizados via
	 * testes unitários, como envio HTTP de dados.
	 */
	public $testMode = false;

	/**
	 * __CONSTRUCT()
	 *
	 * @param array $param:
	 *	  'conexao': Contém a conexão universal
	 */
	function __construct($austNode = ""){

		$this->austNode($austNode);

		if( !empty($_GET['w']) AND is_numeric($_GET['w']) )
			$this->w = $_GET['w'];

		/**
		 * Ajusta a conexao para o módulo
		 */
			$this->connection = Connection::getInstance();
		/**
		 * Usuário atual
		 */
			$this->user = User::getInstance();

	   	$this->config = $this->loadConfig();
		
		if( !empty($this->config['viewmodes']) )
			$this->viewModes = $this->config['viewmodes'];
		else
			$this->viewModes = array('list');

		$this->limit = $this->defaultLimit;
	}
	
	function austNode($id = ""){
		if( !empty($id) && is_numeric($id) ){
			$this->information = Aust::getInstance()->getStructureById($id);
			if( !empty($this->information) )
				$this->name = $this->information["name"];
		}
		return parent::austnode($id);
	}

	/*
	 *
	 * CRUD
	 *
	 */

	public function fixEncoding($post = array()){
		if( Connection::getInstance()->encoding != 'utf8' )
			return $post;

		foreach( $post as $key=>$value ){
			if( is_string($value) &&
				!mb_check_encoding($value, 'UTF-8') )
			{
				$value = mb_convert_encoding($value, "UTF-8", "auto");
			}
			
			$post[$key] = $value;
		}
		
		return $post;
	}
	/**
	 * save()
	 *
	 * Comando que deve poder ser chamado por qualquer módulo.
	 *
	 * Este super-método serve estritamente para salvar dados
	 * no DB. Se um módulo precisa algo diferente disto,
	 * sobrescreva este método na classe do módulo.
	 *
	 * @param <array> $post
	 * @return <bool>
	 */
	public function save($post = array(), $params = false){

		$post = $this->fixEncoding($post);

		if( empty($post['method']) AND
			empty($post['metodo']) )
		{
			throw new Exception("Opção 'method' não especificado em Module");
			return false;
		}

		if( !empty($post['method']) )
			$method = $post['method'];
		else if( !empty($post['metodo']) )
			$method = $post['metodo'];

		/*
		 * Gera SQL
		 */
		$sql = $this->generateSqlFromForm($post, $method);
		/**
		 * Salva no DB
		 */
		if( Connection::getInstance()->exec($sql) !== false ){

			if( !empty($post['w']) OR $post['w'] > 0 ){
				$this->w = $post['w'];
			} else {
				$this->w = Connection::getInstance()->conn->lastInsertId();
			}

			return true;
		}

		return false;
	}
	/**
	 * load()
	 *
	 * Responsável por carregar dados-padrão da estrutura,
	 * como para listagens.
	 *
	 * @return <array>
	 */
	public function load($param = ''){
		$this->loadedIds = array();
		$paramForLoadSql = $param;

		/*
		 * austNode é um conjunto de arrays
		 */
		if( !empty($param['austNode']) ){
			if( is_array($param['austNode']) ){
				if( !is_numeric($param['austNode']) ){
					$arrayKeys = array_keys($param['austNode']);
					$austNode = reset($arrayKeys);
				} else
					$austNode = $param['austNode'];
			} else if( is_numeric($param['austNode']) ){
				$austNode = $param['austNode'];
				$paramForLoadSql['austNode'] = $austNode;
			}
		}
		/*
		 * $params contém mais condições para a busca
		 */
		elseif( is_array($param) ){
			
			if( !empty($param['austNode']) )
		   		$austNode = array($param['austNode']=>'');
			
			$paramForLoadSql = $param;
		}
		/*
		 * Se $params é um número, significa que é um número
		 */
		elseif( is_numeric($param) ){
			$austNode = array( 'austNode' => '' );
			$paramForLoadSql['id'] = $param;

		}

		if( empty($austNode) )
			$austNode = $this->austNode();

		$sql = $this->loadSql($paramForLoadSql);

		// counts rows
		$this->totalRows = $this->_getTotalRows($sql);

		$qry = Connection::getInstance()->query($sql);

		if( empty($qry) )
			return array();

		$qry = $this->_organizesLoadedData($qry);
		
		if( !$this->idAsKeyResult )
			$qry = serializeArray($qry);
		
		$this->lastQuery = $qry;
		return $qry;
	}

	/**
	 * _totalRows()
	 *
	 * Organiza uma array com os dados carregados de um db, botando
	 * como chave de cada índice da array o id do registro.
	 *
	 * @param <mixed> $param O SQL para contar registros ou opções Array
	 * @return <int>
	 */
	function _getTotalRows($param){
		if( is_array($param) ){
			$param['countTotalRows'] = true;
			$param = $this->loadSql($param);
		}
		
		if( is_string($param) ){
			$query = Connection::getInstance()->query($param);
			$result = reset( $query );
			$total = ( !empty($result['rows']) && is_numeric($result['rows']) ) ? $result['rows'] : 0;
		} else {
			return 0;
		}
		
		return $total;
	}

	/**
	 * _organizesLoadedData()
	 *
	 * Organiza uma array com os dados carregados de um db, botando
	 * como chave de cada índice da array o id do registro.
	 *
	 * @param <array> $results
	 * @return <array>
	 */
	public function _organizesLoadedData($results){

		$result = array();
		foreach($results as $value){
			if( !empty($value['id']) ){
				$result[$value['id']] = $value;
				$this->loadedIds[] = $value['id'];
			} else
				$result[] = $value;
		}

		return $result;
	}

	/**
	 * loadSql()
	 *
	 * Tenta ser genérico para todos os módulos
	 *
	 * Retorna simplesmente o SQL para então executar Query
	 */
	public function loadSql($options = array()){
		$tP = "mainTable";
		$austTableAlias = "austTable";
		/*
		 * SET DEFAULT OPTIONS
		 */
		require_once(LIB_DATA_TYPES);
		/*
		 * Default options
		 */
		if( !empty($options['categorias'])
			AND is_array($options) )
		{
			print $options['categorias'];

			print("Argumento <strong>categorias</strong> ultrapassada em \$module->loadSql. Use \$options['austNode'].");
			exit(0);
		}
		/* gera sql para descobrir o número total de rows */
		if( is_array($options) AND !empty($options['countTotalRows']) AND $options['countTotalRows'] ){
			$options['limit'] = false;
			$options['page'] = false;
			$options['countTotalRows'] = true;
		}
		/*
		 * $options sendo array, pode ter várias condições. se $options é
		 * numérico, busca por id.
		 */
		$defaultLimit = $this->defaultLimit;

		$id = null;
		$austNode = null;
		$page = null;
		$customWhere = null;
		$order = 'id DESC';

		if( is_array($options) ){
			$id = (empty($options['id']) || $options['id'] == 0) ? '' : $options['id'];
			$austNode = empty($options['austNode']) ? $this->austNode() : $options['austNode'];
			$page = empty($options['page']) ? false : $options['page'];
			$limit = empty($options['limit']) ? $defaultLimit : $options['limit'];

			if( empty($options['order']) ){
				if( empty($this->order) )
					$order = 'id DESC';
				$order = $this->order;
			} elseif( is_string($options['order']) ) {
				$order = $options['order'];
			}

		} elseif( is_numeric($options) ){
			$id = $options;
			$limit = $defaultLimit;
		}

		if( !empty($options)
			AND !is_array($options) )
			$id = $options;

		if( !empty($id) && $id > 0 ){
			if( is_array($id) ){
				$id = " AND ".$tP.".id IN ('".implode("','", $id)."')";
			} else {
				$id = " AND ".$tP.".id='$id'";
			}
		}

		/*
		 * Gera condições para sql
		 */

		$where = '';
		if( !empty($austNode) ) {
			if( !is_array($austNode) ){
				$austNodeForSql = $austNode;
			} else if(is_array($austNode)) {
				$austNodeForSql = implode("','", array_keys($austNode) );
			}
			$where = $where . " AND ".$tP.".".$this->austField." IN ('".$austNodeForSql."')";
		}

		$userId = User::getInstance()->getId();
		if( !in_array(
				User::getInstance()->type(),
				array('Webmaster', 'Root', 'root', 'Moderador', 'Administrador')
			) &&
			!empty($userId) &&
			( Connection::getInstance()->tableHasField($this->useThisTable(), $this->authorField) )
		)
		{
			$where .= " AND (".$this->authorField." = ".User::getInstance()->getId().")";
		}

		/*
		 * Limit
		 */
		$limitStr = '';
		$limitParams = array(
			'page' => ( empty($page) ) ? false : $page,
			'limit' => ( empty($limit) ) ? false : $limit,
		);
		/*
		 * Limit definido?
		 */
		if( !empty($limit) AND empty($options['countTotalRows']) ){
			$limitStr = $this->_limitSql( $limitParams );
		}
		/*
		 * Contando rows somente
		 */
		elseif( !empty($options['countTotalRows']) AND $options['countTotalRows'] ) {
			$limitStr = ' LIMIT 0,1';
		}

		if( empty($this->describedTable[$this->useThisTable()]) ){
			$tempDescribe = Connection::getInstance()->query('DESCRIBE '.$this->useThisTable());
			foreach( $tempDescribe as $fields ){
				$this->describedTable[$this->useThisTable()][$fields['Field']] = $fields;
			}
		}

		$fieldsInSql = array();
		$fields = '';

		if( is_array($options) && !empty($options['fields']) ){
			if( $options['fields'] == "*" ){
				$fields = $tP.".*, ". $austTableAlias.".name";
			}
			elseif( is_array($options['fields']) ){
				
				foreach( $options['fields'] as $currentField ){
					
					if( $currentField == "node" )
						$fieldsInArray[] = $austTableAlias.".name AS node_name";
					elseif( $currentField == "node_id" )
						$fieldsInArray[] = $austTableAlias.".id AS node_id";
					elseif( $currentField == "node_module" )
						$fieldsInArray[] = $austTableAlias.".tipo AS node_module";
					elseif( preg_match('/node_(.*)/', $currentField, $match) )
						$fieldsInArray[] = $austTableAlias.".".$match[1]." AS ".$currentField;
					else
						$fieldsInArray[] = $tP.".".$currentField;
					
				}
				$fields = implode(", ", $fieldsInArray);
			}
			elseif( is_string($options['fields']) ){
				$fields = $options['fields'];
			}
		}
		else if( !empty( $this->describedTable[$this->useThisTable()] ) ){
			$fieldsToLoad = $this->fieldsToLoad;
			if( !is_array($fieldsToLoad) ){
				$fieldsToLoad = array($fieldsToLoad);
			}

			foreach( $fieldsToLoad as $field ){
				//if( array_key_exists($field, $this->describedTable[$this->useThisTable()]) ){
				if( $field == "*"){
					unset($fieldsInSql);
					$fieldsInSql[] = $tP.".*";
					$fields = "";
					break;
				} else {
					$fieldsInSql[] = $field;
				}
			}

			if( !empty($fieldsInSql) )
				$fields = implode(', ', $fieldsInSql);
		}

		/* where */
		if( is_array($options) && !empty($options['where']) ){
			if( is_array($options['where']) ){
				foreach( $options['where'] as $field=>$condition ){
					
					if( is_string($condition) )
						$tmpWhere[] = $tP.".".$field." LIKE '".addslashes($condition)."'";
					elseif( is_array($condition) ){
						
						foreach( $condition as $value )
							$subWhere[] = $tP.".".$field." LIKE '".addslashes($value)."'";

						if( !empty($subWhere) )
							$tmpWhere[] = "(".implode(" OR ", $subWhere).")";

					}
				}
				$where.= " AND ". implode(" AND ", $tmpWhere);
			}
		}


		/*
		 * countTotalRows
		 */
		if( !empty($options['countTotalRows']) AND $options['countTotalRows'] === true )
			$fields = 'count(id) as rows ';
		
		if( !empty($fields) )
			$fields.= ",";
		
		/*
		 * Sql para listagem
		 */
		$sql = "SELECT
					".$tP.".id AS id,
					$fields
					".$this->austField." AS cat,
					DATE_FORMAT(".$this->date['created_on'].", '".$this->date['standardFormat']."') as ".$this->date['created_on'].",
					(	SELECT
							name
						FROM
							taxonomy AS c
						WHERE
							id=cat
					) AS node
				FROM
					".$this->useThisTable()." AS mainTable
				LEFT JOIN
					".Aust::$austTable." AS austTable
				ON
					mainTable.".$this->austField." = austTable.id
				WHERE 1=1
					$id
					$where
				ORDER BY ".$order."
				$limitStr";

		if( empty($options['countTotalRows']) )
			$this->lastSql = $sql;
		else
			$this->lastCountSql = $sql;
			
		return $sql;
	}

	/**
	 * _limitSql()
	 *
	 * Retorna o LIMIT de um sql
	 *
	 * @param <array> $params
	 * @return <string>
	 */
	function _limitSql($params){
		
		// page
		if( empty($params['page']) OR !is_numeric($params['page']) ){
			$page = $this->page();
		} else {
			$page = $params['page'];
		}
			
		if( $page <= 0 OR !is_numeric($page) )
			$page = 1;
		
		if( empty($params['limit']) OR !is_numeric($params['limit']) ){
			$limit = $this->defaultLimit;
		} else {
			$limit = $params['limit'];
			$this->limit = $limit;
		}
		
		$pageLimit = (($page-1) * $limit);

		$result = " LIMIT ".$pageLimit.",".$limit;

		return $result;
	} // fim _limitSql()
	
	function page(){
		$page = 1;
		if( !empty($_GET['page']) )
			$page = $_GET['page'];
		elseif( !empty($_GET['pagina']) )
			$page = $_GET['pagina'];
			
		return $page;
	}

	/**
	 * delete()
	 *
	 * @param <string> $table
	 * @param <array> $conditions
	 * @return <integer>
	 */
	public function delete($id){

		if( is_int($id) OR is_string($id) ){

			$sql = "DELETE
					FROM
						".$this->useThisTable()."
					WHERE
						id='$id'
				";

			$result = Connection::getInstance()->exec($sql);

			if( $result )
				return true;

		}
		return false;
	}

	/*
	 * CRUD -> SUPPORT
	 */
	public function generateSqlFromForm($post, $method = 'new'){

		if( !empty($post['w']) OR
			!empty($post['id']) )
			$method = 'edit';

		$c = 0;
		
		$where = "";
		foreach($post as $key=>$value){
			/*
			 * Verifica se $post contém algum 'frm' no início
			 */
			if(strpos($key, 'frm') === 0){
				$value = addslashes( $value );
				$sqlcampo[] = str_replace('frm', '', $key);
				$sqlvalor[] = $value;

				/*
				 * Ajusta os campos da tabela nos quais serão gravados dados
				 */
				if($method == 'edit'){
					if($c > 0){
						$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$value.'\'';
					} else {
						$sqlcampostr = str_replace('frm', '', $key).'=\''.$value.'\'';
					}
				} else {
					if($c > 0){
						$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
						$sqlvalorstr = $sqlvalorstr.",'".$value."'";
						$where .= " AND ".str_replace('frm', '', $key) ."='".$value."'";
					} else {
						$sqlcampostr = str_replace('frm', '', $key);
						$sqlvalorstr = "'".$value."'";
						$where .= str_replace('frm', '', $key) ."='".$value."'";
					}
				}

				$c++;
			}
		}

		if($method == 'edit' OR !empty($post['w'])){
			$total = 0;
			$sql = "UPDATE ".$this->useThisTable()." SET $sqlcampostr 
					WHERE id='".$post['w']."'";
		} else {
			$sql = "INSERT INTO ".$this->useThisTable()." ($sqlcampostr) 
					VALUES ({$sqlvalorstr})";
		}

		return $sql;
	}

	public function getGeneratedUrl($w = ""){

		$result = $this->getStructureConfig('generate_preview_url');
		
		if( empty($w) AND empty($this->w) )
			return false;
		else if( empty($w) AND is_numeric($this->w)){
			$w = $this->w;

			$result = str_replace("%id", $w, $result);

			$lastQuery = array();
			if( count($this->lastQuery) >= 1 ){
				$lastQuery = reset($this->lastQuery);
			}
			if( !empty($lastQuery[ $this->titleEncodedField ]) ){
				$titleEncoded = $lastQuery[ $this->titleEncodedField ];

				$result = str_replace("%title_encoded", $titleEncoded, $result);
			}
			
		}

		return $result;
	}

	/**
	 * useThisTable()
	 *
	 * Se $this->useThisTable existe, retorna-a. Senão, retorna
	 * $this->mainTable.
	 *
	 * @return <string> Tabela a ser usada
	 */
	function useThisTable(){
		if( empty($this->useThisTable) )
			return $this->mainTable;

		return $this->useThisTable;
	}
	/**
	 * alias getContentTable()
	 *
	 * Retorna o nome da tabela principal.
	 *
	 * @return <string>
	 */
	public function getContentTable(){
		return $this->useThisTable();
	}
	/**
	 * alias getMainTable()
	 *
	 * Retorna o nome da tabela principal.
	 *
	 * @return <string>
	 */
	public function getMainTable(){
		return $this->useThisTable();
	}

/*
 *
 * MÈTODOS DE SUPORTE
 *
 */

	public function getFieldsFromPost(){
		
	}
	public function getValuesFromPost(){

	}

	/**
	 * loadConfig()
	 *
	 * Carrega a configuração do Módulo.
	 *
	 * @return <array>
	 */
	public function loadConfig(){

		if( !empty($this->config) )
			return $this->config;

		$modDir = $this->getIncludeFolder().'/';

		include $modDir.MOD_CONFIG;

		if( empty($modInfo) )
			return false;

		$this->config = $modInfo;
		return $this->config;
	}

	/**
	 * getIncludeFolder()
	 *
	 * Retorna o endereço até a pasta do módulo.
	 *
	 * @return <string>
	 */
	public function getIncludeFolder(){

		$str = get_class($this);
		
		preg_match_all('/[A-Z][^A-Z]*/', $str, $results);
		$tmpStr = implode('_', $results[0]);
		$tmpStr = strtolower($tmpStr);
		
		if( is_dir(MODULES_DIR.$tmpStr) )
	   		return MODULES_DIR.$tmpStr;
		else
	   		return MODULES_DIR.strtolower( $str );
	}

	/**
	 * setViewMode()
	 *
	 * Alguns módulos tem viewmodes diferentes, ou seja, listagem de formato
	 * thumbs ou lista.
	 */
	public function setViewMode($viewMode = ''){
		if( empty($this->viewModes) ) return false;
		if( empty($viewMode) AND empty($_POST['viewMode']) ) return false;
		else if( !empty($_POST['viewMode']) )
			$viewmode = $_POST['viewMode'];
			
		if( !in_array($viewmode, $this->viewModes) ) return false;
		$user = User::getInstance();
		$params = array(
			"conf_type" => "structure",
			"aust_node" => $this->austNode,
			'author' => $user->getId(),
			'data' => array(
				'viewmode' => $viewmode
			)
		);
		
		$result = $this->saveModConf($params);
		return $result;
	}
	
	/**
	 * viewmode()
	 * 
	 * Retorno o viewmode atual, considerando o usuário atual. 
	 * 
	 */
	public function viewmode(){
		if( count($this->viewModes) == 1 )
			return $this->viewModes[0];
		
		$user = User::getInstance();
		$result = $this->loadModConf('viewmode', null, $user->getId());
		if( empty($result) )
			return $this->viewModes[0];
		else
			return $result;
	}

/**
 *
 * VERIFICAÇÕES
 *
 */

	/**
	 * isCreate()
	 *
	 * Verifica se é formulário de criação.
	 *
	 * @return <bool>
	 */
	public function isCreate(){
		if( $_GET['action'] == CREATE_ACTION )
			return true;

		return false;
	}
	/**
	 * isEdit()
	 *
	 * Verifica se é formulário de edição.
	 *
	 * @return <bool>
	 */
	public function isEdit(){
		if( $_GET['action'] == EDIT_ACTION )
			return true;

		return false;
	}

	/**
	 * hasSchema()
	 *
	 * Ao contrário de Schema, o módulo pode ter Migration (preferido).
	 *
	 * @return <bool>
	 */
	public function hasSchema(){
		if( empty($this->modDbSchema) )
			return false;

		return true;
	}

	public function hasMigration(){
		
	}

	/**
	 * getModuleInformation()
	 *
	 * Retorna informações gerais sobre um módulo.
	 *
	 * @param <array> $params
	 * @return <array>
	 */
	public function getModuleInformation($params){

		/*
		 * Load Migrations
		 */
		$migrationsMods = new MigrationsMods( $this->conexao );

		if( is_array($params) ){
			
			foreach( $params as $modName ){
				$pastas = MODULES_DIR.$modName;

				/**
				 * Carrega arquivos do módulo atual
				 */
				if( !is_file($pastas.'/'.MOD_CONFIG) )
					continue; // cai fora se não tem config
				
				include($pastas.'/'.MOD_CONFIG);

				$result[$modName]['version'] = MigrationsMods::getInstance()->isActualVersion($pastas);
				$result[$modName]['path'] = $pastas;//.'/'.MOD_CONFIG;
				$result[$modName]['config'] = $modInfo;

			}
		}

		return $result;
	}

	/**
	 * verificaInstalacaoRegistro()
	 *
	 * @return <bool>
	 */
	public function verificaInstalacaoRegistro($options = array()) {

		if( !empty($options["pasta"]) ){
			$where = "directory='".$options["pasta"]."'";
		}

		$sql = "SELECT id from modules_installed WHERE ".$where;
		$query = Connection::getInstance()->query($sql);
		if( !$query ){
			return false;
		} else {
			return true;
		}
	}

	/**
	 * saveModConf()
	 *
	 * Salva configurações de um módulo no banco de dados automaticamente.
	 *
	 * Para exemplo de como usar, veja o código de configuração do módulo textos
	 *
	 * @param array $params
	 *
	 * O formato de $params deve ser o seguinte:
	 *
	 * 		array(
	 *			'aust_node' => int,
	 *			'conf_type' => 'structure',
	 *			'data' => array(
	 *				'propriedade_1' => 'valor_1',
	 *				'propriedade_2' => 'valor_2'
	 *			)
	 *		)
	 *
	 * @return bool
	 */
	public function saveModConf($params) {
		$user = User::getInstance();

		/*
		 * Se for para configurar e tiver dados enviados
		 */
		if( !empty($params['conf_type'])
			AND $params['conf_type'] == "structure"
			AND !empty($params['data'])
			AND !empty($params['aust_node']) ) {

			$data = $params["data"];

			if( empty($params['conf_class']) OR is_null($params['conf_class']) )
				$confClass = 'module';
			else
				$confClass = $params['conf_class'];

			/*
			 * ajusta o parâmetro da busca SQL
			 */
			if( $confClass == 'module' )
				$classSearchStatement = '( class IS NULL OR class=\'module\')';
			else
				$classSearchStatement = 'class=\''.$confClass.'\'';

			/*
			 * Se foi definido um usuário específico
			 */
			$whereAuthor = '';
			if( !empty($params['author']) AND is_numeric($params['author']) )
				$whereAuthor = "AND autor='".$params['author']."'";

			/*
			 * Algumas configurações são especiais, como é o caso de estruturas
			 * relacionadas.
			 */
			$moduleConfig = $this->loadConfig();
			$todayDateTime = date('Y-m-d H:i:s');
			
			$sql = "DELETE FROM aust_relations WHERE slave_id='".$params["aust_node"]."'";
			Connection::getInstance()->exec($sql);
			
			$relationalName = $moduleConfig['name'];
			if( !empty($moduleConfig['relationalName']) )
				$relationalName = $moduleConfig['relationalName'];

			foreach( $data as $property=>$value ) {
	
				/*
				 * Quando o tipo de configuração é 'field', os dados vem
				 * em um formato diferente, em array.
				 */
				if( $confClass == 'field' ){
					$refField = $property;
					foreach( $value as $property=>$value ){
						
						$deleteSQL = "DELETE FROM ".Config::getInstance()->table." WHERE type='structure'  AND $classSearchStatement AND local='".$params["aust_node"]."' AND property='$property' AND ref_field='$refField' $whereAuthor";
						Connection::getInstance()->exec($deleteSQL);

						$paramsToSave = array(
							"table" => "configurations",
							"data" => array(
								"type" => "structure",
								'class' => $confClass,
								"local" => $params["aust_node"],
								"admin_id" => $user->LeRegistro("id"),
								"property" => $property,
								'ref_field' => $refField,
								"value" => $value
							)
						);
						Connection::getInstance()->exec(Connection::getInstance()->saveSql($paramsToSave));
					}
				}
				/*
				 * Configurações de módulo acontecem a seguir.
				 */
				else {
				
					$deleteSQL = "DELETE FROM ".Config::getInstance()->table." WHERE type='structure' AND $classSearchStatement AND local='".$params["aust_node"]."' AND property='$property' $whereAuthor";
					Connection::getInstance()->exec($deleteSQL);

					$paramsToSave = array(
						"table" => "configurations",
						"data" => array(
							"type" => "structure",
							'class' => $confClass,
							"local" => $params["aust_node"],
							"admin_id" => $user->LeRegistro("id"),
							"property" => $property,
							"value" => $value
						)
					);
					Connection::getInstance()->exec(Connection::getInstance()->saveSql($paramsToSave));
	
					/*
					 * No caso de relações entre estruturas, salva na devida tabela os dados
					 * deste relacionamento.
					 */
					if( $moduleConfig['configurations'][$property]['inputType'] == 'aust_selection' ){
						
						if( !empty($value) ){
							$sql = "INSERT INTO
							 			aust_relations
							 		(slave_id, slave_name, master_id, created_on, updated_on)
									VALUES
									('".$params["aust_node"]."', 
									(SELECT name FROM taxonomy WHERE id='".$params["aust_node"]."'), 
									'".$value."', '".$todayDateTime."', '".$todayDateTime."')";
							Connection::getInstance()->exec($sql);
						}
					}
	
				}
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * loadModConf()
	 *
	 * Carrega configurações dinâmicas do módulo atual.
	 *
	 * Exemplo de configuração: mostrar resumo, sim ou não?
	 * Estas opções não são estáticas, mas dinâmicas, de acordo
	 * os administradores.
	 *
	 * No caso de ter $author, diz respeito àquelas configurações
	 * específicas de um usuário.
	 *
	 * @param <mixed> $params
	 * @param <string> $confType
	 * @param <string> $author 
	 * @return <array>
	 */
	function loadModConf($params = "", $confClass = '', $author = "") {

		if( is_null($confClass) OR empty($confClass) )
			$confClass = 'module';
		
		/*
		 * ajusta o parâmetro da busca SQL
		 */
		if( $confClass == 'module' )
			$classSearchStatement = '( class IS NULL OR class=\'module\')';
		else
			$classSearchStatement = 'class=\''.$confClass.'\'';
			
		/*
		 * Array: Várias opções podem ser passadas
		 */
		if( is_array($params) ){

			if( empty($params["austNode"]) AND
				empty($params["aust_node"]) )
				return NULL;

			if( !empty($params["aust_node"]) )
				return $this->loadModConf($params["aust_node"], $confClass, $author);

			if( !empty($params["austNode"]) )
				return $this->loadModConf($params["austNode"], $confClass, $author);

			return NULL;

		}
		/*
		 * numeric: Um austNode foi especificado
		 */
		else if( is_numeric($params) OR empty($params) ){
			/*
			 * Carrega as configurações estáticas
			 */
			$staticConfig = $this->loadConfig();

			if( $confClass == 'module' )
				$staticConfig = $staticConfig['configurations'];
			else
				$staticConfig = $staticConfig['field_configurations'];

			if( empty($params) )
				$params = $this->austNode;
			
			$whereAuthor = '';
			if( !empty($author) ){
				$whereAuthor = "AND autor='".$author."'";
			}
			/*
			 * Carrega as configurações já salvas no DB. Pode haver
			 * menos itens que as definidas estaticamente.
			 */
			$sql = "SELECT * FROM ".Config::getInstance()->table." WHERE type='structure' AND $classSearchStatement AND local='".$params."' $whereAuthor LIMIT 300";
			$queryTmp = Connection::getInstance()->query($sql, "ASSOC");
			
			$query = array();
			/*
			 * Configurações de campos individuais têm um formato completamente
			 * diferente de configurações de módulos.
			 */
			
			if( $confClass == 'field' ){

				$fields = $this->getFields();
				
				if( empty($fields) )
					return array();
				foreach($queryTmp as $value) {
		
					// $prop: toma o nome da propriedade
					$prop = $value["ref_field"]; // suas_fotos

					/*
					 * Se não houver dados salvos no db, retorna o que está no
					 * arquivo de configuração. Se houver, já mescla ambos os dados.
					 */
					if( !empty($staticConfig) ){
						foreach( $staticConfig as $configName=>$configValue ){

							/*
							 * Tipo do campo bate com o field_type da configuração?
							 */
							if( !empty($configValue['field_type']) &&
								!empty($fields[$prop]) &&
								$configValue['field_type'] == $fields[$prop]['specie'] )
							{
								if( empty($query[$prop][$configName]) ){
									$query[$prop][$configName] = $configValue;
								}
							}
						}
					}
	
	
					if( !empty( $query[$prop][$value['property']] ) )
						$query[$prop][$value["property"]] = array_merge( $query[$prop][$value["property"]] , $value );
					else
						$query[$prop][$value["property"]] = $value;
					/**
					 * @todo - array $query tem 'value' e 'valor'. Deve-se
					 * tirar uma e ficar somente uma.
					 */
					$query[$prop][$value["property"]]['value'] = $value["value"];
				}
			} else {
				/*
				 * Loop pela configurações salvas para preparar a Array para mesclar
				 * com as configurações estaticas.
				 */
				foreach($queryTmp as $value) {
				
					// $prop: toma o nome da propriedade
					$prop = $value["property"];
					$query[$prop] = array();

					if( !empty($staticConfig[$prop]) ){
						$query[$prop] = $staticConfig[$prop];
					}

					$query[$prop] = array_merge( $query[$prop], $value );
					/**
					 * @todo - array $query tem 'value' e 'valor'. Deve-se
					 * tirar uma e ficar somente uma.
					 */
					$query[$value["property"]]['value'] = $value['value'];
				}
			}
			/*
			 * Loop pela configurações estáticas para se certificar que todas as
			 * configurações serão retornadas, mesmo as que não possuem nenhum
			 * configuração definida.
			 */
			$result = array();

			if( $confClass == 'module' ){
				if( is_array($staticConfig) ){
					foreach( $staticConfig as $key=>$value ){
						if( !empty($query[$key]) )
							$result[$key] = $query[$key];
						else
							$result[$key] = $value;
					}
				}
			} else {
				if( is_array($staticConfig) ){
					foreach( $fields as $fieldName=>$fieldInfo ){
						if( empty($query[$fieldName]) )
							$query[$fieldName] = $staticConfig;
					}
				}
				$result = $query;
			}
			
			// se é para retorna configurações de um único autor, não
			// salva configurações em cache
			if( empty($author) ){
				if( $confClass == 'field')
					$this->structureFieldsConfig = $result;
				elseif( $confClass == 'module')
					$this->structureConfig = $result;
			}
			
			return $result;
		}
		/*
		 * string: quando se deseja uma opção em especial. Leva-se em
		 * consideração $this->austNode
		 */
		else if( is_string($params) ) {
			/*
			 * As configurações encontradas são salvas em $this->structureConfig
			 * para que não seja necessário buscá-las novamente no DB.
			 *
			 * Verifica-se abaixo se não existe ainda, e busca-as.
			 */
			if( !empty($author) ){
				$sql = "SELECT * FROM ".Config::getInstance()->table." WHERE type='structure' AND local='".$this->austNode."' AND admin_id='$author' AND property='$params' LIMIT 1";
				$queryTmp = Connection::getInstance()->query($sql, "ASSOC");

				if( !empty($queryTmp) )
					return $queryTmp[0]['value'];
				else
					return array();
					
			} else if( empty($this->structureConfig) ){
				$result = $this->loadModConf($this->austNode, $confClass, $author);
				return $result[$params];
			} else {
				if( empty($this->structureConfig[$params]) )
					$this->loadModConf($this->austNode, $confClass, $author);
				
				return $this->structureConfig[$params];
			}
		}

		return array();
	}

	/**
	 * getStructureConfig()
	 *
	 * Há configurações específicas de uma estrutura, como:
	 *
	 *	  Mostrar categoria?
	 *	  Tem resumo?
	 *	  Mostrar URL Gerada?
	 *
	 * Este método retorna o valor de uma configuração requisitada em $key.
	 *
	 * NOTA: Subtitui $this->loadModConfig() para pegar valores de configuração
	 * da estrutura.
	 *
	 * O método getFieldConfig() é semelhante, exceto que busca informações
	 * sobre um determinado campo.
	 *
	 * @param <string> $key
	 * @param <bool> $valueOnly
	 * @return <mixed> Se $valueOnly, retorna somente string com valor, senão
	 * array com todo o valor.
	 */
	public function getStructureConfig($key, $valueOnly = true) {

		if( is_string($key) AND empty($this->structureConfig) ) {
			$this->loadModConf($this->austNode);

			if( empty($this->structureConfig[$key]) )
				return array();

			if( $valueOnly )
				return $this->structureConfig[$key]['value'];
			
			return $this->structureConfig[$key];

		} else if( is_string($key) AND !empty($this->structureConfig) ) {
			if( $valueOnly ){
				if( !empty($this->structureConfig[$key]['value']) )
					return $this->structureConfig[$key]['value'];
				else if( !empty($this->structureConfig[$key]['valor']) )
					return $this->structureConfig[$key]['valor'];
				else
					return NULL;

			}
			
			return $this->structureConfig[$key];
		}

		return NULL;
	} // end getStructureConfig()

	/**
	 * getFieldConfig()
	 *
	 * Há configurações específicas de um campo de uma estrutura,
	 * geralmente do Módulo Cadastro:
	 *
	 *	  Campo X tem imagem secundária?
	 *	  Campo Y tem descrição?
	 *	  Campo Z tem múltiplas imagens?
	 *
	 * Este método retorna o valor de uma configuração requisitada em $key.
	 *
	 * NOTA: Subtitui $this->loadModConfig() para pegar valores de configuração
	 * de um campo de estrutura.
	 *
	 * O método getStructureConfig() é semelhante, exceto que busca informações
	 * sobre uma determinada estrutura.
	 *
	 * @param <string> $field
	 * @param <string> $key
	 * @param <bool> $valueOnly
	 * @return <mixed> Se $valueOnly, retorna somente string com valor, senão
	 * array com todo o valor.
	 */
	public function getFieldConfig($field, $key, $valueOnly = true) {

		if( is_string($key) 
			AND is_string($field) 
			AND empty($this->structureFieldsConfig) ) {
			$result = $this->loadModConf($this->austNode, 'field');
			
			if( empty($this->structureFieldsConfig[$field][$key]) )
				return array();

			if( $valueOnly )
				return $this->structureFieldsConfig[$field][$key]['value'];
			
			return $this->structureFieldsConfig[$field][$key];

		} else if( is_string($key) AND !empty($this->structureFieldsConfig) ) {

			if( $valueOnly ){
				if( !empty($this->structureFieldsConfig[$field][$key]['value']) )
					return $this->structureFieldsConfig[$field][$key]['value'];
				else if( !empty($this->structureFieldsConfig[$field][$key]['valor']) )
					return $this->structureFieldsConfig[$field][$key]['valor'];
				else
					return false;
			}
			return $this->structureFieldsConfig[$field][$key];
		}

		return false;
	} // end getFieldConfig()

	/**
	 * replaceFieldsValueIfEmpty()
	 *
	 * Alguns campos em um resultado de conteúdo do DB não podem estar vazios.
	 * Isto é configurado em config.php de cada módulo.
	 *
	 * Este método substitui automaticamente resultados vazios por um padrão.
	 *
	 * @param <array> $query
	 * @return <array> O mesmo $query de entrada, mas tratado
	 */
	public function replaceFieldsValueIfEmpty($query){

		$tmp = $query;

		$config = $this->loadConfig();

		if( empty($config['replaceFieldsValueIfEmpty']) OR
			!is_array($config['replaceFieldsValueIfEmpty']) )
			return $query;

		/*
		 * Loop por cada query
		 */
		foreach( $tmp as $key=>$value ){
			/*
			 * Loop por cada campo
			 */
			foreach( $config['replaceFieldsValueIfEmpty'] as $requiredField=>$standardValue ){

				/*
				 * Substitui campo vazio por valor padrão
				 */
				if( empty($value[$requiredField]) ){
					$query[$key][$requiredField] = $standardValue;
				}

			}
		}

		return $query;
	}

	/*
	 *
	 * INTERFACE
	 *
	 */
	public function loadHtmlEditor($plugins = ""){
		return loadHtmlEditor($plugins);
	}

	/*
	 *
	 *	funções de verificação ou leitura
	 *
	 */
	function leModulos() {

		$modules = Connection::getInstance()->query("SELECT * FROM modules_installed");
		return $modules;

		$diretorio = MODULES_DIR; // pega o endereço do diretório
		foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
			if (is_dir ($pastas)) {
				if( is_file($pastas.'/'.MOD_CONFIG )) {
					if( include($pastas.'/'.MOD_CONFIG )) {
						if(!empty($modInfo['name'])) {
							$str = $result_format;
							$str = str_replace("&%nome", $modInfo['name'] , $str);
							$str = str_replace("&%descricao", $modInfo['description'], $str);
							$str = str_replace("&%pasta", str_replace($diretorio,"",$pastas), $str);
							$str = str_replace("&%diretorio", str_replace($diretorio,"",$pastas), $str);
							echo $str;
							if($c < $t-1) {
								echo $chardivisor;
							} else {
								echo $charend;
							}
							$c++;
						}
						unset($modulo);
					}

				}
			}
		}
	} // fim leModulos()

	/**
	 * @todo - deprecated
	 */
	// retorna o nome da tabela da estrutura
	function LeTabelaDaEstrutura($param='') {
		return $this->useThisTable();
	}

	/*
	 * retorna o nome de cada módulo e suas informações em formato array
	 */
	function LeModulosParaArray() {
		$sql = "SELECT
					DISTINCT directory, name, property, value
				FROM
					modules_installed
				";
		$query = Connection::getInstance()->query($sql);
		$i = 0;
		foreach($query as $dados) {
			$return[$i]['pasta'] = $dados['directory'];
			$return[$i]['nome'] = $dados['name'];
			$return[$i]['chave'] = $dados['property'];
			$return[$i]['valor'] = $dados['value'];
			$i++;
		}
		return $return;
	}

	/*
	 * EXPORT
	 */
	function export($params = ''){
		return false;
	}


}


?>