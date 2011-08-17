<?php
class TaxonomyController extends ActionController {
	function index(){
		
	}
	
	function save(){

		$texto = $_POST['frmdescription'];
		$texto = str_replace("\"","\"", $texto);
		$texto = str_replace("'","\'", $texto);

		$params = array(
			'father' => $_POST["frmfather_id"],
			'name' => $_POST['frmname'],
			'description' => $texto,
			'author' => User::getInstance()->getId(),
		);

		$resultado = Aust::getInstance()->createCategory($params);
		
		$lastInsertId = $resultado;
		/**
		 * Se uma imagem foi enviada, faz todo o processamento
		 */
		if( !empty($_FILES['arquivo']) &&
		 	!empty($_FILES['arquivo']['name']) )
		{

			$file = $_FILES['arquivo'];

			$imageHandler = Image::getInstance();
			$aust = Aust::getInstance();
			$user = User::getInstance();

			if( !empty($lastInsertId) )
				Aust::getInstance()->deleteNodeImages( $lastInsertId );

			$newFile = $imageHandler->resample($file);
			$finalName = $imageHandler->upload($newFile);

			$finalName['systemPath'] = addslashes($finalName['systemPath']);

			$sql = "INSERT INTO 
					austnode_images
					(
					node_id, 
					file_size, file_systempath, file_name, original_file_name,
					file_type, file_ext, 
					created_on, updated_on, admin_id
					)
					VALUES
					('".$lastInsertId."',
					'".$newFile['size']."', '".$finalName['systemPath']."', '".$finalName['new_filename']."', '".$newFile['name']."',
					'".$newFile['type']."','".$finalName['extension']."',
					'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".$user->getId().")";

			// insere no DB
			if (Connection::getInstance()->exec($sql)){
				$status_imagem = true;
			} else {
				$status_imagem = false;
			}

		}

		if( $resultado ){
			notice('Informações salvas com sucesso!');
		} else {
			failure('Ocorreu um erro desconhecido, tente novamente.');
		}
		
		$_POST['redirect_to'] = 'adm_main.php?section=taxonomy&list_content';

	}
	
	function update(){
		
		$_POST['force_redirect'] = true;
		$_POST['redirect_to'] = "adm_main.php?section=taxonomy&action=list_content";
	}
}
?>