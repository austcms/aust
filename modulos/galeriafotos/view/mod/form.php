<?php

/*
 * FORMULÁRIO
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

/*
 * [Se novo conteúdo]
 */
    if($_GET['action'] == 'create'){
        $tagh2 = "Criar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Crie uma nova galeria de fotos a seguir. Primeiro configure as'.
                'informações básicas da galeria e abaixo as fotos.';
        $dados = array('id' => '');
    }
/*
 * [Se modo edição]
 */
    else if($_GET['action'] == 'edit'){

        $tagh2 = "Editar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Edite o conteúdo abaixo.';
        $sql = "
                SELECT
                    id,
                    titulo,
                    titulo_encoded,
                    subtitulo,
                    resumo,
                    texto,
                    link,
                    ordem,
                    bytes,
                    nome,
                    tipo,
                    ref,
                    ref_id,
                    local,
                    classe,
                    especie,
                    adddate,
                    expiredate,
                    visitantes,
                    autor
                FROM
                    ".$modulo->useThisTable()."
                WHERE
                    id='$w'
                ";
        $sql = "
                SELECT
                    *
                FROM
                    ".$modulo->useThisTable()."
                WHERE
                    id='$w'
                ";
        $query = $modulo->connection->query($sql, "ASSOC");
        $dados = $query[0];
    }
