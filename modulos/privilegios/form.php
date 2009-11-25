<?php
/*
 * FORM.PHP -> PROVILÉGIOS
 * Formulário dinâmico para edição de privilégios
 *
 */
    $aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    // se é formulário com $_GET[action] = criar...
	if($_GET['action'] == 'criar'){
		$tagh1 = "Criar: ". $aust->leNomeDaEstrutura($_GET[aust_node]);
		$tagp = 
                'Crie um novo privilégio a seguir. Somente os usuários cadastrados que tiverem este privilégio poderão
                 acessar os conteúdos associados a este privilégio.';
	} else if($_GET['action'] == 'editar'){
		$tagh1 = "Editar: ". $aust->leNomeDaEstrutura($_GET[aust_node]);
		$tagp = 'Edite o conteúdo abaixo. Somente os usuários cadastrados que tiverem este privilégio poderão
                 acessar os conteúdos associados a este privilégio.';
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
        $dados = mysql_fetch_array($mysql);
	}
?>
<p>
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h1><?=$tagh1;?></h1>
<p><?=$tagp;?></p>

<?
/*
 * Div contendo <select>. Está no início escondido para que possa ser mostrado posteriormente.
 */
?>
<div id="categoriaselect" style="visibility: hidden; height: 1px; font-size: 0px;">
    <?php
    // @todo = opção de criar privilégio mas não adicionar e nenhuma estrutura. Opção via <input radio>. Se selecionar que sim, mostra via JS BuildDDList

    if($_GET[action] == "editar")
        $current_node = $dados['categorias_id'];

    // escreve <select>
    echo BuildDDList($aust_table,'frmcategorias_id',$escala,'',$current_node);
    ?>
    <p class="explanation">
        Selecione a estrutura na qual este privilégio se aplica.
    </p>
</div>

<form method="post" action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING'];?>&action=gravar">
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
<input type="hidden" name="frmautor" value="<?php echo $_SESSION[loginid];?>">
<input type="hidden" name="frmtipo" value="grupo">
<input type="hidden" name="frmchave" value="nome">
<input type="hidden" name="frmcategorias_id" value="NULL">
<input type="hidden" name="w" value="<?php ifisset($dados['id']);?>">
<table width="100%" border="0" cellpadding="0" cellspacing="3">
<col width="200">
<col>

    <tr>
        <td valign="top">Nome para este privilégio: </td>
        <td>
            <? if($dados['classe'] == "padrão"){ ?>
                <INPUT TYPE="text" NAME="frmvalor" value="<?php ifisset($dados['valor']);?>" disabled="disabled" SIZE="65">
                <p class="explanation">
                    "<strong><?=$dados['valor']?></strong>" é um privilégio padrão do sistema, sendo este concedido a todos novos cadastrados. Não é possível alterar seu nome.
                </p>
            <? } else { ?>
                <INPUT TYPE="text" NAME="frmvalor" value="<?php ifisset($dados['valor']);?>" SIZE="65">
                <p class="explanation">
                    Digite um nome curto para este privilégio.
                </p>
                <p class="explanation_example">
                    Exemplo: <em>Curso Online 02</em>
                </p>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td valign="top">Opções: </td>
        <td>
            <div class="input_painel">
                <div class="containner">
                    <div class="options">
                        <p>
                            Há basicamente duas formas de criar este privilégio:
                        <ul>
                            <li>relacionado a uma categoria, assim os usuários precisarão deste privilégio para acessar qualquer item da categoria selecionada;</li>
                            <li>não relacionado, deixando para relacionar posteriormente com conteúdos separadamente</li>
                        </ul>
                        </p>
                        <p>
                            <strong>Criar este grupo privilégio relacionado a algum grupo?</strong><br />
                            <input type="radio" name="privilegio_existe" value="0" <?if($dados['categorias_id']==0 or empty($dados['categorias_id'])) echo 'checked="checked"';?> onclick="javascript: form_privilegio(this.value);" /> Não, desejo simplesmente criar este privilégio e depois configurar cada conteúdo que desejar <br/>
                            <input type="radio" name="privilegio_existe" value="1" <?if($dados['categorias_id']>0 or !empty($dados['categorias_id'])) echo 'checked="checked"';?> onclick="javascript: form_privilegio(this.value);" /> Sim, desejo escolher uma categoria
                        </p>
                    </div>
                    <div id="categoriacontainer_priv" style="border-top: 1px dashed silver; padding-top: 20px;">
                    </div>
                        <?if($dados['categorias_id']>0 or !empty($dados['categorias_id'])){?>

                        <script language="JavaScript">
                            var privilegio_escolhido = true;
                        </script>
                        <? } ?>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">Descrição:
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <textarea name="frmdescricao" rows="8"><?=$dados['descricao'];?></textarea>
            <p class="explanation">
                Digite uma descrição para este privilégio.
            </p>
            <br /><br />
        </td>
    </tr>
    <tr>
    <td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" VALUE="Enviar!" name="submit" class="submit"></center></td>
    </tr>
</table>


</form>
<p>
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
