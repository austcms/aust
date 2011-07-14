<?php
/**
 * abstract Modulo
 *
 * Superclasse dos módulos
 *
 * @package Classes
 * @name Módulos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.5, 30/05/2009
 */
class Module extends ActiveModule
{

    /*
     *
     * CONFIGURAÇÕES ESPECÍFICAS DO MÓDULO
     *
     */
        /**
         *
         * @var <string> Tabela principal de dados
         */
        public $mainTable;

        /**
         *
         * @var <array> Formatos de data
         */
        public $date = array(
            'standardFormat' => '%d/%m/%Y',
            'created_on' => 'created_on',
            'updated_on' => 'update_on'
        );

        public $fieldsToLoad = array(
            'titulo', 'visitantes'
        );

		public $authorField = "admin_id";

        public $austField = 'categoria';
        public $order = 'id DESC';

		public $defaultLimit = '25';
		public $limit;
		
		public $viewModes = array();

    /*
     *
     * VARIÁVEIS DE QUERY
     *
     */
        /**
         *
         * @var <int> Id em uso
         */
        public $w;

        /**
         *
         * @var <array>
         */
        public $loadedIds;

        /**
         *
         * @var <string> Contém a última SQL criada.
         */
        public $lastSql;
        /**
         *
         * @var <string> Contém a última SQL criada para contar quantos registros
		 * há no DB.
         */
        public $lastCountSql;
        /**
         *
         * @var <int> Total de registros no DB.
         */
        public $totalRows;
        /**
         *
         * @var <array> Último resultado de query executado.
         */
        public $lastQuery;
    
    /**
     * VARIÁVEIS DO MÓDULO
     */
        /**
         *
         * @var <array> Contém a tabela atual descrita
         */
        public $describedTable;
        /**
         *
         * @var <string> erros e sucessos das operações
         */
        public $status;

    /**
     * VARIÁVEIS DE AMBIENTE
     *
     * Conexão com banco de dados, sistema Aust, entre outros
     */
        /**
         *
         * @var class Classe responsável pela conexão com o banco de dados
         */
        public $connection;

        /**
         *
         * @var <int> Contém o número do Nodo atual
         */
        public $austNode;
        /**
         *
         * @var class Classe responsável pela conexão com o banco de dados
         */
        public $aust;
        /**
         *
         * @var array Configurações estáticas do módulo
         */
        public $config;
        /**
         *
         * @var array Configurações estáticas do módulo
         */
        public $structureConfig;
        /**
         *
         * @var array Configurações estáticas do módulo
         */
        public $structureFieldsConfig;

        public $params;

    /**
     *
     * @var <bool> Indica se este é um teste. O sendo, não realiza
     * alguns procedimentos impossíveis de serem realizados via
     * testes unitários, como envio HTTP de dados.
     */
    public $testMode = false;

    /**
     * __CONSTRUCT()
     *
     * @param array $param:
     *      'conexao': Contém a conexão universal
     */
    function __construct($austNode = ""){

        $this->austNode($austNode);

        if( !empty($_GET['w']) AND is_numeric($_GET['w']) )
            $this->w = $_GET['w'];

        /**
         * Ajusta a conexao para o módulo
         */
            $this->connection = Connection::getInstance();
        /**
         * Usuário atual
         */
            $this->user = User::getInstance();

		if( !defined("TESTING") || !TESTING )
        	$this->config = $this->loadConfig();
		
		if( !empty($this->config['viewmodes']) )
			$this->viewModes = $this->config['viewmodes'];
		else
			$this->viewModes = array('list');

		$this->limit = $this->defaultLimit;
    }
	
	function setAustNode($id){
		$this->austNode = $id;
	}

    /*
     *
     * CRUD
     *
     */

	public function fixEncoding($post = array()){
		if( Connection::getInstance()->encoding != 'utf8' )
			return $post;

		foreach( $post as $key=>$value ){
			if( is_string($value) &&
				!mb_check_encoding($value, 'UTF-8') )
			{
				$value = mb_convert_encoding($value, "UTF-8", "auto");
			}
			
			$post[$key] = $value;
		}
		
		return $post;
	}
    /**
     * save()
     *
     * Comando que deve poder ser chamado por qualquer módulo.
     *
     * Este super-método serve estritamente para salvar dados
     * no DB. Se um módulo precisa algo diferente disto,
     * sobrescreva este método na classe do módulo.
     *
     * @param <array> $post
     * @return <bool>
     */
    public function save($post = array(), $params = false){

		$post = $this->fixEncoding($post);

        if( empty($post['method']) AND
            empty($post['metodo']) )
        {
            throw new Exception("Opção 'method' não especificado em Module");
            return false;
        }

        if( !empty($post['method']) )
            $method = $post['method'];
        else if( !empty($post['metodo']) )
            $method = $post['metodo'];

        /*
         * Gera SQL
         */
        $sql = $this->generateSqlFromForm($post, $method);
        /**
         * Salva no DB
         */
        if( Connection::getInstance()->exec($sql) !== false ){

            if( !empty($post['w']) OR $post['w'] > 0 ){
                $this->w = $post['w'];
            } else {
                $this->w = Connection::getInstance()->conn->lastInsertId();
            }

            /*
             *
             * EMBED SAVE
             *
             */
            $this->saveEmbeddedModules($this->getDataForEmbeddedSaving($post));

            return true;
        }

        return false;
    }
    /**
     * load()
     *
     * Responsável por carregar dados-padrão da estrutura,
     * como para listagens.
     *
     * @return <array>
     */
    public function load($param = ''){
        $this->loadedIds = array();
        $paramForLoadSql = $param;

        /*
         * austNode é um conjunto de arrays
         */
		if( !empty($param['austNode']) ){
	        if( is_array($param['austNode']) ){
				if( !is_numeric($param['austNode']) ){
					$arrayKeys = array_keys($param['austNode']);
	            	$austNode = reset($arrayKeys);
				} else
		            $austNode = $param['austNode'];
			} else if( is_numeric($param['austNode']) ){
	            $austNode = $param['austNode'];
	            $paramForLoadSql['austNode'] = $austNode;
			}
        }
        /*
         * $params contém mais condições para a busca
         */
        elseif( is_array($param) ){
			
           	$austNode = array($param['austNode']=>'');
            $paramForLoadSql['austNode'] = array($param['austNode']=>'');
        }
        /*
         * Se $params é um número, significa que é um número
         */
        elseif( is_numeric($param) ){
            $austNode = array( 'austNode' => '' );
            $paramForLoadSql['id'] = $param;

        }

		// counts rows
		$this->totalRows = $this->_getTotalRows($paramForLoadSql);
		
		$sql = $this->loadSql($paramForLoadSql);

        $qry = Connection::getInstance()->query($sql);
        if( empty($qry) )
            return array();

        $qry = $this->_organizesLoadedData($qry);

        $embedModules = $this->getRelatedEmbed($austNode);

        if( empty($embedModules)
            OR empty($this->loadedIds) )
        {
            $qry = serializeArray($qry);
            $this->lastQuery = $qry;
            return $qry;
        }

        /*
         * Carrega dados dos módulos relacionados via embed
         */
        $paramForEmbedLoad = array(
            'target_id' => $this->loadedIds,
            'target_table' => $this->useThisTable()
        );

        foreach( $embedModules as $object ){
            $embedResults[get_class($object)] = $object->loadEmbed($paramForEmbedLoad);
            //$qry = array_merge( $qry, $object->loadEmbed($paramForEmbedLoad) );
        }

        foreach( $embedResults as $module=>$embedResult ){
            foreach( $embedResult as $mainId=>$eachEmbed ){
                $qry[$mainId][$module] = $eachEmbed;
            }
        }
        
        $qry = serializeArray($qry);
        $this->lastQuery = $qry;
        return $qry;
    }

