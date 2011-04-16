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
$tabela_da_estrutura = $modulo->getTable();

include_once $modulo->getIncludeFolder().'/'.MOD_MODELS_DIR.'CadastroSetup.php';
$setup = new CadastroSetup();
$setup->austNode = $_GET['aust_node'];
$setup->mainTable = $modulo->getTable();

/*
 * VERIFICAÇÕES $_POST
 * Faz verificações de $_POST o que deve ser feito
 */
/**
 * NOVO CAMPO
 * Insere um novo campo na tabela do cadastro
 */
	if(!empty($_POST['add_field'])){

		$params = array(
			'name' => $_POST['data']['name'],
			'type' => $_POST['data']['type'],
			'order' => $_POST['data']['order'],
			'description' => $_POST['data']['description'],
		);
		
		if( !empty($_POST['relacionado_tabela_0']) )
			$_POST['data']['refTable'] = $_POST['relacionado_tabela_0'];

		if( !empty($_POST['relacionado_campo_0']) )
			$_POST['data']['refField'] = $_POST['relacionado_campo_0'];
			
		$setup->addField($_POST['data']);
	}

/*
 * MOD_CONF
 */
if( !empty($_POST['conf_type']) AND $_POST['conf_type'] == "mod_conf" ){
    /**
     *
     */
	
	//pr($_POST);
    $modulo->saveModConf($_POST);
}

