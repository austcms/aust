<?php
/**
 * Manage Hooks
 *
 * This class manages installation of hooks, not performing the Hooks.
 * See HookBase for the hooks' superclass.
 */
class Hook
{
	
	function __construct()
	{
		
	}
	
	/* 
	 * PERFORM
	 */
	/**
	 * perform()
	 *
	 */
	function perform($params = array()){
		if( empty($params['when']) ) return false;
		if( $params['when'] == 'approve_record' &&
			!is_array($params['self']) ){
			return false;
		}
		
		$austNode = $_GET['aust_node'];
		
		$sql = "SELECT * FROM hooks WHERE node_id='".$austNode."' AND when_action='".$params['when']."'";
		$hooks = Connection::getInstance()->query($sql);
		
		if( is_array($params['self']) ){
			foreach( $params['self'] as $self ){
				$this->performInEachSelf($self, $hooks);
			}
		}
	}
	
	function performInEachSelf($self, $hooks){
		foreach( $hooks as $hook ){
			$hookObject = $this->instantiateHookEngine($hook['hook_engine']);
			$hookObject->perform($self, $hook);
		}
	}

	/*
	 * Each HookEngine has a class. This method return its
	 * instantiation.
	 */
	function instantiateHookEngine($engineName = ''){
		if( empty($engineName) )
			return false;
			
		include(HOOKS_DIR.$engineName."/configuration.php");
		$className = $configuration["className"];
		include_once(HOOKS_DIR.$engineName."/".$className.".php");
		$object = new $className();
		return $object;
	}	
	/*
	 * Reading and Saving
	 */
	/*
	 * Returns a list of engines available for usage.
	 */
	function loadHookEngines(){
		$result = array();
		foreach( glob(HOOKS_DIR."*") as $folder ){
			if( !file_exists($folder."/configuration.php") )
				continue;

			include($folder."/configuration.php");
			$result[basename($folder)] = array(
				"configuration" => $configuration
			);
		}
		
		return $result;
	}
	
	/*
	 * Saves a hook, be it new or update.
	 */
	function save($post){
		$new = false;
		if( empty($post['id']) )
			$new = true;
		
		$post['hook_engine'] = addslashes($post['hook_engine']);
		$post['when_action'] = addslashes($post['when_action']);
		$post['description'] = addslashes($post['description']);
		$post['perform'] 	 = addslashes($post['perform']);
		
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
	
	/*
	 * Returns all the Hooks created.
	 */
	function allHooks(){
		$sql = 'SELECT * FROM hooks ORDER BY hook_engine';
		return Connection::getInstance()->query($sql);
	}
	
	function getStructureName($nodeId){
		$st = Aust::getInstance()->pegaInformacoesDeEstrutura($nodeId);
		$st = reset($st);
		return $st["nome"];
	}
	
	function find($id){
		$sql = "SELECT * FROM hooks WHERE id='{$id}'";
		return Connection::getInstance()->query($sql);
	}
}
?>