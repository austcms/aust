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

					    $params = array(
					        'father' => $_POST["frmsubordinadoid"],
					        'name' => $_POST['frmnome'],
							'description' => $_POST["frmdescricao"],
					        'author' => User::getInstance()->getId(),
					    );

					    $resultado = Aust::getInstance()->create($params);
						
						$lastInsertId = $resultado;
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
								Aust::getInstance()->deleteNodeImages( $lastInsertId );

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
							if (Connection::getInstance()->exec($sql)){
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
							if(Aust::getInstance()->createSite($_POST['nome'], '')){
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
						
						
						if(Aust::getInstance()->Instalado()){
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
                            <?php if(User::getInstance()->LeRegistro('tipo') == 'Webmaster'){ ?>
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
