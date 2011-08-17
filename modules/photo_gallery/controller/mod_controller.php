<?php
/**
 * Descrição deste arquivo
 *
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{

	function beforeFilter(){
		if( !empty($_GET["delete"]) AND $_GET["delete"] > 0 ){
			$sql = "DELETE FROM photo_gallery_images
					WHERE id='".$_GET["delete"]."'";
			if( $this->module->connection->exec($sql) ){
				notice("Imagem excluída com sucesso.");
			}

		}
	}

	public function listing(){
		$this->set('title', Aust::getInstance()->getStructureNameById($_GET['aust_node']) );
		$this->set('nome_modulo', Aust::getInstance()->structureModule($_GET['aust_node']) );

		$categorias = Aust::getInstance()->getNodeChildren($_GET['aust_node']);
		$categorias[$_GET['aust_node']] = 'Estrutura';

		$params = array(
			'austNode' => $categorias
		);
		
		$query = $this->module->load($params);
		
		$this->set('query', $query);
	}

	public function create(){
		$this->render('form');
	}

	public function edit(){
		$w = '';
		if( !empty($_GET['w']) ){
			$w = $_GET['w'];
		} else {
			if( !empty($_GET['related_master']) &&
			 	!empty($_GET['related_w']) )
			{
				// Verifica se há uma galeria atual para o item master.
				// Caso não exista, criará uma
				$sql = "SELECT
							id as w
						FROM
							".$this->module->mainTable."
						WHERE
							ref_id='".$_GET['related_w']."' AND
							node_id='".$_GET['aust_node']."'
						";
				$result = Connection::getInstance()->query($sql);
				if( !empty($result) ){
					$result = reset($result);
					$w = $result['w'];
				}
				/*
				 * Master não tem uma galeria ainda
				 */
				else {
					$master = Aust::getInstance()->getStructureInstance($_GET['related_master']);
					$sql = "
							SELECT
								title
							FROM
								".$master->getContentTable()."
							WHERE
								id='".$_GET['related_w']."'
							";

					$query = $master->connection->query($sql);
					$query = reset($query);

					$ref = Aust::getInstance()->getField($_GET['related_master'], 'nome_encoded');

					$sql = "INSERT INTO
								".$this->module->mainTable."
							(ref_id, ref, node_id, title, created_on)
							VALUES
							('".$_GET['related_w']."', '".$ref."', '".$_GET['aust_node']."', '".addslashes($query['titulo'])."', '".date('Y-m-d H:i:s')."')
							";

					$master->connection->exec($sql);
					$w = $master->connection->lastInsertId();
				}
			}
		}

		$sql = "
				SELECT
					*
				FROM
					".$this->module->useThisTable()."
				WHERE
					id='$w' AND
					node_id='".$_GET['aust_node']."'
				";

		$query = $this->module->connection->query($sql, "ASSOC");
		$dados = reset($query);

		$sql = "SELECT id, file_name, text FROM photo_gallery_images
				WHERE gallery_id='".$w."' ORDER BY order_nr ASC";

		$images = $this->module->connection->query($sql, "ASSOC");

		$this->set('images', $images);
		$this->set('dados', $dados);
		$this->set('w', $w);
		$this->render('form');
	}

	public function save(){
		
	}

	public function actions(){
		if( empty($_POST['itens']) ){
			failure('Nenhum item selecionado.');
		}

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
							".$this->module->LeTabelaDaEstrutura($_GET['aust_node'])."
						WHERE
							$where
							";

				if(Connection::getInstance()->exec($sql)){
					$resultado = TRUE;
				} else {
					$resultado = FALSE;
				}

				if($resultado){
					notice('Os dados foram excluídos com sucesso.');
				} else {
					failure('Ocorreu um erro ao excluir os dados.');
				}

		} 
		
	}
	
}
?>