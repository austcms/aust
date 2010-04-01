<?php
/*
 * SETUP -> Cadastro
 * Arquivo responsável pela criação das tabelas e configurações de um novo cadastro
 *
 * Variáveis necessárias:
 * $_POST -> contendo dados provenientes de formulário
 *
 */

/*
 * ATENÇÃO:
 * Este arquivo contém a configuração necessária para instalar a estrutura que depende deste modulo.
 * Não e necessário criar uma nova tag <form>
 */
echo 'oijoi';
// se não está completo o setup e não foi escolhida a quantidade de campos ainda
if(empty($_POST['setup_ready']) and empty($_POST['qtd_campos'])){
?>
	<p>Aqui nós configuraremos esta estrutura.</p>
    <div class="campo">
        <label>Quantos campos terá seu cadastro?</label>
        <div class="input">
        <select name="qtd_campos" style="width: 70px;">
            <?php
            // cria um select com 20 números
            for($i = 1; $i <= 20; $i++){
            ?>
                <option value="<?php echo $i;?>"><?php echo $i;?></option>
            <?php
            }
            ?>
        </select>
    	</div>
	</div>
    <div class="campo">
        <label>Será necessário aprovação para completar cadastro?</label>
        <div class="input">
            <input type="radio" name="aprovacao" value="1" /> Sim, será necessária aprovação de um administrador após cadastro<br />
            <input type="radio" checked="checked" name="aprovacao" value="0" /> Não, qualquer usuário poderá se cadastrar
        </div>
	</div>
    <div class="campo">
        <label>Se for necessário que o usuário digite uma senha para poder se cadastrar, especifique:</label>
        <div class="input">
        <input type="text" name="pre_senha" value="" />
        </div>
	</div>
    <div class="campo">
        <label>Parágrafo introdutório ao formulário:</label>
        <div class="input">
        <textarea name="descricao" cols="50" rows="3"></textarea>
        </div>
	</div>
	<div class="campo">
    	<input type="submit" value="Enviar!" class="submit" />
	</div>
<?php
// edita cada campo do cadastro
} elseif (empty($_POST['setup_ready']) and !empty($_POST['qtd_campos'])){
?>
	<p>Configurando esta estrutura...</p>
	<p>Seu cadastro terá <? echo '<strong>'.$_POST['qtd_campos'].'</strong>'; if($_POST['qtd_campos'] == 1) echo ' campo'; else echo ' campos';?>.
	Preencha abaixo informações sobre cada campo.</p>
    <h3>Cadastro "<?php echo $_SESSION['exPOST']['nome']?>"</h3>
	
	<table width="99%" border="0" class="listagem">
	<col width="15">
	<col width="160">
	<col width="400">
	<col>
	<tr class="titulo">
		<td></td>
		<td valign="top">Nome do campo</td>
		<td valign="top">Tipo de campo</td>
		<td valign="top">Descrição<br /><span style="font-weight: normal">Servirá de ajuda aos usuários</span></td>
	
	</tr>
	<?php for ($i = 1; $i <= $_SESSION['exPOST']['qtd_campos']; $i++){ ?>
	<tr class="conteudo">
		<td align="center" style="font-weight: bold;" valign="top">
		<?php echo $i;?>
		</td>
		<td valign="top">
			<input type="text" name="campo[]" />
		</td>
		<td valign="top">
			<select name="campo_tipo[]" onchange="javascript: SetupCampoRelacionalTabelas(this, '<?php echo 'campooption'.$i?>', '<?php echo $i?>')">
				<option value="varchar(200)">Texto pequeno (ex: nome, idade)</option>
				<option value="text">Texto médio ou grande (ex: descrição, biografia)</option>
				<option value="date">Data (ex: data de nascimento)</option>
				<option value="pw">Senha</option>
				<option value="arquivo">Arquivo</option>
				<option value="relacional_umparaum">Relacional Um-para-um (tabela)</option>
			</select>
            <div class="campooptions" id="<?php echo 'campooption'.$i?>">
                <?
                /*
                 * Se <select campo_tipo> for relacional, então cria dois campos <select>
                 *
                 * -<select relacionado_tabela_<n> onde n é igual a $i (sequencia numérica dos campos)
                 * -<select relacionado_campo_<n> onde n é igual a $i (sequencia numérica dos campos)
                 */
                ?>
                <div class="campooptions_tabela" id="<?php echo 'campooption'.$i?>_tabela"></div>
                <div class="campooptions_campo" id="<?php echo 'campooption'.$i?>_campo"></div>
            </div>
            
        </td>
		<td valign="top">
			<input type="text" name="campo_descricao[]" />
		</td>
	</tr>
	<?php } ?>
	</table>

	<input type='submit' value="Enviar!" name='setup_ready' />

<?php
/*
 * se não há mais configurações a serem definidas, cria estrutura e configura as opções escolhidas anteriormente
 */
} elseif (!empty($_POST['setup_ready'])){
	?>
	<p>Criando estrutura...</p>

	<?php
	// se conseguir registrar uma estrutura, segue...
	if($status_insert = $aust->gravaEstrutura(
                                    array(
                                        'nome' => $_POST['nome'],
                                        'categoriaChefe' => $_POST['categoria_chefe'],
                                        'estrutura' => 'estrutura',
                                        'moduloPasta' => $_POST['modulo'],
                                        'autor' => $administrador->LeRegistro('id')
                                    )
                                )){
        $status_setup[] = "Categoria criada com sucesso.";
		// cria string com o charset geral do projeto
		$cur_charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
        $tabela = RetiraAcentos(strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome'])));
		for($i = 0; $i < count($_POST['campo']); $i++) {
			$valor = '';
			if(!empty($_POST['campo'][$i])){

                
                /*
                 * !ATENÇÃO!: alterar condições abaixo para modificações do $_POST['campo_tipo']
                 */
                // se o tipo de campo for pw, $campo_tipo=varchar(120)
                if($_POST['campo_tipo'][$i] == 'pw'){
                    $campo_tipo = 'varchar(120)';
                // se o tipo de campo for arquivo, $campo_tipo=varchar(240)
                } elseif($_POST['campo_tipo'][$i] == 'arquivo'){
                    $campo_tipo = 'varchar(240)';
                } elseif($_POST['campo_tipo'][$i] == 'relacional_umparaum'){
                    $campo_tipo = 'int';
                } else {
                    $campo_tipo = $_POST['campo_tipo'][$i];
                }

                // Retiras acentuação e caracteres indesejados para criar campos nas tabelas
                $valor = RetiraAcentos(strtolower(str_replace(' ', '_', $_POST['campo'][$i]))).' '. $campo_tipo;

                // se for data ou relacional, não tem charset
				if($campo_tipo <> 'date' AND $campo_tipo <> 'int')
					$valor .= ' '. $cur_charset.' NOT NULL';
				
				if(!empty($_POST['campo_descricao'][$i]))
					$valor .= ' COMMENT \''.$_POST['campo_descricao'][$i] .'\'';

                if($i == 0){
					$campos = $valor;
				} else {
					$campos .= ', '.$valor;
	
				}
                $campo = RetiraAcentos(strtolower(str_replace(' ', '_', $_POST['campo'][$i])));
                
                /*
                 * Analisa campo por campo e grava informações diferenciadas sobre campos especiais (exemplo: password, arquivos)
                 */
                if($_POST['campo_tipo'][$i] == 'pw'){

                    // grava configuração sobre campo password com tipo=campopw
                    $sql_campos[] =
                                "INSERT INTO cadastros_conf
                                    (tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado)
                                VALUES
                                    ('campopw','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$administrador->LeRegistro('id').",0,0,1,0,1)";
                } elseif($_POST['campo_tipo'][$i] == 'arquivo'){

                    // grava configuração sobre campo para arquivo
                    $cria_tabela_arquivos = TRUE;
                    $sql_campos[] =
                                "INSERT INTO cadastros_conf
                                    (tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado)
                                VALUES
                                    ('campoarquivo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$administrador->LeRegistro('id').",0,0,1,0,1)";
                } elseif($_POST['campo_tipo'][$i] == 'relacional_umparaum'){

                    // grava configuração sobre campo relacional um-para-um
                    $sql_campos[] =
                                "INSERT INTO cadastros_conf
                                    (tipo,chave,valor,comentario,categorias_id,ref_tabela,ref_campo,autor,desativado,desabilitado,publico,restrito,aprovado)
                                VALUES
                                    ('camporelacional_umparaum','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", '".$_POST['relacionado_tabela_'.($i+1)]."', '".$_POST['relacionado_campo_'.($i+1)]."', ".$administrador->LeRegistro('id').",0,0,1,0,1)";
                } else {
                    // se for campo normal, grava suas informações
                    $sql_campos[] =
                                "INSERT INTO cadastros_conf
                                    (tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado)
                                VALUES
                                    ('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$administrador->LeRegistro('id').",0,0,1,0,1)";

                }
            }
		}
        //print_r($_POST);
        //echo '<br><br>';
        //echo $_POST['relacionado_tabela_2'].'<br><br>';
        //print_r($sql_campos);
        //echo '<br><br>';
		$sql = 'CREATE TABLE '.$tabela.'(
					id int auto_increment,
					'.$campos.', 
                    bloqueado varchar(120) '.$cur_charset.',
                    aprovado int,
					adddate datetime,
					PRIMARY KEY (id), UNIQUE id (id)
				
				) '.$cur_charset;
        //echo $sql;
        if($cria_tabela_arquivos == TRUE){
            $sql_arquivos =
                        "CREATE TABLE ".$tabela."_arquivos(
                        id int auto_increment,
                        titulo varchar(120) {$cur_charset},
                        descricao text {$cur_charset},
                        local varchar(80) {$cur_charset},
                        url text {$cur_charset},
                        arquivo_nome varchar(250) {$cur_charset},
                        arquivo_tipo varchar(250) {$cur_charset},
                        arquivo_tamanho varchar(250) {$cur_charset},
                        arquivo_extensao varchar(10) {$cur_charset},
                        tipo varchar(80) {$cur_charset},
                        referencia varchar(120) {$cur_charset},
                        categorias_id int,
                        adddate datetime,
                        autor int,
                        PRIMARY KEY (id),
                        UNIQUE id (id)
                    ) ".$cur_charset;
        }
        //echo '<br><br><br>'.$sql_arquivos;
        /*
         * Executa QUERY no MySQL
         *
         * Se retornar sucesso, salva configurações gerais sobre o cadastro na tabela cadastros_conf
         */
		if(mysql_query($sql)){
			$status_setup[] = 'Tabela \''.$tabela.'\' criada com sucesso!';
            if(!empty($sql_arquivos) AND $cria_tabela_arquivos == TRUE){
                if(mysql_query($sql_arquivos)){
                    $status_setup[] = 'Criação da tabela \''.$tabela.'_arquivos\' efetuada com sucesso!';
                } else {
                    $status_setup[] = 'Erro ao criar tabela \''.$tabela.'_arquivos\'.';
                }

                $sql_conf_arquivos =
                            "INSERT INTO
                                cadastros_conf
                                (tipo,chave,valor,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
                            VALUES
                                ('estrutura','tabela_arquivos','".$tabela."_arquivos',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$administrador->LeRegistro('id').",0,0,1,0,1)
                            ";
                if(mysql_query($sql_conf_arquivos)){
                    $status_setup[] = 'Configuração da estrutura \''.$tabela.'_arquivos\' salva com sucesso!';
                } else {
                    $status_setup[] = 'Erro ao criar tabela \''.$tabela.'_arquivos\'.';
                }


            }

            /*
             * CONFIGURAÇÃO
             *
             * Aqui, guardamos as principais configurações de cadastro
             */
            // salva configuração sobre aprovação quanto ao cadastro
                $sql_conf_2 =
                            "INSERT INTO
                                cadastros_conf
                                (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
                            VALUES
                                ('config','aprovacao','".$_SESSION['exPOST']['aprovacao']."','Aprovação','bool',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$administrador->LeRegistro('id').",0,0,1,0,1)
                            ";
                if(mysql_query($sql_conf_2)){
                    $status_setup[] = 'Configuração de aprovação salva com sucesso!';
                } else {
                    $status_setup[] = 'Configuração de aprovação não foi salva com sucesso.';
                }

            // DESCRIÇÃO: salva o parágrafo introdutório ao formulário
                $sql_conf_2 =
                            "INSERT INTO
                                cadastros_conf
                                (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
                            VALUES
                                ('config','descricao','".$_SESSION['exPOST']['descricao']."','Descrição','blob',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$administrador->LeRegistro('id').",0,0,1,0,1)
                            ";
                if(mysql_query($sql_conf_2)){
                    $status_setup[] = 'Configuração de aprovação salva com sucesso!';
                } else {
                    $status_setup[] = 'Configuração de aprovação não foi salva com sucesso.';
                }

            // salva configuração sobre pré-senha para o cadastro
                $sql_conf_2 =
                            "INSERT INTO
                                cadastros_conf
                                (tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
                            VALUES
                                ('config','pre_senha','".$_SESSION['exPOST']['pre_senha']."','Pré-senha','string',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$administrador->LeRegistro('id').",0,0,1,0,1)
                            ";
                if(mysql_query($sql_conf_2)){
                    $status_setup[] = 'Configuração de pré-senha salva com sucesso!';
                } else {
                    $status_setup[] = 'Configuração de pré-senha não foi salva com sucesso.';
                }




            // configurações sobre a estrutura, como tabela a ser usada
			$sql_conf =
						"INSERT INTO
							cadastros_conf
							(tipo,chave,valor,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
						VALUES
							('estrutura','tabela','".RetiraAcentos(strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome'])))."',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$administrador->LeRegistro('id').",0,0,1,0,1)
						";
            if(mysql_query($sql_conf)){
				$status_setup[] = 'Configuração da estrutura \''.RetiraAcentos(strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']))).'\' salva com sucesso!';
				
				// número de erros encontrados
				$status_campos = 0;
				foreach ($sql_campos as $valor) {
					if(!mysql_query($valor)){
						$status_campos++;
					}
				}
				if($status_campos == 0){
					$status_setup[] = 'Campos criados com sucesso!';
				} else {
					$status_setup[] = 'Erro ao criar campos';
				}
			} else {
				$status_setup[] = 'Erro ao salvar configuração da estrutura \''.RetiraAcentos(strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']))).'\'.';
			}
		} else {
			$status_setup[] = 'Erro ao criar tabela \''.RetiraAcentos(strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']))).'\'.';
		}
         
	}
	
	echo '<ul>';
	foreach ($status_setup as $valor){
		echo '<li>'.$valor.'</li>';
	}
	echo '</ul>';
     
    
	
	
}
?>
<p>
<a href="adm_main.php">Voltar para página inicial</a>
</p>