<h2>Atualização de Categoria</h2>

<?php

/*
 * ATUALIZA CATEGORIAS
 */
/**
 * Inicializa variáveis
 */
//$status_imagem = false;

/**
 * Verifica se um arquivo foi realmente enviado. Se não foi, assegura-se de
 * excluir a variável que poderia ter informações sobre algum arquivo.
 */
if(!empty($_FILES['arquivo'])){
    if(empty($_FILES['arquivo']['name']) OR empty($_FILES['arquivo']['type'])){
        $_FILES['arquivo'] = array();
    }
}

/**
 * Se uma imagem foi enviada, faz todo o processamento
 */
if( !empty($_FILES['arquivo']) ){

	$file = $_FILES['arquivo'];
	
	$imageHandler = Image::getInstance();
	$aust = Aust::getInstance();
	$user = User::getInstance();

	if( !empty($_POST['w']) )
		$aust->deleteNodeImages( $_POST['w'] );
	
	$newFile = $imageHandler->resample($file);
	$finalName = $imageHandler->upload($newFile);

	$finalName['systemPath'] = addslashes($finalName['systemPath']);

	$sql = "INSERT INTO 
			austnode_images
			(
			node_id, 
			file_size, systempath, file_name, original_file_name,
			file_type, file_ext, 
			created_on, updated_on, admin_id
			)
			VALUES
			('".$_POST['w']."',
			'".$newFile['size']."', '".$finalName['systemPath']."', '".$finalName['new_filename']."', '".$newFile['name']."',
			'".$newFile['type']."','".$finalName['extension']."',
			'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".$user->getId().")";

	// insere no DB
	if ($conexao->exec($sql)){
	    $status_imagem = true;
	} else {
	    $status_imagem = false;
	}

}



/**
 * Ajusta dados vindos via POST para criar sql
 */
$texto = $_POST['frmdescricao'];
$texto = str_replace("\"","\"", $texto);
$texto = str_replace("'","\'", $texto);


$sql = "UPDATE categorias
        SET
            nome='".$_POST['frmnome']."',
            descricao='".$texto."'
        WHERE
            id='".$_POST['w']."'
        ";

//							echo $sql;
if(!empty($status_imagem) AND $status_imagem == true){
    echo '<p>Nova imagem salva com sucesso!</p>';
} elseif( !empty($status_imagem) ) {
    echo '<p>Houve um erro desconhecido ao salvar a imagem. Contate o administrador.</p>';
}

$result = $conexao->exec($sql);

if ( $result > 0 ){
    echo '<p style="color: green;">As informações foram salvas com sucesso!</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'&action=list_content"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';

} elseif ( is_int($result) AND $result == 0 ){
    echo '<p style="color: green;">Os dados enviados são idênticos aos já existentes. Nenhuma alteração feita.</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'&action=list_content"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';
} else {
    echo '<p style="color: red;">Ocorreu um erro desconhecido ao salvar as informações. Tente novamente.</p>';
    echo '
        <p>
            <a href="adm_main.php?section='.$_GET['section'].'"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
        </p>';
}


?>
