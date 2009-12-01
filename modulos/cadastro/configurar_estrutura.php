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


/**
 * Explicação do código a seguir:
 *
 * A parte de lógica está separada da parte de código de amostragem HTML. O
 * código a seguir verifica cada item que deve ser mostrado posteriormente.
 */
/*
 * VERIFICAÇÕES $_POST
 * Faz verificações de $_POST o que deve ser feito
 */
/**
 * NOVO CAMPO
 * Insere um novo campo na tabela do cadastro
 */
if(!empty($_POST['novo_campo'])){
    if($_POST['campo_tipo'] == 'pw'){
        $campo_tipo = "varchar(80)";
        $tipo = 'campopw';
    } elseif($_POST['campo_tipo'] == 'arquivo'){
        $campo_tipo = "varchar(80)";
        $tipo = 'campoarquivo';
        if( $modulo->CriaTabelaArquivo(Array('tabela' => $_POST['tabela'])) ){
            $status[] = 'Tabela de arquivos criada com sucesso!';
        } else {
            $status[] = 'Tabela de arquivos não foi criada. Talvez esta tabela já exista.';
        }
    } elseif($_POST['campo_tipo'] == 'relacional_umparaum'){
        $campo_tipo = "int";
        $tipo = 'camporelacional_umparaum';
    } else {
        $campo_tipo = $_POST['campo_tipo'];
        $tipo = 'campo';
    }

    $campo = RetiraAcentos(strtolower(str_replace(' ', '_', $_POST['nome'])));

    $sql = "INSERT INTO cadastros_conf
                        (tipo,chave,valor,comentario,categorias_id,ref_tabela,ref_campo,autor,desativado,desabilitado,publico,restrito,aprovado)
                    VALUES
                        ('{$tipo}','".$campo."','".$_POST['nome']."','".$_POST['campo_descricao']."',".$_GET['aust_node'].", '".$_POST['relacionado_tabela_0']."', '".$_POST['relacionado_campo_0']."', ".$administrador->LeRegistro('id').",0,0,1,0,1)";
//    echo $sql;


    if($modulo->conexao->exec($sql)){
        $status[] = 'As informações sobre o campo foram gravadas!';

        $sql = "ALTER TABLE
                    ".$_POST['tabela']."
                ADD COLUMN
                    {$campo} {$campo_tipo}
                ".$_POST['campo_local'];
        if($modulo->conexao->exec($sql)){
            $status[] = "Campo criado na tabela com sucesso.";
        } else {
            $status[] = "<span style=\"color:red;\">Erro na criação do campo <strong>{$campo}</strong> na tabela.";
        }
    } else {
        $status[] = "Erro ao gravar informações sobre o novo campo. Nada foi criado.";
    }
}


/**
 * SALVAR CONFIGURAÇÕES
 */
