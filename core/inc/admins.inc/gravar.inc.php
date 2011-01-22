<?php
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

    if( empty($_POST['frmsenha']))
        unset($_POST['frmsenha']);

	$c = 0;
    foreach($_POST as $key=>$valor) {
        // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0) {
            $sqlcampo[] = str_replace('frm', '', $key);
            $sqlvalor[] = $valor;
            // ajusta os campos da tabela nos quais serão gravados dados
            $valor = addslashes($valor);
            if($_POST['metodo'] == 'criar') {
                if( !empty($c) && $c > 0 ) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                    $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                } else {
                    $sqlcampostr = str_replace('frm', '', $key);
                    $sqlvalorstr = "'".$valor."'";
                }
            } else if($_POST['metodo'] == 'editar') {
                if( !empty($c) && $c > 0) {
                    $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                } else {
                    $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
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

    if($conexao->exec($sql) !== false) {
        $resultado = true;
    } else {
        $resultado = false;
    }

	$connLastId = $conexao->lastInsertId();
	$lastInsertId = '';
	if( !empty($_POST['w']) ){
		$lastInsertId = $_POST['w'];
	}elseif( !empty( $connLastId ) ){
		$lastInsertId = $connLastId;
	}

	/* IMAGEM PRIMARIA */
	if( !empty($_FILES['photo']) && !empty($_FILES['photo']['name']) ){
		$value = $_FILES['photo'];

		$sql = "SELECT * FROM admins_photos
				WHERE admin_id='".$lastInsertId."' AND image_type='primary'
				";
		$query = $conexao->query($sql);
		foreach( $query as $row ){
			if( file_exists($row['systempath']) )
				unlink($row['systempath']);
		}
		$conexao->exec("DELETE FROM admins_photos WHERE admin_id='".$lastInsertId."' AND image_type='primary'");
		
		
		$imageHandler = Image::getInstance();
		$value = $imageHandler->resample($value);
		$finalName = $imageHandler->upload($value);

		$finalName['systemPath'] = addslashes($finalName['systemPath']);
		$finalName['webPath'] = addslashes($finalName['webPath']);

		/*
		 * Salva SQL da imagem
		 */
		$sql = "INSERT INTO admins_photos
				(
					admin_id,
					image_type,
					title,
					systempath,
					path,
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

		$conexao->exec($sql);
	}
	
	/* IMAGEM SECUNDÁRIA */
	if( !empty($_FILES['secondary_photo']) && !empty($_FILES['secondary_photo']['name']) ){
		$value = $_FILES['secondary_photo'];

		$sql = "SELECT * FROM admins_photos
				WHERE admin_id='".$lastInsertId."' AND image_type='secondary'
				";
		$query = $conexao->query($sql);
		foreach( $query as $row ){
			if( file_exists($row['systempath']) )
				unlink($row['systempath']);
		}
		$conexao->exec("DELETE FROM admins_photos WHERE admin_id='".$lastInsertId."' AND image_type='secondary'");
		
		
		$imageHandler = Image::getInstance();
		$value = $imageHandler->resample($value);
		$finalName = $imageHandler->upload($value);

		$finalName['systemPath'] = addslashes($finalName['systemPath']);
		$finalName['webPath'] = addslashes($finalName['webPath']);

		/*
		 * Salva SQL da imagem
		 */
		$sql = "INSERT INTO admins_photos
				(
					admin_id,
					image_type,
					title,
					systempath,
					path,
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

		$conexao->exec($sql);
	}	

}
    if($resultado) {
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> Informações salvas com sucesso!';
    } else {
        $status['classe'] = 'insucesso';
        $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro desconhecido. Tente novamente. '.
            'Se o problema prosseguir, contacte um administrador.';
    }
    EscreveBoxMensagem($status);

?>
<p><a href="adm_main.php?section=admins">Voltar</a></p>

