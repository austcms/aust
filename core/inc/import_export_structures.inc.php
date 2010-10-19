<?php

/*
 * MOD_CONF
 */
if( !empty($_POST['conf_type']) AND $_POST['conf_type'] == "mod_conf" ){
    /**
     *
     */
    $modulo->saveModConf($_POST);
}

/**
 * FUNÇÃO
 *
 * Campo necessário? Desativar campo? Usar em listagem?
 *
 * Se $_GET['function'] existir
 */
if(!empty($_GET['function'])){
    /**
     * DESATIVAR CAMPO
     */
    if($_GET['function'] == "desativar"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    desativado='1'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "Campo desativado com sucesso";
        else
            $status[] = "Erro ao desativar campo.";
    }
    /**
     * ATIVAR CAMPO
     */
    if($_GET['function'] == "ativar"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    desativado='0'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "Campo ativado com sucesso";
        else
            $status[] = "Erro ao ativar campo.";
    }

    /**
     * NECESSARIO
     */
    if($_GET['function'] == "necessario"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    necessario='1'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "Preenchimento do campo ajustado para necessário com sucesso.";
        else
            $status[] = "Erro ao executar ação.";
    }

    /**
     * CAMPO NÃO OBRIGATÓRIO
     */
    if($_GET['function'] == "desnecessario"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    necessario='0'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "Não é necessário preenchimento obrigatório do campo ajustado com sucesso.";
        else
            $status[] = "Erro ao executar ação.";
    }

    /**
     * LISTAR
     *
     * Campo deve aparecer em listagens
     */
    if($_GET['function'] == "listar"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    listagem='1'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "Campo aparecerá na listagem de cadastro.";
        else
            $status[] = "Erro ao executar ação.";
    }

    /**
     * NÃO LISTAR
     *
     * Campo não deve aparecer em listagens
     */
    if($_GET['function'] == "naolistar"){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    listagem='0'
                WHERE
                    chave='".$_GET['w']."' AND
                    categorias_id='".$_GET['aust_node']."'
        ";
        if($modulo->connection->exec($sql))
            $status[] = "O campo selecionado não aparecerá mais em listagens.";
        else
            $status[] = "Erro ao executar ação.";
    }

}

/**
 * Desativar campos
 */
if(!empty($_GET['function'])){
    if($_GET['function'] == 'desativar' AND !empty($_GET['w'])){
        $sql = "
                UPDATE
                    cadastros_conf
                SET
                    tipo='campodesativado'


";
    }
}
?>

<h2>Configuração: <?php echo $aust->leNomeDaEstrutura($_GET['aust_node'])?></h2>
<?php if(!empty($status)){ ?>
    <div class="box-full">
        <div class="box alerta">
            <div class="titulo">
                <h3>Status</h3>
            </div>
            <div class="content">
                <?php
                if(is_string($status))
                    echo $status;
                elseif(is_array($status)){
                    foreach($status as $valor){
                        echo '<span>'.$valor.'</span><br />';
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<div class="widget_group">
    <?
    /**
     * CONFIGURAÇÕES
     *
     * Listagem dos campos deste cadastro e configuração destes
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Configurações</h3>
        </div>
        <div class="content">
            <p>
				teste
			</p>

        </div>
        <div class="footer"></div>
    </div>

    <div class="widget">
        <div class="titulo">
            <h3></h3>
        </div>
        <div class="content">
            <p>
	            <p>
					teste
				</p>
                
            </p>

        </div>
        <div class="footer"></div>
    </div>
</div>

<div class="widget_group">
    <?
    /**
     * LISTAGEM DE CAMPOS
     *
     * Listagem dos campos deste cadastro e configuração destes
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>vasdv</h3>
        </div>
        <div class="content">
            <p>avds</p>

        </div>
        <div class="footer"></div>
    </div>
    <?
    /*
     * Opções gerais do cadastro
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>asdv</h3>
        </div>
        <div class="content">
            <p></p>
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">

            </form>

        </div>
        <div class="footer"></div>
    </div>
</div>
