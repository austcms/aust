<?php
/**
 * AUST
 *
 * Responsible for the Taxonomies, in other words, structures and categories.
 *
 * @since v0.1.5, 30/05/2009
 */

class Aust {

	/**
	 * Name of the taxonomy table
	 */
	static $austTable = 'taxonomy';

	/**
	 * In the taxonomy table, the type value that represents sites is...
	 */
	static $austSiteType = 'site';
	/**
	 * In the taxonomy table, the type value that represents structures is...
	 */
	static $austStructureType = 'structure';
	/**
	 * In the taxonomy table, the type value that represents a category is...
	 */
	static $austCategoryType = 'category';

	protected $AustCategorias = Array();
	public $connection;

	public $_structureModuleCache = array();
	public $_structureCache = array();

	function __construct(){
		$this->connection = Connection::getInstance();
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
	 *	  string  [name]			  structure's name;
	 *	  int	 [site]				structure's father id
	 *	  bool	[public]		   	1, everyone has access; 0, only root has access
	 *	  string  [module_dir]		modules' name
	 *	  string  [author]			struture's author id
	 */
	function createStructure($params) {

		$name = $params['name'];
		$name_encoded = (empty($params['name_encoded'])) ? encodeText($name) : $params['name_encoded'] ;
		$siteId = $params['site'];
		$public = (empty($params['public'])) ? '1' : $params['public'] ;
		$module = $params['module'];
		$author = $params['author'];

		$query = Connection::getInstance()->query("SELECT * FROM ".Aust::$austTable." WHERE id='$siteId' AND class='site'");
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
					'$name','$siteId', '".Aust::$austStructureType."','$module',$public,'$author',
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
	 * Creates a new site inside Aust. Multiple sites can be managed in one CMS instance.
	 *
	 * @param string $name of the new site (e.g. 'my site', 'Site' etc)
	 * @param string $description of the new site
	 * @return int id f the created site
	 */
	public function createSite($name, $description = '') {
		$sql = "INSERT INTO
					taxonomy
						(name, description, class, father_id)
					VALUES
						('$name', '$description', 'site', '0')";
		Connection::getInstance()->exec($sql);
		return Connection::getInstance()->lastInsertId();
	}
	
	/**
	 * Creates a new structure's category.
	 *
	 * Automaticaly finds out what is the structure and what is the site.
	 *
	 * @param array $param Contains the following indexes:
	 *	  string  [name]			  structure's name;
	 *	  int	 [father]			its father id, either a structure or category
	 *	  string  [description]
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
			if( file_exists($value['file_systempath']) )
				unlink( $value['file_systempath'] );
			$sqlDelete = "DELETE FROM austnode_images WHERE id='".$value['id']."'";
			Connection::getInstance()->exec($sqlDelete);
		}
		
		return true;
	}

	/*
	 * If not site was found, creates one automatically.
	 */
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
					class='site'
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
					class='site'
				";
		$query = Connection::getInstance()->query($sql);
		if( count($query) > 0 )
			return true;
		return false;
	}
	
	/*
	 *
	 * Structures related
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
	function getStructureIdByName($string, $params = array()){
		$string = strtolower($string);
		
		$additionalConditions = '';
		if( !empty($params) ){
			if( !empty($params['module']) ){
				$additionalConditions[] = "type IN ('".addslashes($params['module'])."')";
			}
		}
		if( !empty($additionalConditions) )
			$additionalConditions = ' AND ('.implode(") AND (", $additionalConditions).')';
		
		$sql = "SELECT id
				FROM taxonomy
				WHERE
					lower(name) LIKE '$string' AND
					class = '".Aust::$austStructureType."'
					$additionalConditions
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
	public function getStructureById($austNode) {
		
		if( array_key_exists($austNode, $this->_structureCache) )
			return $this->_structureCache[$austNode];
	
		$sql = "SELECT * FROM ".Aust::$austTable." WHERE id='".$austNode."'";
		$result = Connection::getInstance()->query( $sql );

		if( !empty($result) )
			$result = reset($result);
			
		$this->_structureCache[$austNode] = $result;
		return $result;
	}
	/**
	 * Returns information from the selected structure
	 *
	 * @param int $austNode
	 * @return array
	 */
	public function getStructureNameById($id) {
		$structure = $this->getStructureById($id);
		return $structure['name'];
	}

	public function getStructureByCategoryId($id) {
		return $this->getSomethingByNodeId($id, 'structure');
	}

	/**
	 * Some relational structures can be configurated as invisible. For example,
	 * a PhotoGallery structure can be slave of a Textual structure, the first
	 * having pics of the second. The first should not appear on the management
	 * listing for final users. This finds out which one should be invisible.
	 *
	 * @return array with a structures listing
	 */
	function getInvisibleStructures(){
		$sql = "SELECT
					local
				FROM
					".Config::getInstance()->table."
				WHERE
					type='structure' AND
					property='related_and_visible' AND
					value='0'
				";
		$query = Connection::getInstance()->query($sql);
		$result = array();
		foreach( $query as $value ){
			$result[] = $value["local"];
		}
		return $result;
	}

	/**
	 * Some structures can stay as slaves of others. For example,
	 * a PhotoGallery structure can be slave of a Textual structure, the first
	 * having pics of the second.
	 *
	 * @return array with a structures listing
	 */
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

	/**
	 * Some structures can stay as masters of others. For example,
	 * a Textual structure can be master of a PhotoGallery structure, the second
	 * having pics of the first.
	 *
	 * @return array with a structures listing
	 */
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
	public function getStructuresBySite($id = '') {
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
					lp.class = '".Aust::$austStructureType."'
				ORDER BY
					lp.type DESC,
					lp.name ASC
		";
		$query = Connection::getInstance()->query($sql);

		return $query;
	}

	/**
	 * anyStructureExists()
	 *
	 * Return true if there is any structure.
	 *
	 * @return bool
	 */
	public function anyStructureExists($sites = array()) {
		
		if( empty($sites) )
			$sites = $this->getStructures();

		if( empty($sites) )
			return false;
		
		foreach( $sites as $site ){
			if( !empty($site['Structures']) )
				return true;
		}
		
		return false;
	}

	/*
	 *
	 * GENERAL PURPOSE & SUPPORT METHODS
	 * 
	 */

	/**
	 * getSomethingByNodeId()
	 * 
	 * This method shouldn't be used directly. This is used by:
	 *
	 * 		- getStructureByCategoryId
	 * 		- getSiteByCategoryId
	 * 
	 *
	 * @param <int> $id of the node you want to retrieve the site or structure
	 * @param <string> $targetKeyword
	 * 					'site':			if you want to know the site of given category
	 * 					'structure':	if you want to know the structure of a given category
	 * 
	 * @return <array>
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

	/**
	 * Return value from a specific $field where record's id equals $id
	 */
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

	function cleanCategoryCache() {
		if(is_array($this->AustCategorias))
			foreach($this->AustCategorias as $key=>$value)
				array_pop($this->AustCategorias);
	}

	 /**
	  * Retrieves all children of a given node.
	  *
	  * @param int $id of the node we want to know the children
	  * @return array with all the children
	  */
	 function getNodeChildren($nodeId) {
	
		 $this->AustCategorias = array();
		 $this->_getNodeChildrenEngine($nodeId);

		 $result = $this->AustCategorias;
		 $this->cleanCategoryCache();
		 return $result;
	 }

	/**
	 * Serves for the purpose of looping around itself recursively, retrieving all
	 * children of a given node.
	 */
	function _getNodeChildrenEngine($parent=0, $level=0, $current_node=-1) {

		$where = "lp.father_id = '$parent'";

		/*
		 * SQL fabrication
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

			/*
			 * recursively calls itself, retrieving the children.
			 */
			$items.=$this->_getNodeChildrenEngine($myrow["id"], $level+1, $current_node);

		}
	}

}
?>