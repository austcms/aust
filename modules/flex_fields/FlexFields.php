<?php
/**
 * Module's model class
 *
 * @since v0.1.6, 09/07/2009
 */
class FlexFields extends Module {

	public $mainTable = "flex_fields_config";

	public $dataTable;
	public $austNode;

	public $data = array();
	public $relationalData = array();
	
	/**
	 * @var array Guarda os arquivos de imagens para upload
	 */
	public $images = array();
	
	/**
	 * @var array Guarda os arquivos (files) para upload
	 */
	public $files = array();
	
	public $tableProperties = array();

	/**
	 * @var array Contém as configurações sobre campos e estrutura
	 */
	public $configurations = array();
	
	public $fields = array();
	
	public $toDeleteTables = array();

	function __construct($param = ''){

		parent::__construct($param);
	}

	function language(){
		return "R$";
	}
	
	function yesWord(){
		return "Sim";
	}

	function noWord(){
		return "Não";
	}

	/*
	 * LOADING PROCESS
	 * 	
	 */

	/**
	 * getFiles()
	 * 
	 * 
	 * @param $params array É onde contém o id, austNode e campo a qual o arquivo
	 * se refere
	 * @return array arquivos, com path e id
	 */
	public function getFiles($params){
		
		if( empty($params['w']) ) return false;
		if( empty($params['austNode']) ) return false;
		if( empty($params['field']) ) return false;
		
		$w = $params['w'];
		$austNode = $params['austNode'];
		$field = $params['field'];
		
		$tableFiles = (empty($params['tableFiles'])) ? $this->configurations['structure']['table_files']['value'] : $params["tableFiles"];

		$sql = "SELECT
					*
				FROM
					".$tableFiles." as t
				WHERE
					maintable_id='".$w."' AND
					reference_field='".$field."' AND
					node_id='".$austNode."' AND
					type='main'
				ORDER BY t.id DESC
				";

		$query = Connection::getInstance()->query($sql);
		
		return $query;
		
	} // fim getFiles()
	
	/**
	 * getImages()
	 * 
	 * Given an image field, returns all images with the given options ($params)
	 * 
	 * @param $params array Contains the id, austNode and the fields which is the image
	 * @return array with images
	 */
	public function getImages($params){
		
		$mainTable = '';
		$conditions = '';
		$limit = '';

		if( !empty($params['w']) ){
			$w = $params['w'];
			$mainTable = "maintable_id='".$w."' AND";
		}

		if( !empty($params['maintable_ids']) && is_array($params['maintable_ids']) ){
			$conditions[] = "(maintable_id IN ('".implode("','", $params['maintable_ids'])."'))";
		}
		
		if( is_array($conditions) )
			$conditions = " AND (".implode(' AND ', $conditions).")";

		if( !empty($params['limit']) )
			$limit = "LIMIT ".$params['limit'];
		
		if( empty($params['austNode']) ) return false;
		if( empty($params['field']) ) return false;
		
		$austNode = $params['austNode'];
		$field = $params['field'];
		
		$tableImage = $this->configurations['structure']['table_images']['value'];
		
		$sql = "SELECT
					*,
					( SELECT s.id FROM ".$tableImage." as s WHERE s.reference=t.id AND type='secondary' LIMIT 1 )
					as secondaryid
				FROM
					".$tableImage." as t
				WHERE
					$mainTable
					reference_field='".$field."' AND
					type='main'
					$conditions
				ORDER BY t.id DESC
				$limit
		";
		$query = Connection::getInstance()->query($sql);
		return $query;
		
	}
	
	/*
	 * SAVING PROCESS
	 * 	
	 */
	public function sanitizeData($data){
		
		foreach( $data as $table=>$fields ){
			foreach( $fields as $field=>$value ){
				
				// Verifies currency field
				$currencyMask = $this->getFieldConfig($field, 'currency_mask');
				if( !empty($currencyMask) && !is_numeric($currencyMask) ){
					$data[$table][$field] = Resources::currencyToFloat($value);
				}
			}
		}
		
		return $data;
	}
	
