<table border="0" width="100%">
<col width="350" />
<col />
<col />
<?php


//pr($permissoes);

/**
 * Seleciona cada o site para mostrar suas respectivas estruturas
 */
$sql = "SELECT
            id,nome
        FROM
            categorias
        WHERE
            subordinadoid='0'
        ";

$siteQuery = Connection::getInstance()->query($sql);

/**
 * Loop por cada site cadastrado, mostrando suas estruturas
 */
foreach($siteQuery as $chave=>$valor){
?>

    <tr>
        <td colspan="1">
            <h2><?php echo $valor['nome'];?></h2>
        </td>
        <td>
        </td>
    </tr>

    <?php
    /**
     * Seleciona somente estruturas de um determinado site
     */
    $sql = "
            SELECT
                lp.id, lp.subordinadoid, lp.nome, lp.tipo as tipo,
                ( SELECT COUNT(*)
                FROM
                $aust_table As clp
                WHERE
                clp.subordinadoid=lp.id
                ) As num_sub_nodes
            FROM
                $aust_table AS lp
            WHERE
                lp.subordinadoid = '".$valor['id']."' AND
                lp.classe = 'estrutura'
            ORDER BY
                lp.tipo DESC,
                lp.nome ASC
    ";

    $query = Connection::getInstance()->query($sql);
    foreach($query as $estruturas){

        $cur_tipo = (empty($cur_tipo)) ? '' : $cur_tipo;


        if($cur_tipo <> $estruturas['tipo']){
            ?>
        <tr height="1">
            <td colspan="2" bgcolor="silver"></td>
        </tr>
            <tr>
                <td colspan="2" valign="top"><strong>
                <?php
                if(is_file(MODULES_DIR.$estruturas['tipo'].'/'.MOD_CONFIG)){
                    unset($modInfo);
                    include(MODULES_DIR.$estruturas['tipo'].'/'.MOD_CONFIG);

                    echo ''.$modInfo['nome'].'';
                } else {
                    echo ''.$estruturas['tipo'].'';
                }
                ?>
                </strong></td>
            </tr>
        <?php }
        $cur_tipo = $estruturas['tipo'];

        /**
         * Verifica permissões
         */
        if(empty($categoriasPermitidas) or in_array($estruturas['id'], $categoriasPermitidas)){
            ?>
            <tr>
                <?php
                /**
                 * Escreve o nome da estrutura
                 */
                if($estruturas['num_sub_nodes'] > 0 or ($modInfo['somenteestrutura'] == TRUE)){ ?>
                    <td valign="top">
                        <?php echo $estruturas['nome']; ?>
                    </td>
                    <td>
                        <ul class="opcoes">

                        <?php
                        /**
                         * Links para opções de gerenciamento de cada estrutura
                         */
                        $opt = (is_array($modInfo['opcoes'])) ? $modInfo['opcoes'] : Array();
                        foreach ($opt as $chave=>$valor) {
                            echo '<li><a href="adm_main.php?section='.$_GET['section'].'&action='.$chave.'&aust_node='.$estruturas['id'].'">'.$valor.'</a></li>';
                        }
                        ?>
                        </ul>

                    </td>
                <?php } else { ?>
                    <td valign="top">
                        <span style="color: silver"><?php echo $estruturas['nome'];?></span>
                    </td>
                    <td>
                        <span style="color: silver">É necessário cadastrar categorias.</span>
                    </td>
                <?php } ?>
            </tr>
            <?php
        }
    }
    ?>

<?php
}
?>
</table><br />