    /**
     * _totalRows()
     *
     * Organiza uma array com os dados carregados de um db, botando
     * como chave de cada índice da array o id do registro.
     *
     * @param <mixed> $param O SQL para contar registros ou opções Array
     * @return <int>
     */
	function _getTotalRows($param){
		if( is_array($param) ){
			$param['countTotalRows'] = true;
			$param = $this->loadSql($param);
		}
		
		if( is_string($param) ){
			$query = Connection::getInstance()->query($param);
			$result = reset( $query );
			$total = ( !empty($result['rows']) && is_numeric($result['rows']) ) ? $result['rows'] : 0;
		} else {
			return 0;
		}
		
		return $total;
	}

    /**
     * _organizesLoadedData()
     *
     * Organiza uma array com os dados carregados de um db, botando
     * como chave de cada índice da array o id do registro.
     *
     * @param <array> $results
     * @return <array>
     */
    public function _organizesLoadedData($results){

        $result = array();
        foreach($results as $valor){
            if( !empty($valor['id']) ){
                $result[$valor['id']] = $valor;
                $this->loadedIds[] = $valor['id'];
            } else
                $result[] = $valor;
        }

        return $result;
    }

    /**
     * loadSql()
     *
     * Tenta ser genérico para todos os módulos
     *
     * Retorna simplesmente o SQL para então executar Query
     */
    public function loadSql($options = array()){
        /*
         * SET DEFAULT OPTIONS
         */
        require_once(LIB_DATA_TYPES);
        /*
         * Default options
         */
        if( !empty($options['categorias'])
            AND is_array($options) )
        {
            print $options['categorias'];

            print("Argumento <strong>categorias</strong> ultrapassada em \$module->loadSql. Use \$options['austNode'].");
            exit(0);
        }
		/* gera sql para descobrir o número total de rows */
		if( is_array($options) AND !empty($options['countTotalRows']) AND $options['countTotalRows'] ){
			$options['limit'] = false;
			$options['page'] = false;
			$options['countTotalRows'] = true;
		}
        /*
         * $options sendo array, pode ter várias condições. se $options é
         * numérico, busca por id.
         */
		$defaultLimit = $this->defaultLimit;

        $id = null;
        $austNode = null;
        $page = null;
        $customWhere = null;
        $order = 'id DESC';

        if( is_array($options) ){
            $id = empty($options['id']) ? '' : $options['id'];
            $austNode = empty($options['austNode']) ? array() : $options['austNode'];
            $page = empty($options['page']) ? false : $options['page'];
            $limit = empty($options['limit']) ? $defaultLimit : $options['limit'];
            $customWhere = empty($options['where']) ? '' : ' '.$options['where'];

            if( empty($options['order']) ){
                if( empty($this->order) )
                    $order = 'id DESC';
                $order = $this->order;
            } elseif( is_string($options['order']) ) {
                $order = $options['order'];
            }

        } elseif( is_numeric($options) ){
            $id = $options;
            $limit = $defaultLimit;
        }

        if( !empty($options)
            AND !is_array($options) )
            $id = $options;
        
        if( !empty($id) ){
            if( is_array($id) ){
                $id = " AND id IN ('".implode("','", $id)."')";
            } else {
                $id = " AND id='$id'";
            }
        }

        /*
         * Gera condições para sql
         */
        $where = '';
        if( !empty($austNode) ) {
            if( !is_array($austNode) ){
	            $austNodeForSql = $austNode;
			} else if(is_array($austNode)) {
	            $austNodeForSql = implode("','", array_keys($austNode) );
			}
            $where = $where . " AND ".$this->austField." IN ('".$austNodeForSql."')";
        }

		$userId = User::getInstance()->getId();
		if( !in_array(
				User::getInstance()->type(),
				array('Webmaster', 'Root', 'root', 'Moderador', 'Administrador')
			) &&
			!empty($userId) &&
			( Connection::getInstance()->tableHasField($this->useThisTable(), $this->authorField) )
		)
		{
			$where .= " AND (".$this->authorField." = ".User::getInstance()->getId().")";
		}

		/*
		 * Limit
		 */
		$limitStr = '';
		$limitParams = array(
			'page' => ( empty($page) ) ? false : $page,
			'limit' => ( empty($limit) ) ? false : $limit,
		);
		/*
		 * Limit definido?
		 */
		if( !empty($limit) AND empty($options['countTotalRows']) ){
            $limitStr = $this->_limitSql( $limitParams );
		}
		/*
		 * Contando rows somente
		 */
		elseif( !empty($options['countTotalRows']) AND $options['countTotalRows'] ) {
			$limitStr = ' LIMIT 0,1';
		}

        if( empty($this->describedTable[$this->useThisTable()]) ){
            $tempDescribe = Connection::getInstance()->query('DESCRIBE '.$this->useThisTable());
            foreach( $tempDescribe as $fields ){
                $this->describedTable[$this->useThisTable()][$fields['Field']] = $fields;
            }
        }

        $fieldsInSql = array();
        $fields = 'id, ';
        if( !empty( $this->describedTable[$this->useThisTable()] ) ){
            $fieldsToLoad = $this->fieldsToLoad;
            if( !is_array($fieldsToLoad) ){
                $fieldsToLoad = array($fieldsToLoad);
            }

            foreach( $fieldsToLoad as $field ){
                //if( array_key_exists($field, $this->describedTable[$this->useThisTable()]) ){
                if( $field == "*"){
                    unset($fieldsInSql);
                    $fieldsInSql[] = "*";
                    $fields = "";
                    break;
                } else {
                    $fieldsInSql[] = $field;
                }
            }

        }

        if( !empty($fieldsInSql) )
            $fields.= implode(', ', $fieldsInSql).',';

		/*
		 * countTotalRows
		 */
		if( !empty($options['countTotalRows']) AND $options['countTotalRows'] === true )
			$fields = 'count(id) as rows, ';
		
		
        /*
         * Sql para listagem
         */
        $sql = "SELECT
                    $fields
                    ".$this->austField." AS cat,
                    DATE_FORMAT(".$this->date['created_on'].", '".$this->date['standardFormat']."') as adddate,
                    (	SELECT
                            nome
                        FROM
                            categorias AS c
                        WHERE
                            id=cat
                    ) AS node
                FROM
                    ".$this->useThisTable()."
                WHERE 1=1$id".
                $where."".$customWhere.
                " ORDER BY ".$order."
                $limitStr";

		if( empty($options['countTotalRows']) )
        	$this->lastSql = $sql;
		else
			$this->lastCountSql = $sql;
			
        return $sql;
    }

