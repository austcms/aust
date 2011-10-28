<?php
class EmailSenderHook extends HookBase
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
		$options['perform'] = $this->getSqlFunction($options['perform']);
		
		
		$to = $this->to($options['perform']);
		$subject = $this->subject($options['perform']);
		$from = $this->from($options['perform']);

		$options['perform'] = $this->cleanUpText($options['perform']);
		$options['perform'] = nl2br($options['perform']);
		if( empty($to) ) return false;
		if( empty($from) ) $from = "";
		if( empty($subject) ) $subject = "";
		
		$to = explode(";", $to);
		
		foreach( $to as $email ){
			if( !mail($email, $subject, $options['perform'],  "MIME-Version: 1.1\nContent-type: text/html; charset=utf-8\nFrom: ".$from."\nReply-To: ".$from."", "-r".$from) ){
				mail( $email, $subject, $options['perform'],  "MIME-Version: 1.1\nContent-type: text/html; charset=utf-8\nFrom: ".$from."\nReply-To: ".$from."\nReturn-Path: ".$from);
			}
		}

		return true;
	}
	
	function to($options){
		$options = $this->getSqlFunction($options);
		preg_match('/\{to:(.*)}/', $options, $matches);
		
		if( !empty($matches[1]) ){
			return str_replace(";;", ";", $matches[1]);
		}
		return false;
	}

	function from($options){
		preg_match('/\{from:(.*)}/', $options, $matches);
		
		if( !empty($matches[1]) )
			return str_replace(";;", ";", $matches[1]);

		return false;
	}
	
	function subject($options){
		preg_match('/\{subject:(.*)}/', $options, $matches);
		
		if( !empty($matches[1]) ){
			return str_replace(";;", ";", $matches[1]);
		}
		return false;
	}

}
?>