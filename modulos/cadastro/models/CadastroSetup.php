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
class CadastroSetup extends ModsSetup {

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
	public $fieldOrder = 1;
	
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
	
	/*
	 * SUPER METHODS
	 *
	 * Métodos que executam todas as funções de instalação
	 */
	function createStructure($params){
		$this->start();
		$this->setMainTableName( $this->encodeTableName($params['name']) );
		
		$this->setCreateTableMode();
		
		$this->saveStructure($params);
		$this->createMainTable($params);
		
		$this->saveStructureConfiguration($params);
		
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
		$table = $this->connection->exec($sql, 'CREATE_TABLE');
		
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
	 */
	function addField($fields){

		/*
		 * Verifica o formato de $fields e transforma em array adequado
		 */
		if( !array_key_exists('0', $fields) AND !empty($fields['name']) ){
			$fields = array(
				0 => $fields,
			);
		} else if( !array_key_exists('0', $fields) )
			return false;
			
		foreach( $fields as $key=>$value ){
			
			if( empty($value['name']) ) continue;
			
			$params = $value;
			$type = $value['type'];
			$params['name'] = $this->encodeFieldName($value['name']);
			$params['label'] = $value['name'];
			$params['austNode'] = $this->austNode;
			$params['type'] = $value['type'];
			$params['comment'] = $value['description'];
			$params['author'] = $this->user;
			
			/*
			 * Adding a new field
			 */
			/*
			 * String
			 */
			if( $type == 'string' ){
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_String($params));
				
			}
			/*
			 * Text
			 */
			else if( $type == 'text' ) {
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_String($params));
				
			}
			/*
			 * date
			 */
			else if( $type == 'date' ) {
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_String($params));
				
			}
			/*
			 * password
			 */
			else if( $type == 'pw' ) {
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_Password($params));
				
			}
			/*
			 * File
			 */
			else if( $type == 'files' ) {
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_File($params));
				$this->createTableForFiles();
				$this->connection->exec( $this->createSqlForFileConfiguration() );
				
			}
			/*
			 * Relational_onetoone
			 */
			else if( $type == 'relational_onetoone' ) {
				
				if( empty($params['refTable']) ) continue;
				$this->addColumn($params);
				$sql = $this->createFieldConfigurationSql_RelationalOneToOne($params);
				$this->connection->exec($sql);
				
			}
			/*
			 * relational_onetomany
			 */
			else if( $type == 'relational_onetomany' ) {
				
				if( empty($params['refTable']) ) continue;
				
				$params['mainTable'] = $this->mainTable;
				$params['secondaryTable'] = $params['refTable'];
				$params['referenceField'] = $params['refField'];
				$params['referenceTable'] = $this->createReferenceTableName_RelationalOneToMany($params);
				
				$this->addColumn($params);
				$sql = $this->createFieldConfigurationSql_RelationalOneToMany($params);
				$this->connection->exec($sql);
				
				$sqlReferenceTable = $this->createReferenceTableSql_RelationalOneToMany($params);
				$this->connection->exec($sqlReferenceTable);
			}
			/*
			 * images
			 */
			else if( $type == 'images' ) {
				
				$this->addColumn($params);
				$this->connection->exec($this->createFieldConfigurationSql_Images($params));
				$this->createTableForImages();
				$this->connection->exec( $this->createSqlForImagesConfiguration() );
				
			}
			
		}
		return true;
		
	} // end addField()
	
	/**
	 * addColumn()
	 * 
	 * Cria uma coluna numa da tabela.
	 */
	function addColumn($params){
		if( !array_key_exists($params['type'], $this->fieldTypes ) ) return false;
		
		$sql = "ALTER TABLE ".$this->mainTable." ADD COLUMN ".$params['name']." ".$this->fieldTypes[$params['type']]['type']." ";
		if( !empty($params['comment']) )
			$sql.= $this->setCommentForSql($params['comment']);
			
			
		$this->connection->exec($sql);
		
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
	
	function getFieldOrder(){
		$fieldOrder = $this->fieldOrder;
		$this->fieldOrder++;
		return $fieldOrder;
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
		
        $sql =
            "INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().")";
		return $sql;
	}

	/*
	 * Configuration: Images
	 */
	function createFieldConfigurationSql_Images($params){
		if( empty($params['class']) ) $class = 'images';
		else $class = $params['class'];
		
        $sql =
            "INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().")";
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
		            'title varchar(250),'.
		            'description text,'.
		            'local varchar(180),'.
                    'link text,'.
		            'systempath text,'.
		            'path text,'.
		            'file_name varchar(250),'.
		            'original_file_name varchar(250),'.
		            'file_type varchar(250),'.
		            'file_size varchar(250),'.
		            'file_ext varchar(10),'.
		            'reference varchar(120),'.
		            'reference_table varchar(120),'.
		            'reference_field varchar(120),'.
		            'categoria_id int,'.
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
		             "cadastros_conf ".
		             "(tipo,chave,valor,categorias_id,adddate,desativado,desabilitado,publico,restrito,aprovado) ".
		             "VALUES ".
		             "('estrutura','table_images','".$this->imagesTableName."',".$this->austNode.", '".date('Y-m-d H:i:s')."',0,0,1,0,1)";
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
				$result = $this->connection->exec($sql, 'CREATE TABLE');
	
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
				$result = $this->connection->exec($sql);

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

        $sql =
            "INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().")";
		return $sql;
	}
		
		/*
		 * Creates the SQL for creating the table of files.
		 */
		function createSqlForFilesTable($mainTable = ''){
			if( empty($mainTable) AND !empty($this->mainTable) )
				$mainTable = $this->mainTable;
			else if( empty($mainTable) )
				return false;
			
			$this->filesTableName = $mainTable.'_files';
	        $sql =
	            'CREATE TABLE '.$this->filesTableName.'('.
                'id int auto_increment,'.
                'maintable_id int,'.
				'type varchar(80),'.
                'title varchar(250),'.
                'description text,'.
                'local varchar(180),'.
                'link text,'.
                'systempath text,'.
                'path text,'.
                'file_name varchar(250),'.
                'original_file_name varchar(250),'.
                'file_type varchar(250),'.
                'file_size varchar(250),'.
                'file_ext varchar(10),'.
                'reference varchar(120),'.
                'reference_table varchar(120),'.
                'reference_field varchar(120),'.
                'categoria_id int,'.
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
                 "cadastros_conf ".
                 "(tipo,chave,valor,categorias_id,adddate,desativado,desabilitado,publico,restrito,aprovado) ".
                 "VALUES ".
                 "('estrutura','table_files','".$this->filesTableName."',".$this->austNode.", '".date('Y-m-d H:i:s')."',0,0,1,0,1)";
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
			$result = $this->connection->exec($sql, 'CREATE TABLE');
			
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
			$result = $this->connection->exec($sql);
			
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
		
        $sql =
			"INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().",'".$params['refTable']."','".$params['refField']."')";
		return $sql;
	}
	
	/**
	 * Configuration: Relational One to Many Field SQL for Configuration
	 */
	function createFieldConfigurationSql_RelationalOneToMany($params){
		if( empty($params['class']) ) $class = 'relacional_umparamuitos';
		else $class = $params['class'];
		
        $sql =
			"INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo,referencia) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",'".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().",'".$params['refTable']."','".$params['refField']."','".$params['referenceTable']."')";
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
	        $sql = 'CREATE TABLE '.$params['referenceTable'].'('.
	               'id int auto_increment,'.
	               $params['mainTable'].'_id int,'.
	               $params['secondaryTable'].'_id int,'.
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

        $sql =
			"INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."','".$params['austNode']."','".$params['author']."',0,0,1,0,1,'$class',".$this->getFieldOrder().")";
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
                   'blocked varchar(120),'.
                   'approved int,'.
                   'created_on datetime,'.
                   'updated_on datetime,'.
                   'PRIMARY KEY (id), UNIQUE id (id)'.
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
				$this->connection->exec("DELETE FROM cadastros_conf WHERE tipo='config' AND chave='aprovacao' AND categorias_id='$austNode'");
                $sql =
	                "INSERT INTO
	                    cadastros_conf
	                    (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
	                VALUES
	                    ('config','aprovacao','".$value."','Aprovação','bool',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
	                ";
                $this->connection->exec($sql);

			} else if( $key == 'pre_password' ){
				$this->connection->exec("DELETE FROM cadastros_conf WHERE tipo='config' AND chave='pre_senha' AND categorias_id='$austNode'");
                $sql =
	                "INSERT INTO
	                    cadastros_conf
	                    (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
	                VALUES
	                    ('config','pre_senha','".$value."','Pré-Senha','string',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
	                ";
                $this->connection->exec($sql);
			} else if( $key == 'description' ){
				$this->connection->exec("DELETE FROM cadastros_conf WHERE tipo='config' AND chave='descricao' AND categorias_id='$austNode'");
                $sql =
	                "INSERT INTO
	                    cadastros_conf
	                    (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
	                VALUES
	                    ('config','descricao','".$value."','Descrição','blob',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
	                ";
                $this->connection->exec($sql);
			} else if( $key == 'table' ){
				$this->connection->exec("DELETE FROM cadastros_conf WHERE tipo='estrutura' AND chave='tabela' AND categorias_id='$austNode'");
                $sql =
	                "INSERT INTO
	                    cadastros_conf
	                    (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
	                VALUES
	                    ('estrutura','tabela','".$value."','Tabela Principal','blob',".$austNode.", '".date('Y-m-d H:i:s')."', '".$this->user."',0,0,1,0,1)
	                ";
                $this->connection->exec($sql);
			}
			
		}
		
		return true;
		
	}

	/**
	 * saveStructure()
	 *
	 * Cria uma nova estrutura de cadastro
	 *
	 * É necessário os seguintes valores em $params:
	 * 		'name': 	nome da categoria ou estrutura
	 *		'father': 	quem é o pai da nova categoria
	 *
	 * @return bool
	 */
	function saveStructure($params = array()){
		$aust = Aust::getInstance();
		$return = $aust->create($params);
		if( is_numeric($return) )
			$this->austNode = (int) $return;
			
		return $return;
	} // end saveStructure()
}
?>