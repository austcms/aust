<?php
/**
 * EXPORT
 *
 * Responsible for exporting structure datas.
 *
 * @package Classes
 * @name Export
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2, 19/10/2010
 */
class Export
{

    /*
     * OPÇÕES
     */
	    /**
	     * Endereço onde serão salvos os arquivos. Por padrão, uploads/.
	     * 
	     * @var string
	     */
	    public $path = '';


	function __construct(){
        $this->connection = Connection::getInstance();
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
            $instance[0] = new Export;
        }

        return $instance[0];

    }

	/**
	 * getStructures()
	 * 
	 * Pega todos os sites, estruturas e suas configurações (mod_conf)
	 * e retorna no formato Array.
	 *
	 * Os seguintes dados são exportados:
	 *
	 *		'Site'
	 *		   |
	 *		   \___ 'Estruturas'
	 *					  |
	 *					  |---- 'Configurações específicas', como é o caso
	 *					  |						do módulo Cadastro
	 *					  |
	 *					  \---- 'Configurações' contendo dados do tipo 'mod_conf'
	 *										    da tabela 'config'
	 * 
	 * @param $params Array Opcional, pode ter as seguintes chaves:
	 *		- 'site' int: id do site único e específico que deseja-se exportar
	 */
	function getStructures($params = array()){
		$aust = Aust::getInstance();
		$data = $aust->getStructures($params);
		$tmpData = $data;

		/*
		 * Checks if each module has additional data to export
		 */
		foreach( $tmpData as $key=>$site ){
			// clean inexistent fields in site data
			foreach( $site['Site'] as $fieldName=>$fieldValue){
				if( !$this->connection->tableHasField('categorias', $fieldName) ){
					unset($data[$key]['Site'][$fieldName]);
				}
			}
			
			foreach( $site['Structures'] as $stKey=>$st){

				// clean inexistent fields in structure
				foreach( $st as $fieldName=>$fieldValue){
					if( !$this->connection->tableHasField('categorias', $fieldName) ){
						unset($data[$key]['Structures'][$stKey][$fieldName]);
					}
				}
				$stId = $st['id'];

				/*
				 * EXPORT DATA CONFIGURATIONS
				 */
				if( file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG) ){
					
					require(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG);
					if( file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_MODELS_DIR.''.$modInfo['className'].'Export.php') ){
					
						require_once MODULES_DIR.$st['tipo'].'/'.MOD_MODELS_DIR.''.$modInfo['className'].'Export.php';
				        include_once MODULES_DIR.$st['tipo'].'/'.$modInfo['className'].'.php';

						require(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG);
					
						$exportModel = $modInfo['className']."Export";
				        $modExport = new $exportModel($modInfo['className'], $st['id']);
		
						$exportData = $modExport->export();
						if( $exportData ){
							$data[$key]['Structures'][$stKey]['exportData'] = $exportData;
						}
					}
				}
				
				/*
				 * EXPORT STRUCTURE CONFIGURATIONS
				 */
				$sqlConfig = "SELECT * FROM config WHERE tipo='mod_conf' AND local='$stId'";
//				print($sqlConfig."\n");
				$configs = array();
				$configs = $this->connection->query($sqlConfig);
				if( !empty($configs) ){
//					print 'FOUND'."\n";
					//pr($configs);
					$data[$key]['Structures'][$stKey]['modConfig'] = $configs;
					
				}
			}
		}

		return $data;
	}
	
	function getStructuresBySite($id){
		$params = array(
			'site' => $id,
		);
		return $this->getStructures($params);
	}
	
	function json($structures){
		if( !is_array($structures) )
			return false;
		
		$json = json_encode($structures);
		return $json;
	}
	
	function getConfigData(){
		
		
	}
	
	/**
	 * export()
	 *
	 * Exporta dados do DB para um formato JSON.
	 *
	 * @param $params array Opcional, pode conter o id do site que deseja-se exportar
	 */
	function export($params = array()){
		$structures = $this->getStructures($params);
		$json = $this->json($structures);
		
		$handle = fopen(EXPORTED_FILE, 'w' );
		fwrite($handle, "<?php \$json = '".$json."'; ?>");
		fclose($handle);
		
		return $json;
	}
	
	
	/*
	 * IMPORTATION
	 */
	function import($data = ''){
		
		if( empty($data) ){
			include(EXPORTED_FILE);
			if( empty($json) )
				return false;
			
			$data = $json;
			
		}
		
		
		if( is_string($data) )
			$data = $this->jsonToArray($data);
		
		foreach( $data as $site ){
			$this->importSite($site);
		}
	}
	
	/**
	 * importSite()
	 *
	 * Importa estruturas, categorias e suas configurações.
	 *
	 * @param $site Array
	 *		O formato é um array com duas chaves, 'Site' e 'Structures'.
	 *		
	 *		Structures é array possuindo chaves numéricas, cada um representando
	 *		uma estrutura. Se uma das chaves da estrutura for 'exportData', significa
	 * 		que são dados que precisam ser importados usando uma classe especial do módulo.
	 *
	 *		O módulo Cadastro, por exemplo, possui a tabela cadastros_conf. O tratamento
	 *		dos dados desta tabela específica precisa ser feito por uma classe de
	 *		importação especial do módulo Cadastro.
	 */
	function importSite($site = array()){
		if( empty($site) ) return false;
		
		/*
		 * Inicia criação do site
		 */
		unset($site['Site']['id']);
		$siteFields = array_keys($site['Site']);
		$siteData = $site['Site'];

		/*
		 * Não importa site se já existe um com mesmo nome
		 */ 
		$sqlSite = 
		   "SELECT
				id
			FROM
				categorias
			WHERE
				nome LIKE '".$siteData['nome']."' AND
				classe='categoria-chefe'
			LIMIT
				1
			";

		$hasSite = $this->connection->query($sqlSite);
		
		/*
		 * Importando ou não, $siteId é igual ao id do site
		 */
		if( !empty($hasSite['0']['id']) )
			$siteId = $hasSite['0']['id'];
		else {
		
			$sql = 
			   "INSERT INTO
					categorias (".implode(",", $siteFields).") 
				VALUES
					('".implode("','", $siteData)."')
				";
			
			$this->connection->exec($sql);
			$siteId = $this->connection->lastInsertId();
		}

		foreach( $site['Structures'] as $st ){
			unset($st['id']);
			
			
			$exportData = false;
			$modConfig = false;
			/*
			 * Retira a chave 'exportData' da array. 'exportData' possui
			 * possui dados especiais, como configurações.
			 */
			if( !empty($st['exportData']) ){
				$exportData = $st['exportData'];
				unset($st['exportData']);
			}
			
			/*
			 * Retira a chave 'exportData' da array. 'exportData' possui
			 * possui dados especiais, como configurações.
			 */
			if( !empty($st['modConfig']) ){
				$modConfig = $st['modConfig'];
				unset($st['modConfig']);
			}
			
			$st['subordinadoid'] = $siteId;
			$stFields = array_keys($st);
			$stData = $st;
			
			/*
			 * Verifica se estrutura já não existe.
			 */
			$sqlSt = 
			   "SELECT
					id
				FROM
					categorias
				WHERE
					nome LIKE '".$st['nome']."' AND
					classe='estrutura' AND
					tipo = '".$st['nome']."'
				LIMIT
					1
				";

			$hasSt = $this->connection->query($sqlSt);

			
			if( !empty($hasSt['0']['id']) )
				$stId = $hasSt['0']['id'];
			/*
			 * Cria estrutura inexistente.
			 */
			else {

				$sql = 
				   "INSERT INTO
						categorias (".implode(",", $stFields).") 
					VALUES
						('".implode("','", $stData)."')
					";

				$this->connection->exec($sql);
				$stId = $this->connection->lastInsertId();
			}
			
			/*
			 * Importa 'modConfig'
			 */
			if( !empty($modConfig) ){
				$i = 0;
				$fieldNames = array();
				$fieldValues = array();
				foreach( $modConfig as $key=>$value ){

					unset($value['id']);
					$value['local'] = $stId;
					foreach( $value as $fieldName=>$fieldValue ){
						
						$fieldNames[$i][] = $fieldName;
						$fieldValues[$i][] = $fieldValue;
					}
					$i++;
				}
				
				$values = array();
				foreach( $fieldNames as $key=>$fields ){
					$fieldsStr = implode(',', $fields);
					$values[] = "('".implode("','", $fieldValues[$key])."')";
				}

				$sql = "DELETE FROM config WHERE tipo='mod_conf' AND local='$stId'";
				$this->connection->exec($sql);
				$sql = "INSERT INTO config (".$fieldsStr.") VALUES ".implode(",", $values);
				$this->connection->exec($sql);
			}
			
			/*
			 * Inicia importação de dados de 'exportData'
			 */
			if( $exportData ){
				if( file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG) ){
					
					require(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG);
					if( file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_MODELS_DIR.''.$modInfo['className'].'Export.php') ){
					
						require_once MODULES_DIR.$st['tipo'].'/'.MOD_MODELS_DIR.''.$modInfo['className'].'Export.php';
				        include_once MODULES_DIR.$st['tipo'].'/'.$modInfo['className'].'.php';

						require(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG);
					
						$exportModel = $modInfo['className']."Export";
				        $modExport = new $exportModel($modInfo['className'], $st['id']);
		
						$modExport->import($exportData, $stId);
					}
				}				
			}
		}
		
	}
	
	function jsonToArray($json){
		return json_decode($json, true);
	}
}


?>