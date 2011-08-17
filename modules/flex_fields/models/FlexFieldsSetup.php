<?php
/* 
 * INSTALLATION MODEL
 *
 * This class contains all the mechanisms for creating a new Structure. 
 *
 * Fields Available:
 *
 * 		String, Text, password, date, image, file, relationals
 *
 */

/**
 * Description of CadastroInstall
 *
 * @author kurko
 */
class FlexFieldsSetup extends ModsSetup {

	/**
	 * @var $austNode integer What category does this structure belongs to?
	 */
	public $austNode;
	
	public $mainTable;
	
	/**
	 * @var $fieldTypes array Contém os tipos de campos permitidos
	 */
	private $fieldTypes = array(
		'string' => array(
			'type' => 'varchar(250)',
		),
		'text' => array(
			'type' => 'text',
		),
		'date' => array(
			'type' => 'date',
		),
		'pw' => array(
			'type' => 'varchar(250)',
		),
		'files' => array(
			'type' => 'text',
		),
		'relational_onetoone' => array(
			'type' => 'int',
		),
		'relational_onetomany' => array(
			'type' => 'int',
		),
		'images' => array(
			'type' => 'varchar(250)',
		),
	);
	
	/**
	 * @var $fieldOrder integer O número do campo criado atualmente.
	 */
	public $fieldOrder;
	
	/**
	 * @var $filesTableName string Diz o nome da tabela que contém o endereço
	 *				dos arquivos.
	 */
	public $filesTableName = false;
	
	/**
	 * @var bool Tem a informação se a tabela de arquivos já foi criada
	 */
	public $filesTableCreated = false;
	
	/**
	 * @var bool Tem a informação se a tabela de imagens já foi criada
	 */
	public $imagesTableCreated = false;
	
	public $SqlToRun = array();
	
	public $createTable = false;
	
	/**
	 * getInstance()
	 *
	 * Para Singleton
	 *
	 * @staticvar <object> $instance
	 * @return <object>
	 */
	static function getInstance(){
		static $instance;

		if( !$instance ){
			$instance[0] = new CadastroSetup;
		}

		return $instance[0];

	}

	/*
	 * SUPER METHODS
	 *
	 * Métodos que executam todas as funções de instalação
	 */
	function createStructure($params = array()){
		$this->start();
		
		if( !empty($params['austNode']) )
			$this->austNode = $params['austNode'];
		
		$this->setMainTableName( $this->encodeTableName($params['name']) );
		
		$this->setCreateTableMode();

		if( empty($this->austNode) )
			$this->saveStructure($params);
		
		$this->createMainTable($params);
		
		$this->saveStructureConfiguration($params);
		
		if( !empty($params['fields']) )
			$this->addField($params['fields']);
		
		return true;
	}

	/**
	 * createMainTable()
	 *
	 *
	 * @return bool
	 */
	function createMainTable($params = array()){
		$sql = $this->createMainTableSql();
		$this->setCreateTableMode();
		$table = Connection::getInstance()->exec($sql, 'CREATE_TABLE');
		
		if( !empty($params['austNode']) )
			$austNode = $params['austNode'];
		else
			$austNode = $this->austNode;
			
		$params = array(
			'table' => $this->mainTable,
			'austNode' => $austNode,
		);
		$this->saveStructureConfiguration($params);
		
		return $table;
	}
	
	/**
	 * start()
	 * 
	 * Começa um novo processo de instalação. Isto reinicia todas
	 * as variáveis necessárias.
	 */
	function start(){
		$this->SqlToRun = array();
		
		return true;
	} // end start()
	
	/**
	 * setCreateTableMode()
	 *
	 * novos cadastros, cria novas tabelas. cadastro antigo, somente
	 *
	 * adiciona campos
	 */
	function setCreateTableMode(){
		$this->createTable = true;
	}
	
