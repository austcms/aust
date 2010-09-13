<?php
/**
 * Classe do módulo
 *
 * @package Módulos
 * @name Cadastro
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6, 09/07/2009
 */
class Cadastro extends Module {

    public $mainTable = "cadastros_conf";

    public $dataTable;
    public $austNode;

	public $data = array();
	public $relationalData = array();
	public $images = array();
	
	public $tableProperties = array();

	/**
	 * @var array Contém as configurações sobre campos e estrutura
	 */
	public $configurations = array();
	
	public $fields = array();
	
	public $toDeleteTables = array();

    function __construct($param = ''){


        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);
    }


	/*
	 * LOADING PROCESS
	 * 	
	 */
	/**
	 * getImages()
	 * 
	 * 
	 * @param $params array É onde contém o id, austNode e campo a qual a imagem
	 * se refere
	 * @return array imagens, com path e id
	 */
	public function getImages($params){
		
		if( empty($params['w']) ) return false;
		if( empty($params['austNode']) ) return false;
		if( empty($params['field']) ) return false;
		
		$w = $params['w'];
		$austNode = $params['austNode'];
		$field = $params['field'];
		
		$tableImage = $this->configurations['estrutura']['table_images']['valor'];
		
		$sql = "SELECT
					*,
					( SELECT s.id FROM ".$tableImage." as s WHERE s.reference=t.id AND type='secondary' LIMIT 1 )
					as secondaryid
				FROM
					".$tableImage." as t
				WHERE
					maintable_id='".$w."' AND
					reference_field='".$field."' AND
					categoria_id='".$austNode."' AND
					type='main'
				";
		$query = $this->connection->query($sql);
		
		return $query;
		
	} // fim getImages()
	
	/*
	 * SAVING PROCESS
	 * 	
	 */
	/**
	 * setRelationalData()
	 * 
	 * Separa/prepara todos os dados, sendo que os relacionais que não serão salvos
	 * na tabela principal são guardados em uma variável separada.
	 * 
	 * 		Ajusta:
	 * 
	 * 			_ Relational One To Many
	 * 			- Date
	 * 			- Images
	 * 
	 * 
	 */
	public function setRelationalData(){
	
		$infoTabelaFisica = $this->tableProperties;
		$campos = $this->fields;
		$relational = array();
		foreach( $this->data as $tabela=>$dados ){
	        foreach( $dados as $campo=>$valor ){
            
	            /*
	             * Relational One to Many
	             */
	            if( !empty($campos[$campo]) AND $campos[$campo]["especie"] == "relacional_umparamuitos" ){
	                unset($this->data[$tabela][$campo]);

	                $i = 0;
	                foreach( $valor as $subArray ){
	                    if( $subArray != 0 ){
	                        $relational[$campos[$campo]["referencia"]][$i][$campos[$campo]["ref_tabela"]."_id"] = $subArray;
	                        $relational[$campos[$campo]["referencia"]][$i]["created_on"] = date("Y-m-d H:i:s");
	                        $i++;
	                    }
	                    $this->toDeleteTables[$campos[$campo]["referencia"]] = 1;
	                }

	            }
	            /*
	             * Date
	             */
	            else if( !empty( $campos[$campo]["chave"] ) AND
	                     !empty($infoTabelaFisica[$campos[$campo]["chave"]]['Type']) AND
	                     $infoTabelaFisica[$campos[$campo]["chave"]]['Type'] == "date" ){
	                $year = $this->data[$tabela][$campo]['year'];
	                unset($this->data[$tabela][$campo]);

	                if( strlen($year) == '4' ){
	                    $this->data[$tabela][$campo] = $valor['year'].'-'.$valor['month'].'-'.$valor['day'];
	                }
	            }
	            /*
	             * Images
				 *
				 * Limpa imagens de $this->data
	             */
	            else if( !empty($campos[$campo]) AND $campos[$campo]["especie"] == "images" ){
					$this->images[$tabela][$campo] = $valor;
					unset($this->data[$tabela][$campo]);
	            }

	        }
	    }
		$this->relationalData = $relational;
		return true;
	
	}
	
	/**
	 * uploadAndSaveImages()
	 * 
	 * Realiza o upload de uma imagem e a salva no DB.
	 * 
	 * @param $images array contém as imagems a serem enviadas.
	 * @param $lastInsertId int Anexo um id à imagem inserida.
	 */
	function uploadAndSaveImages($images, $lastInsertId, $options = array()){
		
		$imageHandler = Image::getInstance();
		$user = User::getInstance();
		$userId = $user->getId();
		
		if( empty($options['type']) )
			$type = 'main';
		else
			$type = $options['type'];
		
		if( empty($options['reference']) )
			$reference = '';
		else
			$reference = $options['reference'];
		
		if( empty($images) ){
			$images = $this->images;
		}
		
		$imageTable = $this->configurations['estrutura']['table_images']['valor'];
		foreach( $images as $table=>$imagesField ){
			
			foreach( $imagesField as $field=>$images ){
				
				foreach( $images as $key=>$value ){
					if( empty($value['name']) OR
						empty($value['size']) OR
						empty($value['tmp_name'])
					){
						continue;
					}
					
					/*
					 * Realiza upload e salva os dados
					 */
					
					$value = $imageHandler->resample($value);
					$finalName = $imageHandler->upload($value);
					
					/*
					 * Salva SQL da imagem
					 */
					$sql = "INSERT INTO $imageTable
							(
							maintable_id,path,systempath,
							file_name,
							original_file_name,file_type,file_size,file_ext,
							type,
							reference_table,reference_field,
							reference,
							categoria_id,
							created_on, admin_id
							)
							VALUES
							(
							'".$lastInsertId."', '".$finalName['webPath']."', '".$finalName['systemPath']."',
							'".$finalName['new_filename']."',
							'".$value['name']."', '".$value['type']."', '".$value['size']."', '".$imageHandler->getExtension($value['name'])."',
							'$type',
							'".$this->configurations['estrutura']['tabela']['valor']."', '".$field."',
							'".$reference."',
							'".$this->austNode."',
							'".date("Y-m-d H:i:s")."', '".$userId."'
							)
							";
					$this->connection->exec($sql);
					
				}
			}
		}
	} // uploadAndSaveImages()
	
	function saveImageDescription($string, $imageId){
		$string = addslashes($string);
		
		$this->configurations();
		$imageTable = $this->configurations['estrutura']['table_images']['valor'];
		
		$sql = "UPDATE $imageTable SET description='$string' WHERE id='$imageId'";
		return $this->connection->exec($sql);
	}
    
	/**
	 * secondaryImageId() 
	 *
	 * Dado uma image, verifica o id de uma possível imagem secundária.
	 * 
	 */
	function deleteSecondaryImagesById($w = ""){
		if( !is_numeric($w) )
			return false;
		
		$configurations = $this->configurations();
		$imagesTable = $configurations['estrutura']['table_images']['valor'];
		$sql = "SELECT
					id, systempath
				FROM
					".$imagesTable."
				WHERE
					reference='".$w."' AND
					type='secondary'
				";
		
		$query = $this->connection->query($sql);
		foreach( $query as $key=>$value ){
			if( file_exists($value['systempath']) )
				unlink( $value['systempath'] );
			$sqlDelete = "DELETE FROM $imagesTable WHERE id='".$value['id']."'";
			$this->connection->exec($sqlDelete);
		}
		
		return $query[0]['id'];
	} // fim secondaryImageId()

	function deleteImage($w = ""){
		if( !is_numeric($w) )
			return false;
		
		$configurations = $this->configurations();
		$imagesTable = $configurations['estrutura']['table_images']['valor'];
		$sql = "SELECT
					*
				FROM
					".$imagesTable."
				WHERE
					id='".$w."'
				";
		
		$query = reset( $this->connection->query($sql) );
		
		if( file_exists($query['systempath']) )
			unlink( $query['systempath'] );
		$sqlDelete = "DELETE FROM $imagesTable WHERE id='".$w."'";
		$this->connection->exec($sqlDelete);
		
		return true;
	}
	
    /**
     * loadDivisors()
     *
     * Divisores são títulos que aparecem entre campos de cadastro,
     * de forma a separar os inputs por assunto.
     *
     * @return <array>
     */
    function loadDivisors(){
        $sql = "SELECT
                    id, tipo, valor, comentario, descricao
                FROM
                    ".$this->useThisTable()."
                WHERE
                    tipo='divisor' AND
                    categorias_id='".$this->austNode."'
            ";
        $tempResult = $this->connection->query($sql);

        /*
         * Agrupa array de Divisors com as chaves sendo o nome do
         * campo após o título.
         */
        $result = array();
        foreach( $tempResult as $valor ){

            $before = str_replace("BEFORE ", "", $valor['descricao']);
            $result[$before] = $valor;
        }
        
        return $result;
    }

    /**
     * @todo
     *
     * saveDivisor deve excluir um divisor que já existe
     * que seja antes do mesmo campo indicado. Assim,
     * evita-se dois divisores antes de um mesmo campo.
     */
    /**
     * saveDivisor()
     * 
     * Salva um Título Divisor de campos do Cadastro.
     *
     * @param <array> $params
     *      Contém os elementos 'title', 'comment' (não obrigatório)
     *      e 'before', indicando o nome do campo ao qual este divisor
     *      antecede.
     * @return <type>
     */
    function saveDivisor($params){

        /*
         * 'title' e 'before' são obrigatórios
         */
        if( empty($params['title']) OR
            empty($params['before']) )
            return false;

        if( empty($params['comment']) )
            $params['comment'] = "";

        $params['title'] = addslashes($params['title']);
        $params['comment'] = addslashes($params['comment']);

        $sql = "INSERT INTO
                    ".$this->useThisTable()."
                    (tipo,valor,comentario,categorias_id,descricao)
                VALUES
                    (
                    'divisor','".$params['title']."','".$params['comment']."',
                    '".$this->austNode."','".$params['before']."'
                    )
                ";
        
        $result = $this->connection->exec($sql);

        if( $result )
            return true;

        return false;

    }

    function deleteDivisor($id){
        if( is_int($id) OR
            is_string($id) )
        {
            $where = "id='".$id."' AND tipo='divisor'";
        }

        $sql = "DELETE FROM
                    ".$this->useThisTable()."
                WHERE
                    $where
                ";
        $result = $this->connection->exec($sql);

        return $result;
    }

    /**
     * getFields()
     *
     * Return the list of fields as Array.
     *
     *      key = physical field name
     *      value = human field name
     *
     * @return <array>
     */
    public function getFields(){
        $temp = $this->connection->query(
            "SELECT * FROM cadastros_conf
             WHERE
                categorias_id='".$this->austNode."' AND
                tipo='campo'
             ORDER BY ordem ASC",
            PDO::FETCH_ASSOC
        );
        
        foreach( $temp as $chave=>$valor ){
            if( !empty($valor["chave"]) )
                $result[ $valor["chave"] ] = $valor["valor"];
        }

        return $result;
    }

	/**
	 * configurations()
	 * 
	 * Retorna configurações. Se já existe, não carrega duas vezes.
	 * 
	 * @return array Toda a configuração do Módulo Cadastro
	 */
	public function configurations(){
		if( !empty($this->configurations) )
			return $this->configurations;
		
		$this->pegaInformacoesCadastro( $this->austNode );
		return $this->configurations;
	}
    /**
     * Retorna todas as informações sobre o cadastro.
     *
     * Pega todas as informações da tabela cadastros_conf onde categorias_id
     * é igual ao austNode especificado.
     *
     * @param int $austNode
     * @return array
     */
    public function pegaInformacoesCadastro( $austNode ){
        /**
         * Busca na tabela cadastros_conf por informações relacionadas ao
         * austNode selecionado.
         */
        $temp = $this->connection->query(
            "SELECT * FROM cadastros_conf WHERE categorias_id='".$austNode."' ORDER BY ordem ASC",
            PDO::FETCH_ASSOC
        );
        foreach( $temp as $chave=>$valor ){
            if( !empty($valor["chave"]) )
                $result[ $valor["tipo"] ][ $valor["chave"] ] = $valor;
        }
		$this->configurations = $result;
        return $result;
    }

    /**
     * Retorna informações sobre tipagem física da respectiva
     * tabela.
     *
     * @param array $params
     *      'tabela': qual tabela deve ser analisada
     *      'by': indica qual o índice deve ser usado
     *          ex.: se 'Field', o índice de retorno é o nome do
     *          campo.
     * @return array Retorna as características físicas da tabela
     */
    public function pegaInformacoesTabelaFisica( $params ){
        /**
         * DESCRIBE tabela
         *
         * Toma informações físicas sobre a tabela
         */
        if ( !empty( $params["tabela"] ) ){
            $temp = $this->connection->query("DESCRIBE ".$params["tabela"], "ASSOC");
        }

        /**
         * $param["by"]
         *
         * Se o resultado deve ser retornado com uma determinada informação
         * como índice.
         */
        if( !empty($params["by"]) ){
            foreach($temp as $chave=>$valor){
                $result[ $valor[ $params["by"] ] ] = $valor;
            }
        } else {
            $result = $temp;
        }

		$this->tableProperties = $result;

        return $result;
    }

    /**
     *
     * VERIFICAÇÕES E LEITURAS AUTOMÁTICAS DO DB
     * 
     */
    
    public function loadSql($param){
        // configura e ajusta as variáveis
        $categorias = (empty($param['categorias'])) ? '' : $param['categorias'];
        $metodo = (empty($param['metodo'])) ? '' : $param['metodo'];
        $search = (empty($param['search'])) ? '' : $param['search'];
        $searchField = (empty($param['search_field'])) ? '' : $param['search_field'];
        $w = (empty($param['id'])) ? '' : $param['id'];

        /**
         * Se $categorias estiver vazio (nunca deverá acontecer)
         */
        if(!empty($categorias)){
            $order = ' ORDER BY id ASC';
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor){
                if($c == 0)
                    $where = $where . 'categorias_id=\''.$key.'\'';
                else
                    $where = $where . ' OR categorias_id=\''.$key.'\'';
                $c++;
            }
        }
        
        /**
         *  SQL para verificar na tabela CADASTRO_CONF quais campos existem
         */
        $sql = "SELECT
                    *, categorias_id AS cat,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node
                FROM
                    ".$this->config["arquitetura"]["table"]." AS conf ".
                $where.
                $order;
        unset($where);
        /**
         * Campos carregados
         */
        $result = $this->connection->query($sql, "ASSOC");
        /**
         * Configurações
         */
        $tP = "tabelaPrincipal";
        /**
         * Monta algumas arrays para montar um novo SQL definitivo
         *
         * $i = int
         */
        $i = 0;
        foreach($result as $dados){

            if ( in_array( $dados['tipo'], array('campo', 'campopw', 'campoarquivo', 'camporelacional_umparaum')) ){

                if($dados['listagem'] > 0 ){

                    if( $dados["especie"] == "relacional_umparaum" ){
                        $leftJoin[ $dados["chave"] ]["ref_tabela"] = $dados["ref_tabela"];
                        $leftJoin[ $dados["chave"] ]["ref_campo"] = $dados["ref_campo"];
                        $leftJoin[ $dados["chave"] ]["campoNome"] = $dados["valor"];
                    } else {
                        $mostrar['valor'][] = $dados['valor'];
                        $mostrar['chave'][] = $tP.".".$dados['chave']." AS '".$dados["valor"]."'";
                    }
                }

                if( !empty($dados['valor']) )
                    $campos['valor'][] = $dados['valor'];
                if( !empty($dados['chave']) )
                    $campos['chave'][] = $dados['chave'];

            } else if($dados['tipo'] == 'estrutura' AND $dados['chave'] == 'tabela'){
                $est['tabela'][] = $dados['valor'];
                $est['node'][] = $dados['categorias_id'];
            }
            $i++;
        }
        /**
         * LEFT JOIN?
         */
        if( !empty($leftJoin) ){
            $leftJoinTmp = $leftJoin;
            unset($leftJoin);

            if( is_array($leftJoinTmp) ){

                foreach( $leftJoinTmp as $chave=>$valor ){
                    /*
                     * Se há um LeftJOIN, elimina os campo destes do query
                     * principal
                     */
                    unset($mostrar[$chave]);

                    $refTabela = $valor["ref_tabela"];
                    $refCampo = $valor["ref_campo"];

                    $leftJoinCampos[$chave] = $refTabela.".".$refCampo." AS '".$valor["campoNome"]."'";
                    $leftJoin[ $refTabela ] = "LEFT JOIN ".$refTabela." AS ".$refTabela." ON ".$tP.".".$chave."=".$refTabela.".id";
                }
            }
            $virgula = ",";
        }
        /**
         * Segurança
         */
        else {
            $leftJoinCampos = array();
            $leftJoin = array();
            $virgula = "";
        }


        /*
         * SEARCH?
         *
         * Analisa se deve buscar por algo em específico.
         */
        $searchQuery = "";
        if( !empty($search) ){
            $search = addslashes($search);

            if( empty($searchField) ){
                /*
                 * Faz loop por cada campo do cadastro, criando
                 * o comando SQL Where para busca de dados.
                 */
                foreach( $campos['chave'] as $campo ){
                    $searchQueryArray[] = $campo." LIKE '%".$search."%'";
                }
            } else {
                $searchQueryArray[] = $searchField." LIKE '%".$search."%'";
            }
            
            if( !empty($searchQueryArray) )
                $searchQuery = "AND (".implode(" OR ", $searchQueryArray).")";
            //pr($campos);
        }

        /**
         * Novo SQL
         */
        if( $metodo == "listing" ){

            if( empty($mostrar) ){
                $mostrar = "id,";

            } else {
                $mostrar = implode(",", $mostrar["chave"]).",";

            }
			$fields = "".$tP.".id,
			            $mostrar
			            ".implode(", ", $leftJoinCampos).$virgula."
			            ".$tP.".approved AS des_approved";

            $conditions = "    
                    FROM
                        ".$est["tabela"][0]." AS ".$tP."

                    ".implode(" ", $leftJoin)."
                    WHERE
                        1=1
                        $searchQuery
                    ORDER BY
                        ".$tP.".id DESC
                    ";

			// total rows
			$countSql = "SELECT count(*) as rows ".$conditions;
			$this->totalRows = $this->_getTotalRows($countSql);
			
			$sql = "SELECT $fields ".$conditions.$this->_limitSql(array('page'=>$this->page()));
			
        } elseif( $metodo == "edit" ){
            $sql = "SELECT
                        id, ".implode(",", $campos["chave"])."
                    FROM
                        ".$est["tabela"][0]."
                    WHERE
                        id=".$w."
                    ";
        }
        return $sql;
    } // fim SQLParaListagem()

    /**
     * Função para retonar a tabela de dados de uma estrutra de cadastro
     *
     * @param mixed $param contém o id ou nome da estrutura desejada
     * @return array 
     */
    public function LeTabelaDaEstrutura($param){

        /**
         * $param é uma integer
         */
        if( is_int($param) or $param > 0 ){
            $estrutura = "categorias.id='".$param."'";
        }
        /**
         * $param é uma string
         */
        elseif( is_string($param) ){
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                    {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
                //echo $sql;
                
        $resultado = $this->connection->query($sql);
        $dados = $resultado[0];
        return $dados['valor'];
    }

    /*
     * Função para retornar o nome da tabela de dados de uma estrutura da cadastro
     */
    public function LeTabelaDeDados($param){

        if( !empty($this->dataTable) )
            return $this->dataTable;

        if(is_int($param) or $param > 0){
            $estrutura = "categorias.id='".$param."'";
        } elseif(is_string($param)){
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                    {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
                //echo $sql;
        $mysql = $this->connection->query($sql);
        $dados = $mysql[0];
        
        $this->dataTable = $dados['valor'];
        return $dados['valor'];
    }

    /*
     * Cria tabela responsável por guardar arquivos
     */
    function CriaTabelaArquivo($param){
        global $aust_charset;
        if (!empty($aust_charset['db']) and !empty($aust_charset['db_collate'])) {
                $charset = 'CHARACTER SET '.$aust_charset['db'].' COLLATE '.$aust_charset['db_collate'];
        }

        $sql = "SELECT
                    id
                FROM
                    ".$param['tabela']."_arquivos
                LIMIT 0,1
                ";
        $result = $this->connection->query($sql);
        if( count($result) == 0 ){
            $sql_arquivos =
                            "CREATE TABLE ".$param['tabela']."_arquivos(
                            id int auto_increment,
                            titulo varchar(120) {$charset},
                            descricao text {$charset},
                            local varchar(80) {$charset},
                            url text {$charset},
                            arquivo_nome varchar(250) {$charset},
                            arquivo_tipo varchar(250) {$charset},
                            arquivo_tamanho varchar(250) {$charset},
                            arquivo_extensao varchar(10) {$charset},
                            tipo varchar(80) {$charset},
                            referencia varchar(120) {$charset},
                            categorias_id int,
                            adddate datetime,
                            autor int,
                            PRIMARY KEY (id),
                            UNIQUE id (id)
                        ) ".$charset;
            if( $this->connection->exec($sql_arquivos) ){
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return 0;
        }
        return 0;
    }

    /*
     * Le informações do db
     */
    function LeDadosDoDB($tabela, $campo, $valor_condicao, $campo_condicao=''){

        if(empty($campo_condicao)){
            $where = "WHERE id='".$valor_condicao."'";
        } else {
            $where = "WHERE ".$campo_condicao."='".$valor_condicao."'";
        }
        $sql = "SELECT
                    ".$campo."
                FROM
                    ".$tabela."
                ".$where."
                LIMIT 0,1
                ";
        //echo $sql;
        $result = mysql_query($sql);
        if(mysql_num_row > 0){
            $dados = mysql_fetch_array($result);
            return $dados[$campo];
        } else {
            return 0;
        }
        return 0;
    }

    function PegaConfig($param){
        // ajusta variáveis
        $estrutura = $param['estrutura'];
        $chave = $param['chave'];
        // se a categoria passada estiver em formato Integer
        if(is_int($estrutura) or $estrutura > 0){
            $sql = "SELECT
                        *
                    FROM
                        cadastros_conf
                    WHERE
                        categorias_id='".$estrutura."' AND
                        chave='".$chave."'
                    ";
        } elseif(is_string($estrutura)){
            // se o parâmetro $param for uma string
            $sql = "SELECT
                        cadastros_conf.valor AS valor
                    FROM
                        cadastros_conf,categorias
                    WHERE
                        cadastros_conf.categorias_id=categorias.id AND
                        categorias.tipo='cadastro' AND
                        categorias.nome='".$estrutura."' AND
                        cadastros_conf.chave='".$chave."'
                    ";
        }

        $result = $this->connection->query($sql);
        if( count($result) > 0 ){
            $dados = $result[0];
            return $dados;
         } else {
            return FALSE;
         }

    }

    /*
     * INTERFACE DE SETUP
     *
     * Métodos para o setup de novas estruturas
     */

    public function setupAnalisaCamposTipagemFisica(){
        
    }

	/*
	 * INTERFACE DE CONFIGURAÇÃO DE ESTRUTURA
	 *
	 * 
	 */
	public function drawFieldConfiguration(){
    	$result = '';
	}
}

?>