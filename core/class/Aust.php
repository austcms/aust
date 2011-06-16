<?php
/**
 * AUST
 *
 * Classe que contém funções acerca de estrutura de banco de dados em formato árvore
 *
 * @package Classes
 * @name Aust
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

class Aust {

    /**
     *
     * @var string Tabela padrão do sistema Aust
     */
    static $austTable = 'categorias';
    protected $AustCategorias = Array();
    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    public $connection;
    protected $conexao;

    public $_recursiveLimit = 50;
    public $_recursiveCurrent = 1;

    function __construct($conexao = array()){
        $this->conexao = Connection::getInstance();
        $this->connection = Connection::getInstance();
        unset($this->AustCategorias);
    }

    /**
     * getInstance()
     *
     * Para Singleton
     *
     * @staticvar <object> $instance
     * @return <Aust object>
     */
    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Aust;
        }

        return $instance[0];

    }

    /*
     *
     * CATEGORIAS
     *
     */
	function getStructureInstance($austNode){
	    /**
	     * Identifica qual é a pasta do módulo responsável por esta
	     * estrutura/categoria
	     */
	    $modDir = $this->LeModuloDaEstrutura($austNode).'/';

	    /*
	     *
	     * INSTANCIA MÓDULO
	     *
	     */
	    /**
	     * Carrega arquivos principal do módulo requerido
	     */
	        include(MODULOS_DIR.$modDir.MOD_CONFIG);
	        /**
	         * Carrega classe do módulo e cria objeto
	         */
	        $module = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
	        include_once(MODULOS_DIR.$modDir.$module.'.php');

	        $param = array(
	            'config' => $modInfo,
	            'user' => User::getInstance(),
	        );

			
	        $object = new $module($param);
			$object->setAustNode($austNode);
			
			return $object;
	}
	
	/**
	 * create()
	 * 
	 * cria uma nova categoria ou estrutura
	 *
	 * @return int id criado
	 */
    public function create($params) {

        $catName = (empty($params['name'])) ? false : addslashes( str_replace("\n", "", $params['name']) );
        $father = (empty($params['father'])) ? false : $params['father'];
        $descricao = (empty($params['description'])) ? false : addslashes( $params['description'] );
        $autor = (empty($params['author'])) ? false : $params['author'];
        $permissao = (empty($params['permission'])) ? false : $params['permission'];
        $classe = (empty($params['class'])) ? 'categoria' : $params['class'];

        $tipo = (empty($params['type'])) ? '' : $params['type'];
        $tipoLegivel = (empty($params['type_name'])) ? '' : $params['type_name'];

        if( !$catName ) return false;
        if( !$father ) return false;

        /**
         * Faz um loop para descobrir qual é o patriarca
         * desta nova categoria a ser cadastrada.
         */
        $i = 0;
        $subordinadoidtmp = $father;

        while( $i < 1 ) {
            $sql = "SELECT
                        id, nome, subordinadoid, classe, nome_encoded,tipo,tipo_legivel
                    FROM
                        categorias
                    WHERE
                        id='$father'
                    ";

            $query = $this->conexao->query($sql);
			
			if( empty($query[0]) )
				return false;
				
            $dados = $query[0];

            if( empty($tipo) ) {
                $tipo = $dados['tipo'];
                $tipoLegivel = $dados['tipo_legivel'];
            }

            /*
             * Patriarca
            */
            if( !empty($dados['patriarca']) )
                $patriarca = $dados['patriarca'];
            else
                $patriarca = $this->getPatriarch($dados['id']);

            $catNameEncoded = encodeText( $catName );
            $patriarcaEncoded = encodeText( $patriarca );
            $subordinadoNomeEncoded = encodeText( $dados['nome'] );


            if($dados['classe'] == "estrutura") {
                $tipo = $this->LeModuloDaEstrutura($dados['id']);
                $i++;
            } else {
                $subordinadoidtmp = $dados['subordinadoid'];
                $i++;
            }
            $tipo_legivel = $this->LeModuloDaEstruturaLegivel($dados['id']);

        }

        $sql = "INSERT INTO
                    categorias
                    (
                        nome, nome_encoded,
                        descricao,
                        patriarca, patriarca_encoded,
                        subordinadoid,subordinado_nome_encoded,
                        classe,tipo,tipo_legivel,
                        autor
                    )
                VALUES
                    (
                        '{$catName}','{$catNameEncoded}',
                        '$descricao',
                        '$patriarca', '$patriarcaEncoded',
                        '$father', '$subordinadoNomeEncoded',
                        '$classe','$tipo','$tipo_legivel',
                        '".$autor."'
                    )";
        if( $this->conexao->exec($sql) ) {
            return (int) $this->conexao->lastInsertId();
        }

        return false;
    } // end create()

    public function getPatriarch($id) {

        if( $this->_recursiveCurrent > $this->_recursiveLimit )
            return false;

        $sql = "SELECT
                    id, nome, patriarca, subordinadoid, classe
                FROM
                    categorias
                WHERE
                    id='$id'";
		$query = $this->conexao->query($sql);
        $query = reset( $query );

        /**
         * Categoria sem patriarca, busca o patriarca do seu pai
         */
        if( empty($query['patriarca'])
                AND $query['classe'] == 'categoria' ) {
            $this->_recursiveCurrent++;
            return $this->getPatriarch($query['subordinadoid']);
        }
        /*
         * Sem patriarca, mas é uma estrutura, então é o próprio patriarca
        */
        elseif( empty($query['patriarca'])
                AND $query['classe'] == 'estrutura' ) {
            return $query['nome'];
        }
        /*
         * Tem um patriarca já definido.
        */
        else {
            $this->_recursiveCurrent = 1;
            return $query['patriarca'];
        }
    }

	/**
	 * deleteNodeImages( $node_id )
	 * 
	 * Exclui todas as imagens de um dado node.
	 * 
	 * @param int $node_id node_id da categoria
	 * @return bool
	 */
	function deleteNodeImages( $node_id ){
		
		$sql = "SELECT
					id, systempath
				FROM
					austnode_images
				WHERE
					node_id='".$node_id."'
				";
		
		$query = $this->connection->query($sql);
		foreach( $query as $key=>$value ){
			if( file_exists($value['systempath']) )
				unlink( $value['systempath'] );
			$sqlDelete = "DELETE FROM austnode_images WHERE id='".$value['id']."'";
			$this->connection->exec($sqlDelete);
		}
		
		return true;
	}

    /**
     * gravaEstrutura()
     *
     * Grava uma nova estrutura na tabela categorias
     *
     * @param array $param Contém os seguintes índices:
     *      string  [nome]              nome da estrutura;
     *      int     [categoriaChefe]    id do pai da estrutura
     *      string  [estrutura]         indica 'estrutura' se for estrutura, e senão um string sendo o tipo de categoria
     *      bool    [publico]           1, todos tem acesso (exceto quando especificadas permissões); 0, somente Webmaster tem acesso
     *      string  [modulo]            nome da pasta do módulo responsável por esta estrutura
     *      string  [autor]             autor da estrutura
     */
    function gravaEstrutura($params) {

        $nome = $params['nome'];
        $nome_encoded = (empty($params['nome_encoded'])) ? encodeText($nome) : $params['nome_encoded'] ;
        $categoria_chefe = $params['categoriaChefe'];
        $estrutura = (empty($params['estrutura'])) ? 'estrutura' : $params['estrutura'] ;
        $publico = (empty($params['publico'])) ? '1' : $params['publico'] ;
        $modulo = $params['moduloPasta'];
        $autor = $params['autor'];


        if(is_file('modulos/'.$modulo.'/config.php')) {
            include('modulos/'.$modulo.'/config.php');
            $tipo_legivel = $modInfo['nome'];
        } else {
            $tipo_legivel = NULL;
        }

        $sql = "INSERT INTO
            categorias
            (
            nome,subordinadoid,classe,tipo,tipo_legivel,publico,autor,
            nome_encoded
            )
                        VALUES
            (
            '$nome','$categoria_chefe','estrutura','$modulo','$tipo_legivel',$publico,'$autor',
            '$nome_encoded'
            )
                ";
        /**
         * Retorna o id do registro feito
         */
        if ($this->conexao->exec($sql)) {
            return $this->conexao->conn->lastInsertId();
        } else {
            return FALSE;
        }
    }

    public function Instalar($nome, $descricao = '', $classe = '', $tipo = '', $subordinadoid = '0') {
        $sql = "INSERT INTO
                    categorias
                        (nome,descricao,classe,tipo,subordinadoid)
                VALUES
                    ('$nome','$descricao','$classe','$tipo','$subordinadoid')";
        return $this->conexao->exec($sql);
    }

    /**
     *
     * LEITURA DE INFORMAÇÕES
     *
     */
    /**
     * Retorna categoria-chefe
     *
     * @todo - refatorar
     *
     * A string retornada pode conter sufixo e prefixo opcionais
     *
     * @param <type> $columns
     * @param <type> $formato
     * @param <type> $chardivisor
     * @param <type> $charend
     * @param <type> $order
     */
    public function LeCategoriaChefe($columns, $formato, $chardivisor = '', $charend = '', $order = '') {

        for($i = 0; $i < count($columns); $i++) {
            $fields .= $columns[$i];
            if($i != count($columns) - 1) {
                $fields .= ',';
            }
        }
        $sql = "SELECT
                    *
                FROM
                    categorias
                WHERE
                    classe='categoria-chefe'
                ";
        $query = $this->conexao->query($sql);
        $t = count($query);
        $c = 0;
        foreach($query as $menu) {
            $str = $formato;
            for($i = 0; $i < count($columns); $i++) {
                $str = str_replace("&%" . $columns[$i], $menu[$columns[$i]], $str);
            }
            echo $str;
            if($c < $t-1) {
                echo $chardivisor;
            } else {
                echo $charend;
            }
            $c++;
        }
    }

    /**
     * getStructures()
     *
     * Get all sites and its substructures.
     *
     * @return <array>
     */
    public function getStructures($params = array()) {
	
		$where = '';
		if( !empty($params['site']) && is_numeric($params['site']) )
			$where = "AND c.id='".$params['site']."'";
        /**
         * SITES
         */
        $sql = "SELECT
                    c.*, c.nome as name
                FROM
                    categorias AS c
                WHERE
                    c.subordinadoid='0'
					$where
                ";

        $query = $this->conexao->query($sql);
        $result = array();
		$stIds = array();
        /*
         * Each site
        */
        foreach( $query as $key=>$sites) {
            $result[$key]['Site'] = $sites;

            /*
             * Get Structures of each site
            */
            $structures = $this->getStructuresByFather($sites['id']);
            if( is_array($structures) ) {

				foreach( $structures as $sts ){
					$stIds[] = $sts['id'];
				}
                $result[$key]['Structures'] = $structures;
            }
        }

		$slaves = $this->getRelatedSlaves($stIds);
		$masters = $this->getRelatedMasters($stIds);
		if( !empty($slaves) ){
			// loop pelos sites
			foreach( $result as $siteKey=>$site ){
				// loop pelas estruturas
				foreach( $site['Structures'] as $stKey=>$st ){
					
					if( array_key_exists($st['id'], $slaves) ){
						
						$result[$siteKey]['Structures'][$stKey]['slaves'] = $slaves[$st['id']];
					}
					
				}
			}
			
		}
		if( !empty($masters) ){
			// loop pelos sites
			foreach( $result as $siteKey=>$site ){
				// loop pelas estruturas
				foreach( $site['Structures'] as $stKey=>$st ){
					
					if( array_key_exists($st['id'], $masters) ){
						
						$result[$siteKey]['Structures'][$stKey]['masters'] = $masters[$st['id']];
					}
					
				}
			}
			
		}
        return $result;

    } // end getStructures

	function getRelatedSlaves($ids = array()){
		if( empty($ids) )
			return array();
		
		if( is_string($ids) )
			$ids = array($ids);
		
		$whereStatement = "master_id IN ('".implode("','", $ids)."')";
		
		$sql = "SELECT * FROM
					aust_relations
				WHERE
					$whereStatement
				";
		$result = $this->connection->query($sql);
		if( empty($result) )
			return array();
		
		$return = array();
		foreach( $result as $slave ){
			$return[$slave['master_id']][] = $slave;
		}
		
		return $return;
	}

	function getRelatedMasters($ids = array()){
		if( empty($ids) )
			return array();
		
		if( is_string($ids) )
			$ids = array($ids);
		
		$whereStatement = "slave_id IN ('".implode("','", $ids)."')";
		
		$sql = "SELECT * FROM
					aust_relations
				WHERE
					$whereStatement
				";
		
		$result = $this->connection->query($sql);
		if( empty($result) )
			return array();
		
		$return = array();
		foreach( $result as $master ){
			$return[$master['slave_id']][] = $master;
		}
		
		return $return;
	}	
    /**
     * getStructuresByFather()
     *
     * Fetch all structures of a given father
     *
     * @param <int> $id
     * @return <array>
     */
    public function getStructuresByFather($id='') {
        if( empty($id) )
            return false;

        /*
         * Structures of given site
        */
        $sql = "SELECT
                    lp.*, lp.nome as name, lp.tipo as tipo,
                    ( SELECT COUNT(*)
                    FROM
                    ".self::$austTable." As clp
                    WHERE
                    clp.subordinadoid=lp.id
                    ) As num_sub_nodes
                FROM
                    ".self::$austTable." AS lp
                WHERE
                    lp.subordinadoid = '".$id."' AND
                    lp.classe = 'estrutura'
                ORDER BY
                    lp.tipo DESC,
                    lp.nome ASC
        ";
        $query = $this->conexao->query($sql);

        return $query;
    } // end getStructuresByFather()

    /**
     * @todo - refatorar
     *
     * Lê somente as estruturas do site e retorna segundo o formato indicado
     *
     * @param <type> $columns
     * @param <type> $formato
     * @param <type> $chardivisor
     * @param <type> $charend
     * @param <type> $order
     * @param <type> $options
     */
    public function LeEstruturas($columns, $formato, $chardivisor = '', $charend = '', $order = '', $options = '') {
        $fields = '';
        for($i = 0; $i < count($columns); $i++) {
            $fields .= $columns[$i];
            if($i != count($columns) - 1) {
                $fields .= ',';
            }
        }
        $sql = "SELECT
                    *
                FROM
                    categorias
                WHERE
                    classe='estrutura'
                ";
        $query = $this->conexao->query($sql);
        $t = count($query);
        $c = 0;
        foreach($query as $menu) {
            $str = $formato;
            for($i = 0; $i < count($columns); $i++) {
                $str = str_replace("&%" . $columns[$i], $menu[$columns[$i]], $str);
            }
            if(!empty($options)) {
                $diretorio = 'modulos/'.$menu['tipo']; // pega o endereço do diretório
                foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
                    if(is_file($pastas.'/configurar_estrutura.php')) {
                        $str = str_replace('&%options', '<a href="adm_main.php?section='.$_GET['section'].'&aust_node='.$menu['id'].'&action=configurar">Configurar</a>', $str);
                    }
                }
                $str = str_replace("&%options", "", $str);
            }
            echo $str;
            if($c < $t-1) {
                echo $chardivisor;
            } else {
                echo $charend;
            }
            $c++;
        }
    }

    /**
     * Retorna informações da estrutura selecionada.
     *
     * O código retorna a linha inteira da tabela de categorias.
     *
     * @param int $austNode
     * @return array
     */
    public function pegaInformacoesDeEstrutura( $austNode ) {
        /**
         * Busca na tabela do Aust onde o id é igual à estrutura requisitada.
         */
        $result = $this->conexao->query("SELECT * FROM ".Registry::read("austTable")." WHERE id='".$austNode."'" );
        return $result;
    }

    /**
     * Retorna o nome de cada estrutura do sistema (notícias, artigos, etc) no formato ARRAY
     *
     * @param array $param
     * @return array
     */
    function LeEstruturasParaArray($param = '') {
        if(!empty($param['where'])) {
            $where = $param['where'];
        } else {
            $where = "classe='estrutura'";
        }

        /**
         * $orderby: Ordenado por
         */
        $orderby = (empty($param['orderby'])) ? '' : $param['orderby'];
        /**
         * $limit: Limite de rows retornados
         */
        $limit = (empty($param['limit'])) ? '' : $param['limit'];


        // seleciona todas as estruturas
        $sql = "SELECT
                    *
                FROM
                    categorias
                WHERE
                    ".$where."
                ".$orderby." ".$limit;
        $query = $this->conexao->query($sql);

        //pr($mysql);

        $estruturas_array = array();
        //while($dados = mysql_fetch_array($mysql)){
        foreach($query as $chave=>$valor) {
            $estruturas_array[$valor['id']]['nome'] = $valor['nome'];
            $estruturas_array[$valor['id']]['tipo'] = $valor['tipo'];
            $estruturas_array[$valor['id']]['id'] = $valor['id'];
        }
        return $estruturas_array;
    }
    // retorna o módulo responsável por determinada estrutura
    function LeModuloDaEstrutura($node) {
        $sql = "SELECT
                                tipo
                        FROM
                                categorias
                        WHERE
                                id=$node";
        $query = $this->conexao->query($sql);
        return $query[0]['tipo'];
    }

    // retorna o nome legível do módulo
    function LeModuloDaEstruturaLegivel($node) {
        $sql = "SELECT
                                tipo
                        FROM
                                categorias
                        WHERE
                                id=$node";

        $query = $this->conexao->query($sql);
        $tipo = $query[0]['tipo'];
        if(is_file('modulos/'.$tipo.'/config.php')) {
            include('modulos/'.$tipo.'/config.php');
            return $modInfo['nome'];
        } else {
            return NULL;
        }
    }

    public function getField($node, $field = '') {
		if( empty($field) )
			$field = "*";
			
        $sql = "SELECT
                    $field
                FROM
                    categorias
                WHERE
                    id=$node";
        $query = $this->conexao->query($sql);
		if( $field == "*" )
	        return $query[0];
		else
        	return $query[0][$field];
    }

    /**
     * Retorna o nome de estrutura/categoria de acordo com seu ID
     *
     * @param int $node É o ID da estrutura/categoria a ser buscada
     * @return string Nome da estrutura/categoria
     */
    public function leNomeDaEstrutura($node) {
        $sql = "SELECT
                    nome
                FROM
                    categorias
                WHERE
                    id=$node";
        $query = $this->conexao->query($sql);
        return $query[0]['nome'];
    }

    function LimpaVariavelCategorias() {
        if(is_array($this->AustCategorias)) {
            foreach($this->AustCategorias as $key=>$valor) {
                array_pop($this->AustCategorias);
            }
        }
    }


    /**
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    function categoriasFilhas($params) {

        /**
         * Trata cada variável recebida
         */
        $pai = (empty($params['pai'])) ? 0 : $params['pai'];
        $categoriaChefe = (empty($params['categoriaChefe'])) ? '' : $params['categoriaChefe'];
        $nivel = (empty($params['nivel'])) ? 0 : $params['nivel'];
        $nodeAtual = (empty($params['nodeAtual'])) ? 0 : $params['nodeAtual'];
        /**
         * Precisa-se melhorar esta função. Infelizmente, PHP 5.2 ainda não suporte método dentro de método,
         * portanto precisamos usar um método externo
         */
        $this->LeCategoriasFilhasCopy($categoriaChefe, $pai, $nivel, $nodeAtual); // gambiarra

        if($pai >= 0) {
            $this->AustCategorias[$pai] = '';
        }
        $resultado = $this->AustCategorias;
        $this->LimpaVariavelCategorias();
        return $resultado;
    }

    // gambiarra para que LeCategoriasFilhas possa rodar em loop e retornar $this->AustCategorias e limpando esta variÃ¡vel no final
    function LeCategoriasFilhasCopy($categoriachefe, $parent=0, $level=0, $current_node=-1) {
        /**
         * Guarda qual o id do pai para carregar suas filhas
         */
        $where = "lp.subordinadoid = '$parent'";
        /**
         * Se não for especificada uma estrutura, carrega todas as categorias da categoria chefe
         * especificada
         */
        if($parent == 0) {
            if(is_int($categoriachefe)) {
                $where = $where . " AND lp.id='".$categoriachefe."'";
            } elseif(is_string($categoriachefe)) {
                $where = $where . " AND lp.nome='".$categoriachefe."'";
            }
        }
        /**
         * Monta o SQL
         */
        $sql="SELECT
					lp.id, lp.subordinadoid, lp.nome, lp.classe,
					( SELECT COUNT(*)
						FROM
							".self::$austTable." As clp
						WHERE
							clp.subordinadoid=lp.id
					) As num_sub_nodes
				FROM
					".self::$austTable." AS lp
				WHERE
                $where
                ";

        $query = $this->conexao->query($sql);

        $i = 0;
        $items = '';
        foreach ( $query as $chave=>$myrow ) {

            $this->AustCategorias[$myrow['id']] = $myrow['nome'];

            //chamar recursivamente a função
            $items.=$this->LeCategoriasFilhasCopy($categoriachefe, $myrow["id"], $level+1, $current_node);

        }
    }

    /**
     * DEPRECIADO!!!!!
     *
     * Use categoriasFilhas() no lugar desta
     *
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    function LeCategoriasFilhas($categoriachefe, $parent=0, $level=0, $current_node=-1) {
        //trigger_error('Use categoriasFilhas() instead', E_USER_NOTICE);

        $this->LeCategoriasFilhasCopy($categoriachefe, $parent, $level, $current_node); // gambiarra

        if($parent >= 0) {
            $this->AustCategorias[$parent] = 'tetesteste';
        }
        $resultado = $this->AustCategorias;
        $this->LimpaVariavelCategorias();
        return $resultado;
    }

    /**
     * DEPRECIADO!!!!!
     *
     * Use categoriasFilhas() no lugar desta
     *
     * Retorna todas as filhas da categoria
     *
     * @author Alexandre de Oliveira (chavedomundo@gmail.com)
     *
     * @param string    $categoriachefe
     * @param int       $parent
     * @param int       $level
     * @param int       $current_node
     * @return array    retorna array com todas as filhas da categoria dita categorias requisitadas
     */
    // LISTAR: funÃ§Ã£o que retorna diretÃ³rio e arquivo para include da listagem do mÃ³dulo da estrutura com id $aust_node
    function AustListar($aust_node = '0') {
        $pasta_do_modulo = $this->LeModuloDaEstrutura($aust_node);
        if(is_file('modulos/'.$pasta_do_modulo.'/listar.php')) {
            return 'modulos/'.$pasta_do_modulo.'/listar.php';
        } else {
            return 'conteudo.inc/listar.inc.php';
        }
    }

    // Lê somente estruturas que não devem ter categorias e grava em uma $_SESSION
    function EstruturasSemCategorias() {
        unset( $_SESSION['somenteestrutura']);
        $diretorio = 'modulos/'; // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if(is_file($pastas.'/config.php')) {
                include($pastas.'/config.php');
                if($modInfo['somenteestrutura']) {

                    $tmparray = array_reverse( explode("/", $pastas));
                    $_SESSION['somenteestrutura'][] = $tmparray[0];
                }
                //echo 'oi' ;
            }
        }


    }


    /**
     * VERIFICAÇÕES
     */

    // verifica se existe alguma categoria instalada e retorna TRUE ou FALSE
    public function Instalado() {
        $sql = "SELECT
                    id
                FROM
                    categorias";
        return $this->conexao->count($sql);
    }

    /*
     *
     * RENDERIZAÇÃO
     *
     */
    /**
     * getCategoryHtmlSelect()
     *
     * Retorna <select> com as categorias atuais
     *
     * @param <type> $austNode
     * @param <type> $currentNode
     * @return <string>
     */
    public function getCategoryHtmlSelect($austNode, $currentNode = ''){
        include_once (THIS_TO_BASEURL."core/inc/inc_categorias_functions.php");
        $tmp = BuildDDList( Registry::read('austTable') ,'frmcategoria', $administrador->tipo ,$austNode, $currentNode, false, true);
        return $tmp;
    }

}


?>