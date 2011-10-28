<?php
class SqlExecuterHook extends HookBase
{
	
	function perform($self, $options){
		
		$austNode = $options['node_id'];
		if( $options['when_action'] == "approve_record" ){
			$selfObject = Aust::getInstance()->getStructureInstance($austNode);
			$sql = "SELECT approved FROM ".$selfObject->LeTabelaDaEstrutura($austNode)." WHERE id='".$self."'";
			$query = Connection::getInstance()->query($sql);

			if( empty($query) )
				return false;
			if( $query[0]['approved'] == "1" )
				return true;
		}
		
		$options['perform'] = $this->getSelfData($self, $options['node_id'], $options['perform']);


		$options['perform'] = $this->cleanUpText($options['perform']);
		$options['perform'] = str_replace('INSERTINTO', 'INSERT INTO', $options['perform']);

		Connection::getInstance()->query(stripslashes($options['perform']) );
		return true;
	}
	
}
?>