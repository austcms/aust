<?php
/**
 * Controller principal deste módulo
 *
 * @package ModController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */

class ModController extends ModsController
{

    var $helpers = array('Form');
	public $doRender = true;

    public function listing(){

        $austNode = $_GET['aust_node'];
        $this->set('austNode', $austNode);

        $categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
        $categorias[$_GET['aust_node']] = 'Estrutura';
        $param = array(
            'categorias' => $categorias,
            'metodo' => 'listing',
            '' => ''
        );

        $sql = $this->modulo->loadSql($param);
        //echo '<br><br>'.$sql .'<br>';

        $resultado = $this->modulo->connection->query($sql, "ASSOC");
        $this->set('resultado', $resultado);

        $fields = count($resultado);
        $this->set('fields', $fields);
        if( $this->modulo->getStructureConfig("has_search") ){
            $this->set("search_fields", $this->modulo->getFields(false));
        }
        //$this->autoRender= false;
    }

    public function actions(){
//		echo 'hey';
//		return true;
    }

    /**
     * formulário
     */

    public function form(){

    }

    /**
     * FORMULÁRIO DE INSERÇÃO
     */
    public function create($params = array() ){
        /**
         * Verifica se há parâmetros
         */
		$this->modulo->{"teste"} = "hey";
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
        $estrutura = $this->aust->pegaInformacoesDeEstrutura( $this->austNode );
        /**
         * Pega informações sobre o cadastro na tabela cadastro_conf
         */
        $infoCadastro = $this->modulo->pegaInformacoesCadastro( $this->austNode );
        /**
         * Toma informações sobre a tabela física do cadastro
         */
        $infoTabelaFisica = $this->modulo->getPhysicalFields(
            array(
                "tabela" => $infoCadastro["estrutura"]["tabela"]["valor"],
                "by" => "Field",
            )
        );

        $divisorTitles = $this->modulo->loadDivisors();
        $this->set('divisorTitles', $divisorTitles);
        
        $campos = $infoCadastro["campo"];
        /**
         * SE $W EXISTE
         *
         * Se $W existe, significa que esta instancia é uma edição de conteúdo.
         * Agora será feita uma busca na respectiva tabela para tomar os
         * dados gravados e escrevê-los nos respectivos inputs.
         */

            if( !empty($w) ){
                $sql = "SELECT
                            ".implode(",", array_keys($campos))."
                        FROM
                            ".$infoCadastro["estrutura"]["tabela"]["valor"]."
                        WHERE
                            id=".$w."
                        ";
                $dados = $this->connection->query($sql, "ASSOC");
                $dados = $dados[0];
		        $this->set('w', $w);

            } else {
		        $this->set('w', false);
			}
            
        $i = 0;
        /**
         * Loop para organizar a disposição dos campos
         */
        foreach ( $campos as $chave=>$valor ){
            
            $dados['campos'] = $valor;
            /*
             * Mostra inputs automaticamente.
             *
             * Engine:
             *      Pega os registros da tabela do cadastro e os registros
             *      da tabela cadastros_conf e verifica cada um, tentando
             *      coincindi-los. Se algum campo não consta na tabela cadastros_conf
             *      não é mostrado seu input, pois provavelmente é um campo
             *      de configuração.
             *
             */
            $type  = $valor["especie"];
            if( !empty($valor['valor']) ){

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
                    if( array_key_exists($valor["chave"], $dados) ){
                        $camposForm[ $valor["chave"] ]["valor"] = $dados[ $valor["chave"] ];
                    }
                }
                $camposForm[ $valor["chave"] ]["label"] = $valor['valor'];
                $camposForm[ $valor["chave"] ]["nomeFisico"] = $valor['chave'];
                $camposForm[ $valor["chave"] ]["comentario"] = $valor['comentario'];
                $camposForm[ $valor["chave"] ]["tipo"]["especie"] = $valor["especie"];
                $camposForm[ $valor["chave"] ]["tipo"]["referencia"] = $valor["referencia"];
                $camposForm[ $valor["chave"] ]["tipo"]["tabelaReferencia"] = $valor["ref_tabela"];
                $camposForm[ $valor["chave"] ]["tipo"]["refParentField"] = $valor["ref_parent_field"];
                $camposForm[ $valor["chave"] ]["tipo"]["refChildField"] = $valor["ref_child_field"];
                $camposForm[ $valor["chave"] ]["tipo"]["tabelaReferenciaCampo"] = $valor["ref_campo"];
                $camposForm[ $valor["chave"] ]["tipo"]["tipoFisico"] = $infoTabelaFisica[ $valor["chave"] ]["Type"];

				/*
				 * Campo Images
				 */
				if( $valor['especie'] == 'images' ){
	                $camposForm[ $valor["chave"] ]["tipo"]["tabelaReferencia"] = $infoCadastro['estrutura']["table_images"]['valor'];
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
        $this->set('formIntro', $infoCadastro["config"]["descricao"]["valor"]);
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
				$this->modulo->saveImageDescription( $data['description'], $imageId );
			}

			// link 
			if( !empty($data['link']) ){
				$data = reset( $this->data );
				$this->modulo->saveImageLink( $data['link'], $imageId );
			}
			
			if( !empty($data['secondary_image']) ){
				$options = array(
					'reference' => $imageId,
					'type' => 'secondary',
				);
				$this->modulo->deleteSecondaryImagesById($imageId);
				$images['table'][$_POST['image_field']] = $data['secondary_image'];
				$this->modulo->uploadAndSaveImages( $images, $w, $options );
			}
				
		}
		
		
		if( !empty($_GET['deleteimage']) ){
			$deletedImage = $this->modulo->deleteImage( $_GET['deleteimage'] );
		}

		if( !empty($_GET['deletefile']) ){
			$deletedFile = $this->modulo->deleteFile( $_GET['deletefile'] );
		}
		
		$this->doRender = false;
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

        $infoCadastro = $this->modulo->pegaInformacoesCadastro( $this->austNode );

        /*
         * UPDATE?
         */
        if( !empty($this->data[ $infoCadastro["estrutura"]["tabela"]["valor"] ]["id"] ) ){
            $w = $this->data[ $infoCadastro["estrutura"]["tabela"]["valor"]] [ "id"];
        }

        /**
         * Toma informações sobre a tabela física do cadastro
         */
        $infoTabelaFisica = $this->modulo->getPhysicalFields(
            array(
                "tabela" => $infoCadastro["estrutura"]["tabela"]["valor"],
                "by" => "Field",
            )
        );

        $campos = $infoCadastro["campo"];
		$this->modulo->austNode = $this->austNode;
		$this->modulo->fields = $campos;
        $relational = array();
		$images = array();


        if( $this->data ){
			$this->modulo->data = $this->data;
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
			$this->modulo->setRelationalData(); // ajusta inclusive imagens
			$this->data = $this->modulo->data;
			$images = $this->modulo->images;
			$files = $this->modulo->files;
			
			// insert date
			$table = reset(array_keys($this->data));

			if( empty($w) )
				$this->data[$table]['created_on'] = date("Y-m-d H:i:s");
			else
				$this->data[$table]['updated_on'] = date("Y-m-d H:i:s");

			/*
			 *		2) Salva dados principais (não relacionados);
			 */
            $resultado = $this->model->save($this->data);
            if( !empty($w) AND $w > 0 )
                $lastInsertId = $w;
            else
                $lastInsertId = $this->modulo->connection->lastInsertId();

			/*
		 	 *		3) Salva dados físicos (como arquivos).
			 */
			$this->modulo->uploadAndSaveImages($images, $lastInsertId);
			$this->modulo->uploadAndSaveFiles($files, $lastInsertId);
			
			/*
			 *		4) Salva dados relacionados no DB.
			 */
			$relational = $this->modulo->relationalData;
			

            if( (
				!empty($relational) ||
				!empty($this->modulo->toDeleteTables)
				)
				AND
 				!empty($lastInsertId) ){

                unset($sql);
                foreach( $relational as $field=>$dados ){
					foreach( $dados as $tabela=>$dados ){
	                    foreach($dados as $campo=>$valor){
	
							if( !empty($infoCadastro['campo'][$field]['ref_parent_field']) )
								$ref_field = $infoCadastro['campo'][$field]['ref_parent_field'];
							else
								$ref_field = $infoCadastro["estrutura"]["tabela"]["valor"]."_id";
						
						
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
             	foreach( $this->modulo->toDeleteTables as $field=>$value ){
                	foreach( $value as $key=>$value ){
					
						if( !empty($infoCadastro['campo'][$field]['ref_parent_field']) )
							$ref_field = $infoCadastro['campo'][$field]['ref_parent_field'];
						else
							$ref_field = $infoCadastro["estrutura"]["tabela"]["valor"]."_id";

	                    $sql = "DELETE FROM
	                                $key
	                            WHERE
	                                ".$ref_field."='$w'
	                                ";
	                    unset($sql);
					}
                }

				/*
				 * Começa a salvar cada um
				 */
             	foreach( $relational as $field=>$dados ){
	                foreach( $dados as $tabela=>$dados ){

	                    foreach( $dados as $campo=>$valor ){

	                        /*
	                         * Múltiplos Inserts
	                         */
	                        if( is_int($campo) ){
	                            //pr($valor);
	                            foreach( $valor as $multipleInsertsCampo=>$multipleInsertsValor ){
	                                $camposStrMultiplo[] = $multipleInsertsCampo;
	                                $valorStrMultiplo[] = $multipleInsertsValor;
	                            }

	                            /*
	                             * Insere no DB os Checkboxes marcados
	                             */
	                            $tempSql = "INSERT INTO
	                                            ".$tabela."
	                                                (".implode(",", $camposStrMultiplo).")
	                                        VALUES
	                                            ('".implode("','", $valorStrMultiplo)."')
	                                        ";
	                            /**
	                             * SQL deste campo
	                             */
	                            $sql[] = $tempSql;

	                            unset($valorStrMultiplo);
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
                        $this->modulo->connection->exec($uniqueSql);
                    }
                }
            }
            
        }

		/*
		 * EXCLUI IMAGENS EXTRAS
		 */
		foreach( $images as $imageFields ){
			foreach( $imageFields as $field=>$values ){
				$postedImageFields[] = $field;
			}
		}
		$this->modulo->deleteExtraImages($lastInsertId, $postedImageFields);
		
		/*
		 * EXCLUI ARQUIVOS EXTRAS
		 */
		foreach( $files as $fileFields ){
			foreach( $fileFields as $field=>$values ){
				$postedFileFields[] = $field;
			}
		}
		$this->modulo->deleteExtraFiles($lastInsertId, $postedFileFields);
		
        $this->set('resultado', $resultado);

    }
    
}
?>