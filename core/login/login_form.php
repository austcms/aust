<?php
// este é o arquivo onde está o formulário para efetuar login
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Aust - Gerenciador de Conteúdo</title>
<link rel="stylesheet" href="core/login/index.css" type="text/css" />
</head>

<body>

<div id="outer">
  <div id="middle">
    <div id="inner">
    
        <div id="top">&nbsp;</div>
        <div id="body">

            <h1>Administradores</h1>
            <?php
            if (!empty($_GET['status'])){
                if ($_GET['status'] == "invalido"){
                    echo "<p style=\"color: red\">Login incorreto.</p>";
                }
            }
            
            ?>
            
            <p>
                Se voc&ecirc; tem uma senha de administrador, use-a abaixo
                para acessar a &aacute;rea restrita e gerenciar o banco de dados:</p>
                        
            <form method="post" style="margin: 0;" action="index.php?login=verify">
            
                
                <table width="250" border="0" style="margin: 0 auto;" cellpadding="0" cellspacing="3">
                <col width="50" />
                <col />
                <tr>
                <td><font size="2" face="trebuchet ms" color="#0080C0">Login: </font></td>
                <td><input type="text" name="login" size="20" /></td>
                </tr>
                <tr>
                <td><font size="2" face="trebuchet ms" color="#0080C0">Senha: </font></td>
                <td><input type="password" name="senha" size="20" /></td>
                </tr>
                <tr>
                    <td colspan="2"><br /><br /><p>Se você esqueceu sua senha, entre em contato com um administrador.</p></td>
                </tr>
                <tr height="5">
                <td colspan="2"><center></center></td>
                </tr>
                <tr>
                <td colspan="2"><center>
                  <input type="submit" value="Entrar" /></center></td>
                </tr>
                <tr height="15">
                <td colspan="2"><center></center></td>
                </tr>
                </table>
                
            </form>
        </div>
        <div id="bottom">&nbsp;</div>
    </div>
  </div>
</div>

</body>
</html>