    /**
     * _limitSql()
     *
     * Retorna o LIMIT de um sql
     *
     * @param <array> $params
     * @return <string>
     */
	function _limitSql($params){
		
		// page
		if( empty($params['page']) OR !is_numeric($params['page']) ){
			$page = $this->page();
		} else {
			$page = $params['page'];
		}
			
        if( $page <= 0 OR !is_numeric($page) )
            $page = 1;
		
		if( empty($params['limit']) OR !is_numeric($params['limit']) ){
			$limit = $this->defaultLimit;
		} else {
			$limit = $params['limit'];
			$this->limit = $limit;
		}
		
        $pageLimit = (($page-1) * $limit);

        $result = " LIMIT ".$pageLimit.",".$limit;

		return $result;
	} // fim _limitSql()
	
	function page(){
		$page = 1;
		if( !empty($_GET['page']) )
			$page = $_GET['page'];
		elseif( !empty($_GET['pagina']) )
			$page = $_GET['pagina'];
			
		return $page;
	}

    /**
     * delete()
     *
     * @param <string> $table
     * @param <array> $conditions
     * @return <integer>
     */
    public function delete($id){

        if( is_int($id) OR is_string($id) ){

            $sql = "DELETE
                    FROM
                        ".$this->useThisTable()."
                    WHERE
                        id='$id'
                ";

            $result = Connection::getInstance()->exec($sql);

            if( $result )
                return true;

        }
        return false;
    }

    /*
     * CRUD -> SUPPORT
     */
    public function generateSqlFromForm($post, $method = 'new'){

        if( !empty($post['w']) OR
			!empty($post['id']) )
            $method = 'edit';

        $c = 0;
		
        $where = "";
        foreach($post as $key=>$valor){
            /*
             * Verifica se $post contém algum 'frm' no início
             */
            if(strpos($key, 'frm') === 0){
                $valor = addslashes( $valor );
                $sqlcampo[] = str_replace('frm', '', $key);
                $sqlvalor[] = $valor;

                /*
                 * Ajusta os campos da tabela nos quais serão gravados dados
                 */
                if($method == 'edit'){
                    if($c > 0){
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key).'=\''.$valor.'\'';
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key).'=\''.$valor.'\'';
                    }
                } else {
                    if($c > 0){
                        $sqlcampostr = $sqlcampostr.','.str_replace('frm', '', $key);
                        $sqlvalorstr = $sqlvalorstr.",'".$valor."'";
                        $where .= " AND ".str_replace('frm', '', $key) ."='".$valor."'";
                    } else {
                        $sqlcampostr = str_replace('frm', '', $key);
                        $sqlvalorstr = "'".$valor."'";
                        $where .= str_replace('frm', '', $key) ."='".$valor."'";
                    }
                }

                $c++;
            }
        }

        if($method == 'edit' OR !empty($post['w'])){
            $total = 0;
            $sql = "UPDATE ".$this->useThisTable()." SET $sqlcampostr
                    WHERE id='".$post['w']."'";
        } else {
            $sql = "INSERT INTO ".$this->useThisTable()." ($sqlcampostr)
                    VALUES ({$sqlvalorstr})";
        }

        return $sql;
    }

    public function getGeneratedUrl($w = ""){

        $result = $this->getStructureConfig('generate_preview_url');
		
        if( empty($w) AND empty($this->w) )
            return false;
        else if( empty($w) AND is_numeric($this->w)){
            $w = $this->w;

            $result = str_replace("%id", $w, $result);

            $lastQuery = array();
            if( count($this->lastQuery) >= 1 ){
                $lastQuery = reset($this->lastQuery);
            }
            if( !empty($lastQuery['titulo_encoded']) ){
                $titleEncoded = $lastQuery['titulo_encoded'];

                $result = str_replace("%title_encoded", $titleEncoded, $result);
            }
            
        }

        return $result;
    }

    /**
     * useThisTable()
     *
     * Se $this->useThisTable existe, retorna-a. Senão, retorna
     * $this->mainTable.
     *
     * @return <string> Tabela a ser usada
     */
    function useThisTable(){
        if( empty($this->useThisTable) )
            return $this->mainTable;

        return $this->useThisTable;
    }
    /**
     * alias getContentTable()
     *
     * Retorna o nome da tabela principal.
     *
     * @return <string>
     */
    public function getContentTable(){
        return $this->useThisTable();
    }
    /**
     * alias getMainTable()
     *
     * Retorna o nome da tabela principal.
     *
     * @return <string>
     */
    public function getMainTable(){
        return $this->useThisTable();
    }

    /*
     *
     * EMBED
     *
     */
    /*
     * EMBED -> CRUD
     */
    /**
     * saveEmbed()
     *
     * Após save() de um módulo X ser invocado, saveEmbed() é chamado
     * em cada módulo relacionado a X pela forma Embed.
     *
     * Quanto aos parâmetros, eis o formato correto:
     *
     *      Valores necessários:
     *      array(
     *          # dados de cada módulo embed, em 0, 1, 2, ..., n
     *          'embedModules' => array(
     *              0 => array(
     *                  'className' => 'NomeDaClasseDesteMódulo',
     *                  'dir' => 'diretório/do/módulo'
     *                  'data' => array(
     *                      'contém todos os dados que serão salvos'
     *                  )
     *              ),
     *              1 => valores_da_segunda_estrutura_embed...,
     *              # provavelmente o formulário já terá os valores
     *              # inputs dos embed de forma que este formato já
     *              # esteja pronto
     *          ),
     *          'options' => array(
     *              targetTable => 'nome_da_tabela_da_estrutura_líder',
     *              id => 'last_insert_id da estrutura líder',
     *              # como a estrutura líder há pouco inseriu um novo
     *              # registro, o id deste estará na chave acima.
     *          )
     *      )
     *
     * @param <array> $params
     * @return <bool>
     */
    public function saveEmbed($params = array()){
        return false;
    }

    public function deleteEmbed(){
        return false;
    }

    public function loadEmbed($param){
        $targetId = $param['target_id'];

        if( !is_array($targetId) )
            $targetId = array($targetId);

        $targetTable = $param['target_table'];

        $sql = "SELECT
                    id, privilegio_id,
                    target_id
                FROM
                    ".$this->useThisTable()."
                WHERE
                    target_id IN ('". implode("','", $targetId)."') AND
                    target_table='$targetTable'
                ";

        $query = Connection::getInstance()->query($sql);

        if( empty($query) )
            return array();

        foreach( $query as $valor ){
            $result[$valor['target_id']][] = $valor;
        }

        return $result;
    }

    /*
     * EMBED -> DEFINIÇÕES
     */

    /**
     * getDataForEmbeddedSaving()
     *
     * Retorna todos os dados necessário para começar a salvar
     * os embed.
     *
     * @param <array> $post
     * @return <array>
     */
    function getDataForEmbeddedSaving($post){
        if( !isset($post['embed']) )
            $post['embed'] = array();
        
        $embedData = array(
            'embedModules' => $post['embed'],
            'options' => array(
                'targetTable' => $this->mainTable,
                'w' => $this->w,
            )
        );

        return $embedData;
    }

    /**
     * getRelatedEmbed()
     *
     * Dado uma estrutura, verifica quais outras estruturas sao associadas a ele
     * para fazer um embed.
     *
     * Retorna array com objetos dos módulos relacionados
     *
     * @param int $austNode
     * @return array of objects
     */
    public function getRelatedEmbed($austNode){

        $result = array();
        $sql = "SELECT
                    c.tipo
                FROM
                    modulos_conf AS m
                INNER JOIN
                    categorias AS c
                ON
                    m.categoria_id=c.id
                WHERE
                    m.tipo='relacionamentos' AND
                    c.id='".$austNode."'
                ";

        $query = Connection::getInstance()->query($sql);
        if( empty($query) )
            return array();

        foreach( $query as $valor ){

            if( !file_exists( MODULES_DIR.$valor["tipo"].'/'.MOD_CONFIG ) )
                continue;

            include(MODULES_DIR.$valor["tipo"].'/'.MOD_CONFIG);

            if( !file_exists( MODULES_DIR.$valor["tipo"].'/'.$modInfo['className'].'.php' ) )
                continue;
            
            include_once(MODULES_DIR.$valor["tipo"].'/'.$modInfo['className'].'.php');

            $result[] = new $modInfo['className'];
        }

        return $result;
    } // fim getRelatedEmbed()

    /**
     * getRelatedEmbedAsArray()
     *
     * Dado uma estrutura, verifica quais outras estruturas sao associadas a ele
     * para fazer um embed.
     *
     * Se a estrutura é Notícias, verifica quais outras podem fazer embed nos
     * seus formulários.
     *
     * Retorna array com ids das estruturas relacionadas
     *
     * @param int $austNode
     * @return array
     */
    public function getRelatedEmbedAsArray($austNode){
        $sql = "SELECT
                    categoria_id
                FROM
                    modulos_conf as m
                WHERE
                    m.tipo='relacionamentos' AND
                    m.valor='".$austNode."'
                ";
        $query = Connection::getInstance()->query($sql);
        $tmp = array();
        foreach( $query as $valor ){
            $tmp[] = $valor["categoria_id"];
        }
        return $tmp;
    } // fim getRelatedEmbedAsArray()

    /**
     * saveEmbeddedModules()
     *
     * Salva todos os dados de um formulário de embed's.
     *
     * @param <array> $data
     * @return <bool>
     */
    public function saveEmbeddedModules($data){

        //pr( $data );
        if( empty($data) )
            return false;

        if( empty($data['embedModules']) )
            return false;

        foreach($data['embedModules'] AS $embedModule) {

            $modDir = $embedModule['dir'];

            if( is_file($modDir.'/'.MOD_CONFIG) ){
                include($modDir."/".MOD_CONFIG);

                $className = $modInfo['className'];
                include_once($modDir."/".$className.'.php');

                $param = $this->params;

                $this->{$className} = new $className($this->params);
                $dataToSave = array_merge($embedModule, $data['options']);

                $this->{$className}->saveEmbed($dataToSave);
            }

        } // fim do foreach por cada estrutura com embed

        return true;
    } // fim saveEmbeddedModules()