	/**
	 * addField()
	 * 
	 * Insere um novo campo na tabela do cadastro, salvando suas configurações.
	 * 
	 * @param $fields array
	 *
	 *		Os valores que devem vir são os seguintes:
	 *
	 *			'name' => 'Nome do campo',
	 *			'type' => 'string',
	 *			'description' => 'Descrição',
	 *
	 *		Caso seja relational, os seguintes valores devem existir:
	 *
	 *			'refTable' => 'tabela_de_referencia',
	 *			'refField' => 'campo_de_referencia',
	 *
	 */
	function addField($fields){

		/*
		 * Verifica o formato de $fields e transforma em array adequado
		 */
		if( empty($fields) )
			return false;
		else if( !array_key_exists('0', $fields) AND !empty($fields['name']) ){
			$fields = array(
				0 => $fields,
			);
		} else if( !array_key_exists('0', $fields) )
			return false;
			
		foreach( $fields as $key=>$value ){
			
			if( empty($value['name']) ) continue;
			if( empty($value['description']) ) 
				$value['description'] = '';
			
			$params = $value;
			
			if( !empty($value['order']) ){
				if( $value['order'] == 'first_field' ){
					$params['order'] = 1;
					$this->fieldOrder = '';

				} else {
					$params['order'] = $this->getFieldOrder($value['order']) + 1;
				}

				$this->updateAboveOrders($params['order']);
				
			} else {
				$params['order'] = $this->getFieldOrder();
			}
			
			$type = $value['type'];
			$params['name'] = $this->encodeFieldName($value['name']);
			$params['label'] = $value['name'];
			$params['austNode'] = $this->austNode;
			$params['type'] = $value['type'];
			$params['comment'] = addslashes($value['description']);
			$params['author'] = $this->user;
			
			/*
			 * Adding a new field
			 */
			/*
			 * String
			 */
			if( $type == 'string' ){
				
				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_String($params));
				
			}
			/*
			 * Text
			 */
			else if( $type == 'text' ) {
				
				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_Text($params));
				
			}
			/*
			 * date
			 */
			else if( $type == 'date' ) {
				
				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_String($params));
				
			}
			/*
			 * password
			 */
			else if( $type == 'pw' ) {

				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_Password($params));
				
			}
			/*
			 * File
			 */
			else if( $type == 'files' ) {
				
				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_File($params));
				$this->createTableForFiles();
				Connection::getInstance()->exec( $this->createSqlForFileConfiguration() );
			}
			/*
			 * Relational_onetoone
			 */
			else if( in_array($type, array('relational_onetoone','relacional_umparaum') ) ){
				if( empty($params['refTable']) ) continue;
				$this->addColumn($params);
				$sql = $this->createFieldConfigurationSql_RelationalOneToOne($params);
				Connection::getInstance()->exec($sql);
			}
			/*
			 * relational_onetomany
			 */
			else if( in_array($type, array('relational_onetomany','relacional_umparamuitos') ) ){
				
				if( empty($params['refTable']) ) continue;
				
				$params['mainTable'] = $this->mainTable;
				$params['secondaryTable'] = $params['refTable'];
				
				// campo pai e campo filho
				$params['refParentField'] = $params['mainTable'].'_id';
				
				if( $params['mainTable'] == $params['secondaryTable'] )
					$params['refParentField'] = 'parent_'.$params['refParentField'];
					
				$params['refChildField'] = $params['secondaryTable'].'_id';
				
				
				$params['referenceField'] = $params['refField'];
				$params['referenceTable'] = $this->createReferenceTableName_RelationalOneToMany($params);
				
				$this->addColumn($params);
				$sql = $this->createFieldConfigurationSql_RelationalOneToMany($params);
				Connection::getInstance()->exec($sql);
				
				$sqlReferenceTable = $this->createReferenceTableSql_RelationalOneToMany($params);
				Connection::getInstance()->exec($sqlReferenceTable, 'CREATE TABLE');
			}
			/*
			 * images
			 */
			else if( $type == 'images' ) {
				
				$this->addColumn($params);
				Connection::getInstance()->exec($this->createFieldConfigurationSql_Images($params));
				$this->createTableForImages();
				Connection::getInstance()->exec( $this->createSqlForImagesConfiguration() );
				
			}
			
		}
		return true;
		
	} // end addField()
	
	/**
	 * addColumn()
	 * 
	 * Cria uma coluna numa da tabela física.
	 *
	 * Obs.: Você não deveria precisar chamar este método. Use
	 * $this->addField().
	 * 
	 * @param $params array
	 */
	function addColumn($params){
		if( !array_key_exists($params['type'], $this->fieldTypes ) ) return false;
		
		$sql = "ALTER TABLE ".$this->mainTable." ADD COLUMN ".$params['name']." ".$this->fieldTypes[$params['type']]['type']." ";
		if( !empty($params['comment']) )
			$sql.= $this->setCommentForSql($params['comment']);
			
			
		Connection::getInstance()->exec($sql);
		
	}

	/**
	 * isAllowedFieldType()
	 *
	 * Retorna true se o tipo de campo passado é válido
	 *
	 * @return boolean
	 * @author Alexandre de Oliveira
	 **/
	function isAllowedFieldType($type = ""){
		if( empty($type) ) return false;
		if( array_key_exists($type, $this->fieldTypes) ) return true;
		else return false;
	}

	function getFieldPhysicalType($field){
		if( !array_key_exists($field, $this->fieldTypes) ) return false;
		
		return $this->fieldTypes[$field]['type'];
	}
	
