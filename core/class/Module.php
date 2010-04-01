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
class Module
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


    /*
     *
     * VARIÁVEIS DE AMBIENTE
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
         * @var class Classe responsável pela conexão com o banco de dados
         */
        public $aust;
        /**
         *
         * @var class Configurações estáticas do módulo
         */
        public $config;

    /**
     *
     * @var <bool> Indica se este é um teste. O sendo, não realiza
     * alguns procedimentos impossíveis de serem realizados via
     * testes unitários, como envio HTTP de dados.
     */
    public $testMode = false;

    /**
     *
     * @var string Diretório onde estão os módulos
     */
    const MOD_DIR = 'modulos/';
    /**
     * __CONSTRUCT()
     *
     * @param array $param:
     *      'conexao': Contém a conexão universal
     */
    function __construct() {
        /**
         * Ajusta a conexao para o módulo
         */
            $this->connection = Connection::getInstance();
        /**
         * Usuário atual
         */
            $this->user = User::getInstance();

        $this->config = $this->loadConfig();
    }

    /*
     *
     * CRUD
     *
     */

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
    public function save($post = array()){

        $method = $post['method'];
        //pr($post);
        /*
         * Gera SQL
         */
        $sql = $this->generateSqlFromForm($post, $method);
        /**
         * Salva no DB
         */
        if( $this->connection->exec($sql) !== false ){

            if( !empty($post['w']) OR $post['w'] > 0 ){
                $this->w = $post['w'];
            } else {
                $this->w = $this->connection->conn->lastInsertId();
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
        
        $qry = $this->connection->query($this->loadSql($param));

        if( empty($qry) )
            return array();

        $qry = $this->_organizesLoadedData($qry);

        if( is_array($param['austNode']) ){
            $austNode = reset( array_keys( $param['austNode'] ) );
        } else {
            $austNode = $param['austNode'];
        }

        $embedModules = $this->getRelatedEmbed($austNode);


        if( empty($embedModules)
            OR empty($this->loadedIds) )
        {
            sort($qry);
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
        
        sort($qry);
        return $qry;
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
        if( !empty($options['categorias']) ){
            print("Argumento <strong>categorias</strong> ultrapassada em \$modulo->loadSql. Use \$options['austNode'].");
            exit(0);
        }

        $id = empty($options['id']) ? '' : $options['id'];
        $austNode = empty($options['austNode']) ? array() : $options['austNode'];
        $page = empty($options['page']) ? '1' : $options['page'];
        $limit = empty($options['limit']) ? '25' : $options['limit'];
        $order = empty($options['order']) ? 'id DESC' : $options['order'];

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
        if( !empty($austNode) ) {
            $where = ' ';
            if( !is_array($austNode) )
                $austNode = array($austNode);

            $where = $where . " AND categoria IN ('".implode("','", array_keys($austNode) )."')";
        }

        /*
         * Paginação?
         */
            if( $page == 0 )
                $page = 1;
            
            $pageLimit = (($page-1) * $limit);
            $limit = " LIMIT ".$pageLimit.",".$limit;

        if( empty($this->describedTable[$this->useThisTable()]) ){
            $this->describedTable[$this->useThisTable()] = $this->connection->query('DESCRIBE '.$this->useThisTable());
        }

        /*
         * Sql para listagem
         */
        $sql = "SELECT
                    id, titulo, visitantes,
                    categoria AS cat,
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
                WHERE 1=1 $id".
                $where.
                " ORDER BY ".$order."
                $limit";
        return $sql;
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

            $result = $this->connection->exec($sql);

            if( $result )
                return true;

        }
        return false;
    }

    /*
     * CRUD -> SUPPORT
     */
    public function generateSqlFromForm($post, $method = 'new'){

        if( !empty($post['w']) )
            $method = 'edit';

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

        $query = $this->connection->query($sql);

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

        $query = $this->connection->query($sql);
        if( empty($query) )
            return array();

        foreach( $query as $valor ){

            if( !file_exists( MODULOS_DIR.$valor["tipo"].'/'.MOD_CONFIG ) )
                continue;

            include(MODULOS_DIR.$valor["tipo"].'/'.MOD_CONFIG);

            if( !file_exists( MODULOS_DIR.$valor["tipo"].'/'.$modInfo['className'].'.php' ) )
                continue;
            
            include_once(MODULOS_DIR.$valor["tipo"].'/'.$modInfo['className'].'.php');

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
        $query = $this->connection->query($sql);
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

    public function loadConfig(){
        
        $modDir = strtolower( get_class($this) ).'/';
        include THIS_TO_BASEURL.MODULOS_DIR.$modDir.MOD_CONFIG;

        if( empty($modInfo) )
            return false;
        
        $this->config = $modInfo;
        return $this->config;
    }


/**
 *
 * VERIFICAÇÕES
 *
 */
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
        //$migrationsStatus = $migrationsMods->status();

        if( is_array($params) ){
            
            foreach( $params as $modName ){
                $pastas = MODULOS_DIR.$modName;

                /**
                 * Carrega arquivos do módulo atual
                 */
                //include($pastas.'/index.php');
                if( !is_file($pastas.'/'.MOD_CONFIG) )
                    continue; // cai fora se não tem config
                
                include($pastas.'/'.MOD_CONFIG);

                $result[$modName]['version'] = $migrationsMods->isActualVersion($pastas);
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
        $query = $this->connection->query($sql);
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
     * @return bool
     */
    public function saveModConf($params) {

        global $administrador;

        /*
         * Se for para configurar e tiver dados enviados
         */
        if( !empty($params['conf_type'])
            AND $params['conf_type'] == "mod_conf"
            AND !empty($params['data'])
            AND !empty($params['aust_node']) ) {

            $data = $params["data"];
            $this->connection->exec("DELETE FROM config WHERE tipo='mod_conf' AND local='".$params["aust_node"]."'");
            foreach( $data as $propriedade=>$valor ) {

                $paramsToSave = array(
                    "table" => "config",
                    "data" => array(
                    "tipo" => "mod_conf",
                    "local" => $params["aust_node"],
                    "autor" => $administrador->LeRegistro("id"),
                    "propriedade" => $propriedade,
                    "valor" => $valor
                    )
                );
                $this->connection->exec($this->connection->saveSql($paramsToSave));
            }
        }
        return true;
    }

    function loadModConf($params) {
        $sql = "SELECT * FROM config WHERE tipo='mod_conf' AND local='".$params["aust_node"]."' LIMIT 200";

        $queryTmp = $this->connection->query($sql, "ASSOC");

        foreach($queryTmp as $valor) {
            $query[$valor["propriedade"]] = $valor;
        }
        return $query;
    }

    
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

        $estruturas = $aust->LeEstruturasParaArray($param);
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
                    $categorias = $aust->categoriasFilhas( array( 'pai' => $valor['id'] ) );

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

                        $result = $this->connection->query($sql);

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
    public function loadHtmlEditor(){
        return loadHtmlEditor();
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











    /**
     * @todo - ajustar código para baixo
     */
    /**
     * Salva dados sobre o módulo na base de dados.
     *
     * Usado após a criação das tabelas do módulo.
     *
     * @param array $param
     * @return bool
     */
    function configuraModulo($param) {

    /**
     * Ajusta cada variável enviada como parâmetro
     */
    /**
     * $tipo:
     */
        $tipo = (empty($param['tipo'])) ? '' : $param['tipo'];
        /**
         * $chave:
         */
        $chave = (empty($param['chave'])) ? '' : $param['chave'];
        /**
         * $valor:
         */
        $valor = (empty($param['valor'])) ? '' : $param['valor'];
        /**
         * $pasta:
         */
        $pasta = (empty($param['pasta'])) ? '' : $param['pasta'];
        /**
         * $modInfo:
         */
        $modInfo = (empty($param['modInfo'])) ? '' : $param['modInfo'];
        /**
         * $autor:
         */
        $autor = (empty($param['autor'])) ? '' : $param['autor'];


        $this->connection->exec("DELETE FROM modulos WHERE pasta='".$pasta."'");

        $sql = "INSERT INTO
                    modulos
                        (tipo,chave,valor,pasta,nome,descricao,embed,embedownform,somenteestrutura,autor)
                VALUES
                    ('$tipo','$chave','$valor','$pasta','".$modInfo['nome']."','".$modInfo['descricao']."','".$modInfo['embed']."','".$modInfo['embedownform']."','".$modInfo['somenteestrutura']."','$autor')
            ";
        if($this->connection->exec($sql, 'CREATE_TABLE')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     *
     *	funções de verificação ou leitura
     *
     */
    function leModulos() {

        $modulos = $this->connection->query("SELECT * FROM modulos");
        //pr($modulos);
        return $modulos;

        $diretorio = 'modulos/'; // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if (is_dir ($pastas)) {
                if( is_file($pastas.'/'.MOD_CONFIG )) {
                    if( include($pastas.'/'.MOD_CONFIG )) {
                    //include_once($pastas.'/index.php');
                        if(!empty($modInfo['nome'])) {
                            $str = $result_format;
                            $str = str_replace("&%nome", $modInfo['nome'] , $str);
                            $str = str_replace("&%descricao", $modInfo['descricao'], $str);
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
        $query = $this->connection->query($sql);
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
        $query = $this->connection->query($sql);

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
        $query = $this->connection->query($sql);

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
        $query = $this->connection->query($sql);
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


}


?>