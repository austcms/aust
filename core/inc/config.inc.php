<?php

/*
 * Status?
 */
if( !empty($_GET['status']) ){
    unset($status);
    $st = $_GET['status'];
    if( $st == '1' ){
        $status['classe'] = 'sucesso';
        $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
    }
}


/*
 * Salva configuração
 */
if($_POST['gravar']){
    unset($_POST['gravar']);
    foreach($_POST['data'] as $key=>$valor){
        $params = array(
            'id' => $key,
            'valor' => $valor,
        );

        $msg = $config->updateOptions($params);
        unset($params);
    }
    header("Location: ".$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&status=1');
    $status = $msg;
}

/*
 * NOVA CONFIGURAÇÃO
 */
if($_POST['novaconfig']){
    unset($_POST['novaconfig']);
    $params = array(
        'propriedade' => $_POST['propriedade'],
        'tipo' => $_POST['tipo'],
        'valor' => $_POST['valor'],
        'nome' => $_POST['nome'],
    );

    $config->ajustaOpcoes($params);
    // Grava configuração no DB
    $status = $config->GravaConfig();
}

?>

<?php
if(!empty($_POST['inserirmodulo'])){
	$status = $aust->gravaEstrutura(
                                    array(
                                        'nome' => $_POST['nome'],
                                        'categoriaChefe' => $_POST['categoria_chefe'],
                                        'estrutura' => 'estrutura',
                                        'moduloPasta' => $_POST['modulo'],
                                        'autor' => $administrador->LeRegistro('id')
                                    )
                                );
}

/*
 * CONFIGURAÇÕES
 *
 * Carrega todas as configurações existentes
 */
$options = $config->getConfigs(
        array(
            //'type' => 'global'
        )
    );

/*
 * STATUS
 */
    if( !empty($status) )
        EscreveBoxMensagem($status);
?>

<h2>Configurações</h2>
<p>
    Nesta tela estão as principais configurações do sistema.
</p>


<div class="painel">
    <?php
    /*
     * NOME DAS TABS - GERAL E SISTEMA
     */
    ?>

    <div class="tabs_area">

        <!-- TABS -->
        <ul class="tabs">

            <?php
            /*
             * TIPOS DE CONFIGURAÇÕES
             */
            foreach($options as $type=>$conf){
                if( $config->hasPermission($type) ){
                    ?>
                    <li><a href="#"><?php echo $type ?></a></li>
                    <?php
                }
            }

            ?>
        </ul>

    </div>
    
    
    <div class="panes">

        <?php /* TR Header igual para todas as panes */ ?>
            <table border="0" class="pane_listing">
                <tr class="header">
                    <td class="opcoes">Opções</td>
                </tr>
            </table>
            <br clear="all" />
        <?php /***** Até aqui ******/ ?>


        <?php
        /*
         * Background - CONTEÚDO DA PRIMEIRA TAB - GERAL
         */
        ?>
        <?php
        /*
         * PANE - CONFIGURAÇÕES
         */
        foreach($options as $type=>$conf){
            /*
             * Usuário tem permissão para modificar estas permissões
             */
            if( $config->hasPermission($type) ){
                ?>
                <div class="background">
                    <form method="post" action="adm_main.php?section=<?=$_GET['section'];?>">
                    <table class="form">
                    <?php
                    foreach( $conf as $properties ){
                        ?>
                        <tr>
                            <td class="first"><?php echo $properties['nome']; ?></td>
                            <td class="second">
                                <input name="data[<?php echo $properties['id']; ?>]" value="<?php echo $properties['valor']; ?>" class="text" />
                                <p class="explanation"><?php echo $properties['explanation']; ?></p>
                            </td>
                        </tr>
                        <?php
                    } // fim foreach
                    ?>
                    </table>
                    <input type="submit" name="gravar" value="Salvar" class="submit" />
                    </form>
                </div><?php // fim .background ?>
                <?php
            }
        }

        ?>

    </div>

</div>

<br clear="all" />
<?php
/*
 *
 * NOVA CONFIGURAÇÃO
 *
 */
if( $administrador->tipo == "Webmaster" AND 1==0 ){
    ?>

    <?php

    /*
     * MOSTRA CONFIGURAÇÕES
     */

    if( $administrador->tipo != "Webmaster" ){
        $params = array(
            'where' => "tipo='global'",
        );
    }

    $options = $config->getOptions($params);
    pr($options);
    if( $options ){
        ?>
        <form method="post" action="adm_main.php?section=<?=$_GET['section'];?>" class="simples">
        <?php

            foreach($options as $dados ){
                $tipo = $dados['tipo'];

                if($tipo <> $tipo_anterior){
                    echo '<h2>'.$tipo.'</h2>';
                }

                echo '<div class="campo">';
                echo '<label>';

                if( empty($dados['nome']) ){
                    echo $dados['propriedade'];
                } else {
                    echo $dados['nome'];
                }
                echo '</label>';
                echo '<input type="text" name="'.$dados['id'].'" value="'.$dados['valor'].'" class="text" />';
                echo '</div>';
                $tipo_anterior = $tipo;
            }

        ?>
        <input type="submit" name="gravar" value="Enviar" class="submit" />
        </form>
    <?php } else { ?>
    <br clear="all" />
        <p class="alerta">
            Nenhuma configuração ajustada ainda.
        </p>
    <?php } ?>
    <p>
        <a href="javascript: history.back();"><img src="img/layoutv1/voltar.gif" border="0" /></a>
    </p>



    <h2>Nova configuração</h2>
    <p>A seguir, você pode criar uma nova configuração.</p>
    <form method="post" action="adm_main.php?section=<?=$_GET['section']?>" class="simples">

    <div class="campo">
        <label>Nome humano da configuração:</label>
        <input type="text" name="nome" class="text" />
    </div>
    <div class="campo">
        <label>Nome da config. no DB:</label>
        <input type="text" name="propriedade" class="text" />
    </div>
    <div class="campo">
        <label>Valor:</label>
        <input type="text" name="valor" class="text" />
    </div>
    <div class="campo">
        <label>Tipo:</label>
        <select name="tipo">
            <option value="global">Global (todos tem acesso)</option>
            <option value="mod_conf">mod_conf - configuração de módulos</option>
        </select>
    </div>
    <div class="campo">
        <input type="submit" name="novaconfig" value="Enviar" class="submit" />
    </div>


    </form>
    <p>
        <a href="javascript: history.back();"><img src="img/layoutv1/voltar.gif" border="0" /></a>
    </p>
    <?php
}
?>