<?php
/*
 * FORM.PHP -> PROVILÉGIOS
 * Formulário dinâmico para edição de privilégios
 *
 */



/*
 * Carrega configurações automáticas do DB
 */
    $params = array(
        "aust_node" => $_GET["aust_node"],
    );
    $moduloConfig = $modulo->loadModConf($params);

/*
 * Ajusta variáveis iniciais
 */
    $aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    $w = (!empty($_GET['w'])) ? $_GET['w'] : '';


// se é formulário com $_GET[action] = criar...
if($_GET['action'] == 'criar') {
    $tagh1 = "Criar: ". $aust->leNomeDaEstrutura($_GET['aust_node']);
    $tagp = 'Alguns usuários precisam de privilégios para acessar determinados '
           .'conteúdos. Comece criando um privilégio a seguir'
           .'.';

} else if($_GET['action'] == 'editar') {

    $tagh1 = "Editar: ". $aust->leNomeDaEstrutura($_GET['aust_node']);
    $tagp = 'Edite o conteúdo abaixo. Somente os usuários cadastrados que tiverem este privilégio poderão
             acessar os conteúdos associados a este privilégio.';
    //echo $_GET['aust_node'];

    /*
     * monta os parâmetros que serão enviados à função SQLParaListagem da
     * classe do módulo
     */
    $sql = "
            SELECT
                *
            FROM
                ".$modulo->tabela_criar."
            WHERE
                id='$w'
            ";
    $query = $modulo->conexao->query($sql);
    $dados = $query[0];
}
?>
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h1><?php echo $tagh1;?></h1>
<p><?php echo $tagp;?></p>

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
    echo BuildDDList( CoreConfig::read('austTable'),'frmcategorias_id',$escala,'',$current_node);
    ?>
    <p class="explanation">
        Selecione a estrutura a qual este privilégio se aplica.
    </p>
</div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?<?php echo $_SERVER['QUERY_STRING'];?>&action=gravar">
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">

<?php if($_GET['action'] == 'criar'){ ?>
    <input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmupdated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmadmin_id" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

    <input type="hidden" name="frmupdated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmadmin_id" value="<?php ifisset( $dados['admin_id'] );?>">

<?php }?>

<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">


<table width="100%" border="0" cellpadding="0" cellspacing="3">
<col width="200">
<col>

    <tr>
        <td valign="top">Nome para este privilégio: </td>
        <td>
            <? if($dados['classe'] == "padrão"){ ?>
                <INPUT TYPE="text" NAME="frmtitulo" value="<?php ifisset($dados['titulo']);?>" disabled="disabled" SIZE="65">
                <p class="explanation">
                    "<strong><?php echo $dados['valor']?></strong>" é um privilégio padrão do sistema, sendo este concedido a todos novos cadastrados. Não é possível alterar seu nome.
                </p>
            <? } else { ?>
                <INPUT TYPE="text" NAME="frmtitulo" value="<?php ifisset($dados['titulo']);?>" SIZE="65">
                <p class="explanation">
                    Digite um nome curto para este privilégio.
                </p>
                <p class="explanation_example">
                    Exemplo: <em>Curso Online 02</em>
                </p>
            <? } ?>
        </td>
    </tr>
    <?php
    /*
    <tr>
        <td valign="top">Opções: </td>
        <td>
            <div class="input_painel">
                <div class="containner">
                    <div class="options">

                    <?php
                    /*
                     * Tipos de restrições
                     *
                    if( !empty($moduloConfig["only_content"]) 
                        AND $moduloConfig["only_content"]["valor"] == 1 )
                    {
                        ?>
                        <p>
                            Após criar este privilégio, você deve configurar
                            cada conteúdo que você deseja restringir.
                        </p>
                        <p class="explanation">
                            Exemplo: o artigo X poderá ser acessado somente
                            por quem tiver o privilégio Y. Esta configuração
                            pode ser feita no formulário de cadastro dos
                            conteúdos que podem ter o acesso restringido.
                        </p>

                        <?php
                    } else { ?>
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
                    <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
     *
     */
    ?>
    <?php
    if( !empty($moduloConfig["has_description"])
        AND $moduloConfig["only_content"]["valor"] == 1 )
    {
        ?>
        <tr>
            <td colspan="2">Descrição:
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <textarea name="frmdescricao" rows="3"><?php echo $dados['descricao'];?></textarea>
                <p class="explanation">
                    Digite uma descrição para este privilégio.
                </p>
                <br /><br />
            </td>
        </tr>
        <?php
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
