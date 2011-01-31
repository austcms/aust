
<h2>Nova Categoria</h2>
<p>
    <a href="javascript: history.back();"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<?php
    if(!empty($specsection['formnew_description'])){
        echo $specsection['formnew_description'];
    }
?>

<form method="post" action="adm_main.php?section=<?php echo $_GET['section'];?>&action=gravar" enctype="multipart/form-data">
<input type="hidden" name="action" value="gravar">
<input type="hidden" name="frmclasse" value="categoria">

<input type="hidden" name="autorid" value="<?php echo $_SESSION['loginid']; ?>">
<input type="hidden" name="autornome" value="<?php echo $_SESSION['loginnome']; ?>">
<table width="670" border=0 cellpadding=0 cellspacing=0>
<col width="250">
<col>
<tr height="30">
    <td valign="top">Categoria: </td>
    <td>

        <div id="categoriacontainer">
        <?php
        $escala = (empty($escala)) ? '' : $escala;

        $aust->EstruturasSemCategorias();
        echo BuildDDList(Aust::$austTable, 'frmsubordinadoid', $escala);
        ?>
        </div>
        <p class="explanation">
        Selecione acima a categoria ao qual a nova categoria estará subordinada. Exemplo:
        Se você escolher um categoria acima, a que você inserir ficará dentro dela. Para
        a categoria <em>notícias</em>, você pode inserir a categoria <em>futebol</em>, por exemplo.
        </p>
    </td>
</tr>
<?php if($escala == "webmaster"){ ?>
    <tr>
        <td valign="top">Classe: </td>
        <td>
            <INPUT TYPE="radio" NAME="frmclasse" value="estrutura" />Estrutura<br />
            <INPUT TYPE="radio" NAME="frmclasse" checked="checked" value="categoria" />Categoria
            <p class="explanation">
                Digite o nome da categoria. (Título começa com letra maiúscula e não leva
                ponto final)
            </p>
            <p class="explanation" id="exists_titulo">
            </p>
        </td>
    </tr>
<?php } ?>
<tr>
    <td valign="top">Nome: </td>
    <td>
        <INPUT TYPE="text" NAME="frmnome">
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
        <textarea name="frmdescricao" rows="7" cols="40" id="jseditor" style="font-size: 11px; font-family: verdana;"></textarea>
        <p class="explanation">
            Digite uma breve descrição desta categoria.
        </p>
        <br /><br />
    </td>
</tr>
<tr>
    <td valign="top">Arquivo de Imagem: </td>
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