if(!empty($_POST['configurar_opcoes'])){

    foreach($_POST as $key=>$valor){

        if(!get_magic_quotes_gpc()){
            $valor = addslashes($valor);
        }
        // se o argumento $_POST contém 'frm' no início
        if(strpos($key, 'frm') === 0){
            $key = str_replace("frm", "", $key);
            $sql = "UPDATE
                        cadastros_conf
                    SET
                        valor='".$valor."'
                    WHERE
                        chave='".$key."' AND
                        tipo='config' AND
                        categorias_id='".$_GET['aust_node']."'
            ";
            
            if($modulo->conexao->exec($sql)){
                $status[] = "Informação \"".$key."\" salva com sucesso.";
            } else {
                $status[] = "<span style=\"color:red;\">Erro ao salvar \"".$key."\".";
            }
        }
    }
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
        if($modulo->conexao->exec($sql))
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
        if($modulo->conexao->exec($sql))
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
        if($modulo->conexao->exec($sql))
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
        if($modulo->conexao->exec($sql))
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
        if($modulo->conexao->exec($sql))
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
        if($modulo->conexao->exec($sql))
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

<h1>Configuração: <?=$aust->leNomeDaEstrutura($_GET['aust_node'])?></h1>
<?php if(!empty($status)){ ?>
    <div class="box-full">
        <div class="box alerta">
            <div class="titulo">
                <h2>Status</h2>
            </div>
            <div class="corpo">
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

<div class="painel-metade">

    <?php
    /**
     * NOVOS CAMPOS
     *
     * Form para inserir novos campos em um cadastro
     */
    ?>
    <div class="painel">
        <div class="titulo">
            <h2>Novo campo</h2>
        </div>
        <div class="corpo">
            <p>Insira um novo campo no cadastro.</p>
            <form method="post" action="<?=$config->self;?>" class="simples pequeno">
                <input type="hidden" name="tabela" value="<?=$tabela_da_estrutura?>" />

                <?php
                /*
                 * Input CAMPO: Contém o nome do campo
                 */
                ?>
                <div class="campo">
                    <label>Nome do campo:</label>
                    <div class="input">
                        <input type="text" name="nome" class="input" />
                    </div>
                </div>
                <br clear="both" />
                <?php
                /*
                 * Input CAMPO_TIPO: Contém o tipo do campo
                 */
                ?>
                <div class="campo">
                    <label>Tipo do campo: </label>
                    <select name="campo_tipo"  onchange="javascript: SetupCampoRelacionalTabelas(this, '<?='campooption0'?>', '0')">
                        <option value="varchar(200)">Texto pequeno</option>
                        <option value="text">Texto médio ou grande</option>
                        <option value="date">Data</option>
                        <option value="pw">Senha</option>
                        <option value="arquivo">Arquivo</option>
        				<option value="relacional_umparaum">Relacional Um-para-um (tabela)</option>
                    </select>
                </div>
                <? // <select> em ajax ?>
                <div class="campooptions" id="<?='campooption0'?>">
                    <?
                    /*
                     * Se <select campo_tipo> for relacional, então cria dois campos <select>
                     *
                     * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
                     * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
                     */
                    ?>
                    <div class="campooptions_tabela" id="<?='campooption0'?>_tabela"></div>
                    <div class="campooptions_campo" id="<?='campooption0'?>_campo"></div>
                </div>

                <?php
                /*
                 * Input CAMPO_DESCRICAO: Contém uma descrição do campo
                 */
                ?>
                <br clear="both" />
                <div class="campo_descricao">
                    <label>Descrição: </label>
                    <input type="text" name="campo_descricao" />
                </div>
                <br clear="both" />
                <?php
                /*
                 * Input CAMPO_LOCAL: Indica onde será inserido o novo campo
                 */
                ?>
                <div class="campo">
                    <label>Local de inserção do novo campo: </label>
                    <select name="campo_local">
                        <?php

                        // busca todos os campos da tabela do cadastro e mostra em um <select>
                        $sql = "SELECT
                                    *
                                FROM
                                    ".$tabela_da_estrutura."
                                LIMIT 0,1
                                ";
                        $dados = $modulo->conexao->query($sql,"ASSOC");
                        $dados = $dados[0];
                        // pega o valor físico do campo da tabela
                        //$fields = mysql_num_fields($mysql);
                        $i = 0;
                        foreach($dados as $campo=>$valor){
                            // verifica se o campo é editável ou infra-estrutura (ex. de campos: id, adddate, aprovado)
                            $sql = "SELECT
                                        valor
                                    FROM
                                        cadastros_conf
                                    WHERE
                                        chave='".$campo."'
                                    LIMIT 0,2
                                    ";
                            $result = $modulo->conexao->query($sql,"ASSOC");
                            $result = $result[0];
                            if( count($result) > 0 ){
                                $i++;
                                // se for primeiro registro, escreve <option> com opção de "ANTES DE <campo>"
                                if($i == "1"){
                                    echo '<option value="FIRST">Antes de '.$result["valor"].'</option>';
                                }
                                echo '<option value="AFTER '.$result["valor"].'">Depois de '.$result["valor"].'</option>';
                            }

                        }
                        ?>
                        
                    </select>
                </div>
                <br />
                <input type="submit" name="novo_campo" value="Criar!" />

            </form>
        </div>
        <div class="rodape"></div>
    </div>

    <div class="painel">
        <div class="titulo">
            <h2>Instalar Estrutura</h2>
        </div>
        <div class="corpo">
            <p>
                Selecione abaixo a categoria-chefe, o nome da estrutura (ex.: Notícias, Artigos, Arquivos) e o módulo adequado.
            </p>

        </div>
        <div class="rodape"></div>
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
    <div class="painel">
        <div class="titulo">
            <h2>Campos deste cadastro</h2>
        </div>
        <div class="corpo">
            <p>A seguir, você tem a lista dos campos existentes neste cadastro.</p>
            <ul>
            <?php
            // busca todos os campos da tabela do cadastro
            $sql = "SELECT
                        *
                    FROM
                        ".$tabela_da_estrutura."
                    LIMIT 0,1
                    ";
            $mysql = $modulo->conexao->query($sql, 'ASSOC');
            $mysql = $mysql[0];
            /**
             * Pega o valor físico do campo da tabela
             */
            $fields = count($mysql);

            foreach($mysql as $chave=>$valor){
                /**
                 * Verifica se o campo é editável ou infra-estrutura (ex. de campos: id, adddate, aprovado)
                 */
                $sql = "SELECT
                            valor, desativado, listagem, IFNULL(necessario, '0') as necessario
                        FROM
                            cadastros_conf
                        WHERE
                            chave='".$chave."' AND
                            categorias_id='".$_GET['aust_node']."'
                        LIMIT 0,2
                        ";
                $result = $modulo->conexao->query($sql);
                if( count($result) > 0 ){
                    $dados = $result[0];
                    ?>
                    <li>
                    <?php echo $chave; ?>
                    <?php
                    if($dados['desativado'] == '1'){
                        ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=ativar&w=<?php echo $chave; ?>">
                            Ativar
                        </a>
                        <?
                    } else {
                    ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=desativar&w=<?php echo $chave; ?>">
                            Desativar
                        </a>
                    <?
                    }
                    ?> -
                    <?php
                    if($dados['necessario'] == '0'){
                        ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=necessario&w=<?php echo $chave; ?>">
                            Necessario
                        </a>
                        <?
                    } else {
                    ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=desnecessario&w=<?php echo $chave; ?>">
                            Não necessário
                        </a>
                    <?
                    }
                    ?> -
                    <?php
                    if( $dados['listagem'] < '1' ){
                        ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=listar&w=<?php echo $chave; ?>">
                            Listar
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="adm_main.php?section=<?=$_GET['section']?>&aust_node=<?=$_GET['aust_node']?>&action=<?=$_GET['action']?>&function=naolistar&w=<?php echo $chave; ?>">
                            Não Listar
                        </a>
                        <?
                    }
                    ?>
                    </li>
                    <?php

                }
            }
            ?>
            </ul>

        </div>
        <div class="rodape"></div>
    </div>
    <?
    /*
     * Opções gerais do cadastro
     */
    ?>
    <div class="painel">
        <div class="titulo">
            <h2>Opções do cadastro</h2>
        </div>
        <div class="corpo">
            <p>A seguir, você configurar as principais opções deste cadastro.</p>
            <form method="post" action="<?=$config->self;?>" class="simples pequeno">
                <?php
                // busca todos os campos da tabela do cadastro
                $sql = "SELECT
                            *
                        FROM
                            cadastros_conf
                        WHERE
                            tipo='config' AND
                            categorias_id='".$_GET['aust_node']."'
                        ";
                $result = $modulo->conexao->query($sql);
                foreach($result as $dados){
                    ?>
                        <div class="campo">
                            <label><?=$dados['nome']?>:</label>
                            <?php
                            /*
                             * Mostra o campo de acordo
                             */
                            if($dados['especie'] == 'bool'){ ?>
                                <select name="frm<?=$dados['chave']?>">
                                    <option <? makeselected($dados['valor'], '1') ?> value="1">Sim</option>
                                    <option <? makeselected($dados['valor'], '0') ?> value="0">Não</option>
                                </select>
                            <? } elseif($dados['especie'] == 'string') { ?>
                                <input type="text" name="frm<?=$dados['chave']?>" value="<?=$dados['valor']?>" />
                            <? } elseif($dados['especie'] == 'blob') { ?>
                                <textarea name="frm<?=$dados['chave']?>" cols="35" rows="3"><?=$dados['valor']?></textarea>

                            <? } else { ?>
                                <textarea name="frm<?=$dados['chave']?>" cols="30" rows="3"><?=$dados['valor']?></textarea>
                            <? } ?>
                        </div>
                    <?
                }
                ?>
                <br />
                <input type="submit" name="configurar_opcoes" value="Enviar!" />
            </form>

        </div>
        <div class="rodape"></div>
    </div>
</div>