	/**
	 * setRelationalData()
	 * 
	 * Separa/prepara todos os dados, sendo que os relacionais que não serão salvos
	 * na tabela principal são guardados em uma variável separada.
	 * 
	 * 		Ajusta:
	 * 
	 * 			_ Relational One To Many
	 * 			- Date
	 * 			- Images
	 * 
	 * 
	 */
	public function setRelationalData(){
	
		$infoTabelaFisica = $this->tableProperties;
		$campos = $this->fields;
		$relational = array();

		foreach( $this->data as $tabela=>$dados ){
			foreach( $dados as $campo=>$value ){
				/*
				 * Relational One to Many
				 */
				if( !empty($campos[$campo]) AND $campos[$campo]["specie"] == "relacional_umparamuitos" ){
					unset($this->data[$tabela][$campo]);

					// prevents duplicated ids
					$usedIds = array();
					$i = 0;
					foreach( $value as $subArray ){
						if( in_array($subArray, $usedIds) )
							continue;
						
						if( $subArray != 0 ){
							$usedIds[] = $subArray;
							$relational[$campo][$campos[$campo]["reference"]][$i][$campos[$campo]["ref_table"]."_id"] = $subArray;
							$relational[$campo][$campos[$campo]["reference"]][$i]["created_on"] = date("Y-m-d H:i:s");
							$relational[$campo][$campos[$campo]["reference"]][$i]["order_nr"] = $i+1;
							$i++;
						}
					}
					$this->toDeleteTables[$campo][$campos[$campo]["reference"]] = 1;
				}
				/*
				 * Date
				 *
				 * *não mexe em $relational*
				 */
				else if( !empty( $campos[$campo]["property"] ) AND
						 !empty($infoTabelaFisica[$campos[$campo]["property"]]['Type']) AND
						 $infoTabelaFisica[$campos[$campo]["property"]]['Type'] == "date" ){
					$year = $this->data[$tabela][$campo]['year'];
					unset($this->data[$tabela][$campo]);

					if( strlen($year) == '4' ){
						$this->data[$tabela][$campo] = $value['year'].'-'.$value['month'].'-'.$value['day'];
					}
				}
				/*
				 * Images
				 *
				 * Limpa imagens de $this->data
				 *
				 * *não mexe em $relational*
				 */
				else if( !empty($campos[$campo]) AND $campos[$campo]["specie"] == "images" ){
					$this->images[$tabela][$campo] = $value;
					unset($this->data[$tabela][$campo]);
				}
				/*
				 * Files
				 *
				 * Limpa files de $this->data
				 *
				 * *não mexe em $relational*
				 */
				else if( !empty($campos[$campo]) AND $campos[$campo]["specie"] == "files" ){
					$this->files[$tabela][$campo] = $value;
					unset($this->data[$tabela][$campo]);
				}

			}
		}
		$this->relationalData = $relational;
		return true;
	
	}
	
	/**
	 * uploadAndSaveFiles()
	 * 
	 * Realiza o upload de um arquivo e a salva no DB.
	 * 
	 * @param $files array contém os arquivos a serem enviadas.
	 * @param $lastInsertId int Anexo um id ao arquivo inserido.
	 */
	function uploadAndSaveFiles($files, $lastInsertId, $options = array()){
		
		$fileHandler = File::getInstance();
		$user = User::getInstance();
		$userId = $user->getId();
		
		if( empty($options['type']) )
			$type = 'main';
		else
			$type = $options['type'];
		
		if( empty($options['reference']) )
			$reference = '';
		else
			$reference = $options['reference'];
		
		if( empty($files) ){
			$files = $this->files;
		}
		
		if( empty($this->configurations['structure']['table_files']['value']) )
			return false;
		
		$filesTable = $this->configurations['structure']['table_files']['value'];
		foreach( $files as $table=>$filesField ){
			
			foreach( $filesField as $field=>$files ){
				
				foreach( $files as $key=>$value ){
					if( empty($value['name']) OR
						empty($value['size']) OR
						empty($value['tmp_name'])
					){
						continue;
					}
					
					/*
					 * Realiza upload e salva os dados
					 */
					$fileHandler->prependedPath = $this->getStructureConfig('files_save_path');
					$finalName = $fileHandler->upload($value);
					
					$finalName['systemPath'] = addslashes($finalName['systemPath']);
					$finalName['webPath'] = addslashes($finalName['webPath']);
					
					/*
					 * Salva SQL da imagem
					 */
					$sql = "INSERT INTO $filesTable
							(
							maintable_id, file_path, file_systempath,
							file_name,
							original_file_name,file_type,file_size,file_ext,
							type,
							reference_table,reference_field,
							reference,
							node_id,
							created_on, admin_id
							)
							VALUES
							(
							'".$lastInsertId."', '".$finalName['webPath']."', '".$finalName['systemPath']."',
							'".$finalName['new_filename']."',
							'".$value['name']."', '".$value['type']."', '".$value['size']."', '".$fileHandler->getExtension($value['name'])."',
							'$type',
							'".$this->getTable()."', '".$field."',
							'".$reference."',
							'".$this->austNode."',
							'".date("Y-m-d H:i:s")."', '".$userId."'
							)";
					
					Connection::getInstance()->exec($sql);
					
				}
			}
		}
	} // uploadAndSaveFiles()
	
	/**
	 * deleteExtraFiles()
	 *
	 * Arquivos extras são aqueles que estão cadastradas no banco de dados,
	 * mas não deveriam.
	 *
	 * Suponha que o usuário possa inserir 1 arquivo. Quando ele inserir o
	 * próximo, ele terá 2. Este método excluirá o(s) arquivo(s) anterior(es).
	 *
	 * @param $files array Contém o nome dos campos dos quais devem
	 * ser excluidos os arquivos extras.
	 */
	function deleteExtraFiles( $id, $files ){
		
		$this->configurations();
		$filesTable = $this->configurations['structure']['table_files']['value'];
		
		if( empty($files) OR
			!is_array($files) )
			return false;
		
		foreach( $files as $key=>$field ){
			
			$limit = $this->getFieldConfig($field, 'files_field_limit_quantity');

			if( $limit == '0' OR
				empty($limit) )
				continue;
			
			$sql = "
				SELECT id
				FROM $filesTable
				WHERE
					reference_field='$field' AND
					maintable_id='".$id."' AND
					node_id='".$this->austNode."'
				ORDER BY id DESC
				LIMIT $limit, 999999999999999
			";
			
			$result = Connection::getInstance()->query($sql);
			foreach( $result as $value ){
				$this->deleteFile($value['id']);
			}
			
		}
		
		return true;
		
	} // deleteExtraFiles
	
