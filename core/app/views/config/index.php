<?php

/*
 * Salva configuração
 */
if( !empty($_POST['gravar']) && $_POST['gravar'] ){
    unset($_POST['gravar']);
    foreach($_POST['data'] as $key=>$valor){
        $params = array(
            'id' => $key,
            'valor' => $valor,
        );

        $msg = Config::getInstance()->updateOptions($params);

        unset($params);
    }

    header("Location: ".$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&status=1');
    $status = $msg;
}

/*
 * NOVA CONFIGURAÇÃO
 */
if( !empty($_POST['novaconfig']) && $_POST['novaconfig'] ){
    unset($_POST['novaconfig']);
    $params = array(
        'propriedade' => $_POST['propriedade'],
        'tipo' => $_POST['tipo'],
        'valor' => $_POST['valor'],
        'nome' => $_POST['nome'],
    );

    Config::getInstance()->adjustOptions($params);
    // Grava configuração no DB
    $status = Config::getInstance()->save();
}

?>

<?php
if(!empty($_POST['inserirmodulo'])){
	$status = Aust::getInstance()->createStructure(
                                    array(
                                        'name' => $_POST['nome'],
                                        'site' => $_POST['categoria_chefe'],
                                        'module' => $_POST['modulo'],
                                        'author' => User::getInstance()->LeRegistro('id')
                                    )
                                );
}

/*
 * CONFIGURAÇÕES
 *
 * Carrega todas as configurações existentes
 */
$options = Config::getInstance()->getConfigs();

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
				if( !in_array($type, array("Geral", "Privado")) )
					continue;

                if( Config::getInstance()->hasPermission($type, User::getInstance()->type()) ){
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
            <table border="0" class="pane_listing listing">
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
			if( !in_array($type, array("Geral", "Privado")) )
				continue;
            /*
             * Usuário tem permissão para modificar estas permissões
             */
            if( Config::getInstance()->hasPermission($type, User::getInstance()->type()) ){
                ?>
                <div class="background">
                    <form method="post" action="adm_main.php?section=config">
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
if( User::getInstance()->tipo == "Webmaster" AND 1==1 ){
    ?>

    <?php

    /*
     * MOSTRA CONFIGURAÇÕES
     */

    if( User::getInstance()->tipo != "Webmaster" ){
        $params = array(
            'where' => "tipo='global'",
        );
    }

?>


    <h2>Nova configuração</h2>
    <p>A seguir, você pode criar uma nova configuração.</p>
    <form method="post" action="adm_main.php?section=<?php echo $_GET['section']?>" class="simples">

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
            <option value="Geral">Geral (todos tem acesso)</option>
            <option value="mod_conf">mod_conf - configuração de módulos</option>
        </select>
    </div>
    <div class="campo">
        <input type="submit" name="novaconfig" value="Enviar" class="submit" />
    </div>


    </form>
    <p>
        <a href="javascript: history.back();"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
    </p>
    <?php
}
?>