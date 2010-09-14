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
        $infoTabelaFisica = $this->modulo->pegaInformacoesTabelaFisica(
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

		if( !empty($_POST['type']) AND $_POST['type'] = 'image_options' ){
			$data = $this->data;
			$imageId = $_POST['image_id'];
			
			$data = reset( $this->data );
			if( !empty($data['description']) )
				$data = reset( $this->data );
				$this->modulo->saveImageDescription( $data['description'], $imageId );
			
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
        $infoTabelaFisica = $this->modulo->pegaInformacoesTabelaFisica(
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
			
			/*
			 *		4) Salva dados relacionados no DB.
			 */
			$relational = $this->modulo->relationalData;
            if( !empty($relational) AND !empty($lastInsertId) ){

                unset($sql);
                foreach( $relational as $tabela => $dados ){
                    foreach($dados as $campo=>$valor){
                        $relational[$tabela][$campo][$infoCadastro["estrutura"]["tabela"]["valor"]."_id"] = $lastInsertId;
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
                foreach( $this->modulo->toDeleteTables as $key=>$value ){
                    $sql = "DELETE FROM
                                $key
                            WHERE
                                ".$infoCadastro["estrutura"]["tabela"]["valor"]."_id='$w'
                                ";
                    $this->modulo->connection->exec($sql);
                    unset($sql);
                }

				/*
				 * Começa a salvar cada um
				 */
                foreach( $relational as $tabela => $dados ){

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

            if( !empty($sql) ){
                if( is_array($sql) ){
                    foreach( $sql as $uniqueSql ){
                        $this->modulo->connection->exec($uniqueSql);
                    }
                }
            }
            
        }

        $this->set('resultado', $resultado);
        /**
         *
         * GRAVAR
         * Variáveis necessárias:
         *      $_POST -> contendo dados provenientes de formulário
         */

        /*
        $c = 0;
        if(!empty($_POST)){

            $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

            foreach($_POST as $key=>$valor){
                // se o argumento $_POST contém 'frm' no início
                if(strpos($key, 'frm') === 0){
                    $sqlcampo[] = str_replace('frm', '', $key);
                    $sqlvalor[] = $valor;
                    // ajusta os campos da tabela nos quais serão gravados dados
                    $valor = addslashes($valor);
                    if($_POST['metodo'] == 'criar'){
                        if($c > 0){
                            $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                            $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                        } else {
                            $sqlcampostr = str_replace('frm', '', $key);
                            $sqlvalorstr = "'".$valor."'";
                        }
                    } else if($_POST['metodo'] == 'editar'){
                        if($c > 0){
                            $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                        } else {
                            $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                        }
                    }

                    $c++;
                }
            }




            if($_POST['metodo'] == 'criar'){
                $sql = "INSERT INTO
                            ".$this->modulo->tabela_criar."
                            ($sqlcampostr)
                        VALUES
                            ($sqlvalorstr)
                            ";


                $h1 = 'Criando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
            } else if($_POST['metodo'] == 'editar'){
                $sql = "UPDATE
                            ".$this->modulo->tabela_criar."
                        SET
                            $sqlcampostr
                        WHERE
                            id='".$_POST['w']."'
                            ";
                $h1 = 'Editando: '.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
            }
            //echo $sql;
            $query = $this->modulo->connection->exec($sql);
            if($query){
                $resultado = TRUE;

                // se estiver criando um registro, guarda seu id para ser usado por módulos embed a seguir
                if($_POST['metodo'] == 'criar'){
                    $_POST['w'] = $this->modulo->connection->conn->lastInsertId();
                }


                /*
                 * carrega módulos que contenham propriedade embed
                 *
                $embed = $this->modulo->LeModulosEmbed();

                // salva o objeto do módulo atual para fazer embed
                if( !empty($embed) ){
                    /*
                     * Caso tenha embed, serão carregados modulos embed. O objeto do módulo atual
                     * é $modulo, sendo que dos embed também. Então guardamos $modulo,
                     * fazemos unset nele e reccaregamos no final do script.
                     *

                    $tempmodulo = $modulo;
                    unset($modulo);
                    foreach($embed AS $chave=>$valor){
                        foreach($valor AS $chave2=>$valor2){
                            if($chave2 == 'pasta'){
                                if(is_file($valor2.'/embed/gravar.php')){
                                    include($valor2.'/index.php');
                                    include($valor2.'/embed/gravar.php');
                                }
                            }
                        }
                    }
                    $modulo = $tempmodulo;
                } // fim do embed

            } else {
                $resultado = FALSE;
            }

            if($resultado){
                $status['classe'] = 'sucesso';
                $status['mensagem'] = '<strong>Sucesso: </strong> As informações foram salvas com sucesso.';
            } else {
                $status['classe'] = 'insucesso';
                $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro ao salvar informações. Se você tentou copiar um texto do Microsoft Word, provavelmente há letras/caracteres neste texto que não podem ser lidos por seu navegador. Experimente verificar se não há nada de estranho (alguma letra) entre este texto. Se houver, entre em contato com o administrador e explique o que está acontecendo.';
            }
            EscreveBoxMensagem($status);

        }
         * 
         */


    }
    
}
?>