/*
 *
 * MÈTODOS DE SUPORTE
 *
 */

    public function getFieldsFromPost(){
        
    }
    public function getValuesFromPost(){

    }

    /**
     * loadConfig()
     *
     * Carrega a configuração do Módulo.
     *
     * @return <array>
     */
    public function loadConfig(){

        if( !empty($this->config) )
            return $this->config;

        $modDir = $this->getIncludeFolder().'/';

        include $modDir.MOD_CONFIG;

        if( empty($modInfo) )
            return false;

        $this->config = $modInfo;
        return $this->config;
    }

    /**
     * getIncludeFolder()
     *
     * Retorna o endereço até a pasta do módulo.
     *
     * @return <string>
     */
    public function getIncludeFolder(){

		$str = get_class($this);
		
		preg_match_all('/[A-Z][^A-Z]*/', $str, $results);
		$tmpStr = implode('_', $results[0]);
		$tmpStr = strtolower($tmpStr);
		
		if( is_dir(MODULES_DIR.$tmpStr) )
       		return MODULES_DIR.$tmpStr;
		else
       		return MODULES_DIR.strtolower( $str );
    }

	/**
	 * setViewMode()
	 *
	 * Alguns módulos tem viewmodes diferentes, ou seja, listagem de formato
	 * thumbs ou lista.
	 */
	public function setViewMode($viewMode = ''){
		if( empty($this->viewModes) ) return false;
		if( empty($viewMode) AND empty($_POST['viewMode']) ) return false;
		else if( !empty($_POST['viewMode']) )
			$viewmode = $_POST['viewMode'];
			
		if( !in_array($viewmode, $this->viewModes) ) return false;
		$user = User::getInstance();
		$params = array(
	        "conf_type" => "mod_conf",
	        "aust_node" => $this->austNode,
			'author' => $user->getId(),
			'data' => array(
				'viewmode' => $viewmode
			)
		);
		
		$result = $this->saveModConf($params);
		return $result;
	}
	
	/**
	 * viewmode()
	 * 
	 * Retorno o viewmode atual, considerando o usuário atual. 
	 * 
	 */
	public function viewmode(){
		if( count($this->viewModes) == 1 )
			return $this->viewModes[0];
		
		$user = User::getInstance();
		$result = $this->loadModConf('viewmode', null, $user->getId());
		if( empty($result) )
			return $this->viewModes[0];
		else
			return $result;
	}

