<?php
/**
 * AUST
 *
 * Responsible for the Taxonomies, in other words, structures and categories.
 *
 * @since v0.1.5, 30/05/2009
 */

class Aust {

    static $austTable = 'taxonomy';

	/**
	 * In the taxonomy table, the type value that represents sites is...
	 */
	static $austSiteType = 'categoria-chefe';
	/**
	 * In the taxonomy table, the type value that represents structures is...
	 */
	static $austStructureType = 'estrutura';
	/**
	 * In the taxonomy table, the type value that represents a category is...
	 */
	static $austCategoryType = 'categoria';

    protected $AustCategorias = Array();
    public $connection;
    protected $conexao;

    public $_recursiveLimit = 50;
    public $_recursiveCurrent = 1;
	public $_structureModuleCache = array();
	public $_structureCache = array();

    function __construct(){
        $this->connection = Connection::getInstance();
        unset($this->AustCategorias);
    }

    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Aust;
        }

        return $instance[0];
    }

	/**
	 * Return the instance of a structure's model.
	 *
	 * @param $austNode (int)
	 */
	function getStructureInstance($austNode){
	    $modDir = $this->structureModule($austNode).'/';

        include(MODULES_DIR.$modDir.MOD_CONFIG);
        $module = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
        include_once(MODULES_DIR.$modDir.$module.'.php');

        $param = array(
            'config' => $modInfo,
            'user' => User::getInstance(),
        );
		
        $object = new $module($param);
		$object->setAustNode($austNode);
		
		return $object;
	}

    /**
     * Creates a new structure.
     *
     * @param array $param Contém os seguintes índices:
     *      string  [name]              structure's name;
     *      int     [site]			    structure's father id
     *      bool    [public]           	1, everyone has access; 0, only root has access
     *      string  [module_dir]        modules' name
     *      string  [author]            struture's author id
     */
    function createStructure($params) {

        $name = $params['name'];
        $name_encoded = (empty($params['name_encoded'])) ? encodeText($name) : $params['name_encoded'] ;
        $siteId = $params['site'];
        $public = (empty($params['public'])) ? '1' : $params['public'] ;
        $module = $params['module'];
        $author = $params['author'];

		$query = Connection::getInstance()->query("SELECT * FROM ".Aust::$austTable." WHERE id='$siteId' AND class='categoria-chefe'");
		if( empty($query) )
			return false;
		
		$siteName = $query[0]['name'];
		if( empty($query[0]['name_encoded']) )
			$siteNameEncoded = encodeText($siteName);
		else
			$siteNameEncoded = $query[0]['name_encoded'];
		
        $sql = "INSERT INTO
	            taxonomy
	            (
		            name,father_id,class,type,public,admin_id,
		            name_encoded,
					site_id, site_name, site_name_encoded
	            )
				VALUES
	            (
		            '$name','$siteId','estrutura','$module',$public,'$author',
		            '$name_encoded',
					'$siteId', '$siteName', '$siteNameEncoded'
	            )
                ";
        /**
         * Returns the last inserted id
         */
        if (Connection::getInstance()->exec($sql)) {
            return (int) Connection::getInstance()->conn->lastInsertId();
        } else {
            return FALSE;
        }
    }
	
	/**
	 * Creates a new structure's category.
	 *
	 * Automaticaly finds out what is the structure and what is the site.
	 *
	 * @return int id created
	 */
    public function createCategory($params) {

		/*
		 * Compulsory variables
		 */
        if( empty($params['father']) || !is_numeric($params['father']) ) return false;
        if( empty($params['name']) ) return false;

		/*
		 * Sets up all used variables
		 */
        $name 			= addslashes( str_replace("\n", "", $params['name']) );
        $nameEncoded 	= encodeText($name);
        $father 		= $params['father'];
        $description 	= (empty($params['description'])) ? '' : addslashes( $params['description'] );
        $author 		= (empty($params['author'])) ? '' : $params['author'];
        $class 			= Aust::$austCategoryType;

		$site = $this->getSiteByCategoryId($father);
		$siteNameEncoded = $site['name_encoded'];
		if( empty($siteNameEncoded) )
			$siteNameEncoded = encodeText($site['name']);

		$structure = $this->getStructureByCategoryId($father);
		$structureNameEncoded = $structure['name_encoded'];
		if( empty($structureNameEncoded) )
			$structureNameEncoded = encodeText($structure['name']);

        if( $father != $structure['id'] ) {
            $sql = "SELECT 	name
                    FROM 	taxonomy
                    WHERE	id='$father'";
            $query = Connection::getInstance()->query($sql);
            $fatherNameEncoded = encodeText( $query[0]['name'] );
        } else {
			$fatherNameEncoded = $structure['name_encoded'];
			if( empty($fatherNameEncoded) )
            	$fatherNameEncoded = encodeText( $structure['name'] );
		}

        $sql = "INSERT INTO
                    taxonomy
                    (
                        name, 			name_encoded, 		description,
                        father_id, 		father_name_encoded,
                        structure_id, 	structure_name, 	structure_name_encoded,
                        site_id, 		site_name, 			site_name_encoded,
                        class, 			type,
                        admin_id
                    )
                VALUES
                    (
                        '$name',				'$nameEncoded', 			'$description',
                        '$father', 				'$fatherNameEncoded',
                        '".$structure['id']."', '".$structure['name']."', 	'$structureNameEncoded',
                        '".$site['id']."', 		'".$site['name']."', 		'$siteNameEncoded',
                        '$class', 				'".$structure['type']."',
                        '".$author."'
                    )";

        if( Connection::getInstance()->exec($sql) ) 
            return (int) Connection::getInstance()->lastInsertId();

        return false;
    }

	/**
	 * deleteNodeImages( $node_id )
	 * 
	 * Deletes images from a aust node (structure or category).
	 * 
	 * @param int $node_id node_id da categoria
	 * @return bool
	 */
	function deleteNodeImages( $node_id ){
		
		$sql = "SELECT
					id, systempath
				FROM
					austnode_images
				WHERE
					node_id='".$node_id."'
				";
		
		$query = Connection::getInstance()->query($sql);
		foreach( $query as $key=>$value ){
			if( file_exists($value['systempath']) )
				unlink( $value['systempath'] );
			$sqlDelete = "DELETE FROM austnode_images WHERE id='".$value['id']."'";
			Connection::getInstance()->exec($sqlDelete);
		}
		
		return true;
	}

    public function createSite($name, $description = '') {
        $sql = "INSERT INTO
                    taxonomy
                        (name, description, class, type, father_id)
                VALUES
                    ('$name','$description','categoria-chefe','','0')";
        return Connection::getInstance()->exec($sql);
    }

	public function createFirstSiteAutomatically(){
		if( !$this->anySiteExists() )
			if( $this->createSite("Site") )
				return true;
		return false;
	}

    /*
     *
     * READING
     *
     */
	
	/*
	 *
	 * Site related
	 *
	 */

	/**
	 * Given a node in the taxonomy table, recursively find out who's site it
	 * belongs to.
	 */
	public function getSiteByCategoryId($id = ''){
		return $this->getSomethingByNodeId($id, 'site');
	}
		

    /**
     * returns a site's information
     */
    public function getAllSites($columns, $formato, $chardivisor = '', $charend = '', $order = '') {
		
        $sql = "SELECT
                    *
                FROM
                    taxonomy
                WHERE
                    class='categoria-chefe'
                ";
        $query = Connection::getInstance()->query($sql);
        $t = count($query);
        $c = 0;
        foreach($query as $menu) {
            $str = $formato;
            for($i = 0; $i < count($columns); $i++) {
                $str = str_replace("&%" . $columns[$i], $menu[$columns[$i]], $str);
            }
            echo $str;
            if($c < $t-1) {
                echo $chardivisor;
            } else {
                echo $charend;
            }
            $c++;
        }
    }

	public function anySiteExists(){
        $sql = "SELECT
                    name
                FROM
                    taxonomy
                WHERE
                    class='categoria-chefe'
                ";
        $query = Connection::getInstance()->query($sql);
		if( count($query) > 0 )
        	return true;
		return false;
	}
	
	/*
	 *
	 * Category related
	 *
	 */

    /**
     * getStructures()
     *
     * Get all sites and its substructures.
     *
     * @return <array> $params
     */
    public function getStructures($params = array()) {
	
		$where = '';
		if( !empty($params['site']) && is_numeric($params['site']) )
			$where = "AND c.id='".$params['site']."'";
        /**
         * SITES
         */
        $sql = "SELECT
                    c.*, c.name as name
                FROM
                    taxonomy AS c
                WHERE
                    c.father_id='0'
					$where
                ";

        $query = Connection::getInstance()->query($sql);
        $result = array();
		$stIds = array();
		
		$invisibleStructures = $this->getInvisibleStructures();
        /*
         * Each site
        */
        foreach( $query as $key=>$sites) {
            $result[$key]['Site'] = $sites;

            /*
             * Get Structures of each site
            */
            $structures = $this->getStructuresBySite($sites['id']);
            if( is_array($structures) ) {

				foreach( $structures as $stKey => $sts ){
					/*
					 * RELATED AND VISIBLE?
					 *
					 * Clear invisible Structures
					 */
					if( in_array($sts['id'], $invisibleStructures) )
						unset($structures[$stKey]);
					else
						$stIds[] = $sts['id'];
				}
                $result[$key]['Structures'] = $structures;
            }
        }

		$slaves = $this->getRelatedSlaves($stIds);
		$masters = $this->getRelatedMasters($stIds);
		if( !empty($slaves) ){
			// loop through
			foreach( $result as $siteKey=>$site ){
				// loop through structures
				foreach( $site['Structures'] as $stKey=>$st ){
					
					if( array_key_exists($st['id'], $slaves) ){
						
						$result[$siteKey]['Structures'][$stKey]['slaves'] = $slaves[$st['id']];
					}
				}
			}
		}
		if( !empty($masters) ){
			// loop through sites
			foreach( $result as $siteKey=>$site ){
				// loop through structures
				foreach( $site['Structures'] as $stKey=>$st ){
					
					if( array_key_exists($st['id'], $masters) ){
						$result[$siteKey]['Structures'][$stKey]['masters'] = $masters[$st['id']];
					}
				}
			}
		}
        return $result;

    }

	/**
	 * It's used mainly by the API, e.g. ?query=news, this methods digs the
	 * taxonomy table to get the ID of the structure, so we can find out
	 * what's the module
	 *
	 * @param $austNode (int)
	 */
	function getStructureIdByName($string){
		$string = strtolower($string);
		$sql = "SELECT id
				FROM taxonomy
				WHERE
					lower(name) LIKE '$string' AND
					class = 'estrutura'
				";
		$query = Connection::getInstance()->query($sql);
		if( empty($query) )
			return false;
		
		$result = array();
		if( count($query) == 1){
			$result = reset($query);
			$result = array($result["id"]);
		} else {
			foreach( $query as $record ){
				$result[] = $record['id'];
			}
		}
		return $result;
	}

    /**
     * Returns information from the selected structure
     *
     * @param int $austNode
     * @return array
     */
    public function getStructureById( $austNode ) {
		
		if( array_key_exists($austNode, $this->_structureCache) )
			return $this->_structureCache[$austNode];
	
		$sql = "SELECT * FROM ".Aust::$austTable." WHERE id='".$austNode."'";
        $result = Connection::getInstance()->query( $sql );

		if( !empty($result) )
			$result = reset($result);
			
		$this->_structureCache[$austNode] = $result;
        return $result;
    }

    public function getStructureByCategoryId($id) {
		return $this->getSomethingByNodeId($id, 'structure');
    }


	function getInvisibleStructures(){
        $sql = "SELECT
                    local
                FROM
                    config
                WHERE
                    tipo='mod_conf' AND
					propriedade='related_and_visible' AND
					valor='0'
                ";
        $query = Connection::getInstance()->query($sql);
		$result = array();
		foreach( $query as $value ){
			$result[] = $value["local"];
		}
		return $result;
	}

	function getRelatedSlaves($ids = array()){
		if( empty($ids) )
			return array();
		
		if( is_string($ids) )
			$ids = array($ids);
		
		$whereStatement = "master_id IN ('".implode("','", $ids)."')";
		
		$sql = "SELECT * FROM
					aust_relations
				WHERE
					$whereStatement
				";
		$result = Connection::getInstance()->query($sql);
		if( empty($result) )
			return array();
		
		$return = array();
		foreach( $result as $slave ){
			$return[$slave['master_id']][] = $slave;
		}
		
		return $return;
	}

	function getRelatedMasters($ids = array()){
		if( empty($ids) )
			return array();
		
		if( is_string($ids) )
			$ids = array($ids);
		
		$whereStatement = "slave_id IN ('".implode("','", $ids)."')";
		
		$sql = "SELECT * FROM
					aust_relations
				WHERE
					$whereStatement
				";
		
		$result = Connection::getInstance()->query($sql);
		if( empty($result) )
			return array();
		
		$return = array();
		foreach( $result as $master ){
			$return[$master['slave_id']][] = $master;
		}
		
		return $return;
	}	
    /**
     * getStructuresBySite()
     *
     * Fetch all structures of a given site id.
     *
     * @param <int> $id
     * @return <array>
     */
    public function getStructuresBySite($id='') {
        if( empty($id) )
            return false;

        /*
         * Returns all structures of given site id
         */
        $sql = "SELECT
                    lp.*, lp.name as name, lp.type as type,
                    ( SELECT COUNT(*)
                    FROM
                    ".self::$austTable." As clp
                    WHERE
                    clp.father_id=lp.id
                    ) As num_sub_nodes
                FROM
                    ".self::$austTable." AS lp
                WHERE
                    lp.father_id = '".$id."' AND
                    lp.class = 'estrutura'
                ORDER BY
                    lp.type DESC,
                    lp.name ASC
        ";
        $query = Connection::getInstance()->query($sql);

        return $query;
    }

	/*
	 *
	 * GENERAL PURPOSE & SUPPORT METHODS
	 * 
	 */

	function getSomethingByNodeId($id, $targetKeyword = ""){
		
		if( empty($id) || !is_numeric($id) )
			return false;
			
		if( $targetKeyword == 'site' )
			$target = Aust::$austSiteType;
		else if( $targetKeyword == 'structure' )
			$target = Aust::$austStructureType;
		else
			return false;
		
		$sql = "SELECT * FROM ".Aust::$austTable." WHERE id='$id'";
		$query = Connection::getInstance()->query($sql);
		if( empty($query) )
			return false;
		
		$query = reset($query);
		$fatherId = $query['father_id'];
		
		if( $query['class'] == $target )
			return $query;
		
		$fatherId = $query['father_id'];
		if( $targetKeyword == 'site' && empty($query['father_id']) && !empty($query['site_id']) )
			$fatherId = $query['site_id'];

		return $this->getSomethingByNodeId($fatherId, $targetKeyword);
		
	}
	
    /**
     * Retorna o nome de cada estrutura do sistema (notícias, artigos, etc) no formato ARRAY
     *
     * @param array $param
     * @return array
     */
    function LeEstruturasParaArray($param = '') {
        if(!empty($param['where'])) {
            $where = $param['where'];
        } else {
            $where = "class='estrutura'";
        }

        $orderby = (empty($param['orderby'])) ? '' : $param['orderby'];
        $limit = (empty($param['limit'])) ? '' : $param['limit'];

        $sql = "SELECT
                    *
                FROM
                    taxonomy
                WHERE
                    ".$where."
                ".$orderby." ".$limit;
        $query = Connection::getInstance()->query($sql);

        $estruturas_array = array();
        foreach($query as $chave=>$valor) {
            $estruturas_array[$valor['id']]['nome'] = $valor['name'];
            $estruturas_array[$valor['id']]['tipo'] = $valor['type'];
            $estruturas_array[$valor['id']]['id'] = $valor['id'];
        }
        return $estruturas_array;
    }

    /**
 	 * What module is responsible for a given structure?
	 *
	 * @param $node (int)
	 */
    function structureModule($node) {
	
		if( array_key_exists($node, $this->_structureModuleCache) )
			return $this->_structureModuleCache[$node];
	
        $sql = "SELECT
                	type
                FROM
                	taxonomy
                WHERE
                	id=$node";
        $query = Connection::getInstance()->query($sql);

		$this->_structureModuleCache[$node] = $query[0]['type'];
        return $this->_structureModuleCache[$node];
    }

    // retorna o nome legível do módulo
    function LeModuloDaEstruturaLegivel($node) {
        $sql = "SELECT
                        type
                FROM
                        taxonomy
                WHERE
                        id=$node";

        $query = Connection::getInstance()->query($sql);
        $tipo = $query[0]['type'];
        if(is_file(MODULES_DIR.$tipo.'/config.php')) {
            include(MODULES_DIR.$tipo.'/config.php');
            return $modInfo['name'];
        } else {
            return NULL;
        }
    }

    public function getField($node, $field = '') {
		if( empty($field) )
			$field = "*";
			
        $sql = "SELECT
                    $field
                FROM
                    taxonomy
                WHERE
                    id=$node";
        $query = Connection::getInstance()->query($sql);
		if( $field == "*" )
	        return $query[0];
		else
        	return $query[0][$field];
    }

    /**
     * Retorna o nome de estrutura/categoria de acordo com seu ID
     *
     * @param int $node É o ID da estrutura/categoria a ser buscada
     * @return string Nome da estrutura/categoria
     */
    public function leNomeDaEstrutura($node) {
        $sql = "SELECT
                    name
                FROM
                    taxonomy
                WHERE
                    id=$node";
        $query = Connection::getInstance()->query($sql);
        return $query[0]['name'];
    }

    function LimpaVariavelCategorias() {
        if(is_array($this->AustCategorias)) {
            foreach($this->AustCategorias as $key=>$valor) {
                array_pop($this->AustCategorias);
            }
        }
    }

	/*
	 *
	 * Category related
	 *
	 */

    /**
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    function categoriasFilhas($params) {

        /**
         * Trata cada variável recebida
         */
        $pai = (empty($params['pai'])) ? 0 : $params['pai'];
        $categoriaChefe = (empty($params['categoriaChefe'])) ? '' : $params['categoriaChefe'];
        $nivel = (empty($params['nivel'])) ? 0 : $params['nivel'];
        $nodeAtual = (empty($params['nodeAtual'])) ? 0 : $params['nodeAtual'];
        /**
         * Precisa-se melhorar esta função. Infelizmente, PHP 5.2 ainda não suporte método dentro de método,
         * portanto precisamos usar um método externo
         */
        $this->LeCategoriasFilhasCopy($categoriaChefe, $pai, $nivel, $nodeAtual); // gambiarra

        if($pai >= 0) {
            $this->AustCategorias[$pai] = '';
        }
        $resultado = $this->AustCategorias;
        $this->LimpaVariavelCategorias();
        return $resultado;
    }

    // gambiarra para que LeCategoriasFilhas possa rodar em loop e retornar $this->AustCategorias e limpando esta variÃ¡vel no final
    function LeCategoriasFilhasCopy($categoriachefe, $parent=0, $level=0, $current_node=-1) {
        /**
         * Guarda qual o id do pai para carregar suas filhas
         */
        $where = "lp.father_id = '$parent'";
        /**
         * Se não for especificada uma estrutura, carrega todas as categorias da categoria chefe
         * especificada
         */
        if($parent == 0) {
            if(is_int($categoriachefe)) {
                $where = $where . " AND lp.id='".$categoriachefe."'";
            } elseif(is_string($categoriachefe)) {
                $where = $where . " AND lp.name='".$categoriachefe."'";
            }
        }
        /**
         * Monta o SQL
         */
        $sql="SELECT
					lp.id, lp.father_id, lp.name, lp.class,
					( SELECT COUNT(*)
						FROM
							".self::$austTable." As clp
						WHERE
							clp.father_id=lp.id
					) As num_sub_nodes
				FROM
					".self::$austTable." AS lp
				WHERE
                $where
                ";

        $query = Connection::getInstance()->query($sql);

        $i = 0;
        $items = '';
        foreach ( $query as $chave=>$myrow ) {

            $this->AustCategorias[$myrow['id']] = $myrow['name'];

            //chamar recursivamente a função
            $items.=$this->LeCategoriasFilhasCopy($categoriachefe, $myrow["id"], $level+1, $current_node);

        }
    }

    /**
     * DEPRECIADO!!!!!
     *
     * Use categoriasFilhas() no lugar desta
     *
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    function LeCategoriasFilhas($categoriachefe, $parent=0, $level=0, $current_node=-1) {
        //trigger_error('Use categoriasFilhas() instead', E_USER_NOTICE);

        $this->LeCategoriasFilhasCopy($categoriachefe, $parent, $level, $current_node); // gambiarra

        if($parent >= 0) {
            $this->AustCategorias[$parent] = 'tetesteste';
        }
        $resultado = $this->AustCategorias;
        $this->LimpaVariavelCategorias();
        return $resultado;
    }

    /**
     * DEPRECIADO!!!!!
     *
     * Use categoriasFilhas() no lugar desta
     *
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    // LISTAR: funÃ§Ã£o que retorna diretÃ³rio e arquivo para include da listagem do mÃ³dulo da estrutura com id $aust_node
    function AustListar($aust_node = '0') {
        $pasta_do_modulo = $this->structureModule($aust_node);
        if(is_file(MODULES_DIR.$pasta_do_modulo.'/listar.php')) {
            return MODULES_DIR.$pasta_do_modulo.'/listar.php';
        } else {
            return VIEWS_DIR.'content/listar.inc.php';
        }
    }

    // Lê somente estruturas que não devem ter categorias e grava em uma $_SESSION
    function EstruturasSemCategorias() {
	
		if( !empty($_SESSION['structure_only']) )
        	unset( $_SESSION['structure_only']);
		
        $diretorio = MODULES_DIR; // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if(is_file($pastas.'/config.php')) {
                include($pastas.'/config.php');
                if($modInfo['structure_only']) {

                    $tmparray = array_reverse( explode("/", $pastas));
                    $_SESSION['structure_only'][] = $tmparray[0];
                }
                //echo 'oi' ;
            }
        }


    }


    /**
     * VERIFICAÇÕES
     */

    // verifica se existe alguma categoria instalada e retorna TRUE ou FALSE
    public function Instalado() {
        $sql = "SELECT
                    id
                FROM
                    taxonomy";
        return Connection::getInstance()->count($sql);
    }

    /*
     *
     * RENDERIZAÇÃO
     *
     */
    /**
     * getCategoryHtmlSelect()
     *
     * Retorna <select> com as categorias atuais
     *
     * @param <type> $austNode
     * @param <type> $currentNode
     * @return <string>
     */
    public function getCategoryHtmlSelect($austNode, $currentNode = ''){
        $tmp = BuildDDList( Registry::read('austTable') ,'frmnode_id', User::getInstance()->tipo ,$austNode, $currentNode, false, true);
        return $tmp;
    }

}


?>