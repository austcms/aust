<?php
/*
 * LISTAR USUÁRIOS CADASTRADOS
 *
 * Este arquivo contém tudo necessário para listagem geral de usuários cadastrados
 */

// configuração: ajusta variáveis
$tabela = $modulo->LeTabelaDeDados($_GET['aust_node']);
$precisa_aprovacao = $modulo->pegaConfig(Array('estrutura'=>$_GET['aust_node'], 'chave'=>'aprovacao'));
?>

<p><a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a></p>
<h3>Listando conteúdo: <?php echo $aust->leNomeDaEstrutura($_GET['aust_node']);?></h3>
<p>A seguir você vê a lista de registros sob o cadastro "<?php echo $aust->leNomeDaEstrutura($_GET['aust_node'])?>".</p>


<?php

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
    $filtroEspecial = $modulo->connection->query($sql);
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
        $email = $modulo->connection->query($sql);
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
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?section=<?php echo $_GET['section'];?>&action=actions&aust_node=<?php echo $_GET['aust_node'];?>">

    <?php
    if( !empty($resultado) ){ ?>
        <?php
        /*
         * SEARCH
         *
         * Mostra resultados somente se existirem no banco de dados
         */
        if( $modulo->getStructureConfig("has_search") ){
            ?>
            <div class="content_search">
                <strong>Palavras-chave:</strong> <input type="text" id="search_query_input" onkeyup="cadastroSearch(this, <?php echo $this->austNode;?>);" /><button  onclick="cadastroSearch($('.content_search #search_query_input'), <?php echo $this->austNode;?>); return false;">Pesquisar</button>
                <script type="text/javascript">
                $('.content_search #search_query_input').bind('keypress', function(e) {
                    if(e.keyCode==13){
                        return false;
                    }
                });
                </script>
            </div>
            <?php
        }
        /*
         *
         * PAINEL DE CONTROLE
         *
         */
        ?>
        <?php
        if( $permissoes->canEdit($austNode) ){
            ?>
            <div class="painel_de_controle">Selecionados:
            <?php
            /*
             * Se este cadastro precisa de aprovação, mostra botão para aprovar usuário
             */
            if($precisa_aprovacao['valor'] == '1'){ ?>
                <input type="submit" name="aprovar" value="Aprovar" />
                <?php
            }

            /*
             * Pode excluir?
             */
            if( $permissoes->canDelete($austNode) ){
                ?>
                <input type="submit" name="deletar" value="Deletar" />
                <?php
            }
            ?>
            </div>
            <?php
        } // fim de canEdit()

        ?>

        <div id="listing_table">
        <?php
        include($modulo->getIncludeFolder().'/view/mod/listing_table.php');
        ?>
        </div>
                
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
	<a href="adm_main.php?section=<?php echo $_GET['section']?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>