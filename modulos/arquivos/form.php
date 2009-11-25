<?php
/*
 * FORM
 *
 * Formulário de inclusão de conteúdo
 */

// $fm = form_method = gravar, editar, etc, pois é o mesmo formulário para fins diferentes
$aust_node = (!empty($_GET['aust_node'])) ? $_GET['aust_node'] : '';
$h1 = 'Novo arquivo';
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
    $mysql = mysql_query($sql);
    $dados = mysql_fetch_array($mysql);
    $fm = "editar";
}
if(empty($fm))
    $fm = "criar";

?>

<h1><?=$h1?></h1>
<p>
    <a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>

<p>Envie um arquivo para o site. Os usuários poderão baixar este arquivo.</p>

<form method="post" action="adm_main.php?section=<?=$_GET['section'];?>&action=gravar&aust_node=<?=$_GET['aust_node']?>" enctype="multipart/form-data">
    <input type="hidden" name="metodo" value="<?=$fm;?>">
    <input type="hidden" name="w" value="<?php ifisset($dados[id]);?>">

    <input type="hidden" name="frmlocal" value="<?ifisset($dados['local'])?>">
    <input type="hidden" name="frmurl" value="<?php ifisset($dados['url']); ?>">
    <input type="hidden" name="frmarquivo_nome" value="<?php ifisset($dados['arquivo_nome']);?>">
    <input type="hidden" name="frmarquivo_tipo" value="<?php ifisset($dados['arquivo_tipo']);?>">
    <input type="hidden" name="frmarquivo_tamanho" value="<?php ifisset($dados['arquivo_tamanho']);?>">
    <input type="hidden" name="frmtipo" value="<?php ifisset($dados['tipo']);?>">
    <input type="hidden" name="frmreferencia" value="<?php ifisset($dados['referencia']); ?>">
    <input type="hidden" name="frmadddate" value="<?php ifisset($dados['adddate'], $config->DataParaMySQL('datetime'));?>">
    <input type="hidden" name="frmautor" value="<?php ifisset($dados['autor'],$administrador->LeRegistro('id'));?>">
    <table width="670" border=0 cellpadding=0 cellspacing=0>
    <col width="200">
    <col>
    <tr>
        <td valign="top">Selecione a categoria: </td>
        <td>
            <div id="categoriacontainer">
            <?php
            if($fm == "editar"){
                $current_node = $dados['categorias_id'];
            }
            echo BuildDDList($aust_table,'frmcategorias_id',$escala,$aust_node,$current_node);
            ?>
            </div>

        </td>
    </tr>
    <tr>
        <td valign="top">
            <?php if($fm == "editar"){ ?>
                Arquivo:
            <?php }else{ ?>
                Selecione o arquivo:
            <?php } ?>
        </td>
        <td>
            <?php if($fm == "editar"){ ?>
                <p style="font-weight: bold;">
                    <?php echo $dados['url'].$dados['arquivo_nome'] ?>
                </p>
            <?php }else{ ?>
                <INPUT TYPE="file" NAME="arquivo">
                <p class="explanation">
                    Localize o arquivo que você deseja realizar upload.
                    O tamanho máximo aceito neste servidor é <?=ini_get(upload_max_filesize)?>b.
                </p>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td valign="top">Título: </td>
        <td>
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
    <tr>
        <td valign="top">Resumo: </td>
        <td>
            <INPUT TYPE="text" NAME="frmresumo" value="<?php ifisset($dados['resumo']);?>" SIZE="65">
            <p class="explanation">
                Digite um resumo.
            </p>
        </td>
    </tr>
    <?
    /*******************************
    *
    *	PRIVILÉGIOS DESTE CONTEÚDO
    *
    *******************************/
    /*
    ?>
    <tr>
        <td valign="top">Privilégio necessário para ver este conteúdo?</td>
        <td>
            <select name="frmprivilegio">
                <option <? makeselected($dados['privilegio'], ''); ?> value="">Livre para todos visitantes</option>
                <?
                $sql = "SELECT id,nome FROM tipos_de_privilegios";
                $mysql = mysql_query($sql);
                while($privs = mysql_fetch_array($mysql)){
                    echo '<option value="'.$privs[id].'" ';
                    echo makeselected($dados['privilegio'], $privs[id]);
                    echo '>'.$privs[nome].'</option>';
                }
                ?>
            </select>

            <p class="explanation">
                Selecione acima que tipo de privilégio os usuários deverão ter para acessar este conteúdo.
            </p>
        </td>
    </tr>
     *
     */
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

