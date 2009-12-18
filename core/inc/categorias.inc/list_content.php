<?php
/**
 * Arquivo que lista categorias
 *
 * @todo - Deve ser melhorado
 *
 * @package Interface
 * @name list_content
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since 01/01/2009
 */

$w = (!empty($_GET['w'])) ? $_GET['w'] : 'NULL';
?>

<h1>Listando Categorias</h1>
<p>
    A seguir você pode ver a estrutura de categorias do site. O <strong>conceito é simples</strong>, lembre-se de uma Árvore
    Genealógica, onde os filhos ficam abaixo dos pais em hierarquia.
</p>
<p>
    <strong>Passe o mouse</strong> sobre uma categoria para editá-la.
</p>



<?php
if(!empty($_GET['block']) AND $_GET['block'] == "delete"){
    if(empty($_GET['confirm'])){
    ?>
        <div style="width: 680px; display: table;">
            <div style="background: yellow; padding: 15px; text-align: center;">
                <p style="color: black; margin: 0px;">
                    <strong>
                    Tem certeza que deseja apagar o item selecionado?
                    </strong>
                    <br />
                    <a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=<?php echo $_GET['action'];?>&block=delete&w=<?php echo $w;?>&confirm=delete<?php echo $addurl;?>">Sim</a> -
                    <a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=<?php echo $_GET['action'];?><?php echo $addurl;?>">Não</a>
                </p>
            </div>
        </div>
    <?php
    } else if($_GET['confirm'] == "delete"){
        $sql = "DELETE FROM ".$_GET['section']."
                WHERE
                    id='$w'
                ";
        if ($conexao->exec($sql)){
        ?>
            <div style="width: 680px; display: table;">
                <div style="background: black; padding: 15px; text-align: center;">
                    <p style="color: white; margin: 0px;">
                        <strong>
                        O conteúdo foi apagado definitivamente!
                        </strong>
                    </p>
                </div>
            </div>
        <?php

        } else {
            echo '<p style="color: red;">Ocorreu um erro desconhecido ao editar as informações do usuário, tente novamente.</p>';
        }
    }
}
?>

<div class="highlights_painel">
    <div class="containner">
    <?php
        $usertipo = $administrador->LeRegistro('tipo');
        /*
         * ORGANOGRAMA
         * Monta organograma das categorias
         */
        function BuildCategoriasStructure($table, $parent=0, $level=0){
            global $usertipo; // torna local esta variável
            global $conexao;
            $sql = "
                    SELECT
                        cat.id, cat.subordinadoid, cat.nome, cat.autor, cat.tipo, cat.tipo_legivel,
                        ( SELECT COUNT(*)
                        FROM
                            $table AS clp
                        WHERE
                            clp.subordinadoid=cat.id
                        ) AS num_sub_nodes
                    FROM
                        $table AS cat
                    WHERE
                        cat.subordinadoid = '$parent'
                ";
            //echo $sql;
            $query = $conexao->query($sql);
            /**
             * CATEGORIAS
             * Mostra as categoria indentadas
             */

            //pr($query);
            foreach($query as $dados){

                ?>
                <div class="structure structure<?php echo $level;?>" style="margin-left: <?php echo $level*30;?>px">
                    <span onmouseover="javascript: est_options('<?php echo $dados['id']?>')"><?php echo $dados['nome']?></span>

                    <?php
                    if($level <= 1 AND !empty($dados['tipo_legivel'])){
                        echo '<span style="text-transform: lowercase; color: #999999" class="tipo_legivel">('.$dados['tipo_legivel'].')</span>';
                    }
                    echo '<div class="est_options" style="color: #333333; text-transform: none; font-weight: normal;" id="est_options_'.$dados['id'].'">';
                    echo '<a href="adm_main.php?section='.$_GET['section'].'&action=edit_form&w='.$dados['id'].'" style="color: orange;">Editar descrição</a>';
                    if(($level <= 1 AND strtolower($usertipo) == strtolower('webmaster')) OR ($level > 1)){
                        echo ' - <a href="adm_main.php?section='.$table.'&action=list_content&block=delete&w='.$dados['id'].'" class="delete"><img src="img/layoutv1/delete.jpg" height="10" border="0" alt="Deletar" /></a>';
                    }
                    echo '</div>';

                echo '</div>';

                if($dados['num_sub_nodes'] > 0){
                    BuildCategoriasStructure($table, $dados['id'], $level+1);
                }
            }
        }
        BuildCategoriasStructure($_GET['section']);

    ?>
    </div>
</div>
<p style="margin-top: 15px;">
    <a href="adm_main.php?section=<?php echo $_GET['section'];?>"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>