	/**
	 * deleteFile()
	 *
	 * Exclui um arquivo segundo seu ID, excluindo fisicamente e do DB.
	 *
	 * @param $id int Id do arquivo
	 */
	function deleteFile($w = ""){
		if( !is_numeric($w) )
			return false;
		
		$configurations = $this->configurations();
		$filesTable = $configurations['structure']['table_files']['value'];
		$sql = "SELECT
					*
				FROM
					".$filesTable."
				WHERE
					id='".$w."'
				";
		
		$query = reset( Connection::getInstance()->query($sql) );
		
		if( file_exists($query['file_systempath']) )
			unlink( $query['file_systempath'] );
		$sqlDelete = "DELETE FROM $filesTable WHERE id='".$w."'";
		Connection::getInstance()->exec($sqlDelete);
		
		return true;
	} // deleteFile()
		
	/**
	 * uploadAndSaveImages()
	 * 
	 * Realiza o upload de uma imagem e a salva no DB.
	 * 
	 * @param $images array contém as imagens a serem enviadas.
	 * @param $lastInsertId int Anexo um id à imagem inserida.
	 */
	function uploadAndSaveImages($images, $lastInsertId, $options = array()){
		
		$imageHandler = Image::getInstance();
		$user = User::getInstance();
		$userId = $user->getId();
		
		if( empty($options['type']) )
			$type = 'main';
		else
			$type = $options['type'];
		
		if( empty($options['reference']) )
			$reference = '';
		else
			$reference = $options['reference'];
		
		if( empty($images) ){
			$images = $this->images;
		}
		
		if( empty($this->configurations['structure']['table_images']['value']) )
			return false;
		
		$imageTable = $this->configurations['structure']['table_images']['value'];
		foreach( $images as $table=>$imagesField ){
			
			foreach( $imagesField as $field=>$images ){
				
				foreach( $images as $key=>$value ){
					$originalValue = $value;
					if( empty($value['name']) OR
						empty($value['size']) OR
						empty($value['tmp_name'])
					){
						continue;
					}
					
					/*
					 * Realiza upload e salva os dados
					 */
					$imageHandler->prependedPath = $this->getStructureConfig('image_save_path');

					$value = $imageHandler->resample($value);
					$finalName = $imageHandler->upload($value);
					
					$finalName['systemPath'] = addslashes($finalName['systemPath']);
					$finalName['webPath'] = addslashes($finalName['webPath']);
					
					/*
					 * Salva SQL da imagem
					 */
					$sql = "INSERT INTO $imageTable
							(
							maintable_id, file_path, file_systempath,
							file_name,
							original_file_name,file_type,file_size,file_ext,
							type,
							reference_table,reference_field,
							reference,
							node_id,
							created_on, admin_id
							)
							VALUES
							(
							'".$lastInsertId."', '".$finalName['webPath']."', '".$finalName['systemPath']."',
							'".$finalName['new_filename']."',
							'".$value['name']."', '".$value['type']."', '".$value['size']."', '".$imageHandler->getExtension($value['name'])."',
							'$type',
							'".$this->configurations['structure']['table']['value']."', '".$field."',
							'".$reference."',
							'".$this->austNode."',
							'".date("Y-m-d H:i:s")."', '".$userId."'
							)
							";
					Connection::getInstance()->exec($sql);
					
					$shouldCache = $this->getFieldConfig($field, "image_automatic_cache_sizes");
					if( !empty($shouldCache) ){
						
						$cacheSizes = explode(';', $shouldCache);
						$newValue = $value;
						foreach( $cacheSizes as $sizeString ){
							$newValue['tmp_name'] = $finalName["systemPath"];

							$newFilename = 'cached_'.$sizeString."_".$finalName['new_filename'];
							$newFilename = str_replace(" ", "", $newFilename);
							$processingTemporaryFile = dirname( $newValue["tmp_name"])."/processing_".$newFilename;
							
							copy( $newValue["tmp_name"], $processingTemporaryFile );
							$newValue["tmp_name"] = $processingTemporaryFile;
							$imageHandler->resample($newValue, $sizeString);
							$imageHandler->upload($newValue, $newFilename);
						}
					}

				}
			}
		}
	} // uploadAndSaveImages()

	/**
	 * saveImageDescription()
	 *
	 * Salva a descrição de uma imagem.
	 *
	 * @param $string string é o valor da descrição
	 * @param $imageId int É o id da imagem
	 */
	function saveImageDescription($string, $imageId){
		$string = addslashes($string);
		
		$this->configurations();
		$imageTable = $this->configurations['structure']['table_images']['value'];
		
		$sql = "UPDATE $imageTable SET description='$string' WHERE id='$imageId'";
		return Connection::getInstance()->exec($sql);
	}
	
	/**
	 * saveImageLink()
	 *
	 * Salva o link de uma imagem.
	 *
	 * @param $string string É o valor do link
	 * @param $imageId int É o id da imagem
	 */
	function saveImageLink($string, $imageId){
		$string = addslashes($string);
		
		$this->configurations();
		$imageTable = $this->configurations['structure']['table_images']['value'];
		
		$sql = "UPDATE $imageTable SET link='$string' WHERE id='$imageId'";
		return Connection::getInstance()->exec($sql);
	}
	

