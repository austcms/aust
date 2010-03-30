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


if($_GET['action'] == 'edit'){
    $h1 = 'Edite informações do arquivo';
    $sql = "
            SELECT
                *
            FROM
                arquivos
            WHERE
                id='".$_GET['w']."'
            ";
    $mysql = $modulo->connection->query($sql);
    $dados = $mysql[0];
    $fm = "edit";
} else {
    $h1 = 'Novo arquivo';
    $fm = "create";
}

/*
 * Tamanho máximo do Upload.
 */
$maxSize = (int) str_replace('M','', ini_get(upload_max_filesize) );
if( (int) str_replace('M','', ini_get('post_max_size') ) < $maxSize )
    $maxSize = (int) str_replace('M','', ini_get('post_max_size') );

?>

<h2><?=$h1?></h2>
<p>
    <a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<p>Envie um arquivo para o site.</p>

<form method="post" action="adm_main.php?section=<?=$_GET['section'];?>&action=save&aust_node=<?=$_GET['aust_node']?>" enctype="multipart/form-data">
    <input type="hidden" name="method" value="<?php echo $_GET['action'];?>">

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
                if($fm == "edit"){
                    $current_node = $dados['categoria_id'];
                }
                echo BuildDDList( Registry::read('austTable'), 'frmcategoria_id', $escala, $aust_node, $current_node );
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
        if($fm == "edit"){
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
            <?php if($fm == "edit"){ ?>
                Arquivo:
            <?php }else{ ?>
                Selecione o arquivo:
            <?php } ?>
        </td>
        <td class="second">
            <?php if($fm == "edit"){ ?>
                <span style="font-weight: bold;">
                    <?php echo $dados['arquivo_nome'] ?>
                </span>
            <?php }else{ ?>
                <INPUT TYPE="file" NAME="arquivo" class="text">
                <p class="explanation">
                    Localize o arquivo que você deseja realizar upload.
                    O tamanho máximo aceito neste servidor é <?php echo $maxSize; ?>Mb.
                </p>
            <?php } ?>
        </td>
    </tr>
    <?php
    /**
     * PATH DO ARQUIVO PARA LINK?
     */
    if($fm == "edit"){
        $showshow_path_to_link = false; // por padrão, não mostra
        if( !empty($moduloConfig["show_path_to_link"]) ){
            if( $moduloConfig["show_path_to_link"]["valor"] == "1" )
                $showshow_path_to_link = true;
        }
        if( $showshow_path_to_link ){

            $url = '';

            
            if( !empty($dados['url']) ){
                if( strtolower( substr($_SERVER["SERVER_PROTOCOL"], 0, 4) ) == 'http' ){
                    $url = 'http://';
                }
                $url = $modulo->parseUrl( $url.$_SERVER["SERVER_NAME"].$dados['url'] );

            }
            ?>
            <tr>
                <td valign="top" class="first">Endereço do arquivo: </td>
                <td class="second">
                    <?php echo $url;?>
                    <p class="explanation">
                        Copie, se desejar, para criar links para o arquivo.
                    </p>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <td valign="top" class="first">Título: </td>
        <td class="second">
            <input type="text" name="frmtitulo" value="<?php ifisset($dados['titulo']);?>" class="text" />
            <?php tt('Digite um título. Lembre-se, títulos começam com letra maiúscula e não leva
                ponto final.'); ?>
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
        <td colspan="2"><center><input type="submit" value="Enviar" class="submit"></center></td>
    </tr>
    </table>

</form>

<br />
<p>
    Os arquivos enviados poderão ter o tamanho limite de
    <strong><?php echo $maxSize ?>Mb</strong> neste servidor.
</p>


<p>
    <a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

