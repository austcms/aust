<?php
/**
 * Controller principal deste módulo
 *
 * @since v0.1.6 06/07/2009
 */

class ModController extends ModActionController
{

	public $helpers = array('Form');
	public $doRender = true;

	public function listing(){

		$austNode = $_GET['aust_node'];
		$this->set('austNode', $austNode);

		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';
		$param = array(
			'categorias' => array($_GET['aust_node'] => ""),
			'metodo' => 'listing',
		);
	
		$sql = $this->module->loadSql($param);

		$resultado = $this->module->connection->query($sql, "ASSOC");
		$this->set('resultado', $resultado);

		$fieldsCount = count($resultado);
		$this->set('fieldsCount', $fieldsCount);

		$fieldsConfiguration = $this->module->getFields(null, true);
		$this->set('fieldsConfiguration', $fieldsConfiguration);

		if( $this->module->getStructureConfig("has_search") ){
			$this->set("search_fields", $this->module->getFields(false));
		}
	}

	public function actions(){

	}

	public function form(){

	}

	/**
	 * Create Form
	 */
	public function create($params = array() ){
		/**
		 * Verifica se há parâmetros
		 */
		$this->module->{"teste"} = "hey";
		if( !empty($params) ){
			$w = ( empty($params["w"]) ? "" : $params["w"] );
		}

		/**
		 * COLETA DE INFORMAÇÕES
		 */
		/**
		 * Pega todas as informações sobre a estrutura (austNode) atual
		 * em formato array
		 */
		$estrutura = Aust::getInstance()->getStructureById( $this->austNode );
		/**
		 * Pega informações sobre o cadastro na tabela cadastro_conf
		 */
		$infoCadastro = $this->module->pegaInformacoesCadastro( $this->austNode );
		/**
		 * Toma informações sobre a tabela física do cadastro
		 */
		$infoTabelaFisica = $this->module->getPhysicalFields(
			array(
				"table" => $infoCadastro["structure"]["table"]["value"],
				"by" => "Field",
			)
		);

		$divisorTitles = $this->module->loadDivisors();
		$this->set('divisorTitles', $divisorTitles);
		
		$campos = $infoCadastro["campo"];
		/**
		 * SE $W EXISTE
		 *
		 * Se $W existe, significa que esta instancia é uma edição de conteúdo.
		 * Agora será feita uma busca na respectiva tabela para tomar os
		 * dados gravados e escrevê-los nos respectivos inputs.
		 */
			$nodeId = $this->austNode;
			if( !empty($w) ){
				$sql = "SELECT
							node_id, ".implode(",", array_keys($campos))."
						FROM
							".$infoCadastro["structure"]["table"]["value"]."
						WHERE
							id=".$w."
						";
				$dados = Connection::getInstance()->query($sql, "ASSOC");
				$dados = $dados[0];
				$this->set('w', $w);
				$this->set('nodeId', $dados['node_id']);

			} else {
				$this->set('w', false);
			}
			
		$i = 0;
		/**
		 * Loop para organizar a disposição dos campos
		 */
		foreach ( $campos as $chave=>$value ){
			
			$dados['campos'] = $value;
			/*
			 * Mostra inputs automaticamente.
			 *
			 * Engine:
			 *	  Pega os registros da tabela do cadastro e os registros
			 *	  da tabela flex_fields_config e verifica cada um, tentando
			 *	  coincindi-los. Se algum campo não consta na tabela flex_fields_config
			 *	  não é mostrado seu input, pois provavelmente é um campo
			 *	  de configuração.
			 *
			 */
			$type  = $value["specie"];
			if( !empty($value['value']) ){

				/**
				 * LEVANTAMENTO DE INFORMAÇÕES SOBRE CAMPOS
				 *
				 * Guarda todos os dados importantes sobre os campos
				 * para envio à view
				 */
				 
				/**
				 * Se há valores carregados do DB para edição
				 */
				if( !empty($dados) ){
					if( array_key_exists($value["property"], $dados) ){
						$camposForm[ $value["property"] ]["value"] = $dados[ $value["property"] ];
					}
				}
				$camposForm[ $value["property"] ]["label"] = $value['value'];
				$camposForm[ $value["property"] ]["nomeFisico"] = $value['property'];
				$camposForm[ $value["property"] ]["comentario"] = $value['commentary'];
				$camposForm[ $value["property"] ]["tipo"]["especie"] = $value["specie"];
				$camposForm[ $value["property"] ]["tipo"]["referencia"] = $value["reference"];
				$camposForm[ $value["property"] ]["tipo"]["tabelaReferencia"] = $value["ref_table"];
				$camposForm[ $value["property"] ]["tipo"]["refParentField"] = $value["ref_parent_field"];
				$camposForm[ $value["property"] ]["tipo"]["refChildField"] = $value["ref_child_field"];
				$camposForm[ $value["property"] ]["tipo"]["tabelaReferenciaCampo"] = $value["ref_field"];
				$camposForm[ $value["property"] ]["tipo"]["tipoFisico"] = $infoTabelaFisica[ $value["property"] ]["Type"];

				/*
				 * Campo Images
				 */
				if( $value['specie'] == 'images' ){
					$camposForm[ $value["property"] ]["tipo"]["tabelaReferencia"] = $infoCadastro['structure']["table_images"]['value'];
				}
			}

			$i++;
		} // Fim do loop

		/**
		 * ENVIA DADOS PARA O VIEW
		 */
		/**
		 * Informações sobre o cadastro completo
		 */
		$this->set('infoCadastro', $infoCadastro);
		$this->set('formIntro', $infoCadastro["config"]["description"]["value"]);
		/**
		 * Lança as informações sobre campos para o view
		 */
		$this->set('camposForm', $camposForm);

		if( $this->doRender )
			$this->render('form');
		else
			$this->render(false);

	}

