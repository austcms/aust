<?php

/*********************************
*
*	classe do módulo TEXTOS
*
*********************************/

class GaleriaImagens extends Module {

	// TABELA
	protected $db_tabelas;
	protected $sql_das_tabelas;
	protected $sql_registros;
	public $tabela_criar;


    /**
     * @todo - Comentar certo esta classe
     *
     *
     * @global string $aust_charset Contém o charset das tabelas
     * @param Conexao $conexao Objeto que contém as configurações com o DB
     */
	function __construct($param){

        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);
        
		// pega a string global que diz qual é o charset do projeto
		global $aust_charset;
		if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
			$charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
		}

		$this->db_tabelas[] = "galeriaimagens";
		$this->tabela_criar = "galeriaimagens";
		$this->sql_das_tabelas[] = "
            CREATE TABLE galeriaimagens (
                id int NOT NULL auto_increment,
                ordem int,
                bytes mediumint,
                dados longblob,
                nome varchar(150) {$charset},
                tipo varchar(120) {$charset},
                titulo text {$charset},
                ref varchar(120) {$charset},
                categoria varchar(150) {$charset},
                descricao text {$charset},
                classe varchar(120) {$charset},
                especie varchar(120) {$charset},
                visivel bool,
                adddate datetime not null,
                autor varchar(120) {$charset},
                PRIMARY KEY (id),
                UNIQUE id (id)
                )
			";
		$this->sql_registros[] = "";
	
	}

	/*********************************
	*
	*	funções de verificação ou leitura
	*
	*********************************/
	public function SQLParaListagem($categorias = '', $pagina = '', $itens_por_pagina = ''){
		if(!empty($categorias)){
			$order = ' ORDER BY id DESC';
			$where = ' WHERE ';
			$c = 0;
			foreach($categorias as $key=>$valor){
				if($c == 0)
					$where = $where . 'categoria=\''.$key.'\'';
				else
					$where = $where . ' OR categoria=\''.$key.'\'';
				$c++;
			}
		}

        if(!empty($pagina)){
            $item_atual = ($pagina * $itens_por_pagina) - $itens_por_pagina;
            $limit = " LIMIT ".$item_atual.",".$itens_por_pagina;
        }
		$sql = "SELECT
					id, titulo, visitantes, categoria AS cat, DATE_FORMAT(adddate, '%d/%m/%Y %H:%i') as adddate,
					(	SELECT
							nome
						FROM
							categorias AS c
						WHERE
							id=cat
					) AS node
				FROM
					".$this->tabela_criar.$where.$order.
                $limit
                ;
					
		return $sql;
	
	}

    /**
     * Gera código HTML com thumbs do DB e número de colunas indicadas
     *
     * @param array['conditions'][] $params['conditions']
     * @param array['columns'] $params['columns']
     * @param array['script'] $params['script']
     * @param array['inline'] $params['inline']
     * @return string
     */
    function criaThumbsTable($params){
        //print_r($params['conditions']);
        $columns = (empty($params['columns'])) ? $params['columns'] : '3';
        $conditions = $params['conditions'];

        foreach($conditions as $chave=>$valor){
            if(!is_array($valor)){
                $where[] = $chave.'=\''.$valor.'\'';
            } else {
                foreach($valor as $chave2=>$valor2){
                    //echo $chave2.'<-oi';
                    $tmpwhere[] = $chave2;
                }
                $where[] = $chave.' IN (\''.implode('\',\'', $tmpwhere).'\')';
            }

        }
        $sql = "SELECT
                    id
                FROM
                    galeriaimagens
                WHERE
                    ".implode(' AND ', $where)."
                ";

        $query = $this->connection->query($sql);
        $total = $this->connection->count($sql);
        if($total){

            // se total>0, começa a escrever o html
            $c = 1;
            foreach($query as $dados){
                if($c == 1){
                    echo '<tr>';
                }
                ?>
                <td <?php echo $params['inline']?>>
                    <img src="<?php echo $params['script']?><?php echo $dados['id'];?>" />

                    <?php
                    if(!empty($params['options'])){
                        echo '<br clear="both" />'. str_replace(array('&%id'), array($dados['id']), $params['options']);
                    }

                    ?>
                </td>
                <?php

                if($c >= $columns){
                    echo '</tr>';
                    $c = 1;
                } else {
                    $c++;
                }
            }

            // se ficou faltando TDs
            if($c <= $columns AND $c > 0){
                for($o = 0; $o < (($columns+1)-$c); $o++){ ?>
                    <td></td>
                    <?php
                }
                echo '</tr>';
            }

        } else {
            echo '<tr>';
            echo '<td>';
            echo ifisset($params['empty'], 'Não há itens');
            echo '</td>';
            echo '</tr>';
        }


    }


    /*
     * Insere imagem no DB
     */
    function insertImagem($post, $files, $get){

        // seleciona a última ordem do banco de dados
        $sql = "SELECT
                    ordem
                FROM
                    galeriaimagens
                WHERE
                    ref='".$get['w']."' AND
                    categoria='".$get['aust_node']."'
                ORDER BY
                    ordem asc
                ";
        //echo $sql;
        $query = $this->connection->query($sql);
        $total = $this->connection->count($sql);

        $ordem = 0;
        foreach ( $query as $dados ){
            $curordem = $dados["ordem"];
            if ($curordem >= $ordem)
                $ordem = $curordem+1;
        }

        // se não há imagens ainda, $ordem = 1
        if ($ordem == 0)
            $ordem = 1;

            //echo $ordem;
            
        $frmarquivo = $files['frmarquivo']['tmp_name'];
        $frmarquivo_name = $files['frmarquivo']['name'];
        $frmarquivo_type = $files['frmarquivo']['type'];
        //print_r($frmarquivo);


        $fppeq = fopen($frmarquivo,"rb");
        $arquivo = fread($fppeq, filesize($frmarquivo));
        fclose($fppeq);

        $im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
        //echo $arquivo;
        $largurao = imagesx($im);// pegar a largura da amostra
        $alturao = imagesy($im);// pegar a altura da amostra


        if($largurao > 800)
            $largurad = 800;
        else
            $largurad = $largurao; // definir a altura da miniatura em px
        $alturad = ($alturao*$largurad)/$largurao;// calcula a largura da imagem a partir da altura da miniatura
        $nova = imagecreatetruecolor($largurad,$alturad);//criar uma imagem em branco
		// MODO RÁPIDO E POUCA QUALIDADE: copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
        //fastimagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao,5);
		// MODO LENTO E BASTANTE QUALIDADE: copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
        imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

        ob_start();
        imagejpeg($nova, '', 76);
        $mynewimage = ob_get_contents();
        ob_end_clean();


        if(strlen($mynewimage) > "5000000"){
            $result['query'] = false;
            $result['status'] = 'max_size';
            return $result;

        } else {
            $arquivo_temppeq = addslashes($mynewimage);

            if( !empty($frmespecia) AND $frmespecie == "capa"){
                $sql = "DELETE FROM imagens
                        WHERE
                            ref='$w' AND
                            especie='capa'";
                $this->connection->exec($sql);
            }


            $sql = "INSERT INTO
                        galeriaimagens(
                                ordem,bytes,nome,tipo,
                                ref,categoria,descricao,classe,
                                adddate,autor,dados)
                        VALUES(
                                $ordem,".strlen($mynewimage).",'".$frmarquivo_name."','".$frmarquivo_type."',
                                '".$post['w']."','".$post['aust_node']."','".$post['frmdescricao']."','".$post['frmclasse']."',
                                '".$post['frmadddate']."','".$post['frmautor']."',

                                '".$arquivo_temppeq."' )";

            //echo "Tamanho do arquivo pequeno: ".$arquivopeq_size."bytes.";
            $result['query'] = $this->connection->exec($sql);
            //var_dump($result['query']);
            return $result;

        }

        imagedestroy($nova);
    }
	
}

?>