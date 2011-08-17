<?php
class ConfigController extends ActionController {
	
	function beforeFilter(){
		if( !empty($_GET['status']) ){
			unset($status);
			$st = $_GET['status'];
			if( $st == '1' ){
				notice('As informações foram salvas com sucesso.');
			}
		}
		
	}

	function index(){


	}
}
?>