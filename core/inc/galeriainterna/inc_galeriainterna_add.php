<?
header("Cache-Control: no-cache, must-revalidate");

session_id(strip_tags($_GET['sid']));
session_start();

include("../../../conf.php");
include("../../../conn.php");
include("../../../func.php");
?>

<html>
<head>
<title>Adicionar nova imagem - <?php echo $_SESSION["loginnome"];?></title>

<style>
a   { color: blue }
</style>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0">
<div style="width:100%; height: 25px; background:white; padding: 0px; padding-left: 3px; padding-top: 2px; text-align: center;">

</div>
<div style="width:100%; height: 25px; background:#FFFFC4; padding-left: 3px; padding-top: 2px; text-align: center;">

    <a href="inc_galeriainterna_geral.php?sid=<?php echo session_id();?>">Voltar</a>
<form action="inc_galeriainterna_geral.php?sid=<?php echo session_id();?>" method="post" enctype="multipart/form-data">
<input type="hidden" value="gravar" name="acao">

<h3>Adicione uma nova imagem à galeria:</h3>
<br />
<br />
Selecione a imagem: <input type="file" name="arquivopeq" class="input_adm">
<br />
<br />
<input type="submit" value="Enviar">
 
</form>

</div>

</body>
</html>
