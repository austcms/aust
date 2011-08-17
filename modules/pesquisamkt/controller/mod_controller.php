<?php
/**
 * Descrição deste arquivo
 *
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{

	public function listing(){
		//$this->render('listar');
	}

	public function create(){


		$this->render('form');
	}

	public function edit(){

		
		$this->render('form');
	}

	public function save(){
		
	}
	
}
?>