/*
 *
 * DIVISORS
 *
 * Um divisor é um título que há entre campos de um formulário de
 * cadastro deste módulo.
 */
    /*
     *
     * NOVO TÍTULO DIVISOR
     *
     */
    if( !empty($_POST['create_divisor']) AND
        !empty($_POST['title']) )
        {

        $params = array(
            'title' => $_POST['title'],
            'comment' => $_POST['comment'],
            'before' => $_POST['before']
        );

        if( $modulo->saveDivisor($params) ){
            $status[] = 'Divisor criado com sucesso!';
        } else {
            $status[] = "Erro ao gravar informações sobre o novo campo. Nada foi criado.";
        }
    }
    /*
     * Excluir Divisor
     */
    if( !empty($_GET['deleteDivisor']) AND
        $_GET['deleteDivisor'] > 0 )
        {

        if( $modulo->deleteDivisor($_GET['deleteDivisor']) ){
            $status[] = 'Divisor excluído com sucesso!';
        } else {
            $status[] = "Erro. Nada foi excluído.";
            
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
            
            if($modulo->connection->exec($sql)){
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
<p>
    Configure esta estrutura de cadastro.
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
    /**
     * GERENCIAMENTO DE CAMPOS
     *
     * Listagem dos campos deste cadastro e configuração destes
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Gerenciamento dos campos</h3>
        </div>
        <div class="content">
            <p>A seguir, você tem a lista dos campos existentes neste cadastro.</p>
            <ul>
            <?php
			$fields = $modulo->getFields(false);

            foreach($fields as $chave=>$valor){
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
                $result = $modulo->connection->query($sql);
                if( count($result) > 0 ){
                    $dados = $result[0];
                    ?>
                    <li>
                    <?php echo $chave; ?>
                    <?php
                    if($dados['desativado'] == '1'){
                        ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=ativar&w=<?php echo $chave; ?>">
                            Ativar
                        </a>
                        <?
                    } else {
                    ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=desativar&w=<?php echo $chave; ?>">
                            Desativar
                        </a>
                    <?
                    }
                    ?> -
                    <?php
                    if($dados['necessario'] == '0'){
                        ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=necessario&w=<?php echo $chave; ?>">
                            Necessario
                        </a>
                        <?
                    } else {
                    ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=desnecessario&w=<?php echo $chave; ?>">
                            Não necessário
                        </a>
                    <?
                    }
                    ?> -
                    <?php
                    if( $dados['listagem'] < '1' ){
                        ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=listar&w=<?php echo $chave; ?>">
                            Listar
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&function=naolistar&w=<?php echo $chave; ?>">
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
        <div class="footer"></div>
    </div>

    <?php
    /**
     * NOVOS CAMPOS
     *
     * Form para inserir novos campos em um cadastro
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Novo Campo</h3>
        </div>
        <div class="content">
            <p>Insira um novo campo.</p>
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">
                <input type="hidden" name="add_field" value="1" />

                <?php
                /*
                 * Input CAMPO: Contém o nome do campo
                 */
                ?>
                <div class="campo">
                    <label>Nome:</label>
                    <div class="input">
                        <input type="text" name="data[name]" class="input" />
                    </div>
                </div>
                <br clear="both" />
                <?php
                /*
                 * Input CAMPO_TIPO: Contém o tipo do campo
                 */
                ?>
                <div class="campo">
                    <label>Tipo: </label>
                    <select name="data[type]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption0'?>', '0')">
                        <option value="string">Texto pequeno</option>
                        <option value="text">Texto médio ou grande</option>
                        <option value="date">Data</option>
                        <option value="pw">Senha</option>
                        <option value="images">Imagens</option>
                        <option value="files">Arquivo</option>
	                    <option value="relational_onetomany">Relacional 1-para-muitos (tabela)</option>
						<?php
						/*
						 * faltam os campos relacionais
						 */
						?>
                    </select>
                </div>
                <?php // <select> em ajax ?>
                <div class="campooptions" id="<?php echo 'campooption0'?>">
                    <?
                    /*
                     * Se <select campo_tipo> for relacional, então cria dois campos <select>
                     *
                     * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
                     * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
                     */
                    ?>
                    <div class="campooptions_tabela" id="<?php echo 'campooption0'?>_tabela"></div>
                    <div class="campooptions_campo" id="<?php echo 'campooption0'?>_campo"></div>
                </div>

                <?php
                /*
                 * Input CAMPO_DESCRICAO: Contém uma descrição do campo
                 */
                ?>
                <br clear="both" />
                <div class="campo_descricao">
                    <label>Descrição: </label>
                    <input type="text" name="data[description]" />
                </div>
                <br clear="both" />
                <?php
                /*
                 * Input CAMPO_LOCAL: Indica onde será inserido o novo campo
                 */
                ?>
                <div class="campo">
                    <label>Local de inserção do novo campo: </label>
                    <select name="data[order]">
                        <?php


                        // pega o valor físico do campo da tabela
                        $fields = $modulo->getFields();
                        $i = 0;
                        foreach($fields as $campo=>$valor){
                            // verifica se o campo é editável ou infra-estrutura (ex. de campos: id, adddate, aprovado)
                            $sql = "SELECT
                                        valor, chave
                                    FROM
                                        cadastros_conf
                                    WHERE
                                        chave='".$campo."' AND
										categorias_id='".$_GET['aust_node']."'
                                    LIMIT 0,2
                                    ";
                            $result = $modulo->connection->query($sql,"ASSOC");
                            $result = $result[0];
                            if( count($result) > 0 ){
                                $i++;
                                // se for primeiro registro, escreve <option> com opção de "ANTES DE <campo>"
                                if($i == "1"){
                                    echo '<option value="first_field">Antes de '.$result["valor"].'</option>';
                                }
                                echo '<option value="'.$result["chave"].'">Depois de '.$result["valor"].'</option>';
                            }

                        }
                        unset($campo);
                        unset($dados);
                        unset($valor);
                        ?>
                        
                    </select>
                </div>
                <br />
                <input type="submit" name="novo_campo" value="Criar!" />

            </form>
        </div>
        <div class="footer"></div>
    </div>

    <?php
    /**
     * TÍTULOS DIVISORES
     *
     * No formulário, há estes títulos que servem para dividir os
     * inputs, como por exemplo, 'Informações Pessoais' e
     * 'Informações Profissionais'.
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <a name="divisors"><h3>Títulos Divisores</h3></a>
        </div>
        <div class="content">
            <p>
                Insira um novo título divisor no formulário de cadastro.
            </p>
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">
                <input type="hidden" name="tabela" value="<?php echo $tabela_da_estrutura?>" />

                <?php
                /*
                 * Input CAMPO: Contém o nome do campo
                 */
                ?>
                <div class="campo">
                    <label>Nome do título:</label>
                    <div class="input">
                        <input type="text" name="title" class="input" />
                    </div>
                </div>
                <br clear="both" />
                <div class="campo">
                    <label>Parágrafo de comentário:</label>
                    <div class="input">
                        <input type="text" name="comment" class="input" />
                    </div>
                </div>
                <br clear="both" />
                <?php
                /*
                 * Input CAMPO_LOCAL: Indica onde será inserido o novo campo
                 */
                ?>
                <div class="campo">
                    <label>Antes de: </label>
                    <?php
                    /*
                     * Busca campos do DB
                     */
                    $sql = "SELECT
                                chave, valor
                            FROM
                                cadastros_conf
                            WHERE
                                tipo='campo' AND
                                categorias_id='".$_GET['aust_node']."'
                            ";
                    $dados = $modulo->connection->query($sql,"ASSOC");
                    ?>

                    <select name="before">
                        <?php
                        /*
                         * Lista campos para criar título divisor
                         */
                        foreach($dados as $valor){
                            ?>
                            <option value="BEFORE <?php echo $valor["chave"]?>"><?php echo $valor["valor"]?></option>
                            <?php
                        }
                        ?>

                    </select>
                </div>
                <br />
                <input type="submit" name="create_divisor" value="Criar!" />
            </form>

            <h4>Divisores atuais</h4>
            <?php
            $divisorTitles = $modulo->loadDivisors();
            if( empty($divisorTitles) ){
                ?>

                <?php
            } else {
                foreach( $divisorTitles as $div ){
                    ?>
                    <strong><?php echo $div['valor'];?></strong>
                    <br clear="all" />
                    <em><?php echo $div['descricao'];?></em>
                    <a href="adm_main.php?section=<?php echo $_GET['section']?>&aust_node=<?php echo $_GET['aust_node']?>&action=<?php echo $_GET['action']?>&deleteDivisor=<?php echo $div['id'] ?>#divisors">Excluir</a>
                    <br clear="all" />
                    <br clear="all" />
                    <?php
                }
            }
            ?>

        </div>
        <div class="footer"></div>
    </div>

    <?php
    /*
     * FILTROS ESPECIAIS
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Opções de Filtragem</h3>
        </div>
        <div class="content">
            <p>
                Se você especificar o campo de email abaixo, será mostrado um input
                na listagem para que o usuário possa ver os emails dos usuários
                cadastrados e copiá-los.
            </p>
            <?php
            if( !empty($_POST["filtro_especial_campo_email"])
                AND $_POST["filtro_especial_campo_email"] == "Salvar" ){

                $sql = "DELETE FROM cadastros_conf WHERE tipo='filtros_especiais'";
                $modulo->connection->exec($sql);

                if( !empty($_POST['email']) ){
                    $sql = "INSERT INTO
                                cadastros_conf
                                (tipo, chave, valor, categorias_id)
                            VALUES
                                ('filtros_especiais', 'email', '".$_POST['email']."', '".$_GET["aust_node"]."')
                            ";
                    $modulo->connection->exec($sql);
                }
            }

            $sql = "SELECT valor
                    FROM
                        cadastros_conf
                    WHERE
                        tipo='filtros_especiais' AND
                        chave='email' AND
                        categorias_id='".$_GET["aust_node"]."'
                    ";
            $dados = $modulo->connection->query($sql);
			if( !empty($dados[0]["valor"]) )
            	$dados = $dados[0]["valor"];
			else 
				$dados = '';

            ?>
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">
                <input type="hidden" name="tabela" value="<?php echo $tabela_da_estrutura ?>" />
                Campo de email? <input type="text" name="email" value="<?php echo $dados ?>" />
                <br />
                <input type="submit" name="filtro_especial_campo_email" value="Salvar" />
            </form>

           

        </div>
        <div class="footer"></div>
    </div>
</div>

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
	                            <input type="text" name="data[<?php echo $key; ?>]" value="<?php echo $options['value'] ?>" class="input" />
                                <?php
                            }
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

    <?php
    /**
     * CONFIGURAÇÕES ESPECÍFICAS DE CAMPOS
     */
    ?>
    <div class="widget">
        <div class="titulo">
            <h3>Configurações de Campos</h3>
        </div>
        <div class="content">
            <?php
            $configurations = $modulo->loadModConf(null,'field');
			$fields = $modulo->getFields(false);
			//pr($fields);
            if( !empty($configurations) && is_array($configurations) ){
                ?>

                <p>Configure os campos abaixo:</p>
                <form method="post" action="adm_main.php?section=conf_modulos&aust_node=<?php echo $_GET['aust_node']; ?>&action=configurar" class="simples pequeno">
                <input type="hidden" name="conf_type" value="mod_conf" />
                <input type="hidden" name="conf_class" value="field" />
                <input type="hidden" name="aust_node" value="<?php echo $_GET['aust_node']; ?>" />
                <?php

                foreach( $fields as $fieldName=>$fieldOptions ){
					if( empty($fieldOptions["valor"]) )
						continue;
                    ?>

                    <div class="campo">
                        <div><?php echo $fieldOptions["valor"] ?></div>
                        <div style="margin-left: 15px">
	                        <?php
							if( empty($configurations[$fieldName]) )
								$configurations[$fieldName] = array();

							foreach( $configurations[$fieldName] as $key=>$options ){
								
								if( !empty($options['field_type']) AND
									$options['field_type'] != $fieldOptions['especie']
								)
									continue;
									
								?>
								<div>
								<?php
		                        if( !empty($options["inputType"]) &&
									$options["inputType"] == "checkbox" )
								{

		                            /*
		                             * Verifica valores no banco de dados.
		                             */
		
		                            $checked = "";
		                            if( !empty($options['value'])
										AND $options['ref_field'] == $fieldName
		 								)
									{
		                                if( $options["value"] == "1" ){
		                                    $checked = 'checked="checked"';
		                                }
		                            }
		                            ?>
		                            <input type="hidden" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" value="0" />

		                            <input type="checkbox" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" <?php echo $checked; ?> value="1" class="input" />
						
		                            <?php
		                        }

		                        else {
									$size = '';
									if( !empty($options['size']) &&
									 	$options['size'] == 'small' )
										$size = '5';
		                            ?>
		                            <input type="text" size="<?php echo $size?>" name="data[<?php echo $fieldName ?>][<?php echo $key; ?>]" value="<?php echo $options['value'] ?>" class="input" />
		                            <?php
		                        }
								if( !empty($options['label']) ){
									echo $options['label'];
								} else {
									echo "não possui label.";
								}
								if( !empty($options["help"]) )
									tt($options["help"]);
								?>
								
								</div>
								<?php
							}
	                        ?>
                        </div>
                    </div>
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
            <form method="post" action="<?php echo $config->self;?>" class="simples pequeno">
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
                $result = $modulo->connection->query($sql);
                foreach($result as $dados){
                    ?>
                        <div class="campo">
                            <label><?php echo $dados['nome']?>:</label>
                            <?php
                            /*
                             * Mostra o campo de acordo
                             */
                            if($dados['especie'] == 'bool'){ ?>
                                <select name="frm<?php echo $dados['chave']?>">
                                    <option <? makeselected($dados['valor'], '1') ?> value="1">Sim</option>
                                    <option <? makeselected($dados['valor'], '0') ?> value="0">Não</option>
                                </select>
                            <? } elseif($dados['especie'] == 'string') { ?>
                                <input type="text" name="frm<?php echo $dados['chave']?>" value="<?php echo $dados['valor']?>" />
                            <? } elseif($dados['especie'] == 'blob') { ?>
                                <textarea name="frm<?php echo $dados['chave']?>" cols="35" rows="3"><?php echo $dados['valor']?></textarea>

                            <? } else { ?>
                                <textarea name="frm<?php echo $dados['chave']?>" cols="30" rows="3"><?php echo $dados['valor']?></textarea>
                            <? } ?>
                        </div>
                    <?
                }
                ?>
                <br />
                <input type="submit" name="configurar_opcoes" value="Enviar!" />
            </form>

        </div>
        <div class="footer"></div>
    </div>
</div>
