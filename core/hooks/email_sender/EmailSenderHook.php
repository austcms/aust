<?php
class EmailSenderHook
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
	
	function cleanUpText($perform){
		$perform = preg_replace('/\{.*?\}/', '', $perform);
		return $perform;
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
	/**
	 * Parses everything sql()
	 */
	function getSqlFunction($perform){

		/*
		 * matches everything {self.field_name}
		 */
		preg_match_all('/sql\((.*?)\)/', $perform, $data);
		
		foreach( $data[1] as $sql ){
			$query = Connection::getInstance()->query($sql);
			$queryResults = array();
			foreach( $query as $field ){
				$queryResults[] = reset($field);
			}
			
			$resultString = implode(';', $queryResults);
			
			$perform = str_replace('sql('.$sql.')', $resultString, $perform);
		}
		
		$result = $perform;
		return $result;
	}
		
	/**
	 * Parses everything {self.field_name}
	 */
	function getSelfData($self, $austNode, $perform){

		$selfObject = Aust::getInstance()->getStructureInstance($austNode);
		$selfQuery = $selfObject->load(
			array(
				'metodo' => 'edit',
				'categorias' => $austNode,
				'austNode' => $austNode,
				'id' => $self
			)
		);
		if( empty($selfQuery) )
			return false;
		
		$selfQuery = reset($selfQuery);
		
		/*
		 * matches everything {self.field_name}
		 */
		preg_match_all('/\{self.(.*?)}/', $perform, $selfData);
		
		foreach( $selfData[1] as $field ){
			if( !array_key_exists($field, $selfQuery) )
				continue;
			
			$perform = str_replace('{self.'.$field.'}', $selfQuery[$field], $perform);
		}
		
		$result = $perform;
		return $result;
	}
}
?>