	/**
	 * secondaryImageId() 
	 *
	 * Dado uma image, verifica o id de uma possível imagem secundária.
	 * 
	 */
	function deleteSecondaryImagesById($references = ""){
		if( !is_numeric($references) ) return false;
		return $this->deleteSecondaryImages( array('references' => $references) );
	}
		
	function deleteSecondaryImages($params = array()){
		if( !is_numeric($params['references'] ) )
			return false;
		
		$references = $params['references'];
		
		$configurations = $this->configurations();
		$imagesTable = $configurations['structure']['table_images']['value'];
		$sql = "SELECT
					id, file_systempath
				FROM
					".$imagesTable."
				WHERE
					reference='".$references."' AND
					type='secondary'
				";
		
		$query = Connection::getInstance()->query($sql);
		foreach( $query as $key=>$value ){
			if( file_exists($value['file_systempath']) )
				unlink( $value['file_systempath'] );
			$sqlDelete = "DELETE FROM $imagesTable WHERE id='".$value['id']."'";
			Connection::getInstance()->exec($sqlDelete);
		}
		
		return true;
	} // fim secondaryImageId()

	function deleteImage($w = ""){
		if( !is_numeric($w) )
			return false;
		
		$configurations = $this->configurations();
		$imagesTable = $configurations['structure']['table_images']['value'];
		$sql = "SELECT
					*
				FROM
					".$imagesTable."
				WHERE
					id='".$w."'
				";
		
		$query = Connection::getInstance()->query($sql);
		$query = reset( $query );
		
		if( file_exists($query['file_systempath']) )
			unlink( $query['file_systempath'] );
		$sqlDelete = "DELETE FROM $imagesTable WHERE id='".$w."'";
		Connection::getInstance()->exec($sqlDelete);
		
		return true;
	}
	
	/**
	 * deleteExtraImages()
	 *
	 * Imagens extras são aquelas que estão cadastradas no banco de dados,
	 * mas não deveriam.
	 *
	 * Suponha que o usuário possa inserir 1 imagem. Quando ele inserir a
	 * próxima, ele terá 2. Este método excluir a(s) imagem(ns) anterior(es).
	 *
	 * @param $images array Contém o nome dos campos dos quais devem
	 * ser excluidas as imagens extras.
	 */
	function deleteExtraImages( $id, $images ){
		
		$this->configurations();
		$imageTable = $this->configurations['structure']['table_images']['value'];
		
		if( empty($images) OR
			!is_array($images) )
			return false;
		
		foreach( $images as $key=>$field ){
			
			$limit = $this->getFieldConfig($field, 'image_field_limit_quantity');

			if( $limit == '0' OR
				empty($limit) )
				continue;
			
			$sql = "
				SELECT id
				FROM $imageTable
				WHERE
					reference_field='$field' AND
					maintable_id='".$id."' AND
					node_id='".$this->austNode."'
				ORDER BY id DESC
				LIMIT $limit, 999999999999999
			";
			
			$result = Connection::getInstance()->query($sql);
			foreach( $result as $value ){
				$this->deleteImage($value['id']);
				$this->deleteSecondaryImagesById($value['id']);
			}
			
		}
		
		return true;
		
	}
	
	/**
	 * loadDivisors()
	 *
	 * Divisores são títulos que aparecem entre campos de cadastro,
	 * de forma a separar os inputs por assunto.
	 *
	 * @return <array>
	 */
	function loadDivisors(){
		$sql = "SELECT
					id, type, value, commentary, description
				FROM
					".$this->useThisTable()."
				WHERE
					type='divisor' AND
					node_id='".$this->austNode."'
			";
		$tempResult = Connection::getInstance()->query($sql);

		/*
		 * Agrupa array de Divisors com as chaves sendo o nome do
		 * campo após o título.
		 */
		$result = array();
		foreach( $tempResult as $value ){

			$before = str_replace("BEFORE ", "", $value['description']);
			$result[$before] = $value;
		}
		
		return $result;
	}

	/**
	 * @todo
	 *
	 * saveDivisor deve excluir um divisor que já existe
	 * que seja antes do mesmo campo indicado. Assim,
	 * evita-se dois divisores antes de um mesmo campo.
	 */
	/**
	 * saveDivisor()
	 * 
	 * Salva um Título Divisor de campos do Cadastro.
	 *
	 * @param <array> $params
	 *	  Contém os elementos 'title', 'comment' (não obrigatório)
	 *	  e 'before', indicando o nome do campo ao qual este divisor
	 *	  antecede.
	 * @return <type>
	 */
	function saveDivisor($params){

		/*
		 * 'title' e 'before' são obrigatórios
		 */
		if( empty($params['title']) OR
			empty($params['before']) )
			return false;

		if( empty($params['comment']) )
			$params['comment'] = "";

		$params['title'] = addslashes($params['title']);
		$params['comment'] = addslashes($params['comment']);

		$sql = "INSERT INTO
					".$this->useThisTable()."
					(type,value,commentary,node_id,description)
				VALUES
					(
					'divisor','".$params['title']."','".$params['comment']."',
					'".$this->austNode."','".$params['before']."'
					)
				";
		
		$result = Connection::getInstance()->exec($sql);

		if( $result )
			return true;

		return false;

	}

