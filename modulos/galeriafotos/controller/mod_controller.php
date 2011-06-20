<?php
/**
 * Descrição deste arquivo
 *
 * @package ModController
 * @name nome
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModActionController
{

    public function listing(){
        $this->set('h2', 'Listando conteúdo: '.Aust::getInstance()->leNomeDaEstrutura($_GET['aust_node']) );
        $this->set('nome_modulo', Aust::getInstance()->LeModuloDaEstrutura($_GET['aust_node']) );

		$categorias = Aust::getInstance()->LeCategoriasFilhas('',$_GET['aust_node']);
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
							galeria_fotos
						WHERE
							ref_id='".$_GET['related_w']."' AND
							categoria='".$_GET['aust_node']."'
						";
				$result = $this->connection->query($sql);
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
			                    titulo
			                FROM
			                    ".$master->getContentTable()."
			                WHERE
			                    id='".$_GET['related_w']."'
			                ";

			        $query = $master->connection->query($sql);
					$query = reset($query);

					$ref = Aust::getInstance()->getField($_GET['related_master'], 'nome_encoded');

					$sql = "INSERT INTO
								galeria_fotos
							(ref_id, ref, categoria, titulo,adddate)
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
					categoria='".$_GET['aust_node']."'
                ";

        $query = $this->module->connection->query($sql, "ASSOC");
        $dados = reset($query);

		$this->set('dados', $dados);
		$this->set('w', $w);
        $this->render('form');
    }

    public function save(){
        
    }
    
}
?>