	public function edit(){

		$w = $_GET["w"];
		$params = array(
			"w" => $_GET["w"]
		);

		// IMAGES
		if( !empty($_POST['type']) AND $_POST['type'] = 'image_options' ){
			$data = $this->data;
			$imageId = $_POST['image_id'];
			
			// os keys são os campos (description, secondary_image etc)
			$data = reset( $this->data );
			
			// descrição
			if( !empty($data['description']) ){
				$data = reset( $this->data );
				$this->module->saveImageDescription( $data['description'], $imageId );
			}

			// link 
			if( !empty($data['link']) ){
				$data = reset( $this->data );
				$this->module->saveImageLink( $data['link'], $imageId );
			}
			
			if( !empty($data['secondary_image']) ){
				$options = array(
					'reference' => $imageId,
					'type' => 'secondary',
				);
				$this->module->deleteSecondaryImagesById($imageId);
				$images['table'][$_POST['image_field']] = $data['secondary_image'];
				$this->module->uploadAndSaveImages( $images, $w, $options );
			}
				
		}
		
		
		if( !empty($_GET['deleteimage']) ){
			$deletedImage = $this->module->deleteImage( $_GET['deleteimage'] );
		}

		if( !empty($_GET['deletefile']) ){
			$deletedFile = $this->module->deleteFile( $_GET['deletefile'] );
		}
		
		$this->doRender = false;
		$this->autoRender = false;
		$this->create($params);
		$this->render('form');
	}

	public function printing(){

		$params = array(
			"w" => $_GET["w"]
		);
		$this->doRender = false;
		$this->create($params);
		$this->render('printing');
		//$this->render('form');
	}

