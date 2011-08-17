<?php
$page_title = 'Criar o primeiro usuário';
require 'header.php';

?>
	<h1>Ops.. problemas...</h1>
<?php

if( $errorStatus == "no_database_configuration" ){
	?>
	<p>O arquivo de configuração da conexão com o banco de dados não foi encontrado.</p>
	<?php
}
else if( $errorStatus == "no_permission_create_uploads" ){
	?>
	<p>Não foi possível criar o diretório de uploads. Não obtive permissão para isto. Você poderia criar?</p>
	<?php
}
else if( $errorStatus == "no_permission_create_uploads_editor" ){
	?>
	<p>O diretório de uploads existe, mas o do editor não. Não obtive permissão para criá-lo. Você poderia fazer isto?</p>
	<?php
}
?>
	<p>
		<a href="<?php echo THIS_TO_BASEURL ?>index.php">Atualizar esta página</a>
	</p>
<?php


require 'footer.php';
?>