<?php
$object = new Hook();
$saved = $object->save($_POST);

if( $saved ){
	
    header("Location: adm_main.php?section=hooks&action=index");
	exit();
} else {
	?>
	<p>
		Ocorreu um erro e o Hook não pôde ser criado.
	</p>
	<?php
	$_GET['hook_engine'] = $_POST['hook_engine'];
	$data = $_POST;
	include INC_DIR."hooks.inc/new.inc.php";
}

?>