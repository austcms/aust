<?php
/*
 * LISTAR USUÁRIOS CADASTRADOS
 *
 * Este arquivo contém tudo necessário para listagem geral de usuários cadastrados
 */

// configuração: ajusta variáveis
$tabela = $modulo->LeTabelaDeDados($_GET['aust_node']);
$precisa_aprovacao = $modulo->PegaConfig(Array('estrutura'=>$_GET['aust_node'], 'chave'=>'aprovacao'));
?>

<p><a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a></p>
<h2>Listando conteúdo: <?=$aust->leNomeDaEstrutura($_GET['aust_node']);?></h2>
<p>A seguir você vê a lista de registros sob o cadastro "<?=$aust->leNomeDaEstrutura($_GET['aust_node'])?>".</p>

<?
/*
 * Verifica se há a necessidade de aprovação de cadastro e se há alguém necessitando aprovação
 */
if($precisa_aprovacao['valor'] == '1'){
    $sql = "SELECT id FROM ".$tabela." WHERE approved=0 or approved IS NULL";
    $result = $modulo->conexao->query($sql);
    if( count($result) > 0 ){
        echo '<p>Há cadastros para serem aprovados.</p>';
    }
}
?>

<?php

$categorias = $aust->LeCategoriasFilhas('',$_GET['aust_node']);
$categorias[$_GET['aust_node']] = 'Estrutura';
$param = Array(
                'categorias' => $categorias,
                'metodo' => 'listar',
                '' => ''
);
$sql = $modulo->SQLParaListagem($param);
//echo '<br><br>'.$sql .'<br>';
$resultado = $modulo->conexao->query($sql, "ASSOC");
$fields = count($resultado);
//print_r($precisa_aprovacao);

//pr($resultado);


/*
 * FILTROS ESPECIAIS
 */
if( $fields > 0 ){
    $sql = "SELECT valor
            FROM
                cadastros_conf
            WHERE
                tipo='filtros_especiais' AND
                chave='email' AND
                categorias_id='".$_GET["aust_node"]."'
            ";
    $filtroEspecial = $modulo->conexao->query($sql);
    $filtroEspecial = $filtroEspecial[0]["valor"];

    if( !empty($filtroEspecial) ){
        $sql = "SELECT
                    t.".$filtroEspecial."
                FROM
                    ".$tabela." as t
                GROUP BY
                    t.".$filtroEspecial."
                ORDER BY t.id DESC
                ";
        $email = $modulo->conexao->query($sql);
        foreach( $email as $valor ){
            $emails[] = $valor['email'];
        }

        ?>
        Emails: <input type="text" size="25" value="<?php echo implode("; ", $emails) ?>" />
        <br clear="all" />
        <?php

    }
}

/*
 * Começa a listagem
 */

