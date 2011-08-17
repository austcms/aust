<?php
/**
 * Controller principal deste módulo
 *
 * @since v0.1.6 06/07/2009
 */

class SetupController extends ModActionController
{

	function beforeFilter(){
		$_SESSION['exPOST'] = $_POST;
		$this->set('exPOST', $_SESSION['exPOST']);
		if( !empty($_POST) && !empty($_POST['setupAction']) ){
			$this->customAction = $_POST['setupAction'];
		}
		parent::beforeFilter();
	}

	function index(){

	}
	/**
	 * setuppronto()
	 *
	 * Cria cadastro
	 *
	 * Campos especificados, agora começa a criar tabelas e configurações.
	 *
	 * @global array $aust_charset Contém o charset global do sistema
	 */
	function setuppronto(){
		
		$this->loadModel("FlexFieldsSetup");
		
		global $aust_charset;

		$fields = array();
		$i = 0;
		// prepara array com campos
		foreach( $_POST['campo'] as $key=>$value ){
			if( empty($value) )
				continue;
			
			$fields[$i] = array(
				'name' => $value,
				'type' => $_POST['campo_tipo'][$key],
				'description' => $_POST['campo_descricao'][$key],
			);
			
			/*
			 * Campos relacionados têm informações sobre quais campos são
			 * relacionados.
			 */
			if( !empty($_POST['relacionado_tabela_'.($key+1)])
				AND !empty($_POST['relacionado_campo_'.($key+1)]) )
			{
				$fields[$i]['refTable'] = $_POST['relacionado_tabela_'.($key+1)];
				$fields[$i]['refField'] = $_POST['relacionado_campo_'.($key+1)];
			}
			
			$i++;
		}
		
		if( empty($_POST['approval']) )
			$_POST['approval'] = "";
		
		if( empty($_POST['pre_password']) )
			$_POST['pre_password'] = "";
		
		if( empty($_POST['description']) )
			$_POST['description'] = "";
		
		/**
		 * Parâmetros para gravar uma nova estrutura no DB.
		 */
		$params = array(
			'name' => $_POST['nome'],
			'site' => $_POST['categoria_chefe'],
			'module' => $_POST['modulo'],
			'author' => User::getInstance()->getId(),
			'fields' => $fields,
			'options' => array(
				'approval' => $_POST['approval'],
				'pre_password' => $_POST['pre_password'],
				'description' => $_POST['description'],
			),
		);

		if( $this->FlexFieldsSetup->createStructure($params) ){
			$this->render();
		}
		
		
		return true;
		/**
		 * CRIA ESTRUTURA (Aust)
		 *
		 * Verifica se consegue gravar a estrutura (provavelmente na tabela
		 * 'categorias').
		 */
		$status_insert = $this->FlexFieldsSetup->create( $params );
		if( $status_insert ){
			
			$status_setup[] = "Categoria criada com sucesso.";

			/**
			 * Cria string com o charset geral do projeto
			 */
			$cur_charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];

			/**
			 * Trata o nome da tabela para poder criar no db
			 */
			$tabela = $_SESSION['exPOST']['nome'];
			$tabela = str_replace(' ', '_', $tabela );
			$tabela = mb_strtolower(  $tabela, "UTF-8" );
			$tabela = RetiraAcentos( $tabela );
			
			/**
			 * TRATAMENTO DE CAMPOS
			 *
			 * Gera o SQL dos campos para salvar em 'flex_fields_config'
			 */
			/*
			 * Loop por cada campo para geração de SQL para salvar suas
			 * configurações em 'flex_fields_config'.
			 */
			$ordem = 0; // A ordem do campo
			$camposExistentes = array();
			$campoExiste = false;
			for($i = 0; $i < count($_POST['campo']); $i++) {
				$ordem++;

				$value = ''; // Por segurança
				$_POST['campo_descricao'][$i] = addslashes( $_POST['campo_descricao'][$i] );

				/**
				 * Verifica se o atual campo analisado está especificado.
				 *
				 * Se...
				 *	  - sim: faz os devidos tratamentos;
				 *	  - não: não faz nada.
				 */
				if( !empty($_POST['campo'][$i]) ){

					/**
					 * !!!ATENÇÃO!!!
					 *
					 * Altere condições abaixo para modificações do $_POST['campo_tipo']
					 */

					/**
					 * TIPAGEM FÍSICA DOS CAMPOS
					 *
					 * Define os tipos físicos dos dados.
					 *
					 * A tabela criada para o cadastro terá campos especificados
					 * na instalação do mesmo, e estes campos devem receber um
					 * formato adequado. Se é campo texto, será varchar, e assim
					 * por diante.
					 */
					/**
					 * Tipo Password
					 * Se o tipo de campo for pw, $campo_tipo=varchar(180)
					 */

					$tipagemFisicaDosCampos = array(
						"pw" => "varchar(180)",
						"arquivo" => "varchar(240)",
						"relacional_umparaum" => array(
							"tipo" => "int",
						),
						"relacional_umparamuitos" => array(
							"tipo" => "int",
						)
					);

					/**
					 * Se o tipo físico foi configurado anteriormente, salva de
					 * acordo, senão o tipo é aquele especificado no formulário
					 * de configuração.
					 */
					if ( array_key_exists( $_POST['campo_tipo'][$i], $tipagemFisicaDosCampos ) ){
						if( is_array( $tipagemFisicaDosCampos[ $_POST['campo_tipo'][$i] ] ) ){
							$campo_tipo = $tipagemFisicaDosCampos[ $_POST['campo_tipo'][$i] ]["tipo"];
						} else {
							$campo_tipo = $tipagemFisicaDosCampos[ $_POST['campo_tipo'][$i] ];
						}
					} else {
						$campo_tipo = $_POST['campo_tipo'][$i];
					}

					/*
					if($_POST['campo_tipo'][$i] == 'pw'){
						$campo_tipo = 'varchar(180)';
					}
					/**
					 * Se o tipo de campo for arquivo, $campo_tipo=varchar(240)
					 *
					elseif($_POST['campo_tipo'][$i] == 'arquivo'){
						$campo_tipo = 'varchar(240)';
					} elseif($_POST['campo_tipo'][$i] == 'relacional_umparaum'){
						$campo_tipo = 'int';
					} else {
						$campo_tipo = $_POST['campo_tipo'][$i];
					}
					 * 
					 */

					/**
					 * Retira acentuação e caracteres indesejados para criar
					 * campos nas tabelas
					 */
					$value = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_POST['campo'][$i]), 'UTF-8'));
					$campo = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_POST['campo'][$i]), 'UTF-8'));

					$campoExiste = false;
					if( in_array( $value, $camposExistentes ) )
						$campoExiste = true;

					$adicionalAtual = 2;
					while( $campoExiste ){
						$value = $value.'_'.$adicionalAtual;
						$campo = $campo.'_'.$adicionalAtual;
						
						if( !in_array( $value, $camposExistentes ) )
							$campoExiste = false;


						$adicionalAtual++;

					}
					unset($adicionalAtual);

					$camposExistentes[] = $value;
					$value = $value.' '. $campo_tipo;
					/**
					 * Se for data ou relacional, não tem charset
					 */
					if($campo_tipo <> 'date' AND $campo_tipo <> 'int')
						$value .= ' '. $cur_charset.' NOT NULL';

					/**
					 * Descrição: ajusta comentário do campo
					 */
					if(!empty($_POST['campo_descricao'][$i]))
						$value .=  ' COMMENT \''. $_POST['campo_descricao'][$i] .'\'';

					/**
					 * Ajusta vírgulas (se for o primeiro campo, não tem vírgula)
					 */
					if($i == 0){
						$campos = $value;
					} else {
						$campos .= ', '.$value;
					}
					

					/**
					 * CONFIGURAÇÃO DE CAMPOS
					 *
					 * Analisa campo por campo e grava informações diferenciadas
					 * sobre campos especiais (exemplo: password, arquivos)
					 */
					/**
					 * Password. tipo=campopw
					 */
					if($_POST['campo_tipo'][$i] == 'pw'){
						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem)
									VALUES
										('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1,'password',".$ordem.")";
					}
					/**
					 * Arquivos
					 */
					elseif($_POST['campo_tipo'][$i] == 'arquivo'){
						$cria_tabela_arquivos = TRUE;
						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem)
									VALUES
										('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1,'arquivo',".$ordem.")";
					}
					/**
					 * Campo relacional um-para-um
					 */
					elseif($_POST['campo_tipo'][$i] == 'relacional_umparaum'){
						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo)
									VALUES
										('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1, 'relacional_umparaum',".$ordem.", '".$_POST['relacionado_tabela_'.($i+1)]."', '".$_POST['relacionado_campo_'.($i+1)]."')";
					}
					/**
					 * Campo relacional um-para-um
					 */
					elseif($_POST['campo_tipo'][$i] == 'relacional_umparamuitos'){
						/*
						 * CRIA TABELA RELACIONAL
						 *
						 * Será criada agora uma tabela relacional.
						 *
						 * O nome da nova tabela será no formato
						 * tabelacadastro_camporelacional_tabelareferenciada
						 *
						 */

						$tabelaReferencia = $tabela;
						$tabelaRelacionada = $_POST['relacionado_tabela_'.($i+1)];
						/*
						 * verifica tamanho total do nome da nova tabela
						 * MYSQL máximo 64 caracteres
						 */
						$tMySQL = 63;

						/*
						 * Fora o tamanho do nome das tabelas, leva-se em consideração os sublinhados
						 */
						$tamanhoRestante = $tMySQL - strlen($tabelaReferencia) - strlen($tabelaRelacionada) - 2;

						/*
						 * Se só o nome das tabelas já foi maior que o total
						 * de 64 caracteres aceitos no MySQL, cria tabela sem
						 * o nome do campo, somente tabela_tabela
						 */
						if($tamanhoRestante == 0){
							$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabela."_".$tabelaRelacionada ), 'UTF-8'));
						}
						/*
						 * Se só o nome das tabelas já foi maior que 64
						 * caracteres, cria a string tabela_tabela e retira
						 * caracteres do final da string até ficar com 64
						 * caracteres.
						 */
						else if($tamanhoRestante < 0){
							$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabela."_".$tabelaRelacionada ), 'UTF-8'));
							$tabelasRelacionadasNome = substr($tabelasRelacionadasNome, 0, strlen($tabelasRelacionadasNome)-$tamanhoRestante);
						}
						/*
						 * Se tem espaço para o nome da tabela, mas o tamanho
						 * do nome do cmpo é maior que o possível, diminui
						 * o tamanho doo nome do campo.
						 */
						else if( strlen($campo) > $tamanhoRestante ) {
							$campoRelacionado = substr($campo, 0, $tamanhoRestante);
							$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabela."_".$campoRelacionado."_".$tabelaRelacionada ), 'UTF-8'));
						}
						/*
						 * Tudo está normal. Cria o nome da tabela sem
						 * problemas.
						 */
						else {
							$tabelasRelacionadasNome = RetiraAcentos(mb_strtolower(str_replace(' ', '_', $tabela."_".$campo."_".$tabelaRelacionada ), 'UTF-8'));
						}

						/**
						 * CREATE TABLE
						 *
						 * Tabela Relacional Um para Muitos
						 */
						$sql = 'CREATE TABLE '.$tabelasRelacionadasNome.'(
									id int auto_increment,
									'.$tabela.'_id int,
									'.$tabelaRelacionada.'_id int,
									blocked varchar(120),
									approved int,
									created_on datetime,
									updated_on datetime,
									PRIMARY KEY (id), UNIQUE id (id)

								) ';

						$createdRelational = $this->module->connection->exec($sql, 'CREATE_TABLE');
						//var_dump($createdRelational);
						if($createdRelational){
							$status_setup[] = 'Criação da tabela relacional um-para-muitos \''.$tabelasRelacionadasNome.'\' efetuada com sucesso.';
						} else {
							$status_setup[] = 'Erro ao criar tabela relacional um-para-muitos \''.$tabelasRelacionadasNome.'\' do campo <em>'.$campo.'</em>';
						}
						unset($sql);

						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo,referencia)
									VALUES
										('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1, 'relacional_umparamuitos',".$ordem.", '".$_POST['relacionado_tabela_'.($i+1)]."', '".$_POST['relacionado_campo_'.($i+1)]."', '$tabelasRelacionadasNome')";



						//$comentarios = 'Tabela relacional para as tabelas '.$tabela.' e '.$tabelaRelacionada;
						/*
						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem,ref_tabela,ref_campo)
									VALUES
										('tabela_relacional','".$campo."','".$tabelasRelacionadasNome."','$comentarios',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1, 'relacional_umparamuitos',".$ordem.", '".$_POST['relacionado_tabela_'.($i+1)]."', '".$_POST['relacionado_campo_'.($i+1)]."')";
						 *
						 */
						//unset($comentarios);

					}
					/**
					 * Campo normal, grava suas informações
					 */
					else {
						$sql_campos[] =
									"INSERT INTO flex_fields_config
										(tipo,chave,valor,comentario,categorias_id,autor,desativado,desabilitado,publico,restrito,aprovado,especie,ordem)
									VALUES
										('campo','".$campo."','".$_POST['campo'][$i]."','".$_POST['campo_descricao'][$i]."',".$status_insert.", ".$this->administrador->LeRegistro('id').",0,0,1,0,1,'string',".$ordem.")";
					}
				}
			}

			//pr($sql_campos);
			/**
			 * SQL
			 *
			 * Cria tabela
			 */
			$sql = 'CREATE TABLE '.$tabela.'(
						id int auto_increment,
						'.$campos.',
						blocked varchar(120) '.$cur_charset.',
						approved int,
						created_on datetime,
						updated_on datetime,
						PRIMARY KEY (id), UNIQUE id (id)

					) '.$cur_charset;
			//echo $sql;

			/**
			 * Se o tipo de campo é arquivo, cria outra tabela para os arquivos
			 */
			if( !empty( $cria_tabela_arquivos )
				AND $cria_tabela_arquivos == TRUE ){
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

			//exit();

			/**
			 * TABELA FÍSICA
			 */
			/*
			 * Executa QUERY na base de dados
			 *
			 * Se retornar sucesso, salva configurações gerais sobre o cadastro na tabela flex_fields_config
			 */
			pr( $sql );
			//var_dump($this->module);

			if( Connection::getInstance()->exec( $sql, 'CREATE_TABLE') ){
				$status_setup[] = "Tabela '".$tabela."' criada com sucesso!";

				/**
				 * Se há SQL para criação de tabela para arquivos
				 */
				if( !empty($sql_arquivos) AND $cria_tabela_arquivos == TRUE ){
					if($this->module->connection->exec($sql_arquivos, 'CREATE_TABLE')){
						$status_setup[] = 'Criação da tabela \''.$tabela.'_arquivos\' efetuada com sucesso!';
					} else {
						$status_setup[] = 'Erro ao criar tabela \''.$tabela.'_arquivos\'.';
					}

					$sql_conf_arquivos =
								"INSERT INTO
									flex_fields_config
									(tipo,chave,valor,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
								VALUES
									('structure','tabela_arquivos','".$tabela."_arquivos',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$this->administrador->LeRegistro('id').",0,0,1,0,1)
								";
					if($this->module->connection->exec($sql_conf_arquivos)){
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
									flex_fields_config
									(tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
								VALUES
									('config','approval','".$_SESSION['exPOST']['approval']."','Aprovação','bool',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$this->administrador->LeRegistro('id').",0,0,1,0,1)
								";
					if($this->module->connection->exec($sql_conf_2)){
						$status_setup[] = 'Configuração de aprovação salva com sucesso!';
					} else {
						$status_setup[] = 'Configuração de aprovação não foi salva com sucesso.';
					}

				// DESCRIÇÃO: salva o parágrafo introdutório ao formulário
					$sql_conf_2 =
								"INSERT INTO
									flex_fields_config
									(tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
								VALUES
									('config','description','".$_SESSION['exPOST']['description']."','Descrição','blob',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$this->administrador->LeRegistro('id').",0,0,1,0,1)
								";
					if($this->module->connection->exec($sql_conf_2)){
						$status_setup[] = 'Configuração de aprovação salva com sucesso!';
					} else {
						$status_setup[] = 'Configuração de aprovação não foi salva com sucesso.';
					}

				// salva configuração sobre pré-senha para o cadastro
					$sql_conf_2 =
								"INSERT INTO
									flex_fields_config
									(tipo,chave,valor,nome,especie,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
								VALUES
									('config','pre_password','".$_SESSION['exPOST']['pre_password']."','Pré-senha','string',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$this->administrador->LeRegistro('id').",0,0,1,0,1)
								";
					if($this->module->connection->exec($sql_conf_2)){
						$status_setup[] = 'Configuração de pré-senha salva com sucesso!';
					} else {
						$status_setup[] = 'Configuração de pré-senha não foi salva com sucesso.';
					}




				// configurações sobre a estrutura, como tabela a ser usada
				$sql_conf =
							"INSERT INTO
								flex_fields_config
								(tipo,chave,valor,categorias_id,adddate,autor,desativado,desabilitado,publico,restrito,aprovado)
							VALUES
								('structure','table','".RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']), 'UTF-8'))."',".$status_insert.", '".date('Y-m-d H:i:s')."', ".$this->administrador->LeRegistro('id').",0,0,1,0,1)
							";
				if($this->module->connection->exec($sql_conf)){
					$status_setup[] = 'Configuração da estrutura \''.RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']), 'UTF-8')).'\' salva com sucesso!';

					// número de erros encontrados
					$status_campos = 0;
					foreach ($sql_campos as $value) {
						if(!$this->module->connection->exec($value)){
							$status_campos++;
						}
					}
					if($status_campos == 0){
						$status_setup[] = 'Campos criados com sucesso!';
					} else {
						$status_setup[] = 'Erro ao criar campos';
					}
				} else {
					$status_setup[] = 'Erro ao salvar configuração da estrutura \''.RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']), 'UTF-8')).'\'.';
				}
			} else {
				$status_setup[] = 'Erro ao criar tabela \''.RetiraAcentos(mb_strtolower(str_replace(' ', '_', $_SESSION['exPOST']['nome']), 'UTF-8')).'\'.';
			}

		}

		echo '<ul>';
		foreach ($status_setup as $value){
			echo '<li>'.$value.'</li>';
		}
		echo '</ul>';


		$this->autoRender = false;
	}

}
?>