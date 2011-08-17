<?php
class AdminsController extends ActionController {
	
	function beforeFilter(){
		if(	$this->_action() == 'edit' ){
			$this->customAction = 'form';
		}
		
		if( !empty($_GET['block'])
			AND $_GET['block'] == "block")
			$this->changeBlockStatus(true);

		if( !empty($_GET['block'])
			AND $_GET['block'] == "unblock")
			$this->changeBlockStatus(false);
		
	}
	
	function index(){
		
	}
	
	function save(){
		/*
		 * USUÀRIOS
		 *
		 * -> Arquivo de gravação de dados no DB
		*/
		/**
		 * Se é edição, busca informações sobre o usuário.
		 */

		$resultado = false;
		if(!empty($_POST)) {

			if( empty($_POST['frmpassword']))
				unset($_POST['frmpassword']);

			if( $_POST['metodo'] == 'criar' ){
				$_POST['frmadmin_id'] = User::getInstance()->LeRegistro('id');
				$_POST['frmcreated_on'] = date("Y-m-d H:i:s");
			}
			$c = 0;
			foreach($_POST as $key=>$value) {
				// se o argumento $_POST contém 'frm' no início
				if(strpos($key, 'frm') === 0) {
					$sqlcampo[] = str_replace('frm', '', $key);
					$sqlvalor[] = $value;
					// ajusta os campos da tabela nos quais serão gravados dados
					$value = addslashes($value);
					if($_POST['metodo'] == 'criar') {
						if( !empty($c) && $c > 0 ) {
							$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
							$sqlvalorstr = $sqlvalorstr.",'".$value."'";
						} else {
							$sqlcampostr = str_replace('frm', '', $key);
							$sqlvalorstr = "'".$value."'";
						}
					} else if($_POST['metodo'] == 'editar') {
						if( !empty($c) && $c > 0) {
							$sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$value.'\'';
						} else {
							$sqlcampostr = str_replace('frm', '', $key).'=\''.$value.'\'';
						}
					}

					$c++;
				}
			}



			if($_POST['metodo'] == 'criar') {
				$sql = "INSERT INTO
							admins
							($sqlcampostr)
					  VALUES
							($sqlvalorstr)
						";

			} else if($_POST['metodo'] == 'editar') {
				$sql = "UPDATE
							admins
						SET
							$sqlcampostr
						WHERE
							id='".$_POST['w']."'";
			}

			if(Connection::getInstance()->exec($sql) !== false) {
				$resultado = true;
			} else {
				$resultado = false;
			}

			$connLastId = Connection::getInstance()->lastInsertId();
			$lastInsertId = '';
			if( !empty($_POST['w']) ){
				$lastInsertId = $_POST['w'];
			}elseif( !empty( $connLastId ) ){
				$lastInsertId = $connLastId;
			}

			/* IMAGEM PRIMARIA */
			if( !empty($_FILES['photo']) && !empty($_FILES['photo']['name']) ){
				$value = $_FILES['photo'];

				$sql = "SELECT * FROM admin_photos
						WHERE admin_id='".$lastInsertId."' AND file_type='primary'
						";
				$query = Connection::getInstance()->query($sql);
				foreach( $query as $row ){
					if( file_exists($row['file_systempath']) )
						unlink($row['file_systempath']);
				}
				Connection::getInstance()->exec("DELETE FROM admin_photos WHERE admin_id='".$lastInsertId."' AND file_type='primary'");


				$imageHandler = Image::getInstance();
				$value = $imageHandler->resample($value);
				$finalName = $imageHandler->upload($value);

				$finalName['systemPath'] = addslashes($finalName['systemPath']);
				$finalName['webPath'] = addslashes($finalName['webPath']);

				/*
				 * Salva SQL da imagem
				 */
				$sql = "INSERT INTO admin_photos
						(
							admin_id,
							file_type,
							title,
							file_systempath,
							file_path,
							file_name,
							file_type,
							file_size,
							created_on,
							updated_on
						)
						VALUES
						(
						'".$lastInsertId."',
						'primary',
						'',
						'".$finalName['systemPath']."',
						'".$finalName['webPath']."',
						'".$finalName['new_filename']."',
						'".$value['type']."',
						'".$value['size']."',
						'".date("Y-m-d H:i:s")."',
						'".date("Y-m-d H:i:s")."'
						)
						";

				Connection::getInstance()->exec($sql);
			}

			/* IMAGEM SECUNDÁRIA */
			if( !empty($_FILES['secondary_photo']) && !empty($_FILES['secondary_photo']['name']) ){
				$value = $_FILES['secondary_photo'];

				$sql = "SELECT * FROM admin_photos
						WHERE admin_id='".$lastInsertId."' AND file_type='secondary'
						";
				$query = Connection::getInstance()->query($sql);
				foreach( $query as $row ){
					if( file_exists($row['file_systempath']) )
						unlink($row['file_systempath']);
				}
				Connection::getInstance()->exec("DELETE FROM admin_photos WHERE admin_id='".$lastInsertId."' AND file_type='secondary'");


				$imageHandler = Image::getInstance();
				$value = $imageHandler->resample($value);
				$finalName = $imageHandler->upload($value);

				$finalName['systemPath'] = addslashes($finalName['systemPath']);
				$finalName['webPath'] = addslashes($finalName['webPath']);

				/*
				 * Salva SQL da imagem
				 */
				$sql = "INSERT INTO admin_photos
						(
							admin_id,
							file_type,
							title,
							file_systempath,
							file_path,
							file_name,
							file_type,
							file_size,
							created_on,
							updated_on
						)
						VALUES
						(
						'".$lastInsertId."',
						'secondary',
						'',
						'".$finalName['systemPath']."',
						'".$finalName['webPath']."',
						'".$finalName['new_filename']."',
						'".$value['type']."',
						'".$value['size']."',
						'".date("Y-m-d H:i:s")."',
						'".date("Y-m-d H:i:s")."'
						)
						";

				Connection::getInstance()->exec($sql);
			}	

		}

		if( $resultado )
			notice('<strong>Sucesso: </strong> Informações salvas com sucesso!');
		else {
			failure('Ocorreu um erro desconhecido. Tente novamente. '.
				'Se o problema prosseguir, contacte um administrador.');
		}
	
		$_POST['redirect_to'] = "adm_main.php?section=admins";
		$this->render(false);

	}
	
	private function changeBlockStatus($block = ''){
		if( !is_bool($block) ){
			failure('Não foi possível alterar o status do usuário.');
			return false;
		}
		
		$blockValue = "0";
		if( $block === true )
			$blockValue = "1";
		
		$sql = "UPDATE
  					admins
				SET
					is_blocked='".$blockValue."'
				WHERE
					id='".$_GET["w"]."' AND
					admin_group_id!=( SELECT id FROM admin_groups WHERE name IN ('Webmaster', 'Root') )
				";

		if( Connection::getInstance()->exec($sql) !== false )
			notice('Usuário alterado com sucesso!');
		else
			failure('Erro ao alterar usuário.');
	}
	
}
?>