?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>?section=<?=$_GET['section'];?>&action=actions&aust_node=<?=$_GET['aust_node'];?>">
    <a name="list">&nbsp;</a>

    <?php
    /*
     * Mostra resultados somente se existirem no banco de dados
     */
    if( !empty($resultado) ){ ?>

        <? // Painel de controle ?>
        <div class="painel_de_controle">Selecionados:
            <?
            // se este cadastro precisa de aprovação, mostra botão para aprovar usuário
            //if($precisa_aprovacao['valor'] == '1'){ ?>
                <input type="submit" name="aprovar" value="Aprovar" />
            <?// } ?>
            <input type="submit" name="deletar" value="Deletar" />
        </div>


        <table width="680" class="listagem">
            <?
            /*
             * Título dos campos
             */
            ?>
            <tr class="titulo">

                <?php
                    $total_td = 0;
                    $cabecalhos = $resultado[0];
                    foreach($cabecalhos as $campo=>$valor){

                            if( strpos($campo, 'des_') === 0 ){

                            } else {
                                $total_td++;
                                ?>
                                <td bgcolor="#333333" class="<? echo $campo; ?>">
                                    <?php
                                    echo $campo;
                                    ?>
                                </td>
                                <?php
                            }

                    }

                /*
                 * Necessita aprovação?
                 */
                ?>
                <td bgcolor="#333333" width="80" align="center">
                    Opções
                </td>
            </tr>
        <?
        /**
         * LISTAGEM DO CONTÉUDO EM SI
         */
        if(count($resultado) > 0){
            foreach($resultado as $dados){
                /*
                 * Valor dos campos
                 */
                ?>
                <tr class="conteudo">
                    <?php
                    $total_td = 0;
                    foreach($dados as $campo=>$valor) {
                        //$campo = 'teste';
                        if(strpos($campo, 'des_') === 0){
                            //echo $campo;
                        } else {
                            ?>
                            <td>
                                <?php
                                $total_td++;
                                //echo $total_td;
                                if($total_td == 1){
                                    echo '<a href="adm_main.php?section='.$_GET['section'].'&action=editar&aust_node='.$_GET['aust_node'].'&w='.$dados["id"].'">';
                                    echo $dados[$campo];
                                    echo '</a>';
                                    if( ($precisa_aprovacao['valor'] == '1'
                                         AND (
                                                 $dados['des_aprovado'] == 0
                                                 OR empty($dados['des_aprovado'])
                                             )
                                        )
                                        OR $dados['des_aprovado'] == 0)
                                    {
                                        echo '<span style="font-size: 10px;"> (necessita aprovação)</span>';
                                    }
                                    
                                } else {

                                    /**
                                     * Nas duas primeiras colunas, coloca um link
                                     * para edição
                                     */
                                    if( $total_td <= 2 ){
                                        ?>
                                        <a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=editar&aust_node=<?php echo $_GET['aust_node'];?>&w=<?php echo $dados["id"];?>">
                                        <?php
                                    }

                                    echo $dados[$campo];
                                    if( $total_td <= 2 ){
                                        ?>
                                        </a>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                       <?php
                        }
                    }
                    ?>
                    <td align="center">
                        <input type='checkbox' name='itens[]' value='<?=$dados[id];?>'>
                        <!-- <a href="adm_main.php?section=<?=$_GET['section']?>&action=see_info&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/lupa.jpg" alt="Ver Informações" border="0" /></a> -->
                    <!--
                        <a href="adm_main.php?section=<?=$_GET['section']?>&action=edit_form&aust_node=<?=$aust_node;?>&w=<?php echo $dados["id"]; ?>" style="text-decoration: none;"><img src="img/layoutv1/edit.jpg" alt="Editar" border="0" /></a>
                        <?php
                        if($escala == "administrador"
                        OR $escala == "moderador"
                        OR $escala == "webmaster"
                        OR $_SESSION["loginid"] == $dados[autorid]){

                            if((!empty($filter)) AND ($filter <> 'off')){
                                $addurl = "&filter=$filter&filterw=" . urlencode($filterw);
                            }
                            ?>
                            <a href="adm_main.php?section=<?=$_GET['section']?>&action=<?=$action;?>&block=delete&aust_node=<?=$aust_node;?>&w=<?php echo $dados["id"]; ?><?php echo $addurl;?>" style="text-decoration: none;"><img src="img/layoutv1/delete.jpg" alt="Deletar" border="0" /></a>
                            <?php
                        }
                        ?>
                        <?php
                        // Verifica se tipo conteúdo atual está configurado para usar galeria de fotos
                        if(in_array($cat, $aust_conf['where_gallery'])){ ?>
                            <a href="adm_main.php?section=<?=$_GET['section']?>&action=photo_content_manage&w=<?php echo $dados["id"]; ?>#add" style="text-decoration: none;"><img src="img/layoutv1/fotos.jpg" alt="Adicionar fotos a este conteúdo" border="0" /></a>
                        <?php } ?>
                       -->
                    </td>
                </tr>
            <?php
            } // fim do loop
        } else {
            ?>
            <tr>
                <td colspan="<?=$total_td?>"><strong>Não há registros.</strong></td>
            </tr>
            <?php
        }


        ?>
        </table>

    <?php
    } // fim da tabela
    /*
     * Não há resultados, tabela vazia
     */
    else {
        echo "<p>";
        echo "<strong>Não há dados salvos ainda.</strong>";
        echo "</p>";
    }

    ?>

</form>

<p style="margin-top: 15px;">
	<a href="adm_main.php?section=<?=$_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>