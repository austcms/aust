<?php
/**
* Manage Hooks
*/
class Hook
{
	
	function __construct()
	{
		
	}
	
	/*
	 * Returns a list of engines available for usage.
	 */
	function loadHookEngines(){
		$result = array();
		foreach( glob(HOOKS_DIR."*") as $folder ){
			if( !file_exists($folder."/configuration.php") )
				continue;
			
			include($folder."/configuration.php");
			$result = array(
				basename($folder) => array(
					"configuration" => $configuration
				),
			);
		}
		
		return $result;
	}
	
	function instantiateHookEngine($engineName = ''){
		if( empty($engineName) )
			return false;
			
		include(HOOKS_DIR.$engineName."/configuration.php");
		$className = $configuration["className"];
		include(HOOKS_DIR.$engineName."/".$className.".php");
		$object = new $className();
		return $object;
	}
	
	function save($post){
		$new = false;
		if( empty($post['id']) )
			$new = true;
		
		if( $new ){
			$sql = "INSERT INTO hooks
						(hook_engine, when_action, node_id, description, perform)
					VALUES
						('".$post['hook_engine']."', '".$post['when_action']."', '".$post['node_id']."', '".$post['description']."', '".$post['perform']."')";
		} else {
			$sql = "UPDATE hooks
					SET
						when_action = '".$post['when_action']."',
						node_id = '".$post['node_id']."',
						description = '".$post['description']."',
						perform = '".$post['perform']."'
					WHERE
						id = '".$_POST['id']."'";
		}
		$connection = Connection::getInstance();
		if( $connection->exec($sql) )
			return true;
		else
			return false;
	}
	
	function allHooks(){
		$sql = 'SELECT * FROM hooks ORDER BY hook_engine';
		return Connection::getInstance()->query($sql);
	}
}
?>