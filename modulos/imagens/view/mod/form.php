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
    $aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
    $w = (!empty($_GET['w'])) ? $_GET['w'] : '';

/*
 * [Se novo conteúdo]
 */
    if($_GET['action'] == 'criar'){
        $tagh1 = "Criar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $tagp = 'Crie um novo conteúdo abaixo.';
        $dados = array('id' => '');
    }
/*
 * [Se modo edição]
 */
    else if($_GET['action'] == 'editar'){

        $tagh1 = "Editar: ". $this->aust->leNomeDaEstrutura($_GET['aust_node']);
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
                    ".$modulo->tabela_criar."
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

<h2><?php echo $tagh1;?></h2>
<p><?php echo $tagp;?></p>



<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET["section"] ?>&action=save" enctype="multipart/form-data" >
<input type="hidden" name="metodo" value="<?php echo $_GET['action'];?>">
<?php if($_GET['action'] == 'criar'){ ?>
    <input type="hidden" name="frmadddate" value="<?php echo date("Y-m-d H:i:s"); ?>">
    <input type="hidden" name="frmautor" value="<?php echo $_SESSION['loginid'];?>">
<?php } else { ?>

    <input type="hidden" name="frmadddate" value="<?php ifisset( $dados['adddate'] );?>">
    <input type="hidden" name="frmautor" value="<?php ifisset( $dados['autor'] );?>">

<?php }?>
<input type="hidden" name="w" value="<?php ifisset( $dados['id'] );?>">
<input type="hidden" name="aust_node" value="<?php echo $austNode; ?>">
<table border=0 cellpadding=0 cellspacing=0 class="form">
    <col width="200">
    <col width="470">


    <?php
    /*
     * EXPIRETIME
     *
     * Se expireTime está ativo, mostra que o tempo está expirado
     */
    $showExpireTime = false;
    if( !empty($moduloConfig["expireTime"]) ){
        if( $moduloConfig["expireTime"]["valor"] == "1" )
            $showExpireTime = true;
    }
    if( $showExpireTime ){

        if( !empty($dados["expiredate"]) ){
            $date = date('Y-m-d', strtotime($dados["expiredate"]));
        } else {
            $date = "";
        }

        if( !empty($date) ){
            $today = date("Y-m-d");
            $tDate = strtotime($date);
            $tToday = strtotime($today);

            $expired = false;
            if( ($tToday-$tDate) > 0 ){
                $expired = true;
            }
        }
        if( $expired ){
            ?>
            <tr>
                <td colspan="2">
                    <div class="insucesso">
                    Este conteúdo já expirou e não está mais sendo mostrado.
                    </div>
                    <br />
                </td>
            </tr>
            <?php
        }
    }
    ?>

    <?php
    /*
     * Mostra imagem preview
     */
    if( $dados["bytes"] > 0 ){
        ?>
        <tr>
            <td valign="top">Imagem atual:</td>
            <td>
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
        <td valign="top"><label>Arquivo:</label></td>
        <td>
            <input type="file" name="frmarquivo" />
            <p class="explanation">
            Selecione a imagem que será carregada.
            </p>
        </td>
    </tr>
    <tr>
        <td valign="top"><label>Título:</label></td>
        <td>
            <INPUT TYPE='text' NAME='frmtitulo' class='text' value='<?php if( !empty($dados['titulo']) ) echo $dados['titulo'];?>' />
            <p class="explanation">
            Um título. Sua utilidade básica é você identificar este item na listagem.
            </p>
        </td>
    </tr>

        <?php
    /*
     * DESCRIÇÃO
     */
    $showLink = true; // por padrão, mostra
    if( !empty($moduloConfig["link"]) ){
        if( $moduloConfig["link"]["valor"] == "0" )
            $showLink = false;
    }
    if( $showLink ){
        ?>
        <tr>
            <td valign="top"><label>Link:</label></td>
            <td>
                <INPUT TYPE='text' NAME='frmlink' class='text' value='<?php if( !empty($dados['link']) ) echo $dados['link'];?>' />
                <p class="explanation">
                Se você deseja que, ao clicar na imagem, o usuário seja levado a
                um endereço específico, digite-o acima. (ex.: http://www.meusite.com.br/noticia/27)
                </p>
            </td>
        </tr>
        <?php
    }
    ?>

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

    <?php
    /*
     * EXPIRETIME
     *
     * Quando este conteúdo deve parar de aparecer
     */
    $showExpireTime = false;
    if( !empty($moduloConfig["expireTime"]) ){
        if( $moduloConfig["expireTime"]["valor"] == "1" )
            $showExpireTime = true;
    }
    if( $showExpireTime ){

        if( !empty($dados["expiredate"]) ){
            $day = date('d', strtotime($dados["expiredate"]));
            $month = date('m', strtotime($dados["expiredate"]));
            $year = date('Y', strtotime($dados["expiredate"]));
        } else {
            $day = "";
            $month = "";
            $year = "";
        }
    ?>
    <tr>
        <td valign="top"><label>Expirar em:</label></td>
        <td>
            <input type='text' name='grouped_data[expiredate][day]' value='<?php echo $day; ?>' size="1" maxlength="2" />
            <input type='text' name='grouped_data[expiredate][month]' value='<?php echo $month; ?>' size="1" maxlength="2" />
            <input type='text' name='grouped_data[expiredate][year]' value='<?php echo $year; ?>' size="3" maxlength="4" />
            <input type='hidden' name='grouped_data[expiredate][_options][divisor]' value='-' />
            <input type='hidden' name='grouped_data[expiredate][_options][type]' value='date' />
            <p class="explanation">
            <strong>Formato dd/mm/aaaa (dia/mes/ano)</strong>
            <br />Indique qual o último dia que este conteúdo deve ser mostrado.
            <br />Deixe em branco para não expirar.
            </p>
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

<br />
<p>
    <a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