/**
 *
 * VERIFICAÇÕES
 *
 */

    /**
     * isCreate()
     *
     * Verifica se é formulário de criação.
     *
     * @return <bool>
     */
    public function isCreate(){
        if( $_GET['action'] == CREATE_ACTION )
            return true;

        return false;
    }
    /**
     * isEdit()
     *
     * Verifica se é formulário de edição.
     *
     * @return <bool>
     */
    public function isEdit(){
        if( $_GET['action'] == EDIT_ACTION )
            return true;

        return false;
    }

    /**
     * hasSchema()
     *
     * Ao contrário de Schema, o módulo pode ter Migration (preferido).
     *
     * @return <bool>
     */
    public function hasSchema(){
        if( empty($this->modDbSchema) )
            return false;

        return true;
    }

    public function hasMigration(){
        
    }

    /**
     * getModuleInformation()
     *
     * Retorna informações gerais sobre um módulo.
     *
     * @param <array> $params
     * @return <array>
     */
    public function getModuleInformation($params){

        /*
         * Load Migrations
         */
        $migrationsMods = new MigrationsMods( $this->conexao );

        if( is_array($params) ){
            
            foreach( $params as $modName ){
                $pastas = MODULES_DIR.$modName;

                /**
                 * Carrega arquivos do módulo atual
                 */
                if( !is_file($pastas.'/'.MOD_CONFIG) )
                    continue; // cai fora se não tem config
                
                include($pastas.'/'.MOD_CONFIG);

                $result[$modName]['version'] = MigrationsMods::getInstance()->isActualVersion($pastas);
                $result[$modName]['path'] = $pastas;//.'/'.MOD_CONFIG;
                $result[$modName]['config'] = $modInfo;

            }
        }

        return $result;
    }

    /**
     * verificaInstalacaoRegistro()
     *
     * @return <bool>
     */
    public function verificaInstalacaoRegistro($options = array()) {

        if( !empty($options["pasta"]) ){
            $where = "pasta='".$options["pasta"]."'";
        }

        $sql = "SELECT id FROM modulos WHERE ".$where;
        $query = Connection::getInstance()->query($sql);
        if( !$query ){
            return false;
        } else {
            return true;
        }
    }

    /**
     * saveModConf()
     *
     * Salva configurações de um módulo no banco de dados automaticamente.
     *
     * Para exemplo de como usar, veja o código de configuração do módulo textos
     *
     * @param array $params
	 *
	 * O formato de $params deve ser o seguinte:
	 *
	 * 		array(
	 *			'aust_node' => int,
	 *			'conf_type' => 'mod_conf',
	 *			'data' => array(
	 *				'propriedade_1' => 'valor_1',
	 *				'propriedade_2' => 'valor_2'
	 *			)
	 *		)
	 *
     * @return bool
     */
    public function saveModConf($params) {
        $user = User::getInstance();

        /*
         * Se for para configurar e tiver dados enviados
         */
        if( !empty($params['conf_type'])
            AND $params['conf_type'] == "mod_conf"
            AND !empty($params['data'])
            AND !empty($params['aust_node']) ) {

            $data = $params["data"];

			if( empty($params['conf_class']) OR is_null($params['conf_class']) )
				$confClass = 'module';
			else
				$confClass = $params['conf_class'];

			/*
			 * ajusta o parâmetro da busca SQL
			 */
			if( $confClass == 'module' )
				$classSearchStatement = '( class IS NULL OR class=\'module\')';
			else
				$classSearchStatement = 'class=\''.$confClass.'\'';

			/*
			 * Se foi definido um usuário específico
			 */
			$whereAuthor = '';
			if( !empty($params['author']) AND is_numeric($params['author']) )
				$whereAuthor = "AND autor='".$params['author']."'";

			/*
			 * Algumas configurações são especiais, como é o caso de estruturas
			 * relacionadas.
			 */
			$moduleConfig = $this->loadConfig();
			$todayDateTime = date('Y-m-d H:i:s');
			
			$sql = "DELETE FROM aust_relations WHERE slave_id='".$params["aust_node"]."'";
			Connection::getInstance()->exec($sql);

			$relationalName = $moduleConfig['nome'];
			if( !empty($moduleConfig['relationalName']) )
				$relationalName = $moduleConfig['relationalName'];

            foreach( $data as $propriedade=>$valor ) {
	
				/*
				 * Quando o tipo de configuração é 'field', os dados vem
				 * em um formato diferente, em array.
				 */
				if( $confClass == 'field' ){
					$refField = $propriedade;
					foreach( $valor as $propriedade=>$valor ){
						
						$deleteSQL = "DELETE FROM config WHERE tipo='mod_conf'  AND $classSearchStatement AND local='".$params["aust_node"]."' AND propriedade='$propriedade' AND ref_field='$refField' $whereAuthor";
			            Connection::getInstance()->exec($deleteSQL);

		                $paramsToSave = array(
		                    "table" => "config",
		                    "data" => array(
			                    "tipo" => "mod_conf",
								'class' => $confClass,
			                    "local" => $params["aust_node"],
			                    "autor" => $user->LeRegistro("id"),
			                    "propriedade" => $propriedade,
								'ref_field' => $refField,
			                    "valor" => $valor
		                    )
		                );
		                Connection::getInstance()->exec(Connection::getInstance()->saveSql($paramsToSave));
					}
				}
				/*
				 * Configurações de módulo acontecem a seguir.
				 */
				else {
				
					$deleteSQL = "DELETE FROM config WHERE tipo='mod_conf' AND $classSearchStatement AND local='".$params["aust_node"]."' AND propriedade='$propriedade' $whereAuthor";
		            Connection::getInstance()->exec($deleteSQL);

	                $paramsToSave = array(
	                    "table" => "config",
	                    "data" => array(
		                    "tipo" => "mod_conf",
							'class' => $confClass,
		                    "local" => $params["aust_node"],
		                    "autor" => $user->LeRegistro("id"),
		                    "propriedade" => $propriedade,
		                    "valor" => $valor
	                    )
	                );
	                Connection::getInstance()->exec(Connection::getInstance()->saveSql($paramsToSave));
	
					/*
					 * No caso de relações entre estruturas, salva na devida tabela os dados
					 * deste relacionamento.
					 */
					if( $moduleConfig['configurations'][$propriedade]['inputType'] == 'aust_selection' ){
						
						if( !empty($valor) ){
							$sql = "INSERT INTO
							 			aust_relations
							 		(slave_id, slave_name, master_id, created_on, updated_on)
									VALUES
									('".$params["aust_node"]."', 
									(SELECT nome FROM categorias WHERE id='".$params["aust_node"]."'), 
									'".$valor."', '".$todayDateTime."', '".$todayDateTime."')";
							Connection::getInstance()->exec($sql);
						}
					}
	
				}
            }
	        return true;
        } else {
			return false;
		}
    }

    /**
     * loadModConf()
     *
     * Carrega configurações dinâmicas do módulo atual.
     *
     * Exemplo de configuração: mostrar resumo, sim ou não?
     * Estas opções não são estáticas, mas dinâmicas, de acordo
     * os administradores.
     *
     * No caso de ter $author, diz respeito àquelas configurações
     * específicas de um usuário.
     *
     * @param <mixed> $params
     * @param <string> $confType
     * @param <string> $author 
     * @return <array>
     */
    function loadModConf($params = "", $confClass = '', $author = "") {

		if( is_null($confClass) OR empty($confClass) )
			$confClass = 'module';
		
		/*
		 * ajusta o parâmetro da busca SQL
		 */
		if( $confClass == 'module' )
			$classSearchStatement = '( class IS NULL OR class=\'module\')';
		else
			$classSearchStatement = 'class=\''.$confClass.'\'';
			
        /*
         * Array: Várias opções podem ser passadas
         */
        if( is_array($params) ){

            if( empty($params["austNode"]) AND
                empty($params["aust_node"]) )
                return NULL;

            if( !empty($params["aust_node"]) )
                return $this->loadModConf($params["aust_node"], $confClass, $author);

            if( !empty($params["austNode"]) )
                return $this->loadModConf($params["austNode"], $confClass, $author);

            return NULL;

        }
        /*
         * numeric: Um austNode foi especificado
         */
        else if( is_numeric($params) OR empty($params) ){
			/*
			 * Carrega as configurações estáticas
			 */
            $staticConfig = $this->loadConfig();

			if( $confClass == 'module' )
            	$staticConfig = $staticConfig['configurations'];
			else
        		$staticConfig = $staticConfig['field_configurations'];

            if( empty($params) )
                $params = $this->austNode;
			
			$whereAuthor = '';
			if( !empty($author) ){
				$whereAuthor = "AND autor='".$author."'";
			}
			/*
			 * Carrega as configurações já salvas no DB. Pode haver
			 * menos itens que as definidas estaticamente.
			 */
            $sql = "SELECT * FROM config WHERE tipo='mod_conf' AND $classSearchStatement AND local='".$params."' $whereAuthor LIMIT 300";
			$queryTmp = Connection::getInstance()->query($sql, "ASSOC");
			
            $query = array();
			/*
			 * Configurações de campos individuais têm um formato completamente
			 * diferente de configurações de módulos.
			 */
			
			if( $confClass == 'field' ){

				$fields = $this->getFields();
				if( empty($fields) )
					return array();
					
	            foreach($queryTmp as $valor) {
		
					// $prop: toma o nome da propriedade
	                $prop = $valor["ref_field"]; // suas_fotos

					/*
					 * Se não houver dados salvos no db, retorna o que está no
					 * arquivo de configuração. Se houver, já mescla ambos os dados.
					 */
	                if( !empty($staticConfig) ){
						foreach( $staticConfig as $configName=>$configValue ){

							/*
							 * Tipo do campo bate com o field_type da configuração?
							 */
							if( !empty($configValue['field_type'])
								AND $configValue['field_type'] == $fields[$prop]['especie'] )
							{
								if( empty($query[$prop][$configName]) ){
		                    		$query[$prop][$configName] = $configValue;
								}
							}
						}
	                }
	
	
					if( !empty( $query[$prop][$valor['propriedade']] ) )
	                	$query[$prop][$valor["propriedade"]] = array_merge( $query[$prop][$valor["propriedade"]] , $valor );
					else
                		$query[$prop][$valor["propriedade"]] = $valor;
	                /**
	                 * @todo - array $query tem 'value' e 'valor'. Deve-se
	                 * tirar uma e ficar somente uma.
	                 */
	                $query[$prop][$valor["propriedade"]]['value'] = $valor["valor"];
	            }
			} else {
				/*
				 * Loop pela configurações salvas para preparar a Array para mesclar
				 * com as configurações estaticas.
				 */
	            foreach($queryTmp as $valor) {
				
					// $prop: toma o nome da propriedade
	                $prop = $valor["propriedade"];
	                $query[$prop] = array();

	                if( !empty($staticConfig[$prop]) ){
	                    $query[$prop] = $staticConfig[$prop];
	                }

	                $query[$prop] = array_merge( $query[$prop], $valor );
	                /**
	                 * @todo - array $query tem 'value' e 'valor'. Deve-se
	                 * tirar uma e ficar somente uma.
	                 */
	                $query[$valor["propriedade"]]['value'] = $valor['valor'];
	            }
			}
			/*
			 * Loop pela configurações estáticas para se certificar que todas as
			 * configurações serão retornadas, mesmo as que não possuem nenhum
			 * configuração definida.
			 */
			$result = array();

			if( $confClass == 'module' ){
				if( is_array($staticConfig) ){
					foreach( $staticConfig as $key=>$value ){
						if( !empty($query[$key]) )
							$result[$key] = $query[$key];
						else
							$result[$key] = $value;
					}
				}
			} else {
				if( is_array($staticConfig) ){
					foreach( $fields as $fieldName=>$fieldInfo ){
						if( empty($query[$fieldName]) )
							$query[$fieldName] = $staticConfig;
					}
				}
				$result = $query;
			}
			
			// se é para retorna configurações de um único autor, não
			// salva configurações em cache
			if( empty($author) ){
				if( $confClass == 'field')
            		$this->structureFieldsConfig = $result;
				elseif( $confClass == 'module')
            		$this->structureConfig = $result;
			}
			
            return $result;
        }
        /*
         * string: quando se deseja uma opção em especial. Leva-se em
         * consideração $this->austNode
         */
        else if( is_string($params) ) {
            /*
             * As configurações encontradas são salvas em $this->structureConfig
             * para que não seja necessário buscá-las novamente no DB.
             *
             * Verifica-se abaixo se não existe ainda, e busca-as.
             */
			if( !empty($author) ){
	            $sql = "SELECT * FROM config WHERE tipo='mod_conf' AND local='".$this->austNode."' AND autor='$author' AND propriedade='$params' LIMIT 1";
	            $queryTmp = Connection::getInstance()->query($sql, "ASSOC");

				if( !empty($queryTmp) )
					return $queryTmp[0]['valor'];
				else
					return array();
					
			} else if( empty($this->structureConfig) ){
                $result = $this->loadModConf($this->austNode, $confClass, $author);
                return $result[$params];
            } else {
                if( empty($this->structureConfig[$params]) )
                    $this->loadModConf($this->austNode, $confClass, $author);
                
                return $this->structureConfig[$params];
            }
        }

        return array();
    }

    /**
     * getStructureConfig()
     *
     * Há configurações específicas de uma estrutura, como:
     *
     *      Mostrar categoria?
     *      Tem resumo?
     *      Mostrar URL Gerada?
     *
     * Este método retorna o valor de uma configuração requisitada em $key.
     *
     * NOTA: Subtitui $this->loadModConfig() para pegar valores de configuração
     * da estrutura.
     *
     * O método getFieldConfig() é semelhante, exceto que busca informações
     * sobre um determinado campo.
     *
     * @param <string> $key
     * @param <bool> $valueOnly
     * @return <mixed> Se $valueOnly, retorna somente string com valor, senão
     * array com todo o valor.
     */
    public function getStructureConfig($key, $valueOnly = true) {

        if( is_string($key) AND empty($this->structureConfig) ) {
            $this->loadModConf($this->austNode);

            if( empty($this->structureConfig[$key]) )
                return array();

            if( $valueOnly )
                return $this->structureConfig[$key]['value'];
            
            return $this->structureConfig[$key];

        } else if( is_string($key) AND !empty($this->structureConfig) ) {
            if( $valueOnly ){
				if( !empty($this->structureConfig[$key]['value']) )
                	return $this->structureConfig[$key]['value'];
				else if( !empty($this->structureConfig[$key]['valor']) )
                	return $this->structureConfig[$key]['valor'];
				else
					return NULL;

			}
            
            return $this->structureConfig[$key];
        }

        return NULL;
    } // end getStructureConfig()

    /**
     * getFieldConfig()
     *
     * Há configurações específicas de um campo de uma estrutura,
	 * geralmente do Módulo Cadastro:
     *
     *      Campo X tem imagem secundária?
     *      Campo Y tem descrição?
     *      Campo Z tem múltiplas imagens?
     *
     * Este método retorna o valor de uma configuração requisitada em $key.
     *
     * NOTA: Subtitui $this->loadModConfig() para pegar valores de configuração
     * de um campo de estrutura.
     *
     * O método getStructureConfig() é semelhante, exceto que busca informações
     * sobre uma determinada estrutura.
     *
     * @param <string> $field
     * @param <string> $key
     * @param <bool> $valueOnly
     * @return <mixed> Se $valueOnly, retorna somente string com valor, senão
     * array com todo o valor.
     */
    public function getFieldConfig($field, $key, $valueOnly = true) {

        if( is_string($key) 
			AND is_string($field) 
			AND empty($this->structureFieldsConfig) ) {
            $result = $this->loadModConf($this->austNode, 'field');
			
            if( empty($this->structureFieldsConfig[$field][$key]) )
                return array();

            if( $valueOnly )
                return $this->structureFieldsConfig[$field][$key]['value'];
            
            return $this->structureFieldsConfig[$field][$key];

        } else if( is_string($key) AND !empty($this->structureFieldsConfig) ) {

            if( $valueOnly ){
				if( !empty($this->structureFieldsConfig[$field][$key]['value']) )
                	return $this->structureFieldsConfig[$field][$key]['value'];
				else if( !empty($this->structureFieldsConfig[$field][$key]['valor']) )
                	return $this->structureFieldsConfig[$field][$key]['valor'];
				else
					return false;
			}
            return $this->structureFieldsConfig[$field][$key];
        }

        return false;
    } // end getFieldConfig()

    /**
     * replaceFieldsValueIfEmpty()
     *
     * Alguns campos em um resultado de conteúdo do DB não podem estar vazios.
     * Isto é configurado em config.php de cada módulo.
     *
     * Este método substitui automaticamente resultados vazios por um padrão.
     *
     * @param <array> $query
     * @return <array> O mesmo $query de entrada, mas tratado
     */
    public function replaceFieldsValueIfEmpty($query){

        $tmp = $query;

        $config = $this->loadConfig();

        if( empty($config['replaceFieldsValueIfEmpty']) OR
            !is_array($config['replaceFieldsValueIfEmpty']) )
            return $query;

        /*
         * Loop por cada query
         */
        foreach( $tmp as $key=>$value ){
            /*
             * Loop por cada campo
             */
            foreach( $config['replaceFieldsValueIfEmpty'] as $requiredField=>$standardValue ){

                /*
                 * Substitui campo vazio por valor padrão
                 */
                if( empty($value[$requiredField]) ){
                    $query[$key][$requiredField] = $standardValue;
				}

            }
        }

        return $query;
    }

    /*
     *
     * MÉTODOS PRIVADOS
     *
     */

    /**
     *
     * RESPONSER
     *
     */
    /**
     * Carrega conteúdo para leitura externa. Retorna, geralmente, em array.
     *
     * @global Aust $aust
     * @return array
     */
    public function retornaResumo() {
        global $aust;

        /**
         * Configurações específicas deste módulo
         */
        $moduloConf = $this->config['arquitetura'];

        /**
         * Pega as estruturas deste módulo através do método a seguir. Em
         * $param['where'] tem-se uma parte do código SQL necessário para tal.
         */
        $param = array(
            "where" => "tipo='textos' and classe='estrutura'"
        );

        $estruturas = Aust::getInstance()->LeEstruturasParaArray($param);
        /**
         * Se há estruturas instaladas, rodará uma por uma tomando os conteúdos
         */
        if(!empty($estruturas)) {

        /**
         * Se o retorno estiver configurado para array ou vazio, retorna array.
         * Alguns módulos retornam textos diretamente
         */
            if( empty($moduloConf['returnTipo'])
                OR $moduloConf['returnTipo'] == 'array' ) {

            /**
             * Cada estrutura possui várias categorias.
             *
             * Vamos:
             *      - um loop por cada estrutura
             *      - um loop por cada categoria de cada estrutura
             *
             * O resultado será todo guardado em $conteudo
             */
                foreach($estruturas as $chave=>$valor) {

                    $response['intro'] = 'A seguir, os últimos conteúdos.';
                    $categorias = Aust::getInstance()->categoriasFilhas( array( 'pai' => $valor['id'] ) );

                    if(!empty($categorias)) {
                    /**
                     * Pega cada índice contendo id das categorias da respectiva estrutura
                     */
                        foreach($categorias as $cChave=>$cValor) {
                            $tempCategorias[] = $cChave;
                        }

                        /**
                         * Monta SQL
                         *
                         * Monta cláusula WHERE com as categorias selecionadas e
                         * desmancha $tempCategorias
                         */
                        $sql = "SELECT
                                    id, titulo
                                FROM
                                    ".$moduloConf['table']."
                                WHERE ".$moduloConf['foreignKey'] . " IN ('" . implode("','", $tempCategorias) ."')
                                ORDER BY id DESC
                                LIMIT 0,4
                                ";

                        $result = Connection::getInstance()->query($sql);

                        foreach($result as $dados) {
                        /**
                         * Toma os dados do DB e os guarda
                         */
                            $tempResponse[] = $dados;
                        }

                        /**
                         * Organiza array que vai ser retornada nesta função
                         */
                        $response[$valor['id']]['titulo'] = $valor['nome'];
                        $response[$valor['id']]['conteudo'] = (empty($tempResponse)) ? array() : $tempResponse;

                        unset($tempCategorias);
                        unset($tempResponse);
                    }
                }
            }
        }
        return $response = (empty($response)) ? array() : $response;
    }

    /*
     *
     * INTERFACE
     *
     */
    public function loadHtmlEditor($plugins = ""){
        return loadHtmlEditor($plugins);
    }

    /**
     * @todo - trataImagem() não deveria estar nesta função.
     */
    /**
     * trataImagem()
     *
     * Trata uma imagem
     *
     * @param array $files O mesmo $_FILE vindo de um formulário
     * @param string $width Valor padrão de largura
     * @param string $height Valor padrão de altura
     * @return array
     */
    function trataImagem($files, $width = "1024", $height = "768") {

        /*
         * Toma dados de $files
         */
        $frmarquivo = $files['tmp_name'];
        $frmarquivo_name = $files['name'];
        $frmarquivo_type = $files['type'];

        /*
         * Abre o arquivo e tomas as informações
         */
        $fppeq = fopen($frmarquivo,"rb");
        $arquivo = fread($fppeq, filesize($frmarquivo));
        fclose($fppeq);

        /*
         * Cria a imagem e toma suas proporções
         */
        $im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
        $largurao = imagesx($im);// pegar a largura da amostra
        $alturao = imagesy($im);// pegar a altura da amostra

        /*
         * Configura o tamanho da nova imagem
         */
        if($largurao > $width)
            $largurad = $width;
        else
            $largurad = $largurao; // definir a altura da miniatura em px

        $alturad = ($alturao*$largurad)/$largurao; // calcula a largura da imagem a partir da altura da miniatura
        $nova = imagecreatetruecolor($largurad,$alturad); // criar uma imagem em branco
        //imagecopyresized($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);
        imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

        ob_start();
        imagejpeg($nova, '', 100);
        $mynewimage = ob_get_contents();
        ob_end_clean();

        /*
         * Prepara dados resultados para retornar
         */
        imagedestroy($nova);

        $result["filesize"] = strlen($mynewimage);
        //$result["filedata"] = addslashes($mynewimage);
        $result["filedata"] = $mynewimage;
        $result["filename"] = $frmarquivo_name;
        $result["filetype"] = $frmarquivo_type;

        return $result;

    }

    /*
     *
     *	funções de verificação ou leitura
     *
     */
    function leModulos() {

        $modulos = Connection::getInstance()->query("SELECT * FROM modulos");
        return $modulos;

        $diretorio = MODULES_DIR; // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if (is_dir ($pastas)) {
                if( is_file($pastas.'/'.MOD_CONFIG )) {
                    if( include($pastas.'/'.MOD_CONFIG )) {
                        if(!empty($modInfo['name'])) {
                            $str = $result_format;
                            $str = str_replace("&%nome", $modInfo['name'] , $str);
                            $str = str_replace("&%descricao", $modInfo['description'], $str);
                            $str = str_replace("&%pasta", str_replace($diretorio,"",$pastas), $str);
                            $str = str_replace("&%diretorio", str_replace($diretorio,"",$pastas), $str);
                            echo $str;
                            if($c < $t-1) {
                                echo $chardivisor;
                            } else {
                                echo $charend;
                            }
                            $c++;
                        }
                        unset($modulo);
                    }

                }
            }
        }
    } // fim leModulos()

    /**
     * @todo - deprecated
     */
    // retorna o nome da tabela da estrutura
    function LeTabelaDaEstrutura($param='') {
        return $this->useThisTable();
    }

    /*
     * Retorna os módulos que tem a propriedade Embed como TRUE
     */
    function LeModulosEmbed() {
        $sql = "SELECT
                    DISTINCT m.pasta, m.nome, m.chave, m.valor, c.id, c.nome
                FROM
                    modulos as m
                INNER JOIN
                    categorias as c
                ON
                    m.valor=c.tipo
                WHERE
                    m.embed='1'
                ";
        $query = Connection::getInstance()->query($sql);
        $i = 0;
        $return = '';

        foreach($query as $dados) {
            $return[$i] = $dados;
            $i++;
        }
        return $return;
    }

    /**
     * Retorna os módulos que tem a propriedade EmbedOwnForm = TRUE
     *
     * EmbedOwnForm significa módulos que vão dentro de formulário de
     * inclusão/edição de conteúdo, exceto aqueles que tem seu próprio formulário
     *
     * @return array Todos os módulos com habilidade Embed
     */

    function LeModulosEmbedOwnForm() {
        $sql = "SELECT
                    DISTINCT pasta, nome, chave, valor
                FROM
                    modulos
                WHERE
                    embedownform='1'
                ";
        $query = Connection::getInstance()->query($sql);

        $return = '';
        $i = 0;
        foreach($query as $dados) {
            $return[$i]['pasta'] = $dados['pasta'];
            $return[$i]['nome'] = $dados['nome'];
            $return[$i]['chave'] = $dados['chave'];
            $return[$i]['valor'] = $dados['valor'];
            $i++;
        }
        return $return;
    }

    /**
     * Retorna somente EmbedOwnForms liberados para serem mostrados
     * na $estrutura indicada.
     *
     * @return array
     */
    function leModulosEmbedOwnFormLiberados($estrutura) {
        $sql = "SELECT
                    id, nome
                FROM
                    modulos_conf
                WHERE
                    valor='".$estrutura."'
                ";
        $query = Connection::getInstance()->query($sql);

        $return = '';
        foreach($query as $dados) {
            $return[] = $dados['nome'];
        }
        return $return;
    }

    /*
     * retorna o nome de cada módulo e suas informações em formato array
     */
    function LeModulosParaArray() {
        $sql = "SELECT
                    DISTINCT pasta, nome, chave, valor
                FROM
                    modulos
                ";
        $query = Connection::getInstance()->query($sql);
        $i = 0;
        foreach($query as $dados) {
            $return[$i]['pasta'] = $dados['pasta'];
            $return[$i]['nome'] = $dados['nome'];
            $return[$i]['chave'] = $dados['chave'];
            $return[$i]['valor'] = $dados['valor'];
            $i++;
        }
        return $return;
    }




    /*
     *
     * TRATAMENTO DE IMAGENS
     *
     */

    function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality) {
        if (empty($src_image) || empty($dst_image)) { return false; }
        if ($quality <= 1) {
            $temp = imagecreatetruecolor ($dst_w + 1, $dst_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
            imagecopyresized ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
            imagedestroy ($temp);
        } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $tmp_w = $dst_w * $quality;
            $tmp_h = $dst_h * $quality;
            $temp = imagecreatetruecolor ($tmp_w + 1, $tmp_h + 1);
            imagecopyresized ($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
            imagecopyresampled ($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
            imagedestroy ($temp);
        } else {
            imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }
        return true;
    }

	
	/*
	 * EXPORT
	 */
	function export($params = ''){
		return false;
	}


}


?>