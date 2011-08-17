<?php
/**
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{

	public function getQuery(){
		
		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';

		/*
		 * SQL para listagem
		 */
		$params = array(
			'austNode' => $categorias,
		);

		$query = $this->module->load($params);
		return $query;
		
	}
	
	public function view_items(){
		
		if( !empty($_POST['viewMode']) )
			$this->module->setViewMode();
			
		$viewMode = $this->module->viewmode();
		
		$this->set('viewMode', $viewMode);
		
		$query = $this->getQuery();
		$this->set('query', $query);

		$this->render('listing_'.$viewMode.'_view');
		
	}

	public function listing(){

		$h1 = ''.Aust::getInstance()->getStructureNameById($_GET['aust_node']);
		$this->set('h1', $h1);

		$sql = "SELECT
					id, nome
				FROM
					".Aust::$austTable."
				WHERE
					id='".$_GET['aust_node']."'";

		$query = $this->module->connection->query($sql);

		$cat = $query[0]['nome'];

		/*
		 * VIEW MODE
		 */
		$viewMode = $this->module->viewmode();
		$this->set('viewMode', $viewMode);
		
		$query = $this->getQuery();
		$query = $this->module->replaceFieldsValueIfEmpty($query);
		$this->set('query', $query);
	}

	public function create(){


		$this->render('form');
	}

	public function edit(){

		
		$this->render('form');
	}

	public function save(){
		
		$editing = false;
		$creating = false;
		if( $_POST["metodo"] == "create" AND !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
			$save = true;
			$creating = true;
		} else if( $_POST["metodo"] == "edit" ) {
			$save = true;
			$editing = true;
			$id = $_POST['w'];
		} else {
			$save = false;
		}

		$imageHandler = Image::getInstance();
		$fileHandler = File::getInstance();

		if( !empty($_POST) AND $save  ) {
	
			/*
			 * [Se imagem foi enviada para upload...]
			 */
			if( !empty($_FILES) AND
				$_FILES["frmarquivo"]["size"] > 0 AND
				$this->testMode == false )
			{
				$file = $_FILES['frmarquivo'];

				/*
				 * É um arquivo do tipo IMAGEM
				 */
				
				$value = array(
					'size' 		=> $file['size'],
					'tmp_name' 	=> $file['tmp_name'],
					'name' 		=> $file['name'],
					'type' 		=> $file['type'],
				);
				if( $imageHandler->isImage($file['type']) ){
				
					// resample?
					if( $this->module->getStructureConfig('resample_images') != '0' ){
						$value = $imageHandler->resample($file);
					} 
						
					/*
					 * Por padrão, as imagens são salvas fisicamente. Opcionalmente,
					 * pode-se salvar as imagens no banco de dados.
					 */
					if( $this->module->getStructureConfig('save_files_to_db') == '1' ){
						
						if( $editing ){
							$oldFile = reset( $this->module->load($id) );
							if( !empty($oldFile['file_systempath']) ){
								$oldFile = $oldFile['file_systempath'];
								if( file_exists($oldFile) ){
									unlink($oldFile);
								}
							}
						}
						
						$_POST["frmfile_systempath"] = '';
						$_POST["frmfile_path"] = '';
		
						$_POST["frmfile_bytes"] = $value["size"];
						$_POST["frmfile_binary_data"] = file_get_contents($value["tmp_name"]);
						$_POST["frmfile_name"] = $value["name"];
						$_POST["frmfile_type"] = $value["type"];
					} else {
						$paths = $imageHandler->upload($_FILES['frmarquivo']);
						if( $editing ){
							$oldFile = reset( $this->module->load($id) );
							if( !empty($oldFile['file_systempath']) ){
								$oldFile = $oldFile['file_systempath'];
								if( file_exists($oldFile) ){
									unlink($oldFile);
								}
							}
						}

						$_POST["frmfile_binary_data"] 	= '';
						$_POST['frmfile_systempath'] 	= $paths['systemPath'];
						$_POST['frmfile_path'] 		= $paths['webPath'];
						$_POST["frmfile_bytes"] 		= $value["size"];
						$_POST["frmfile_name"] 		= $value["name"];
						$_POST["frmfile_type"] 		= $value["type"];
					}

				} else if( $fileHandler->isFlash($_FILES['frmarquivo']['type']) AND 
						   $this->module->getStructureConfig('allow_flash_upload') == '1' )
				{
					
					$path = $fileHandler->upload($_FILES['frmarquivo']);
					if( $editing ){
						$oldFile = reset( $this->module->load($id) );
						if( !empty($oldFile['file_systempath']) ){
							$oldFile = $oldFile['file_systempath'];
							if( file_exists($oldFile) ){
								unlink($oldFile);
							}
						}
					}
					
					// links não são permitidos em arquivos Flash
					$_POST['frmlink'] 		= '';
					
					$_POST["frmfile_binary_data"] 	= '';
					$_POST['frmfile_systempath'] 	= $path['systemPath'];
					$_POST['frmfile_path'] 		= $path['webPath'];
					$_POST["frmfile_bytes"] 		= $value["size"];
					$_POST["frmfile_name"] 		= $value["name"];
					$_POST["frmfile_type"] 		= $value["type"];
					
				} else {
					/**
					 * @todo - quando não for imagem nem flash, deve-se mostrar
					 * o erro ao usuário, e não simplesmente dizer que não foi
					 * possível fazer o upload.
					 */
					$result = array(
						'class' => 'error',
						'msg' => 'O arquivo enviado não é permitido. Envie uma imagem.'
					);
					$this->set('resultado', $result);
					return false;
				}

				/*
				 * É um arquivo do tipo FLASH
				 */
			}

			/*
			 * Prepara a ordem da imagem
			 */
			if( empty($_POST["frmorder_nr"]) ){
				// seleciona a última ordem do banco de dados
				$sql = "SELECT
							order_nr
						FROM
							".$this->module->useThisTable()."
						WHERE
							node_id='".$_POST['aust_node']."'
						ORDER BY
							order_nr asc
						LIMIT 1
						";
				//echo $sql;
				$query = $this->module->connection->query($sql);
				$total = $this->module->connection->count($sql);

				$ordem = 0;
				foreach ( $query as $dados ){
					$curordem = $dados["order_nr"];
					if ($curordem >= $ordem)
						$ordem = $curordem+1;
				}

				/*
				 * Se não há imagens ainda, $ordem = 1
				 */
				if ($ordem == 0)
					$ordem = 1;

				/*
				 * Últimos ajustes de campos a serem inseridos
				 */
				$_POST["frmorder_nr"] = $ordem;
			} // fim ordem automática

			$_POST['frmtitle_encoded'] = encodeText($_POST['frmtitle']);

			/*
			 * GROUPED_DATA
			 *
			 * Alguns dados, como data, precisam ser mostrados em mais de um input.
			 *
			 * Ex.:
			 *	  <input name="grouped_data[expire_date][day]" />
			 *	  <input name="grouped_data[expire_date][month]" />
			 *	  <input name="grouped_data[expire_date][year]" />
			 *
			 * O formato adequado é grouped_data[nome_da_coluna_no_db][nome_do_campo]
			 */
			if( !empty($_POST["grouped_data"]) ){

				$gD = $_POST["grouped_data"];


				foreach( $gD as $chave=>$coluna ){

					$gDR = groupedDataFormat($coluna);
					//if( !empty($gDR) ){
						$_POST["frm".$chave] = $gDR;
					//}

				}

			}
			$result = $this->module->save($_POST);
			$this->set('resultado', $result);
		}

		return $result;

	}

	function actions(){
		if( !empty($_POST['deletar']) ){
			/*
			 * Identificar tabela que deve ser excluida
			 */

			$itens = $_POST['itens'];
			$c = 0;
			foreach($itens as $key=>$value){
				if($c > 0){
					$where = $where." OR id='".$value."'";
				} else {
					$where = "id='".$value."'";
				}
				$c++;
			}
			$sql = "DELETE FROM
						".$this->module->useThisTable()."
					WHERE
						$where
						";

			if($this->module->connection->exec($sql)){
				notice('Os dados foram excluídos com sucesso.');
			} else {
				failure('Ocorreu um erro ao excluir os dados.');
			}
		}
	}
	
}
?>