	function deleteDivisor($id){
		if( is_int($id) OR
			is_string($id) )
		{
			$where = "id='".$id."' AND tipo='divisor'";
		}

		$sql = "DELETE FROM
					".$this->useThisTable()."
				WHERE
					$where
				";
		$result = Connection::getInstance()->exec($sql);

		return $result;
	}

	/**
	 * getFields()
	 *
	 * Return the list of fields as Array.
	 *
	 *	  key = physical field name
	 *	  value = human field name
	 * @param $fieldNamesOnly = false: returns all information about a field
	 * @param $humanNameAsKey = false: when returning, the field human name will
	 *			be as Key of the Array
	 * @return <array>
	 */
	public function getFields($fieldNamesOnly = false, $humanNameAsKey = false){
		$sql = "SELECT
					*
				FROM
					flex_fields_config
				WHERE
				   node_id='".$this->austNode."' AND
				   type='campo'
					ORDER BY order_nr ASC";

		$temp = Connection::getInstance()->query(
			$sql,
			PDO::FETCH_ASSOC
		);
		$result = array();
		foreach( $temp as $chave=>$value ){
			if( !empty($value["property"]) ){

				/*
				 * O usuário pode querer somente o nome do campo,
				 * mas também pode querer a informação completa.
				 */
				$shouldBeKey = $value["property"];
				if( $humanNameAsKey === true )
					$shouldBeKey = $value["value"];
				
				if( $fieldNamesOnly === true )
					$result[ $shouldBeKey ] = $value["value"];
				else
					$result[ $shouldBeKey ] = $value;

			}
		}

		// pega tipo físico para o caso de type=string, pois pode ser
		// text
		$described = $this->getPhysicalFields();
		foreach( $described as $fieldName=>$value ){
			$result[$fieldName]['physical_type'] = $value['Type'];

			/*
			 * Ambos os campos text e string possuem o campo 'especie'
			 * igual a 'string', não sendo possível saber quando é
			 * um textarea ou não.
			 *
			 * O código abaixo faz com que 'especie' seja igual a text,
			 * enquanto os demais continuam sendo 'string'
			 */
			if( $value['Type'] == 'text' AND
				!empty($result[$fieldName]['specie']) AND
			 	$result[$fieldName]['specie'] == 'string' )
				$result[$fieldName]['specie'] = 'text';
		}

		return $result;
	}

	/**
	 * getPhysicalFields()
	 *
	 * Retorna informações sobre tipagem física da respectiva
	 * tabela.
	 *
	 * @param array $params
	 *	  'table': qual tabela deve ser analisada
	 *	  'by': indica qual o índice deve ser usado
	 *		  ex.: se 'Field', o índice de retorno é o nome do
	 *		  campo.
	 * @return array Retorna as características físicas da tabela
	 */

	function getPhysicalFields( $params = array() ){
		
		$result = array();
		/**
		 * DESCRIBE tabela
		 *
		 * Toma informações físicas sobre a tabela
		 */
		if ( !empty( $params["table"] ) )
			$tabela = $params["table"];
		else
			$tabela = $this->getTable();

		$temp = Connection::getInstance()->query("DESCRIBE ".$tabela, "ASSOC");

		if ( empty( $params["by"] ) )
			$params["by"] = "Field";
			
		/**
		 * $param["by"]
		 *
		 * Se o resultado deve ser retornado com uma determinada informação
		 * como índice.
		 */
		if( !empty($params["by"]) ){
			foreach($temp as $chave=>$value){
				$result[ $value[ $params["by"] ] ] = $value;
			}
		} else {
			$result = $temp;
		}

		$this->tableProperties = $result;

		return $result;
		
	}
		// deprecated
		public function pegaInformacoesTabelaFisica( $params = array() ){
			return $this->getPhysicalFields($params);
		}	

	/**
	 * configurations()
	 * 
	 * Retorna configurações. Se já existe, não carrega duas vezes.
	 * 
	 * @return array Toda a configuração do Módulo Cadastro
	 */
	public function configurations(){
		if( !empty($this->configurations) )
			return $this->configurations;
		
		$this->pegaInformacoesCadastro( $this->austNode );
		return $this->configurations;
	}
	
	function getTable(){
		$this->configurations();
		if( empty($this->configurations['structure']['table']['value']) )
			return false;
		
		$table = $this->configurations['structure']['table']['value'];
		return $table;
	}
	
		// alias
		function table(){ return $this->getTable(); }
	
	function imagesTable(){
		$this->configurations();
		$table = $this->configurations['structure']['table_images']['value'];
		return $table;
	}
	
	/**
	 * Retorna todas as informações sobre o cadastro.
	 *
	 * Pega todas as informações da tabela flex_fields_config onde categorias_id
	 * é igual ao austNode especificado.
	 *
	 * @param int $austNode
	 * @return array
	 */
	public function pegaInformacoesCadastro( $austNode = '' ){
	
		if( empty($austNode) && empty($this->austNode) )
			return false;
		else if( empty($austNode) )
			$austNode = $this->austNode;
		
		/**
		 * Busca na tabela flex_fields_config por informações relacionadas ao
		 * austNode selecionado.
		 */
		$sql = "SELECT * FROM flex_fields_config WHERE node_id='".$austNode."' ORDER BY order_nr ASC";
		$temp = Connection::getInstance()->query(
			$sql,
			PDO::FETCH_ASSOC
		);
		$result = array();
		
		foreach( $temp as $chave=>$value ){
			if( !empty($value["property"]) )
				$result[ $value["type"] ][ $value["property"] ] = $value;
		}
		$this->configurations = $result;
		return $result;
	}

