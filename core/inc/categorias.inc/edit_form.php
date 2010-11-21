<?php
/*
 * CARREGA DADOS
 * Carrega dados das tabelas para amostragem do formulário de edição
 */

    $sql = "SELECT
                *
            FROM
                categorias
            WHERE
                id='".$_GET['w']."'
    ";
    $query = $conexao->query($sql);
    $dados = $query[0];
?>


<h2>Editar categoria</h2>
<p>
    <a href="javascript: history.back();"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<?php
    if(!empty($specsection['formnew_description'])){
        echo $specsection['formnew_description'];
    }
?>

<form method="post" action="adm_main.php?section=<?php echo $_GET['section'];?>&action=update" enctype="multipart/form-data">
<input type="hidden" name="action" value="update">
<input type="hidden" name="autorid" value="<?php echo $loginid; ?>">
<input type="hidden" name="w" value="<?php echo $_GET['w']; ?>">
<table width="670" border=0 cellpadding=0 cellspacing="3">
<col width="250">
<col>
<tr>
    <td>Categoria: </td>
    <td>
        <strong><?php echo $dados['patriarca']?></strong>
    </td>
</tr>
<tr>
    <td valign="top">Nome: </td>
    <td>
        <INPUT TYPE="text" NAME="frmnome" value="<?php echo $dados['nome']?>">
        <p class="explanation">
            Digite o nome da categoria. (Começa com letra maiúscula e não leva
            ponto final)
        </p>
        <p class="explanation" id="exists_titulo">
        </p>
    </td>
</tr>
<tr>
    <td valign="top">Descrição: </td>
    <td>
        <textarea name="frmdescricao" rows="7" cols="40" id="jseditor" style="font-size: 11px; font-family: verdana;"><?php echo $dados['descricao']?></textarea>
        <p class="explanation">
            Digite uma breve descrição desta categoria.
        </p>
        <br /><br />
    </td>
</tr>
<?php
/*
 * FOTO
 */

?>
<tr>
    <td valign="top" colspan="2">
        <h2>Imagem da categoria</h2>

        <?php
        /*
         * Mostra foto da categoria se houver
         */
        $sql = "SELECT id, nome FROM imagens WHERE classe='categorias' AND ref='".$_GET['w']."' ORDER BY id DESC LIMIT 0,1";
        $query = $conexao->query($sql);
        if(count($query)){
            $result = $query[0];
            ?>
            <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid silver;">
                <p><img src=<?php echo LIB_DIR.IMAGE_VIEWER_DIR;?>"visualiza_foto.php?myid=<?php echo $result['id']?>&thumbs=yes&ysize=60" /></p>
                <strong>Imagem atual: <?php echo $result['nome']?></strong>
            </div>
            <?php
        }
        ?>

        <p>Se o site necessita de uma imagem para a categoria, escolha o arquivo da imagem abaixo.</p>

    </td>
</tr>
<tr>
    <td valign="top">Arquivo: </td>
    <td>
        <input type="file" name="arquivo" value="" />
    </td>
</tr>
<tr>
    <td colspan="2" style="padding-top: 30px;"><center><INPUT TYPE="submit" VALUE="Entrar"></center></td>
</tr>
</table>

</form>
<p>
    <a href="javascript: history.back();"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>