/*
 *
 * CREATING FIELDS CONFIGURATION
 *
 */
	/*
	 * Support methods for creating fields.
	 */
	/**
	 * getFieldOrder()
	 *
	 * Esta funções retorna a ordem de um campo ou a próxima ordem.
	 *
	 * @param $field string Campo que se deseja saber a ordem.
	 */
	function getFieldOrder($field = ''){
		
		if( !empty($field) ){
			$sql = "SELECT order_nr
					FROM flex_fields_config
					WHERE
						node_id='".$this->austNode."' AND
						type='campo' AND
						property='".$field."'
						";
			$query = Connection::getInstance()->query($sql);
			$query = reset($query);

			if( empty($query['order_nr']) )
				return false;
			else {
				$this->fieldOrder = '';
				return $query['order_nr'];
			}
		}
		
		/*
		 * Se não há uma ordenação ajustada, busca qual é o último
		 * valor no DB. Se não há, supõe que seja o primeiro registro
		 * e atribui ordem 1.
		 */
		if( empty($this->fieldOrder) ){
			$sql = "SELECT MAX(order_nr) as order_nr
					FROM flex_fields_config
					WHERE node_id='".$this->austNode."'";
			$query = Connection::getInstance()->query($sql);
			$query = reset($query);

			if( empty($query['order_nr']) )
				$this->fieldOrder = 1;
			else
				$this->fieldOrder = $query['order_nr']+1;
		}
		
		$fieldOrder = $this->fieldOrder;
		$this->fieldOrder++;
		
		return $fieldOrder;
	}
	
	/**
	 * updateAboveOrders()
	 *
	 * Digamos que tenhamos os registros com ordem 1, 2, 3 e 4. Se
	 * inserirmos um registro com ordem 2, então temos de fazer com que
	 * os registros 2, 3 e 4 sejam incrementados, se tornando 3, 4 e 5.
	 *
	 * Após isto, poderemos salvar o registro com ordem 2, resultando em
	 * 1, 2 (novo registro), 3, 4 e 5.
	 *
	 * @param $int integer Ordem base (no exemplo acima, seria 2)
	 */
	function updateAboveOrders($int){
		$sql = "UPDATE
					flex_fields_config
				SET
					order_nr=order_nr+1
				WHERE
					node_id='".$this->austNode."' AND
					order_nr >= $int
				";

		return Connection::getInstance()->exec($sql);
	}
	
	function decreaseFieldOrder(){
		if( $this->fieldOrder > 1 )
			$this->fieldOrder--;
		else
			return false;
			
		return true;
	}
	
	/*
	 * Configuration: Password
	 */
	function createFieldConfigurationSql_Password($params){
		if( empty($params['class']) ) $class = 'password';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$params['order'].")";
		return $sql;
	}

	/*
	 * Configuration: Images
	 */
	function createFieldConfigurationSql_Images($params){
		if( empty($params['class']) ) $class = 'images';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$params['order'].")";
		return $sql;
	}
	
			/*
			 * Creates the SQL for creating the table of Images.
			 */
			function createSqlForImagesTable($mainTable = ''){
				if( empty($mainTable) AND !empty($this->mainTable) )
					$mainTable = $this->mainTable;
				else if( empty($mainTable) )
					return false;
		
				$this->imagesTableName = $mainTable.'_images';
				$sql =
					'CREATE TABLE '.$this->imagesTableName.'('.
					'id int auto_increment,'.
					'maintable_id int,'.
					'type varchar(80) COMMENT "type=main são as imagens principais",'.
					'order_nr int COMMENT "Contém o número da ordenação deste registro",'.
					'title varchar(250),'.
					'description text,'.
					'local varchar(180),'.
					'link text,'.
					'file_systempath text,'.
					'file_path text,'.
					'file_name varchar(250),'.
					'original_file_name varchar(250),'.
					'file_type varchar(250),'.
					'file_size varchar(250),'.
					'file_ext varchar(10),'.
					'reference varchar(120),'.
					'reference_table varchar(120),'.
					'reference_field varchar(120),'.
					'node_id int,'.
					'created_on datetime,'.
					'updated_on datetime,'.
					'admin_id int,'.
					'PRIMARY KEY (id),'.
					'UNIQUE id (id))';
				return $sql;
			}
			
			/*
			 * SQL for the configuration of images table
			 */
			function createSqlForImagesConfiguration(){
				if( empty($this->imagesTableName) ) return false;
				if( empty($this->austNode) ) return false;
	
				$sql =
					 "INSERT INTO ".
					 "flex_fields_config ".
					 "(type,property,value,node_id,created_on,deactivated,disabled,public,restricted,approved) ".
					 "VALUES ".
					 "('structure','table_images','".$this->imagesTableName."',".$this->austNode.", '".date('Y-m-d H:i:s')."',0,0,1,0,1)";
				return $sql;
			}
			/*
			 * Creates the table of files
			 */
			function createTableForImages(){
				if( empty($this->mainTable) ) return false;
				if( empty($this->austNode) ) return false;
				if( $this->imagesTableCreated ) return false;
	
				$sql = $this->createSqlForImagesTable($this->mainTable);
				$result = Connection::getInstance()->exec($sql, 'CREATE TABLE');
	
				if( $result ){
					$this->imagesTableCreated = true;
					return true;
				}
	
				return false;
	
			}	
	
			/*
			 * Save the configurations of the files table
			 */
			function createConfigurationForImages(){
				if( empty($this->austNode) ) return false;

				$sql = $this->createSqlForImagesConfiguration();

				if( !$sql ) return false;
				$result = Connection::getInstance()->exec($sql);

				if( $result ){
					return true;
				}

				return false;
			}
	/*
	 * Configuration: Files
	 */
	function createFieldConfigurationSql_File($params){
		if( empty($params['class']) ) $class = 'files';
		else $class = $params['class'];

		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$params['order'].")";
		return $sql;
	}
		
		/*
		 * Creates the SQL for creating the table of files.
		 */
		function createSqlForFilesTable($mainTable = ''){
			if( empty($mainTable) AND !empty($this->mainTable) )
				$mainTable = $this->mainTable;
			else if( empty($mainTable) )
				$mainTable = $this->getTable();
			
			if( empty($mainTable) )
				return false;
			
			$this->filesTableName = $mainTable.'_files';
			$sql =
				'CREATE TABLE '.$this->filesTableName.'('.
				'id int auto_increment,'.
				'maintable_id int,'.
				'type varchar(80),'.
				'order_nr int COMMENT "Contém o número da ordenação deste registro",'.
				'title varchar(250),'.
				'description text,'.
				'local varchar(180),'.
				'link text,'.
				'file_systempath text,'.
				'file_path text,'.
				'file_name varchar(250),'.
				'original_file_name varchar(250),'.
				'file_type varchar(250),'.
				'file_size varchar(250),'.
				'file_ext varchar(10),'.
				'reference varchar(120),'.
				'reference_table varchar(120),'.
				'reference_field varchar(120),'.
				'node_id int,'.
				'created_on datetime,'.
				'updated_on datetime,'.
				'admin_id int,'.
				'PRIMARY KEY (id),'.
				'UNIQUE id (id))';
			return $sql;
		}
	
		/*
		 * SQL for the configuration of files
		 */
		function createSqlForFileConfiguration(){
			if( empty($this->filesTableName) ) return false;
			if( empty($this->austNode) ) return false;
			
			$sql =
				 "INSERT INTO ".
				 "flex_fields_config ".
				 "(type,property,value,node_id,created_on,deactivated,disabled,public,restricted,approved) ".
				 "VALUES ".
				 "('structure','table_files','".$this->filesTableName."',".$this->austNode.", '".date('Y-m-d H:i:s')."',0,0,1,0,1)";
			return $sql;
		}
		/*
		 * Creates the table of files
		 */
		function createTableForFiles(){
			if( empty($this->mainTable) ) return false;
			if( empty($this->austNode) ) return false;
			if( $this->filesTableCreated === true ) return false;
			
			$sql = $this->createSqlForFilesTable($this->mainTable);
			$result = Connection::getInstance()->exec($sql, 'CREATE TABLE');

			if( $result ){
				$this->filesTableCreated = true;
				return true;
			}
			
			return false;
			
		}
		
		/*
		 * Save the configurations of the files table
		 */
		function createConfigurationForFiles(){
			//if( empty($this->mainTable) ) return false;
			if( empty($this->austNode) ) return false;
			
			$sql = $this->createSqlForFileConfiguration();

			if( !$sql ) return false;
			$result = Connection::getInstance()->exec($sql);

			if( $result ){
				return true;
			}
			
			return false;
		}
	// end FILE FIELD TYPE
	

	/**
	 * Configuration: Relational One to One Field SQL for Configuration
	 */
	function createFieldConfigurationSql_RelationalOneToOne($params){
		if( empty($params['class']) ) $class = 'relacional_umparaum';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr,ref_table,ref_field) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$params['order'].",'".$params['refTable']."','".$params['refField']."')";
		return $sql;
	}
	
	/**
	 * Configuration: Relational One to Many Field SQL for Configuration
	 */
	function createFieldConfigurationSql_RelationalOneToMany($params){
		if( empty($params['class']) ) $class = 'relacional_umparamuitos';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();
			
		if( empty($params['refChildField']) ) $params['refChildField'] = '';
		if( empty($params['refParentField']) ) $params['refParentField'] = '';

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr,".
				"ref_table,ref_field,reference,ref_parent_field,ref_child_field) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$params['order'].",'".
				$params['refTable']."','".$params['refField']."','".$params['referenceTable']."','".$params['refParentField']."','".$params['refChildField']."')";
		return $sql;
	}
		function createReferenceTableName_RelationalOneToMany($params){
			/*
			 * CRIA TABELA RELACIONAL
			 *
			 * Será criada agora uma tabela relacional.
			 *
			 * O nome da nova tabela será no formato
			 * tabelacadastro_camporelacional_tabelareferenciada
			 *
			 */
		
			$tabelaReferencia = $params['mainTable'];
			$tabelaRelacionada = $params['secondaryTable'];
			$campo = $params['referenceField'];
			/*
			 * verifica tamanho total do nome da nova tabela
			 * MYSQL máximo 64 caracteres
			 */
			$tMySQL = 63;

			/*
			 * Fora o tamanho do nome das tabelas, leva-se em consideração os sublinhados
			 */
			$tamanhoRestante = $tMySQL - strlen($tabelaReferencia) - strlen($tabelaRelacionada) - 2;

			/*
			 * Se só o nome das tabelas já foi maior que o total
			 * de 64 caracteres aceitos no MySQL, cria tabela sem
			 * o nome do campo, somente tabela_tabela
			 */
			if($tamanhoRestante == 0){
				$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabelaReferencia."_".$tabelaRelacionada ), 'UTF-8'));
			}
			/*
			 * Se só o nome das tabelas já foi maior que 64
			 * caracteres, cria a string tabela_tabela e retira
			 * caracteres do final da string até ficar com 64
			 * caracteres.
			 */
			else if($tamanhoRestante < 0){
				$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabelaReferencia."_".$tabelaRelacionada ), 'UTF-8'));
				$tabelasRelacionadasNome = substr($tabelasRelacionadasNome, 0, strlen($tabelasRelacionadasNome)-$tamanhoRestante);
			}
			/*
			 * Se tem espaço para o nome da tabela, mas o tamanho
			 * do nome do cmpo é maior que o possível, diminui
			 * o tamanho doo nome do campo.
			 */
			else if( strlen($campo) > $tamanhoRestante ) {
				$campoRelacionado = substr($campo, 0, $tamanhoRestante);
				$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabelaReferencia."_".$campoRelacionado."_".$tabelaRelacionada ), 'UTF-8'));
			}
			/*
			 * Tudo está normal. Cria o nome da tabela sem
			 * problemas.
			 */
			else {
				$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabelaReferencia."_".$campo."_".$tabelaRelacionada ), 'UTF-8'));
				
			}	
			return $tabelasRelacionadasNome;
		}
		
		function createReferenceTableSql_RelationalOneToMany($params){
			$tableOne = $params['refParentField'];
			$tableTwo = $params['refChildField'];
			
			if( $tableOne == $tableTwo ){
				$tableOne = 'parent_'.$tableOne;
			}
			
			$sql = 'CREATE TABLE '.$params['referenceTable'].'('.
				   'id int auto_increment,'.
				   $tableOne.' int,'.
				   $tableTwo.' int,'.
				   'order_nr int,'.
				   'blocked varchar(120),'.
				   'approved int,'.
				   'created_on datetime,'.
				   'updated_on datetime,'.
				   'PRIMARY KEY (id), UNIQUE id (id))';
			
			return $sql;
		}

	/**
	 * STRING FIELD SQL for Configuration
	 */
	function createFieldConfigurationSql_String($params){
		if( empty($params['class']) ) $class = 'string';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."','".$params['austNode']."','".$params['author']."',0,0,1,0,1,'$class',".$params['order'].")";
		return $sql;
	}
	
	/**
	 * TEXT FIELD SQL for Configuration
	 */
	function createFieldConfigurationSql_Text($params){
		if( empty($params['class']) ) $class = 'text';
		else $class = $params['class'];
		
		if( empty($params['order']) )
			$params['order'] = $this->getFieldOrder();

		$sql =
			"INSERT INTO flex_fields_config ".
			"(type,property,value,commentary,node_id,admin_id,deactivated,disabled,public,restricted,approved,specie,order_nr) ".
			"VALUES ".
			"('campo','".$params['name']."','".$params['label']."','".$params['comment']."','".$params['austNode']."','".$params['author']."',0,0,1,0,1,'$class',".$params['order'].")";
		return $sql;
	}
	/*
	 *
	 * END FILES CREATION
	 *
	 */
	
	/*
	 *
	 * MAIN TABLE
	 *
	 */
	function createMainTableSql($params = array()){
		/*
		if( empty($params) ) return false;
		
		$fields = array();
		foreach( $params as $key=>$value ){
			if( !is_numeric($key) ) continue;
			
			$fields[] = $this->encodeFieldName($value['name']).' '.
						$this->getFieldPhysicalType($value['type']).' '.
						$this->setCommentForSql($value['description']);
		}
		*/
		
		$sql = 'CREATE TABLE '.$this->mainTable.'('.
				   'id int auto_increment,'.
				   'node_id int,'.
				   'blocked varchar(120),'.
				   'approved int,'.
				   'created_on datetime,'.
				   'updated_on datetime,'.
				   'PRIMARY KEY (id), UNIQUE id (id), INDEX (node_id)'.
				')';
		return $sql;
	}
	
	/**
	 * saveStructureConfiguration()
	 *  
	 * salva dados como aprovação
	 *  
	 */
	function saveStructureConfiguration($params){
		
		if( empty($params['austNode']) AND empty($this->austNode) )
			return false;
		elseif( !empty($param['austNode']) )
			$austNode = $param['austNode'];
		elseif( !empty($this->austNode) )
			$austNode = $this->austNode;
			
		if( array_key_exists('options', $params) ){
			$params = $params['options'];
		}

		foreach( $params as $key=>$value ){
			
			if( $key == 'approval' ){
				Connection::getInstance()->exec("DELETE FROM flex_fields_config WHERE type='config' AND property='approval' AND node_id='$austNode'");
				$sql =
					"INSERT INTO
						flex_fields_config
						(type,property,value,name,specie,node_id,created_on,admin_id,deactivated,disabled,public,restricted,approved)
					VALUES
						('config','approval','".$value."','Aprovação','bool',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
					";
				Connection::getInstance()->exec($sql);

			} else if( $key == 'pre_password' ){
				Connection::getInstance()->exec("DELETE FROM flex_fields_config WHERE type='config' AND property='pre_password' AND node_id='$austNode'");
				$sql =
					"INSERT INTO
						flex_fields_config
						(type,property,value,name,specie,node_id,created_on,admin_id,deactivated,disabled,public,restricted,approved)
					VALUES
						('config','pre_password','".$value."','Pré-Senha','string',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
					";
				Connection::getInstance()->exec($sql);
			} else if( $key == 'description' ){
				Connection::getInstance()->exec("DELETE FROM flex_fields_config WHERE type='config' AND property='description' AND node_id='$austNode'");
				$sql =
					"INSERT INTO
						flex_fields_config
						(type,property,value,name,specie,node_id,created_on,admin_id,deactivated,disabled,public,restricted,approved)
					VALUES
						('config','description','".$value."','Descrição','blob',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
					";
				Connection::getInstance()->exec($sql);
			} else if( $key == 'table' ){
				Connection::getInstance()->exec("DELETE FROM flex_fields_config WHERE type='structure' AND property='table' AND node_id='$austNode'");
				$sql =
					"INSERT INTO
						flex_fields_config
						(type,property,value,name,specie,node_id,created_on,admin_id,deactivated,disabled,public,restricted,approved)
					VALUES
						('structure','table','".$value."','Tabela Principal','blob',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
					";
				Connection::getInstance()->exec($sql);
			}
			
		}
		
		return true;
		
	}

	/**
	 * saveStructure()
	 *
	 * Cria uma nova estrutura de cadastro. Se austNode já existe,
	 * não faz nada.
	 *
	 * É necessário os seguintes valores em $params:
	 * 		'name': 	nome da categoria ou estrutura
	 *		'site': 	quem é o site da nova categoria
	 *
	 * @return bool
	 */
	function saveStructure($params = array()){
		
		if( !empty($this->austNode) &&
			is_numeric($this->austNode) )
			return false;
			
		if( empty($params) )
			return false;
		$aust = Aust::getInstance();
		$return = Aust::getInstance()->createStructure($params);
		if( is_numeric($return) )
			$this->austNode = (int) $return;
			
		return $return;
	} // end saveStructure()
}
?>