	/**
	 *
	 * VERIFICAÇÕES E LEITURAS AUTOMÁTICAS DO DB
	 * 
	 */

	/**
	 * load()
	 *
	 * We're overwriting the default parent's load() method because
	 * sometimes we need to load further data, like images from a field.
	 *
	 * This post-processing is made in this method.
	 *
	 * @return <array>
	 */
	public function load($params = ''){

		/* Start configuration */
		/* Load will not happen if any of these keys are present */
		$notLoadIfApiQueryKey = array('last_fields');
		$this->idAsKeyResult = true;
		$mainQuery = array();
		$configurations = $this->configurations();
		
		
		/* api: starts verifying if there are special keys */
		require_once(MODULES_DIR.$this->directory().MOD_MODELS_DIR.'FlexFieldsApiSpecificsParser.php');
		
		if( !empty($params['api_query']) )
			$params = array_merge($params, FlexFieldsApiSpecificsParser::parseQuery($params['api_query']));
		
		/* Loads data from the data, expect if a special request was made (usually by the API) */
		if( !array_intersect($notLoadIfApiQueryKey, array_keys($params)) )
			$mainQuery = parent::load($params);

		/* special queries */
		if( !empty($params['last_fields']) ){
			
			foreach( $params['last_fields'] as $fieldToLoad=>$properties ){

				if( empty($configurations['campo'][$fieldToLoad]) ||
				 	empty($configurations['campo'][$fieldToLoad]['specie']) )
					continue;

				$specie = $configurations['campo'][$fieldToLoad]['specie'];

				if( $specie == 'images' ){
					$fieldParams = array(
						'field' => $fieldToLoad,
						'austNode' => $this->austNode,
					);
					
					if( array_key_exists('limit', $properties) )
						$fieldParams['limit'] = $properties['limit'];

					$mainQuery = $this->getImages($fieldParams);
				}
			}

		}
		
		/*
		 * by default, are loaded only fields of type string, text, bool, date
		 * for performance issues. If the user wants more, like images, it needs
		 * to specify.
		 */
		/* include_fields */
		if( array_key_exists('include_fields', $params) && !empty($params['include_fields']) ){
			$configurations = $this->configurations();
			
			foreach( $params['include_fields'] as $fieldToLoad ){
				if( empty($configurations['campo'][$fieldToLoad]) ||
				 	empty($configurations['campo'][$fieldToLoad]['specie']) )
					continue;

				$specie = $configurations['campo'][$fieldToLoad]['specie'];

				if( $specie == 'images' ){
					$fieldParams = array(
						'field' => $fieldToLoad,
						'austNode' => $this->austNode,
						'maintable_ids' => array_keys($mainQuery),
					);

					$results = $this->getImages($fieldParams);
					foreach( $results as $image ){
						/* the main field for this image was loaded. */
						$imageMainTable = $image['maintable_id'];
						$mainQuery[$imageMainTable][$fieldToLoad][] = $image;
					}
				}
			}
			
		}

		$mainQuery = serializeArray($mainQuery);
		return $mainQuery;
	}
	
