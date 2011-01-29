<?php
function notice($str = '', $bad = false){
	
	$sucessOrFailure = 'success';
	if( $bad )
		$sucessOrFailure = 'failure';
	
	if( !empty($str) &&
	 	is_string($str) )
	{
		unset($_SESSION[$sucessOrFailure]);
		$_SESSION[$sucessOrFailure]['message'] = $str;
		$_SESSION[$sucessOrFailure]['failure'] = $bad;
	} else {
		if( empty($_SESSION[$sucessOrFailure]['message']) ){
			unset($_SESSION[$sucessOrFailure]);
			return false;
		}
		
		if( empty($_SESSION[$sucessOrFailure]['request_uri']) ){
			$_SESSION[$sucessOrFailure]['request_uri'] = $_SERVER['REQUEST_URI'];
		}
		
		if( $_SESSION[$sucessOrFailure]['request_uri'] != $_SERVER['REQUEST_URI'] ){
			unset($_SESSION[$sucessOrFailure]);
		}
		
		if( !empty($_SESSION[$sucessOrFailure]['message']) ){
			return $_SESSION[$sucessOrFailure]['message'];
		}
				
		return false;
	}
}

function failure($str = ''){
	return notice($str, true);
}


?>