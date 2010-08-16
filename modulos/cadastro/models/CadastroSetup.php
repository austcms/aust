<?php
/* 
 * INSTALLATION MODEL
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
		'file' => array(
			'type' => 'text',
		),
		'relacional_umparaum' => array(
			'type' => 'int',
		),
		'relacional_umparamuitos' => array(
			'type' => 'int',
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
	
	public $filesTableCreated = false;
	
//    function  __construct() {  }

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
 * CREATING FIELDS
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
	
	function createFieldConfigurationSql_Password($params){
		if( empty($params['class']) ) $class = 'password';
		else $class = $params['class'];
		
        $sql =
            "INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",".$params['author'].",0,0,1,0,1,'$class',".$this->getFieldOrder().")";
		return $sql;
	}
	
	/*
	 * FILES FIELD
	 */
	function createFieldConfigurationSql_File($params){
		if( empty($params['class']) ) $class = 'arquivo';
		else $class = $params['class'];
		
        $sql =
            "INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",".$params['author'].",0,0,1,0,1,'$class',".$this->getFieldOrder().")";
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
			
			$this->filesTableName = $mainTable.'_arquivos';
	        $sql =
	            'CREATE TABLE '.$this->filesTableName.'('.
	            'id int auto_increment,'.
	            'titulo varchar(120),'.
	            'descricao text,'.
	            'local varchar(80),'.
	            'url text,'.
	            'arquivo_nome varchar(250),'.
	            'arquivo_tipo varchar(250),'.
	            'arquivo_tamanho varchar(250),'.
	            'arquivo_extensao varchar(10),'.
	            'tipo varchar(80),'.
	            'referencia varchar(120),'.
	            'categorias_id int,'.
	            'adddate datetime,'.
	            'autor int,'.
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
                 "('estrutura','tabela_arquivos','".$this->filesTableName."',".$this->austNode.", '".date('Y-m-d H:i:s')."',0,0,1,0,1)";
			return $sql;
		}
		/*
		 * Creates the table of files
		 */
		function createTableForFiles(){
			if( empty($this->mainTable) ) return false;
			if( empty($this->austNode) ) return false;
			if( $this->filesTableCreated ) return false;
			
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
	 * Relational One to One Field SQL for Configuration
	 */
	function createFieldConfigurationSql_RelationalOneToOne($params){
		if( empty($params['class']) ) $class = 'relacional_umparaum';
		else $class = $params['class'];
		
        $sql =
			"INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",".$params['author'].",0,0,1,0,1,'$class',".$this->getFieldOrder().",'".$params['refTable']."','".$params['refField']."')";
		return $sql;
	}
	
	/**
	 * Relational One to One Field SQL for Configuration
	 */
	function createFieldConfigurationSql_RelationalOneToMany($params){
		if( empty($params['class']) ) $class = 'relacional_umparamuitos';
		else $class = $params['class'];
		
        $sql =
			"INSERT INTO cadastros_conf ".
            "(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo,referencia) ".
            "VALUES ".
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",".$params['author'].",0,0,1,0,1,'$class',".$this->getFieldOrder().",'".$params['refTable']."','".$params['refField']."','".$params['referenceTable']."')";
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
            "('campo','".$params['name']."','".$params['label']."','".$params['comment']."',".$params['austNode'].",".$params['author'].",0,0,1,0,1,'$class',".$this->getFieldOrder().")";
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
		if( empty($params) ) return false;
		
		$fields = array();
		foreach( $params as $key=>$value ){
			if( !is_numeric($key) ) continue;
			
			$fields[] = $this->encodeFieldName($value['name']).' '.$this->getFieldPhysicalType($value['type']).' '.$this->setCommentForSql($value['description']);
		}
		
        $sql = 'CREATE TABLE '.$this->mainTable.'('.
                   'id int auto_increment,'.
                   implode(",", $fields).','.
                   'blocked varchar(120),'.
                   'approved int,'.
                   'created_on datetime,'.
                   'updated_on datetime,'.
                   'PRIMARY KEY (id), UNIQUE id (id)'.
                ')';
		return $sql;
	}
}
?>
