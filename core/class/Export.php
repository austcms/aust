<?php
/**
 * EXPORT
 *
 * Responsible for exporting structure datas.
 *
 * @package Classes
 * @name Image
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
				
				if( file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG) ){
					
					require(MODULES_DIR.$st['tipo'].'/'.MOD_CONFIG);
					if( !file_exists(MODULES_DIR.$st['tipo'].'/'.MOD_MODELS_DIR.''.$modInfo['className'].'Export.php') )
						continue;
					
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
		}
//		pr($data);
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
	
	function export($params = array()){
		$structures = $this->getStructures($params);
		$json = $this->json($structures);
		return $json;
	}
	
	/*
	 * IMPORTATION
	 */
	function import($data){
		if( is_string($data) )
			$data = $this->jsonToArray($data);
		
		foreach( $data as $site ){
			$this->importSite($site);
		}
	}
	
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
			if( !empty($st['exportData']) ){
				$exportData = $st['exportData'];
				unset($st['exportData']);
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
		}
		
	}
	
	function jsonToArray($json){
		return json_decode($json, true);
	}
}


?>