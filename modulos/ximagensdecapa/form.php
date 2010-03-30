<?php
    $aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    $w = (!empty($_GET['w'])) ? $_GET['w'] : '';
    if($_GET[action] == 'criar'){
		$tagh1 = "Criar: ". $aust->leNomeDaEstrutura($_GET['aust_node']);
		$tagp = 'Crie um novo conteúdo abaixo.';
	} else if($_GET[action] == 'editar'){
		$tagh1 = "Editar: ". $aust->leNomeDaEstrutura($_GET['aust_node']);
		$tagp = 'Edite o conteúdo abaixo.';
		$sql = "
				SELECT
					*
				FROM
					".$modulo->tabela_criar."
				WHERE
					id='$w'
				";
		$mysql = mysql_query($sql);
		$dados = mysql_fetch_array($mysql);
	}
?>
<p>
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h2><?=$tagh1;?></h2>
<p><?=$tagp;?></p>



<form method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING'];?>&action=gravar">
<input type="hidden" name="metodo" value="<?php echo $_GET[action];?>">
<? if($_GET[action] == 'criar'){ ?>
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<? } ?>
<input type="hidden" name="w" value="<?php ifisset($dados[id]);?>">
<table border=0 cellpadding=0 cellspacing=0 class="form">
<col width="200">
<col width="470">
<tr>
	<td valign="top"><label>Categoria:</label></td>
	<td>
		<div id="categoriacontainer">
		<?php
		if($_GET[action] == "editar")
			$current_node = $dados[categoria];
        echo BuildDDList($aust_table,'frmcategoria',$escala,$aust_node,$current_node);
		?>
		</div>

	</td>    
</tr>
<tr>
	<td valign="top"><label>Título:</label></td>
	<td>
		<INPUT TYPE="text" NAME="frmtitulo" class="text" value="<?php ifisset($dados['titulo']);?>">
		<p class="explanation">

		</p>
	</td>
</tr>
<tr>
	<td colspan="2"><label>Texto: </label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<textarea name="frmtexto" id="jseditor" rows="20" style="width: 670px"><?=$dados['texto'];?></textarea>
        <br />
	</td>
</tr>
<tr>
	<td valign="top"><label>Modo:</label></td>
	<td>
		<select name="frmrestrito" class="select">
			<option <? makeselected($dados['restrito'], 'normal'); ?> value="normal">Mostrar em todas as páginas</option>
			<option <? makeselected($dados['restrito'], 'naofrontend'); ?> value="naofrontend">Não mostrar na página principal</option>
			<option <? makeselected($dados['restrito'], 'invisivel'); ?> value="invisivel">Tornar invisível este item em todo o site</option>
		</select>
		<p class="explanation">
			Selecione acima que tipo de exibição você deseja para este conteúdo.
		</p>
	</td>
</tr>

<?php
    /*
     * mostra <input> de módulos embed
     */
    $embed = $modulo->LeModulosEmbed();
    if(count($embed)){
        ?>
        <tr>
            <td colspan="2"><h2>Outras opções</h2></td>
        </tr>
        <?
        foreach($embed AS $chave=>$valor){
            foreach($valor AS $chave2=>$valor2){
                if($chave2 == 'pasta'){
                    if(is_file($valor2.'/embed/form.php')){
                        include($valor2.'/embed/form.php');
                        for($i = 0; $i < count($embed_form); $i++){
                            //echo $chave4. '-'.$valor4.'<br>';
                            ?>
                            <tr>
                                <td valign="top"><label><?=$embed_form[$i]['propriedade']?>:</label></td>
                                <td>
                                <? if(!empty($embed_form[$i]['intro'])){ echo '<p class="explanation">'.$embed_form[$i]['intro'].'</p>'; } ?>
                                <?=$embed_form[$i]['input'];?>
                                <? if(!empty($embed_form[$i]['explanation'])){ echo '<p class="explanation">'.$embed_form[$i]['explanation'].'</p>'; } ?>
                                </td>
                            </tr>
                            <?
                        }
                    }
                }
            }
        }
    }
?>

<tr>
    <td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" VALUE="Enviar!" name="submit" class="submit"></center></td>
</tr>
</table>

</form>
<p>
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