	/**
	 * save()
	 *
	 * Salva os dados enviados de um formulário do módulo Cadastro
	 *
	 * Campos Images são inseridos ao final, pois eles precisam saber qual
	 * é o lastInsertId.
	 *
	 * O processo acontece na seguinte ordem:
	 *
	 * 		1) Prepara dados relacionados para salvá-los;
	 *		2) Salva dados principais (não relacionados);
	 *		3) Salva dados físicos (como arquivos).
	 *		4) Salva dados relacionados no DB.
	 */
	public function save(){

		$infoCadastro = $this->module->pegaInformacoesCadastro( $this->austNode );

		/*
		 * UPDATE?
		 */
		if( !empty($this->data[ $infoCadastro["structure"]["table"]["value"] ]["id"] ) ){
			$w = $this->data[ $infoCadastro["structure"]["table"]["value"]] [ "id"];
		}

		/**
		 * Toma informações sobre a tabela física do cadastro
		 */
		$infoTabelaFisica = $this->module->getPhysicalFields(
			array(
				"table" => $infoCadastro["structure"]["table"]["value"],
				"by" => "Field",
			)
		);

		$campos = $infoCadastro["campo"];
		$this->module->austNode = $this->austNode;
		$this->module->fields = $campos;
		$relational = array();
		$images = array();


		if( $this->data ){
			$this->module->data = $this->data;
			/*
			 * PREPARA DADOS PARA POSTERIOR SALVAMENTO DE DADOS
			 * RELACIONADOS
			 *
			 * O processo acontece na seguinte ordem:
			 *
			 * 		1) Prepara dados relacionados para salvá-los;
			 *		2) Salva dados principais (não relacionados);
			 *		3) Salva dados físicos (como arquivos).
			 *		4) Salva dados relacionados no DB.
			 */
			
		 	/*
		 	 * 		1) Prepara dados relacionados para salvá-los;
			 */
			$this->module->setRelationalData(); // ajusta inclusive imagens
			$this->data = $this->module->sanitizeData($this->module->data);
			$images = $this->module->images;
			$files = $this->module->files;
			
			// insert date
			$table = array_keys($this->data);
			$table = reset($table);

			if( empty($w) )
				$this->data[$table]['created_on'] = date("Y-m-d H:i:s");
			else
				$this->data[$table]['updated_on'] = date("Y-m-d H:i:s");

			/*
			 *		2) Salva dados principais (não relacionados);
			 */
			# Model
			$resultado = Model::getInstance()->save($this->data);
			if( !empty($w) AND $w > 0 )
				$lastInsertId = $w;
			else
				$lastInsertId = $this->module->connection->lastInsertId();

			/*
		 	 *		3) Salva dados físicos (como arquivos).
			 */
			$this->module->uploadAndSaveImages($images, $lastInsertId);
			$this->module->uploadAndSaveFiles($files, $lastInsertId);
			
			/*
			 *		4) Salva dados relacionados no DB.
			 */
			$relational = $this->module->relationalData;
			

			if( (
				!empty($relational) ||
				!empty($this->module->toDeleteTables)
				)
				AND
 				!empty($lastInsertId) ){

				unset($sql);
				foreach( $relational as $field=>$dados ){
					foreach( $dados as $tabela=>$dados ){
						foreach($dados as $campo=>$value){
	
							if( !empty($infoCadastro['campo'][$field]['ref_parent_field']) )
								$ref_field = $infoCadastro['campo'][$field]['ref_parent_field'];
							else
								$ref_field = $infoCadastro["structure"]["table"]["value"]."_id";
						
						
							$relational[$field][$tabela][$campo][$ref_field] = $lastInsertId;
						}
					}
				}
				
				/*
				 * Exclui todos os dados expecificados em $toDeleteTables para salvá-los novamente
				 * 
				 * No caso de Checkboxes, só devem existir os que foram selecionados
				 * no formulário. Assim, é necessário excluir todos os registros antes de 
				 * começar a salvar o que foi enviado.
				 *
				 */
			 	foreach( $this->module->toDeleteTables as $field=>$value ){
					foreach( $value as $key=>$value ){
					
						if( !empty($infoCadastro['campo'][$field]['ref_parent_field']) )
							$ref_field = $infoCadastro['campo'][$field]['ref_parent_field'];
						else
							$ref_field = $infoCadastro["structure"]["table"]["value"]."_id";

						$sql = "DELETE FROM
									$key
								WHERE
									".$ref_field."='$w'
									";
						Connection::getInstance()->exec($sql);
						unset($sql);
					}
				}

				/*
				 * Começa a salvar cada um
				 */
			 	foreach( $relational as $field=>$dados ){
					foreach( $dados as $tabela=>$dados ){

						foreach( $dados as $campo=>$value ){

							/*
							 * Múltiplos Inserts
							 */
							if( is_int($campo) ){
								//pr($value);
								foreach( $value as $multipleInsertsCampo=>$multipleInsertsValor ){
									$camposStrMultiplo[] = $multipleInsertsCampo;
									$valueStrMultiplo[] = $multipleInsertsValor;
								}

								/*
								 * Insere no DB os Checkboxes marcados
								 */
								$tempSql = "INSERT INTO
												".$tabela."
													(".implode(",", $camposStrMultiplo).")
											VALUES
												('".implode("','", $valueStrMultiplo)."')
											";
								/**
								 * SQL deste campo
								 */
								$sql[] = $tempSql;

								unset($valueStrMultiplo);
								unset($camposStrMultiplo);
								unset($tempSql);

							}
						}
					}
				}
			}

			if( !empty($sql) ){
				if( is_array($sql) ){
					foreach( $sql as $uniqueSql ){
						$this->module->connection->exec($uniqueSql);
					}
				}
			}
			
		}

		/*
		 * EXCLUI IMAGENS EXTRAS
		 */
		if( !empty($images) ){
			foreach( $images as $imageFields ){
				foreach( $imageFields as $field=>$values ){
					$postedImageFields[] = $field;
				}
			}
			if( !empty($postedImageFields) )
				$this->module->deleteExtraImages($lastInsertId, $postedImageFields);
		}		
		
		/*
		 * EXCLUI ARQUIVOS EXTRAS
		 */
		if( !empty($files) ){
			foreach( $files as $fileFields ){
				foreach( $fileFields as $field=>$values ){
					$postedFileFields[] = $field;
				}
			}
			
			if( !empty($postedFileFields) )
				$this->module->deleteExtraFiles($lastInsertId, $postedFileFields);
		}
		
		$this->set('resultado', $resultado);

	}
	
}
?>