?>
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<h3><?php echo $tagh2;?></h3>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<?php if($_GET['action'] == 'create'){ ?>
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

<?php }?>
<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
<table border=0 cellpadding=0 cellspacing=0 class="form">

    <?php
    /*
     * Mostra imagem preview
     */
    if( $dados["bytes"] > 0 ){
        ?>
        <tr>
            <td valign="top" class="first">Imagem atual:</td>
            <td class="second">
                <img src="core/libs/imageviewer/visualiza_foto.php?table=imagens&thumbs=yes&myid=<?php echo $dados["id"]; ?>&maxxsize=450&maxysize=400" />
                <p class="explanation">
                Imagem cadastrada atualmente. Para alterá-la, envie uma nova no formulário abaixo.
                </p>
            </td>
        </tr>
        <?php
    }
    ?>

    <tr>
        <td valign="top" class="first"><label>Título da galeria:</label></td>
        <td class="second">
            <INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
            <p class="explanation">
            Exemplo: Fotos do Segundo Encontro Nacional
            </p>
        </td>
    </tr>

    <?php
    /*
     * RESUMO
     */
    $showResumo = false;
    if( !empty($moduloConfig["resumo"]) ){
        if( $moduloConfig["resumo"]["valor"] == "1" )
            $showResumo = true;
    }
    if( $showResumo ){
    ?>
    <tr>
        <td valign="top"><label>Resumo:</label></td>
        <td>
            <INPUT TYPE='text' NAME='frmresumo' class='text' value='<?php if( !empty($dados['resumo']) ) echo $dados['resumo'];?>' />
            <p class="explanation">

            </p>
        </td>
    </tr>
    <?php
    }
    ?>

    <?php
    /*
     * ORDEM
     */
    $showOrdem = false; // por padrão, não mostra
    if( !empty($moduloConfig["ordenate"]) ){
        if( $moduloConfig["ordenate"]["valor"] == "1" )
            $showOrdem = true;
    }
    if( $showOrdem ){
    ?>
    <tr>
        <td valign="top"><label>Ordem:</label></td>
        <td>
            <select name="frmordem" class="select">
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '10'); ?> value="10">10</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '9'); ?> value="9">9</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '8'); ?> value="8">8</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '7'); ?> value="7">7</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '6'); ?> value="6">6</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '5'); ?> value="5">5</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '4'); ?> value="4">4</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '3'); ?> value="3">3</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '2'); ?> value="2">2</option>
                <option <?php if( !empty($dados['ordem']) ) makeselected($dados['ordem'], '1'); ?> value="1">1</option>
            </select>
            <p class="explanation">
                Selecione um número que representa a importância deste item.
                Quanto maior o número, maior a prioridade.
            </p>
        </td>
    </tr>
    <?php
    }
    ?>
    <?php
    /*
     * DESCRIÇÃO
     */
    $showDesc = true; // por padrão, não mostra
    if( !empty($moduloConfig["descricao"]) ){
        if( $moduloConfig["descricao"]["valor"] == "0" )
            $showDesc = false;
    }
    if( $showDesc ){
        ?>
        <tr>
            <td valign="top"><label>Descrição da galeria: </label>
            </td>
            <td>
                <textarea name="frmtexto" id="jseditor" rows="8" style="width: 400px"><?php if( !empty($dados['texto']) ) echo $dados['texto'];?></textarea>
            <br />
            </td>
        </tr>
        <?php
    }
    ?>

    <tr>
        <td colspan="2">
            <h3>Fotos da Galeria</h3>
        </td>
    </tr>
    <tr>
        <td colspan="2">
                <?php
                if( !empty($_GET["delete"]) AND $_GET["delete"] > 0 ){
                    $sql = "DELETE FROM galeria_fotos_imagens
                            WHERE id='".$_GET["delete"]."'";
                    if( $modulo->connection->exec($sql) ){
                        echo "<div style='color: green;'>";
                        echo "<p>Imagem excluída com sucesso</p>";
                        echo "</div>";
                    }

                }


                if( $_GET["action"] != "create" ){


                    $columns = 4;
                    echo '<table width="99%" style="margin-bottom: 15px;">';


                    $sql = "SELECT id, nome FROM galeria_fotos_imagens
                            WHERE galeria_foto_id='".$w."'";
                    $query = $modulo->connection->query($sql, "ASSOC");
                    $c = 1;
                    foreach($query as $dados){
                        if($c == 1){
                            echo '<tr>';
                        }
                        ?>
                        <td valign="bottom" <?php echo $params['inline']?>>
                            <center>
                                <img src="core/libs/imageviewer/visualiza_foto.php?table=galeria_fotos_imagens&thumbs=yes&myid=<?php echo $dados["id"]; ?>&maxxsize=150&maxysize=200" />
                                <br clear="all" />
                                <a href="javascript: void(0);" style="display: block; margin-top: 5px;" onclick="if( confirm('Você tem certeza que deseja excluir esta imagem?') ) window.open('adm_main.php?section=<?php echo $_GET["section"]; ?>&action=<?php echo $_GET["action"]; ?>&aust_node=<?php echo $_GET["aust_node"]; ?>&w=<?php echo $_GET["w"];?>&delete=<?php echo $dados["id"]; ?>','_top');">Excluir</a>
                            </center>
                        </td>
                        <?php

                        if($c >= $columns){
                            echo '</tr>';
                            $c = 1;
                        } else {
                            $c++;
                        }
                    }

                    // se ficou faltando TDs
                    if($c <= $columns AND $c > 0){
                        for($o = 0; $o < (($columns+1)-$c); $o++){ ?>
                            <td></td>
                            <?php
                        }
                        echo '</tr>';

                    }
                    echo "</table>";
                }
                ?>


            <p>Para inserir novas imagens, selecione-as abaixo.</p>
            <?php
            $loop = 8;
            for($i = 1; $i <= $loop; $i++){

                $pid = $i;
                ?>
                <div style="display: block">
                <label for="<?php echo $pid; ?>">Arquivo:</label>&nbsp;
                <input type="file" id="<?php echo $pid; ?>" name="frmarquivo[<?php echo $pid; ?>]" />
                </div>
                <br />
                <?php
            }
            ?>
            
        </td>
    </tr>



    <tr>
        <td colspan="2" style="padding-top: 10px;"><center><INPUT TYPE="submit" VALUE="Enviar!" name="submit" class="submit"></center></td>
    </tr>
</table>

</form>

<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
