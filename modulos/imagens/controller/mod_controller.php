<?php
/**
 * Descrição deste arquivo
 *
 * @package ModController
 * @name nome
 * @author Alexandre <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */

class ModController extends ModsController
{

	public function getQuery(){
		
		$categorias = $this->aust->LeCategoriasFilhas('',$_GET['aust_node']);
        $categorias[$_GET['aust_node']] = 'Estrutura';

        /*
         * SQL para listagem
         */
        $params = array(
            'austNode' => $categorias,
        );

        $query = $this->modulo->load($params);
		return $query;
		
	}
	
	public function view_items(){
		
		if( !empty($_POST['viewMode']) )
			$this->modulo->setViewMode();
			
		$viewMode = $this->modulo->viewmode();
		
		$this->set('viewMode', $viewMode);
		
		$query = $this->getQuery();
        $this->set('query', $query);

		$this->render('listing_'.$viewMode.'_view');
		
	}

    public function listing(){

        $h1 = ''.$this->aust->leNomeDaEstrutura($_GET['aust_node']);
        $this->set('h1', $h1);

        $sql = "SELECT
                    id,nome
                FROM
                    ".Aust::$austTable."
                WHERE
                    id='".$_GET['aust_node']."'";

        $query = $this->modulo->connection->query($sql);

        $cat = $query[0]['nome'];

		/*
		 * VIEW MODE
		 */
		$viewMode = $this->modulo->viewmode();
		$this->set('viewMode', $viewMode);
		
		$query = $this->getQuery();
        $this->set('query', $query);
    }

    public function create(){


        $this->render('form');
    }

    public function edit(){

        
        $this->render('form');
    }

    public function save(){
        
        if( $_POST["metodo"] == "create" AND !empty($_FILES) AND $_FILES["frmarquivo"]["size"] > 0 ){
            $save = true;
        } else if( $_POST["metodo"] == "edit" ) {
            $save = true;
        } else {
            $save = false;
        }

        if( !empty($_POST) AND $save  ) {
            if( !empty($_FILES) AND
                $_FILES["frmarquivo"]["size"] > 0 AND
                $this->testMode == false )
            {
                $imagem = $this->modulo->trataImagem($_FILES);
                $_POST["frmbytes"] = $imagem["filesize"];
                $_POST["frmdados"] = $imagem["filedata"];
                $_POST["frmnome"] = $imagem["filename"];
                $_POST["frmtipo"] = $imagem["filetype"];

            }
            /*
             * Prepara a ordem da imagem
             */
            if( empty($_POST["frmordem"]) ){
                // seleciona a última ordem do banco de dados
                $sql = "SELECT
                            ordem
                        FROM
                            ".$this->modulo->useThisTable()."
                        WHERE
                            categoria='".$_POST['aust_node']."'
                        ORDER BY
                            ordem asc
                        LIMIT 1
                        ";
                //echo $sql;
                $query = $this->modulo->connection->query($sql);
                $total = $this->modulo->connection->count($sql);

                $ordem = 0;
                foreach ( $query as $dados ){
                    $curordem = $dados["ordem"];
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
                $_POST["frmordem"] = $ordem;
            } // fim ordem automática

            $_POST["frmcategoria"] = $_POST["aust_node"];
            $_POST['frmtitulo_encoded'] = encodeText($_POST['frmtitulo']);

            /*
             * GROUPED_DATA
             *
             * Alguns dados, como data, precisam ser mostrados em mais de um input.
             *
             * Ex.:
             *      <input name="grouped_data[expire_date][day]" />
             *      <input name="grouped_data[expire_date][month]" />
             *      <input name="grouped_data[expire_date][year]" />
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
            
            $result = $this->modulo->save($_POST);
            $this->set('resultado', $result);
        }

        return $result;

    }


    
}
?>