	public function loadSql($param = array()){
		// configura e ajusta as variáveis
		$categorias = (empty($param['categorias'])) ? '' : $param['categorias'];
		$metodo = (empty($param['metodo'])) ? 'listing' : $param['metodo'];
		$search = (empty($param['search'])) ? '' : $param['search'];
		$searchField = (empty($param['search_field'])) ? '' : $param['search_field'];
		$w = (empty($param['id'])) ? '' : $param['id'];
		
		/**
		 * Se $categorias estiver vazio (nunca deverá acontecer)
		 */
		if( empty($categorias) ){
			$categorias = array($this->austNode() => '0');
		}
		
		$order = ' ORDER BY id ASC';
		$where = ' WHERE ';
		$c = 0;
		foreach($categorias as $key=>$value){
			if($c == 0)
				$where = $where . 'node_id=\''.$key.'\'';
			else
				$where = $where . ' OR node_id=\''.$key.'\'';
			$c++;
		}


		/**
		 *  SQL para verificar na tabela CADASTRO_CONF quais campos existem
		 */
		$sql = "SELECT
					*, node_id AS cat,
					(	SELECT
							name
						FROM taxonomy AS c
						WHERE
							id=cat
					) AS node
				FROM
					flex_fields_config AS conf ".
				$where.
				$order;

		unset($where);
		/**
		 * Campos carregados
		 */
		$result = Connection::getInstance()->query($sql, "ASSOC");
		/**
		 * Configurações
		 */
		$tP = "mainTable";
		/**
		 * Monta algumas arrays para montar um novo SQL definitivo
		 *
		 * $i = int
		 */
		$i = 0;

		foreach($result as $dados){

			if ( in_array( $dados['type'], array('campo', 'campopw', 'campoarquivo', 'camporelacional_umparaum')) ){

				if($dados['listing'] > 0 ){

					if( $dados["specie"] == "relacional_umparaum" ){
						$leftJoin[ $dados["property"] ]["ref_tabela"] = $dados["ref_table"];
						$leftJoin[ $dados["property"] ]["ref_campo"] = $dados["ref_field"];
						$leftJoin[ $dados["property"] ]["campoNome"] = $dados["value"];
					} else {
						$mostrar['valor'][] = $dados['value'];
						$mostrar['chave'][] = $tP.".".$dados['property']." AS '".$dados["value"]."'";
					}
				}

				if( !empty($dados['value']) )
					$campos['valor'][] = $dados['value'];
				if( !empty($dados['property']) )
					$campos['chave'][] = $dados['property'];

			} else if($dados['type'] == 'structure' AND $dados['property'] == 'table'){
				$est['table'][] = $dados['value'];
				$est['node'][] = $dados['node_id'];
			}
			$i++;
		}
		/**
		 * LEFT JOIN?
		 */
		if( !empty($leftJoin) ){
			$leftJoinTmp = $leftJoin;
			unset($leftJoin);

			if( is_array($leftJoinTmp) ){

				foreach( $leftJoinTmp as $chave=>$value ){
					/*
					 * Se há um LeftJOIN, elimina os campo destes do query
					 * principal
					 */
					unset($mostrar[$chave]);

					$refTabela = $value["ref_tabela"];
					$refCampo = $value["ref_campo"];

					$leftJoinCampos[$chave] = $refTabela.".".$refCampo." AS '".$value["campoNome"]."'";
					$leftJoin[ $refTabela ] = "LEFT JOIN ".$refTabela." AS ".$refTabela." ON ".$tP.".".$chave."=".$refTabela.".id";
				}
			}
			$virgula = ",";
		}
		/**
		 * Segurança
		 */
		else {
			$leftJoinCampos = array();
			$leftJoin = array();
			$virgula = "";
		}

		/*
		 * SEARCH?
		 *
		 * Analisa se deve buscar por algo em específico.
		 */
		$searchQuery = "";
		if( !empty($search) ){
			$search = addslashes($search);

			if( empty($searchField) ){
				/*
				 * Faz loop por cada campo do cadastro, criando
				 * o comando SQL Where para busca de dados.
				 */
				foreach( $campos['chave'] as $campo ){
					$searchQueryArray[] = $campo." LIKE '%".$search."%'";
				}
			} else {
				$searchQueryArray[] = $searchField." LIKE '%".$search."%'";
			}
			
			if( !empty($searchQueryArray) )
				$searchQuery = "AND (".implode(" OR ", $searchQueryArray).")";
		}
		
		/**
		 * Novo SQL
		 */
		$where = '';
		if( $metodo == "listing" ){

			if( empty($mostrar) ){
				$mostrar = "";

			} else {
				$mostrar = implode(",", $mostrar["chave"]).",";
			}

			/* fields */
			if( !empty($param['fields']) ){
				if( $param['fields'] == "*" ){
					$fields = $tP.".*";
				}
				elseif( is_array($param['fields']) ){
					
					foreach( $param['fields'] as $currentField ){
						$fieldsInArray[] = $tP.".".$currentField;
					}
					$fields = implode(", ", $fieldsInArray);
				}
				elseif( is_string($params['fields']) ){
					$fields = $params['fields'];
				}
			} 
			
			if( empty($fields) ) {
				$fields = "".$tP.".id,
							$mostrar
							".implode(", ", $leftJoinCampos).$virgula."
							".$tP.".approved AS des_approved";
			}

			/* where */
			if( !empty($param['where']) ){
				if( is_array($param['where']) ){
					foreach( $param['where'] as $field=>$condition ){
						
						if( is_string($condition) )
							$where[] = $tP.".".$field." LIKE '".addslashes($condition)."'";
						elseif( is_array($condition) ){
							
							foreach( $condition as $value )
								$subWhere[] = $tP.".".$field." LIKE '".addslashes($value)."'";

							if( !empty($subWhere) )
								$where[] = "(".implode(" OR ", $subWhere).")";

						}
					}
					$where = implode(" AND ", $where);
				}
				$where = ' AND '.$where;
			} else {
				$where = "";
			}
			
			/* order */
			if( !empty($param['order']) ){
				$order = $param['order'];
			} else {
				$order = $tP.".id DESC";
			}
			
			/* conditions */
			$conditions = "".
					"FROM ".
						$est["table"][0]." AS ".$tP." ".
						implode(" ", $leftJoin)." ".
					"WHERE ".
						"1=1".$searchQuery.$where." ".
					"ORDER BY ".
						"$order";

			/* total rows */
			$countSql = "SELECT count(*) as rows ".$conditions;
			$this->totalRows = $this->_getTotalRows($countSql);
			
			$sql = "SELECT $fields ".$conditions;

			/* limit */
			$limit = null;
			if( !empty($param['limit']) )
				$limit = $param['limit'];
			
			$sql.= $this->_limitSql(
				array(
					'page' => $this->page(),
					'limit' => $limit
				)
			);

		} elseif( $metodo == "edit" ){
			$sql = "SELECT
						id, ".implode(",", $campos["chave"])."
					FROM
						".$est["table"][0]."
					WHERE
						id=".$w."
					";
		}

		return $sql;
	}


