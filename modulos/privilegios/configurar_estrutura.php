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

    <?php
    /**
     * NOVOS CAMPOS
     *
     * Form para inserir novos campos em um cadastro
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Configurações gerais</h3>
        </div>
        <div class="content">
            <p></p>

            <?php
            /*
             * CONFIGURAÇÕES AUTOMÁTICAS
             */
            /*
             * Opções do módulo para salvar
             */
            $optionsToSave = array(
                /*
                 * Privilégios se aplicam a que?
                 */
                array(
                    "propriedade" => "only_content", // nome da propriedade
                    "value" => "",
                    "label" => "Privilégios se aplicam somente a conteúdos específicos",
                    "inputType" => "checkbox",
                ),
                /*
                 * Tem descrição?
                 */
                array(
                    "propriedade" => "has_description", // nome da propriedade
                    "value" => "",
                    "label" => "Tem descrição?",
                    "inputType" => "checkbox",
                ),

            );


            $sql = "SELECT *
                    FROM
                        config
                    WHERE
                        tipo  = 'mod_conf' AND
                        local = '".$_GET["aust_node"]."'
                    ";
            $queryTmp = $modulo->connection->query($sql, "ASSOC");

            foreach($queryTmp as $valor){
                $query[$valor["propriedade"]] = $valor;
            }

            ?>

            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">
                <input type="hidden" name="conf_type" value="mod_conf" />
                <input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
                <?php

                if( !empty($optionsToSave) && is_array($optionsToSave) ){

                    foreach( $optionsToSave as $options ){

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
                                    if( !empty($query[$options["propriedade"]]) ){
                                        if( $query[$options["propriedade"]]["valor"] == "1" ){
                                            $checked = 'checked="checked"';
                                        }
                                    }
                                    ?>
                                    <input type="hidden" name="data[<?php echo $options["propriedade"]; ?>]" value="0" />

                                    <input type="checkbox" name="data[<?php echo $options["propriedade"]; ?>]" <?php echo $checked; ?> value="1" class="input" />
                                    <?php
                                }

                                else {
                                    ?>
                                    <input type="text" name="nome" class="input" />
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                        <br clear="both" />

                        <?php

                    }

                }

                ?>

                <input type="submit" name="novo_campo" value="Criar!" />

            </form>
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

<div class="painel-metade painel-dois">
    <?
    /**
     * LISTAGEM DE CAMPOS
     *
     * Listagem dos campos deste cadastro e configuração destes
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Relacionamento entre Módulos</h3>
        </div>
        <div class="content">
            <p>
                A quais módulos estes privilégios se aplicam?
            </p>

            <?php

            $categorias = $conexao->find(array(
                                            'table' => 'categorias',
                                            'conditions' => array(
                                                //'id' => $_POST['id'],
                                                'classe' => 'estrutura',
                                            ),
                                            'fields' => array('id', 'nome', 'classe', 'tipo'),
                                        ), 'all'
            );

            /**
             * Carrega dados
             */
                $condition = array('admins_tipos_id' => $_POST['id']);

            $sql = "SELECT
                        valor
                    FROM
                        modulos_conf
                    WHERE
                        categoria_id='".$_GET['aust_node']."' AND
                        tipo='relacionamentos'
                    ";


            $relacionamentos = $conexao->query($sql);
            //pr($sql);
            $categoriasChecked = array();
            foreach($relacionamentos as $valor){
                $categoriasChecked[] = $valor['valor'];
            }


            foreach($categorias as $valor){

                /**
                 * Se for estrutura, deixa negrito
                 */
                if($valor['classe'] == 'estrutura'){
                    //echo '<strong>';
                }
                ?>
                <input type="checkbox" id="<?php echo $valor['nome']; ?>" <?php if(in_array($valor['id'], $categoriasChecked)) echo 'checked="true"'; ?> onchange="alteraRelacionamentos('categoria=<?php echo $_GET['aust_node']; ?>&target=<?php echo $valor['id']; ?>', this)" value="<?php echo $valor['nome']; ?>" /> <?php echo $valor['nome']; ?> (<?php echo $valor['tipo']; ?>)<br />
                <?php
                if($valor['classe'] == 'estrutura'){
                    //echo '</strong>';
                }
            }
            ?>
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
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">

            </form>

        </div>
        <div class="footer"></div>
    </div>
</div>
