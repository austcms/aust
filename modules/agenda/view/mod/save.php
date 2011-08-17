<?php
//pr($_POST);
if( $resultado ){
	$status['classe'] = 'sucesso';
	$status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
} else {
	$status['classe'] = 'insucesso';
	$status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
}

EscreveBoxMensagem($status);
?>
<br />
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