	/**
	 * Função para retonar a tabela de dados de uma estrutra de cadastro
	 *
	 * @param mixed $param contém o id ou nome da estrutura desejada
	 * @return array 
	 */
	public function LeTabelaDaEstrutura($param = ""){

		/**
		 * $param é uma integer
		 */
		if( is_int($param) or $param > 0 ){
			$estrutura = "taxonomy.id='".$param."'";
		}
		/**
		 * $param é uma string
		 */
		elseif( is_string($param) ){
			$estrutura = "taxonomy.nome='".$param."'";
		}

		$sql = "SELECT
					flex_fields_config.valor AS valor
				FROM
					flex_fields_config, taxonomy
				WHERE
					taxonomy.id=flex_fields_config.node_id AND
					{$estrutura} AND
					flex_fields_config.tipo='structure' AND
					flex_fields_config.chave='table'
				LIMIT 0,1";
				//echo $sql;
				
		$resultado = $this->connection->query($sql);
		$dados = $resultado[0];
		return $dados['valor'];
	}

	function dataTable($param){
		return $this->LeTabelaDeDados($param);
	}


	/*
	 * Cria tabela responsável por guardar arquivos
	 */
	function CriaTabelaArquivo($param){
		global $aust_charset;
		if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
				$charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
		}

		$sql = "SELECT
					id
				FROM
					".$param['table']."_arquivos
				LIMIT 0,1
				";
		$result = Connection::getInstance()->query($sql);
		if( count($result) == 0 ){
			$sql_arquivos =
							"CREATE TABLE ".$param['table']."_arquivos(
							id int auto_increment,
							titulo varchar(120) {$charset},
							descricao text {$charset},
							local varchar(80) {$charset},
							url text {$charset},
							arquivo_nome varchar(250) {$charset},
							arquivo_tipo varchar(250) {$charset},
							arquivo_tamanho varchar(250) {$charset},
							arquivo_extensao varchar(10) {$charset},
							tipo varchar(80) {$charset},
							referencia varchar(120) {$charset},
							node_id int,
							adddate datetime,
							autor int,
							PRIMARY KEY (id),
							UNIQUE id (id)
						) ".$charset;
			if( Connection::getInstance()->exec($sql_arquivos) ){
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return 0;
		}
		return 0;
	}

	/*
	 * Le informações do db
	 */
	function LeDadosDoDB($tabela, $campo, $value_condicao, $campo_condicao=''){

		if(empty($campo_condicao)){
			$where = "WHERE id='".$value_condicao."'";
		} else {
			$where = "WHERE ".$campo_condicao."='".$value_condicao."'";
		}
		$sql = "SELECT
					".$campo."
				FROM
					".$tabela."
				".$where."
				LIMIT 0,1
				";
		//echo $sql;
		$result = mysql_query($sql);
		if(mysql_num_row > 0){
			$dados = mysql_fetch_array($result);
			return $dados[$campo];
		} else {
			return 0;
		}
		return 0;
	}

	function PegaConfig($param){
		// ajusta variáveis
		$estrutura = $param['structure'];
		$chave = $param['chave'];
		// se a categoria passada estiver em formato Integer
		if(is_int($estrutura) or $estrutura > 0){
			$sql = "SELECT
						*
					FROM
						flex_fields_config
					WHERE
						node_id='".$estrutura."' AND
						property='".$chave."'
					";
		} elseif(is_string($estrutura)){
			// se o parâmetro $param for uma string
			$sql = "SELECT
						flex_fields_config.valor AS valor
					FROM
						flex_fields_config,taxonomy
					WHERE
						flex_fields_config.node_id=taxonomy.id AND
						taxonomy.tipo='cadastro' AND
						taxonomy.nome='".$estrutura."' AND
						flex_fields_config.chave='".$chave."'
					";
		}

		$result = Connection::getInstance()->query($sql);
		if( count($result) > 0 ){
			$dados = $result[0];
			return $dados;
		 } else {
			return FALSE;
		 }

	}

	/*
	 * Função para retonar a tabela de dados de uma estrutra da cadastro
	*/
	public function LeTabelaDeDados($param) {
		if(is_int($param) or $param > 0) {
			$estrutura = "taxonomy.id='".$param."'";
		} elseif(is_string($param)) {
			$estrutura = "taxonomy.nome='".$param."'";
		}

		$sql = "SELECT
					flex_fields_config.value AS value
				FROM
					flex_fields_config, taxonomy
				WHERE
					taxonomy.id=flex_fields_config.node_id AND
				{$estrutura} AND
					flex_fields_config.type='structure' AND
					flex_fields_config.property='table'
				LIMIT 0,1";
		
		$resultado = Connection::getInstance()->query($sql);
		$dados = $resultado[0];
		return $dados['value'];
	}

	/*
	 * INTERFACE DE SETUP
	 *
	 * Métodos para o setup de novas estruturas
	 */

	public function setupAnalisaCamposTipagemFisica(){
		
	}

	/*
	 * INTERFACE DE CONFIGURAÇÃO DE ESTRUTURA
	 *
	 * 
	 */
	public function drawFieldConfiguration(){
		$result = '';
	}
	

}
?>