<?php
/*
 * FORM
 *
 * Formulário de inclusão de conteúdo
 */

// $fm = form_method = gravar, editar, etc, pois é o mesmo formulário para fins diferentes
$aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';

/*
 * Carrega configurações automáticas do DB
 */
    $params = array(
        "aust_node" => $_GET["aust_node"],
    );
    $moduloConfig = $modulo->loadModConf($params);


if($_GET['action'] == 'editar'){
    $h1 = 'Edite informações do arquivo';
    $sql = "
            SELECT
                *
            FROM
                arquivos
            WHERE
                id='".$_GET['w']."'
            ";
    $mysql = $modulo->conexao->query($sql);
    $dados = $mysql[0];
    $fm = "editar";
} else {
    $h1 = 'Novo arquivo';
    $fm = "criar";
}

?>

<h2><?=$h1?></h2>
<p>
    <a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<p>Envie um arquivo para o site. Os usuários poderão baixar este arquivo.</p>

<form method="post" action="adm_main.php?section=<?=$_GET['section'];?>&action=gravar&aust_node=<?=$_GET['aust_node']?>" enctype="multipart/form-data">
    <input type="hidden" name="metodo" value="<?=$fm;?>">

    <input type="hidden" name="w" value="<?php ifisset($_GET['w']);?>">
    <input type="hidden" name="aust_node" value="<?php echo $austNode;?>">
    <input type="hidden" name="frmcreated_on" value="<?php echo date("Y-m-d H:i:s"); ?>">

    <input type="hidden" name="frmurl" value="<?php ifisset($dados['url']); ?>">
    <input type="hidden" name="frmarquivo_nome" value="<?php ifisset($dados['arquivo_nome']);?>">
    <input type="hidden" name="frmarquivo_tipo" value="<?php ifisset($dados['arquivo_tipo']);?>">
    <input type="hidden" name="frmarquivo_tamanho" value="<?php ifisset($dados['arquivo_tamanho']);?>">
    <input type="hidden" name="frmadmin_id" value="<?php ifisset($dados['autor'], $administrador->LeRegistro('id'));?>">
    
    <table border=0 cellpadding=0 cellspacing=0 class="form">
    <?php
    /*
     * CATEGORIA
     */
    $showCategoria = true; // por padrão, não mostra
    if( !empty($moduloConfig["semcategoria"]) ){
        if( $moduloConfig["semcategoria"]["valor"] == "1" )
            $showCategoria = false;
    }
    if( $showCategoria ){
        ?>
        <tr>
            <td valign="top">Selecione a categoria: </td>
            <td>
                <div id="categoriacontainer">
                <?php
                if($fm == "editar"){
                    $current_node = $dados['categoria_id'];
                }
                echo BuildDDList( CoreConfig::read('austTable'), 'frmcategoria_id', $escala, $aust_node, $current_node );
                ?>
                </div>
                <?php
                /*
                 * Nova_Categoria?
                 */
                $showNovaCategoria = false;
                if( !empty($moduloConfig["nova_categoria"]) ){
                    if( $moduloConfig["nova_categoria"]["valor"] == "1" )
                        $showNovaCategoria = true;
                }
                if( $showNovaCategoria ){
                    $param = array(
                        'austNode' => $austNode,
                        'categoryInput' => 'frmcategoria_id',
                    );
                    lbCategoria( $param );
                }
                ?>

            </td>
        </tr>
    <?php
    } else {
        if($fm == "editar"){
            $current_node = $dados['categoria_id'];
        } else {
            $current_node = $aust_node;
        }
        ?>
        <input type="hidden" name="frmcategoria_id" value="<?php echo $current_node; ?>">
        <?php
    }
    ?>

    <tr>
        <td valign="top" class="first">
            <?php if($fm == "editar"){ ?>
                Arquivo:
            <?php }else{ ?>
                Selecione o arquivo:
            <?php } ?>
        </td>
        <td class="second">
            <?php if($fm == "editar"){ ?>
                <p style="font-weight: bold;">
                    <?php echo $dados['arquivo_nome'] ?>
                </p>
            <?php }else{ ?>
                <INPUT TYPE="file" NAME="arquivo">
                <p class="explanation">
                    Localize o arquivo que você deseja realizar upload.
                    O tamanho máximo aceito neste servidor é <?php echo ini_get(upload_max_filesize)?>b.
                </p>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td valign="top" class="first">Título: </td>
        <td class="second">
            <INPUT TYPE="text" NAME="frmtitulo" value="<?php ifisset($dados[titulo]);?>" SIZE="65">
            <p class="explanation">
                Digite um título. (Título começa com letra maiúscula e não leva
                ponto final)
            </p>
                <p class="explanation_example">
                    Exemplo: <em>Arquivo de exercícios segunda prova</em>
                </p>
            <p class="explanation" id="exists_titulo">
            </p>
        </td>
    </tr>
    <?php
    /*
     * DESCRICAO
     */
    $showDescricao = false; // por padrão, não mostra
    if( !empty($moduloConfig["descricao"]) ){
        if( $moduloConfig["descricao"]["valor"] == "1" )
            $showDescricao = true;
    }
    if( $showDescricao ){
        ?>
        <tr>
            <td valign="top">Descrição: </td>
            <td>
                <textarea name="frmdescricao" id="jseditor" rows="8" cols="45" style="font-size: 11px; font-family: verdana;"><?php ifisset( str_replace("\n","<br>",$dados['descricao']) ); // Para TinyMCE ?></textarea>
                <p class="explanation">
                    Digite uma descrição para este arquivo.
                </p>
            </td>
        </tr>
        <?php
    }
    ?>

    <?php
    /*
     * EMBED
     * mostra <input> de módulos embed
     *
     * Embed significa que os <input>s aqui mostrados serão enviados juntamente
     * com o <form> principal
     *
     * O arquivo inserido é /embed/form.php do módulo que $embed==true
     */

        include(INC_DIR.'conteudo.inc/form_embed.php');

    ?>


    <tr>
    <td colspan="2" style="padding-top: 30px;"><center><INPUT TYPE="submit" VALUE="Entrar"></center></td>
    </tr>
    </table>

</form>

<br />
<p>
    Configurações deste servidor:<br>
    <strong>upload_max_filesize</strong> => <?=ini_get(upload_max_filesize)?> -
    <strong>post_max_size</strong> => <?=ini_get('post_max_size');?>
</p>


<p>
    <a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

