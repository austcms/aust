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
    $austNode = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
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
    $query = $modulo->connection->query($sql);
    $dados = $query[0];
}
?>
<p>
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h2><?php echo $tagh1;?></h2>
<p><?php echo $tagp;?></p>

<?php
/*
 *
 * CATEGORIAS (hidden pode default)
 *
 * Div contendo <select>. Está no início escondido para que possa ser mostrado posteriormente.
 */
?>
<div id="categoriaselect" style="visibility: hidden; height: 1px; font-size: 0px;">
    <?php
    // @todo = opção de criar privilégio mas não adicionar e nenhuma estrutura.
    // Opção via <input radio>. Se selecionar que sim, mostra via JS BuildDDList

    $current_node = false;
    if($_GET['action'] == "editar"){

        /*
         * Verifica quais categorias este módulo está associado
         */
        $categorias = $modulo->getRelatedCategories($austNode);

        if( !empty($categorias) ){

            /*
             * Descobre qual a categoria o privilégio atual está relacionado
             */
            $sql = "
                    SELECT
                        target_id
                    FROM
                        privilegio_target
                    WHERE
                        privilegio_id='$w'
                    ";
            $query = $modulo->connection->query($sql);
            $node = $query[0];
            if( is_array($node) )
                $current_node = reset($node);

            /*
             * Pega categorias para criar select
             */
            $sql = "
                    SELECT
                        id, nome
                    FROM
                        ".Registry::read('austTable')."
                    WHERE
                        id IN ('".implode("','", $categorias)."')
                    ";
            $query = $modulo->connection->query($sql);

            /*
             * Cria <select>
             *
             * @todo - deve ser melhorado para múltiplos sites. Atualmente,
             * só mostra listagem plana, sem diferenciação de sites.
             */
            if( !empty($query) ){
                ?>
                <select name="categoria_id">
                <?php
                foreach( $query as $valor ){
                    ?>
                    <option value="<?php echo $valor["id"]?>" <?php if($current_node == $valor["id"]) echo 'selected="selected"'; ?>><?php echo $valor["nome"]?></option>
                    <?php
                }
                ?>
                </select>
                <p class="explanation">
                    Selecione a estrutura a qual este privilégio se aplica.
                </p>
                <?php
            }

        } // fim getRelatedCategories


    }

    // escreve <select>
    //echo BuildDDList( Registry::read('austTable'),'categoria_id',$escala,'',$current_node);
    ?>
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
<input type="hidden" name="frmcategoria_id" value="<?php echo $austNode; ?>">


<table border="0" cellpadding="0" cellspacing="3" class="form">
    <tr>
        <td valign="top" class="first">Nome para este privilégio: </td>
        <td class="second">
            <?php if($dados['classe'] == "padrão"){ ?>
                <INPUT TYPE="text" NAME="frmtitulo" value="<?php ifisset($dados['titulo']);?>" disabled="disabled" SIZE="65">
                <p class="explanation">
                    "<strong><?php echo $dados['valor']?></strong>"
                    é um privilégio padrão do sistema, sendo este concedido a
                    todos novos cadastrados. Não é possível alterar seu nome.
                </p>
            <?php } else { ?>
                <INPUT TYPE="text" NAME="frmtitulo" value="<?php ifisset($dados['titulo']);?>" SIZE="65">
                <p class="explanation">
                    Digite um nome curto para este privilégio.
                </p>
                <p class="explanation_example">
                    Exemplo: <em>Curso Online 02</em>
                </p>
            <?php } ?>
        </td>
    </tr>
    
    <tr>
        <td valign="top">Opções: </td>
        <td>
            <div class="input_painel">
                <div class="containner">
                    <div class="options">

                    <?php
                    /*
                     * Tipos de restrições
                     */
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
                    } else {

                        ?>
                        <p>
                            <strong>Selecione uma opção para este privilégio:</strong><br />
                            <input type="radio" name="privilegio_tipo" value="especifico" <?php if($dados['type']=='content' or empty($dados['type']) ) echo 'checked="checked"'; ?> onclick="javascript: form_privilegio(0);" /> Relacionar cada conteúdo a este privilégio <br/>
                            <input type="radio" name="privilegio_tipo" value="categoria" <?php if($dados['type']=='structure' ) echo 'checked="checked"'; ?> onclick="javascript: form_privilegio(1);" /> Este privilégio bloqueia uma categoria inteira
                        </p>
                    </div>
                    <div id="categoriacontainer_priv" style="border-top: 1px dashed silver; padding-top: 20px;">
                    </div>
                        <script type="text/javascript">
                        <?php
                        if( $dados['type']=='structure' ){
                            ?>
                                var privilegio_escolhido = true;
                        <? } else { ?>
                                var privilegio_escolhido = false;
                        <? } ?>
                        </script>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </td>
    </tr>
    <?php
    if( !empty($moduloConfig["has_description"])
        AND $moduloConfig["has_description"]["valor"] == 1
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
