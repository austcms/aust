<?php
/* 
 * Arquivo que contém interface para configurar estrutura que usa este módulo depois de já estar instalado.
 *
 * Ex: Inserir novos campos
 *
 * Inicialmente há todo o código PHP para executar as funções requisitadas e o FORM html está no final do documento. O action
 * dos FORMs enviam as informações para a própria página
 */

// Inicialização

$nome_modulo = explode('/', $_GET['modulo']);
$nome_modulo = array_reverse($nome_modulo, false);
$nome_modulo = $nome_modulo[0];

/*
 *  se $_GET['function'] existir
 */
if(!empty($_GET['function'])){
    // se $_GET['function'] == desativar
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
        if(mysql_query($sql))
            $status[] = "Campo desativado com sucesso";
        else
            $status[] = "Erro ao desativar campo.";
    }
    // ativar campo desativado
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
        if(mysql_query($sql))
            $status[] = "Campo ativado com sucesso";
        else
            $status[] = "Erro ao ativar campo.";
    }

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
        if(mysql_query($sql))
            $status[] = "Preenchimento do campo ajustado para necessário com sucesso.";
        else
            $status[] = "Erro ao executar ação.";
    }
    // ativar campo desativado
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
        if(mysql_query($sql))
            $status[] = "Não é necessário preenchimento obrigatório do campo ajustado com sucesso.";
        else
            $status[] = "Erro ao executar ação.";
    }
}

/*
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

<h2><?php echo $modInfo['nome']; ?></h2>
<p>
    Esta é a tela de configuração do Módulo, e não de estruturas ou categorias.
</p>
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
    /*
     * Configura qual estrutura pode ter imagens de capa
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Imagens de capa</h3>
        </div>
        <div class="content">
            <p>Selecione a seguir quais estruturas podem ter imagens de capa.</p>
                <?php

                $sql = "SELECT
                            id, valor
                        FROM
                            modulos_conf
                        WHERE
                            nome='".$nome_modulo."' AND
                            tipo='liberacao'
                        ";
                $mysql = mysql_query($sql);
                $return = array();
                while($dados = mysql_fetch_array($mysql)){
                    $return[] = $dados['valor'];
                }

                $sql = "SELECT
                            *
                        FROM
                            categorias
                        WHERE
                            classe='estrutura'
                        ORDER BY
                            tipo ASC
                        ";
                $mysql = mysql_query($sql);
                $t = mysql_num_rows($mysql);
                while($dados = mysql_fetch_array($mysql)){

                    // input checkbox, ao ser clicado usa Ajax para guardar dados no DB
                    ?>
                    <input type="checkbox" onchange="javascript: selectLiberacao(this);" name="<?=$dados['id']?>" <? if(in_array($dados['id'], $return)) echo 'checked="true"'; ?> value="<?=$nome_modulo;?>"> <strong><?=$dados['nome']?></strong> (módulo <?=$dados['tipo'];?>)<br />
                    <?
                }

                ?>

        </div>
        <div class="footer"></div>
    </div>
</div>

<div class="painel-metade painel-dois">
    <?
    /*
     * Listagem dos campos deste cadastro
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Campos deste cadastro</h3>
        </div>
        <div class="content">
            <p>A seguir, você tem a lista dos campos existentes neste cadastro.</p>


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
            <h3>Opções do cadastro</h3>
        </div>
        <div class="content">
            <p>A seguir, você configurar as principais opções deste cadastro.</p>


        </div>
        <div class="footer"></div>
    </div>
</div>
