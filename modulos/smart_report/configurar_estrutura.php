<?php
/**
 * Arquivo que contém interface para configurar estrutura que usa este módulo depois de já estar instalado.
 *
 * Ex: Inserir novos campos
 *
 * Inicialmente há todo o código PHP para executar as funções requisitadas e o FORM html está no final do documento. O action
 * dos FORMs enviam as informações para a própria página
 *
 * @package Módulos
 * @category Cadastro
 * @name Configurar Estrutura
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 16/08/2009
 */

/**
 * INICIALIZAÇÃO
 */
$tabela_da_estrutura = $modulo->LeTabelaDaEstrutura($_GET['aust_node']);



/*
 * MOD_CONF
 */
if( !empty($_POST['conf_type']) AND $_POST['conf_type'] == "mod_conf" ){
    /**
     *
     */
    $modulo->saveModConf($_POST);
}


?>

<h2>Configuração: <?php echo Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node'])?></h2>
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
            <?php
            $configurations = $modulo->loadModConf();
			//pr($configurations);
            if( !empty($configurations) && is_array($configurations) ){
                ?>

                <p>Configure este módulo.</p>
                <form method="post" action="adm_main.php?section=conf_modulos&aust_node=<?php echo $_GET['aust_node']; ?>&action=configurar" class="simples pequeno">
                <input type="hidden" name="conf_type" value="mod_conf" />
                <input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
                <?php

                foreach( $configurations as $key=>$options ){
                    ?>

                    <div class="campo">
                        <label><?php echo $options["label"] ?></label>
                        <div class="input">
                            <?php
                            if( $options["inputType"] == "checkbox" ){

                                /*
                                 * Verifica valores no banco de dados.
                                 */
                                $checked = "";
                                if( !empty($options['value']) ){
                                    if( $options["value"] == "1" ){
                                        $checked = 'checked="checked"';
                                    }
                                }
                                ?>
                                <input type="hidden" name="data[<?php echo $key; ?>]" value="0" />

                                <input type="checkbox" name="data[<?php echo $key; ?>]" <?php echo $checked; ?> value="1" class="input" />
                                <?php
                            }

                            else {
                                ?>
                                <input type="text" name="data[<?php echo $key; ?>]" class="input" value="<?php echo $options['value'] ?>" />
                                <?php
                            }

                            if( !empty($options['help']) )
                                tt($options['help']);
                            ?>

                        </div>
                    </div>
                    <br clear="both" />

                    <?php
                }
                ?>
                <input type="submit" name="submit" value="Salvar" />
                </form>
                <?php
            }
            ?>

        </div>
        <div class="footer"></div>
    </div>

    <div class="widget">
        <div class="titulo">
            <h3></h3>
        </div>
        <div class="content">
            <p>
                
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
            <h3>Novo Filtro</h3>
        </div>
        <div class="content">
            <p>Crie novos filtros abaixo.</p>

            <form method="post" action="adm_main.php?section=conf_modulos&aust_node=<?php echo $_GET['aust_node']; ?>&action=configurar" class="simples pequeno">
            <input type="hidden" name="conf_type" value="new_filter" />
            <input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
                <div class="campo">
                    <label>Código SQL</label>
                    <div class="input">
						<textarea name="sql_statement"></textarea>
                    </div>
                    <br clear="both" />
				</div>
                <div class="campo">
                    <label>Título</label>
                    <div class="input">
						<input type="text" name="title" value="" />
                    </div>
                    <br clear="both" />
				</div>
                <div class="campo">
                    <label>Descrição</label>
                    <div class="input">
						<input type="text" name="description" value="" />
                    </div>
                    <br clear="both" />
				</div>
                <div class="campo">
                    <label>Actions</label>
                    <div class="input">
						<input type="text" name="description" value="" />
                    </div>
                    <br clear="both" />
				</div>

	        <input type="submit" name="submit" value="Salvar" />
			</form>
			
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
            <h3></h3>
        </div>
        <div class="content">
            <p></p>
            <form method="post" action="<?php echo Config::getInstance()->self;?>" class="simples pequeno">

            </form>

        </div>
        <div class="footer"></div>
    </div>
</div>
