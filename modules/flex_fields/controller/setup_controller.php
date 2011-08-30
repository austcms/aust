<?php
/**
 * Controller principal deste módulo
 *
 * @since v0.1.6 06/07/2009
 */

class SetupController extends ModActionController
{

	function beforeFilter(){
		
		$_POST['name'] = trim($_POST['name']);
		$_SESSION['exPOST'] = $_POST;
		$this->set('exPOST', $_SESSION['exPOST']);
		
		if( !empty($_POST) && !empty($_POST['setupAction']) ){
			$this->customAction = $_POST['setupAction'];
		}
		parent::beforeFilter();
	}

	function index(){
		$this->set('fieldsQuantity', 2);
	}
	/**
	 * setuppronto()
	 *
	 * Cria cadastro
	 *
	 * Campos especificados, agora começa a criar tabelas e configurações.
	 *
	 * @global array $aust_charset Contém o charset global do sistema
	 */
	function setuppronto(){
		
		$this->loadModel("FlexFieldsSetup");
		
		global $aust_charset;

		$fields = array();
		$i = 0;
		// prepara array com campos
		foreach( $_POST['campo'] as $key=>$value ){
			if( empty($value) )
				continue;
			
			$fields[$i] = array(
				'name' => $value,
				'type' => $_POST['campo_tipo'][$key],
				'description' => $_POST['campo_descricao'][$key],
			);
			
			/*
			 * Campos relacionados têm informações sobre quais campos são
			 * relacionados.
			 */
			if( !empty($_POST['relacionado_tabela_'.($key+1)])
				AND !empty($_POST['relacionado_campo_'.($key+1)]) )
			{
				$fields[$i]['refTable'] = $_POST['relacionado_tabela_'.($key+1)];
				$fields[$i]['refField'] = $_POST['relacionado_campo_'.($key+1)];
			}
			
			$i++;
		}
		
		/**
		 * Parâmetros para gravar uma nova estrutura no DB.
		 */
		$params = array(
			'name' => $_POST['name'],
			'site' => $_POST['site'],
			'module' => $_POST['module'],
			'author' => User::getInstance()->getId(),
			'fields' => $fields,
		);

		if( $this->FlexFieldsSetup->createStructure($params) ){
			$this->render();
		}

		return true;
	}

}
?>