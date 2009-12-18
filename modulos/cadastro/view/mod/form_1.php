<?php
/**
 * Formulário deste módulo
 *
 * @package ModView
 * @name form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 09/07/2009
 */

/*
 * EDIT.PHP -> CADASTRO
 * Formulário dinâmico para edição de cadastros
 *
 */
    $this->aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    // se é formulário com $_GET[action] = criar...
	if($_GET[action] == 'criar'){
            $h1 = "Criar: ". $this->aust->leNomeDaEstrutura($_GET[aust_node]);
            $tagp = 'Crie um novo conteúdo abaixo.';
	} else if($_GET[action] == 'editar'){
            $tagh1 = "Editar: ". $this->aust->leNomeDaEstrutura($_GET[aust_node]);
            $tagp = 'Edite o conteúdo abaixo.';
            //echo $_GET['aust_node'];

            /*
             * monta os parâmetros que serão enviados à função SQLParaListagem da
             * classe do módulo
             */
            $param = Array(
                            'categorias' => Array($_GET['aust_node'] => 'Estrutura'),
                            'metodo' => 'editar',
                            'id' => $_GET['w']);
                    $sql = $modulo->SQLParaListagem($param);
                    $mysql = mysql_query($sql);
                    //echo $sql;
	}
?>
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h1><?php echo $tagh1;?></h1>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&action=gravar">
<input type="hidden" name="metodo" value="<?php echo $_GET[action];?>">
<input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
<input type="hidden" name="frmautor" value="<?php echo $administrador->LeRegistro('id');?>">
<input type="hidden" name="w" value="<?php ifisset($_GET['w']);?>">
<table width="670" border=0 cellpadding="0" cellspacing="3">
<col width="200">
<col>



<?php
    /*
     * mostra <input> de módulos embed
     */
    $embed = $modulo->LeModulosEmbed();
    if(count($embed)){
        ?>
        <tr>
            <td colspan="2"><h1>Outras opções</h1></td>
        </tr>
        <?
        foreach($embed AS $chave=>$valor){
            foreach($valor AS $chave2=>$valor2){
                if($chave2 == 'pasta'){
                    if(is_file($valor2.'/embed/usuarios_form.php')){
                        include($valor2.'/embed/usuarios_form.php');
                        for($i = 0; $i < count($embed_form); $i++){
                            ?>
                            <tr>
                                <td valign="top"><label><?php echo $embed_form[$i]['propriedade']?>:</label></td>
                                <td>
                                <? if(!empty($embed_form[$i]['intro'])){ echo '<p class="explanation">'.$embed_form[$i]['intro'].'</p>'; } ?>
                                <?php echo $embed_form[$i]['input'];?>
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
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
