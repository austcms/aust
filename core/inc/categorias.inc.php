<?php

switch($_GET['action']){
	case "gravar" : {
                        /**
                         * Faz a gravação
                         */
						?>
						<h2>Gravar</h2>
						<?php

						$texto = $_POST['frmdescricao'];
						$texto = str_replace("\"","\"", $texto);
						$texto = str_replace("'","\'", $texto);


						if($_POST['frmclasse'] == "categoria"){

                            /**
                             * Faz um loop para descobrir qual é o patriarca
                             * desta nova categoria a ser cadastrada.
                             */
							$i = 0;
							$subordinadoidtmp = $_POST['frmsubordinadoid'];

							while( $i < 1 ){
								$sql = "SELECT
											id, nome, subordinadoid, classe
										FROM
											".$_GET['section']."
										WHERE
											id='$subordinadoidtmp'
										";

								$query = $conexao->query($sql);
                                $dados = $query[0];

								if($dados['classe'] == "estrutura"){
									$patriarca = $dados['nome'];
                                    $tipo = $aust->LeModuloDaEstrutura($dados['id']);;
									$i++;
								} else {
									$subordinadoidtmp = $dados['subordinadoid'];
								}
                                $tipo_legivel = $aust->LeModuloDaEstruturaLegivel($dados['id']);

							}
						}

						$sql = "INSERT INTO ".$_GET['section']." (nome,patriarca,subordinadoid,descricao,classe,tipo,tipo_legivel,autor)
                                VALUES('{$_POST['frmnome']}','$patriarca','{$_POST['frmsubordinadoid']}','$texto','{$_POST['frmclasse']}','{$tipo}','{$tipo_legivel}','".$administrador->LeRegistro('id')."')";

                        /**
                         * Se insere com sucesso
                         */
                        if($conexao->exec($sql)){
                            $resultado = TRUE;
                        } else {
                            $resultado = FALSE;
                        }
						
						$lastInsertId = $conexao->lastInsertId();
						/**
						 * Se uma imagem foi enviada, faz todo o processamento
						 */
						if( !empty($_FILES['arquivo']) &&
						 	!empty($_FILES['arquivo']['name']) )
						{

							$file = $_FILES['arquivo'];

							$imageHandler = Image::getInstance();
							$aust = Aust::getInstance();
							$user = User::getInstance();

							if( !empty($lastInsertId) )
								$aust->deleteNodeImages( $lastInsertId );

							$newFile = $imageHandler->resample($file);
							$finalName = $imageHandler->upload($newFile);

							$finalName['systemPath'] = addslashes($finalName['systemPath']);

							$sql = "INSERT INTO 
									austnode_images
									(
									node_id, 
									file_size, systempath, file_name, original_file_name,
									file_type, file_ext, 
									created_on, updated_on, admin_id
									)
									VALUES
									('".$lastInsertId."',
									'".$newFile['size']."', '".$finalName['systemPath']."', '".$finalName['new_filename']."', '".$newFile['name']."',
									'".$newFile['type']."','".$finalName['extension']."',
									'".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', ".$user->getId().")";

							// insere no DB
							if ($conexao->exec($sql)){
							    $status_imagem = true;
							} else {
							    $status_imagem = false;
							}

						}


                        if($resultado){
                            $status['classe'] = 'sucesso';
                            $status['mensagem'] = '<strong>Sucesso: </strong> Informações salvas com sucesso!';
                        } else {
                            $status['classe'] = 'insucesso';
                            $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro desconhecido, tente novamente.';
                        }
                        EscreveBoxMensagem($status);
                        ?>
						<p style="margin-top: 15px;">
							<a href="adm_main.php?section=<?php echo $_GET['section'];?>"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
						</p>
                        <?php
						break;
	}
	case "update" : {
                        include(INC_DIR.$_GET['section'].'.inc/update.php');
						break;
	}
	case "new" : {
                        include(INC_DIR.$_GET['section'].'.inc/new.php');
                        break;
	}
	case "edit_form" : {
                        include(INC_DIR.$_GET['section'].'.inc/edit_form.php');
						break;
	}
    /*
     * Lista as categorias
     */
	case "list_content" : {
                        include(INC_DIR.$_GET['section'].'.inc/list_content.php');
						break;
	}
	default : {
						?>
						<?php
						if(!empty($_POST['categoria_chefe']) AND $_POST['categoria_chefe'] <> ''){
							if($aust->Instalar($_POST['nome'], '', 'categoria-chefe')){
								?>
                                <h2 class="ok">Categoria-chefe incluida com sucesso!</h2>
                                <p>Simples assim. Inserimos a primeira categoria com sucesso.</p>
                                <?php
							} else {
								?>
                                <h2 class="falha">Ops... Ocorreu um problema!</h2>
                                <p>Simples assim. Inserimos a primeira categoria com sucesso.</p>
                                <?php
							}
						}
						
						
						if($aust->Instalado()){
						?>
                            <h2>Gerenciar Categorias</h2>
                            <p>
                                <strong>O que são Categorias?</strong> Todo o conteúdo do site está dividido em sessões, como em um jornal.
                                Estas divisões chamam-se <em>Categorias</em>.
                            </p>
                            <p>
                                <strong>ATENÇÃO:</strong> Aqui você pode criar e editar as categorias do site. Se você não sabe o que está fazendo,
                                contacte um administrador. Qualquer erro poderá fazer o site parar de funcionar.
                            </p>
                            <p>
                                Selecione abaixo o que você deseja fazer:
                            </p>
                            <div class="action_options">
                                <ul>
                                    <li>
                                        <?php
                                        if(0==0){
                                        ?>
                                        <a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=new">Inserir nova categoria</a>
                                        <?php
                                        }
                                        ?>
                                    </li>
                                    <li>
                                        <a href="adm_main.php?section=<?php echo $_GET['section'];?>&action=list_content">
                                        Ver e Editar estruturas do site
                                        </a>
                                    </li>
                                </ul>
                            
                            </div>
                            <?php if($administrador->LeRegistro('tipo') == 'Webmaster'){ ?>
                                <h2 class="restrito">Webmaster</h2>
                                <p>
                                A seguir, opções que somente webmasters podem modificar.
                                </p>
                                <div class="links">
                                    <ul>
                                        <li>
                                            <a href="adm_main.php?section=conf_modulos">Gerenciar Módulos e Estruturas</a>
                                        </li>
                                    </ul>
                                </div>
                        	<?php } ?>
						
                        <?php
						} else {
						?>
                            <h2 class="falha">Nenhuma categoria encontrada!</h2>
                            <p>
                            Não foi encontrada nenhuma categoria. Provavelmente você está <strong>instalando</strong> o sistema.
                            </p>
                            <p>
                            Crie abaixo a <strong>categoria-chefe</strong> do site.
                            </p>
                            
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>" class="simples">
                            	<h3>Formulário</h3>
                            	<div class="campo">
                                    <label>Nome da categoria-chefe:</label>
                                    <div class="input">
                                        <input type="text" name="nome" />
                                        <p class="explanation">Não use maiúsculas nem espaços nem acentos.</p>
                                        <p class="explanation">Ex.: site1; site2; juridico</p>
                                    </div>
                                </div>
                            	<input type="submit" name="categoria_chefe" value="Enviar!" class="submit" />
                            </form>
                        
                        
                        
                        <?php
						}
						?>
						<?php
	}
}
?>
<br />
