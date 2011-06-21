<?php
class Config {

    private $Opcoes; // variável possuindo as configurações
    public $self = "";

    public $options;
    public $conexao;

	public $table = "config";

    /**
     * Contém configurações de permissões de acesso às configurações do sistema
     *
     * O formato é o seguinte:
     *
     *      array(
     *          tipo_1 => grupo_de_admins_1,
     *          tipo_2 => array(
     *              grupo_de_admins_1,
     *              grupo_de_admins_2,
     *          ),
     *          tipo_3 => '*'
     *      )
     *
     * Interpretação:
     *
     *      - tipo_1 : somente grupo_de_admins_1 pode ver,
     *      - tipo_2 : somente grupo_de_admins 1 e 2 podem ver,
     *      - tipo_3 : todos podem ver
     *      - tipo_4 : não especificado, ninguém pode ver, exceto webmaster.
     *
     * O tipo_4 neste formato, ninguém podendo ver, evita que novas configurações
     * venham à tona e possam ser vistos por qualquer usuário.
     *
     * @var <array>
     */
    public $permissions = array('*'=>'*');

    /**
     * Grupo do usuário
     */
    public $_userType = '';

    /**
     * Grupo root
     */
    public $_rootType = 'root';

    /**
     * Contém os itens de configuração que estão faltando para
     * que o sistema esteja íntegro.
     *
     * @var <array>
     */
    public $_missingConfig = array();

    
    function __construct( $params = "" ) {
        $this->conexao = Connection::getInstance();

		if( !empty($_SERVER['QUERY_STRING']) && !empty($_SERVER['PHP_SELF']) )
        	$this->self = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

        /*
         * Permissões de usuários acessar configurações
         */
		$permissions = StructurePermissions::getInstance();
        $this->permissions = StructurePermissions::getInstance()->read($params);

        /*
         * Grupo do usuário (webmaster, administrador, moderador, etc)
         */
        $this->_userType = User::getInstance()->type();

        /*
         * Grupo root
         */
        $this->_rootType = User::getInstance()->rootType();

        if( !$this->checkIntegrity() )
            $this->_initConfig();
    }

    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new Config;
        }

        return $instance[0];
    }

    public function getConfig($property){
        if( !empty($property) ){

            /*
             * String, ou seja, apenas um valor dado.
             */
            if( is_string($property) ){
                $type = Registry::read('configStandardType');
                $field = 'valor';
                $params = array(
                    'where' => "propriedade='".$property."'",
                    'mode' => 'single',

                );
            } else {
                return false;
            }

			$config = $this->getConfigs($params);
			
            $result = reset( $config );
			if( empty($result) )
				return false;
            $result = reset($result);
            return $result[$field];

        } else {
            return false;
        }
    }

    /**
     * getConfig()
     * 
     * @param <array> $params
     * @return <array>
     */
    public function getConfigs($params = array()){

        /*
         * Modo de retorno
         */
        $mode = (empty($params["mode"])) ? '' : $params["mode"];
        unset($params['mode']);

        $where = (empty($params["where"])) ? '' : 'AND ( '. $params["where"].')';
        unset($params['where']);

        /**
         * Tipo de configuração, geralmente global
         */
        $type = (empty($params["type"])) ? array() : $params["type"];
        if( is_string($type) )
            $type = array($type);
        $type = (empty($type)) ? '' : ' AND tipo IN (\''. implode("','", $type) .'\')';

        $sql = "SELECT * FROM
                    ".$this->table."
                WHERE
                    1=1
                    $where
                    $type
                ORDER BY tipo ASC
                ";
        $query = Connection::getInstance()->query($sql);

        $result = array();
        foreach( $query as $valor ){
            $result[$valor['tipo']][] = $valor;
        }

        if( !empty($result) ){
            return $result;
        } else {
            return array();
        }
    }

    /**
     * hasPermission()
     *
     * Verifica se usuário tem permissão de ver uma determinada configuração
     * seguindo as regras em $this->permission.
     *
     * @param <mixed> $param
     * @return <bool>
     */
    function hasPermission($param){
        /*
         * $ut = $this->_userType
         */
        $uT = $this->_userType;

        /*
         * Usuários Root podem tudo
         */
        if( $uT == $this->_rootType )
            return true;
        
        /*
         * Por padrão, param é = type (global, etc)
         */
        if( is_string($param) ){
            $givenType = $param;
            /*
             * Se existe alguma permissão definida
             */
            if( array_key_exists($givenType, $this->permissions) ){
                /*
                 * String
                 */
                if( is_string( $this->permissions[$givenType]) ){
                    if( $uT == strtolower($this->permissions[$givenType]) )
                        return true;
                    if( $this->permissions[$givenType] == '*' )
                        return true;
                }
                /*
                 * Array
                 */
                elseif( is_array( $this->permissions[$givenType]) ){
                    foreach( $this->permissions[$givenType] as $typePermitted ){
                        if( $uT == strtolower($typePermitted) )
                            return true;
                    }
                }
            }
        }
        return false;
    } // end hasPermission()

    /*
     *
     * INTEGRIDADE DAS CONFIGURAÇÕES
     *
     */
    /**
     * checkIntegrity()
     *
     * Verifica a integridade das configurações, se todas
     * necessárias estão presentes.
     *
     * @return <bool>
     */
    public function checkIntegrity(){

        $neededConfig = Registry::read('neededConfig');

        if( !empty($neededConfig) AND is_array($neededConfig) ){
            foreach( $neededConfig as $valor ){
                $whereConfig[] = "(tipo='".$valor['tipo']."' AND propriedade='".$valor['propriedade']."')";
            }
        } else {
            return true;
        }

        $qtdNeeded = count($neededConfig);

        $sql = "SELECT tipo, propriedade FROM
                    ".$this->table."
                WHERE
                    ".implode(" OR ", $whereConfig)."
                ";
        $query = Connection::getInstance()->query($sql);

        /*
         * Não é igual o número de valores encontrados e o de necessários
         */
        if( $qtdNeeded != count($query) ){

            $actualConfig = array();
            foreach( $query as $valor ){
                $actualConfig[$valor['tipo']][] = $valor['propriedade'];
            }
            
            foreach( $neededConfig as $valor ){
                if( empty($actualConfig[$valor['tipo']]) OR
                    !in_array($valor['propriedade'], $actualConfig[$valor['tipo']]) )
                {
                    $this->_missingConfig[] = $valor;
                }
            }
            
            return false;
        } else
            return true;

    } // end checkIntegrity()

    public function _initConfig(){

        $i = 0;
        foreach( $this->_missingConfig as $neededConfig ){
            foreach( $neededConfig as $key=>$value ){
                $fields[$i][] = $key;
                $values[$i][] = $value;
            }
            $i++;
        }

        //pr($fields);
        //pr($values);
        foreach( $fields as $i=>$valor ){
            
            $sql =
                "INSERT INTO
                    config
                    (".implode(', ', $valor).")
                 VALUES
                    ('".implode("', '", $values[$i])."')";
            Connection::getInstance()->query($sql);
        }
        
        return true;
    }


    // Atualiza Configurações
    function AtualizaConfig(){
        $this->__construct();
    }

    // Acessa a variável que guarda as configurações e retorna seu valor
    public function LeOpcao($propriedade, $metodo = ''){
            if(empty($this->Opcoes[$propriedade])){
                    if($metodo == 'form'){
                            return '';
                    } else if(empty($metodo)) {
                            return ''; //return 'con'.$propriedade.'fig01';
                    }
            } else {
                    return $this->Opcoes[$propriedade];
            }
    }

    function updateOptions($params){
		
		$params = sanitizeString($params);
		$sql = "UPDATE ".$this->table." SET valor='".$params["valor"]."' WHERE id='".$params["id"]."'";
        Connection::getInstance()->exec($sql);

        return '<span style="color: green;">Configuração salva com sucesso!</span>';
    }
    
    // Ajusta a configuração
    function ajustaOpcoes($params){ //$propriedade, $valor, $nome = '', $tipo = '', $local = ''){

        $this->options[$params["tipo"]][$params["propriedade"]] = $params;
        return true;
        /*
        $this->Opcoes[$propriedade] = $valor;
        $this->OpcoesNomes[$propriedade] = $nome;
        $this->OpcoesTipo[$propriedade] = $tipo;
        $this->OpcoesLocal[$propriedade] = $local;
         * 
         */
    }

    // Grava as configurações definitivamente no banco de dados
    public function gravaConfig() {

        foreach($this->options as $tipo=>$valor) {

            $sql = "SELECT id FROM ".$this->table." WHERE tipo='".$tipo."' AND propriedade='".key($valor)."'";
            $query = Connection::getInstance()->count($sql);

            if( $query ) {
                $valores = reset($valor);
                $sql = "UPDATE
                            ".$this->table."
                        SET
                            valor='".$valores["valor"]."'
                        WHERE
                            propriedade='".key($valor)."'
                            AND tipo='".$tipo."'
                            ";
            } else {

                $valores = reset($valor);

                foreach( $valores as $coluna=>$info ){
                    $colunas[] = $coluna;
                    $infos[] = $info;
                }

                $sql = "INSERT INTO
                            ".$this->table."
                                (".implode(",", $colunas).")
                        VALUES
                            ('".implode("','", $infos)."')";
            }

            if( !Connection::getInstance()->exec($sql) ) {
                $erro[] = key($valor);
            }
        }

        if(count($erro) == 0) {
            return array(
				'classe' => 'sucesso',
				'mensagem' => 'Configuração salva com sucesso!'
			);
        } else {
            return array(
				'classe' => 'insucesso',
				'mensagem' => 'Ocorreu um erro desconhecido. Algumas opções não foram salvas.'
			);

        }

    }



    public function ContaConfig(){
        return count($this->options);
    }

    // retorna a data de agora no formato certo para o campo MySQL DATETIME
    function DataParaMySQL($param = ''){
        if(empty($param) or $param == 'datetime'){
            return date("Y-m-d H:i:s");
        } elseif($param == 'date'){
            return date("Y-m-d");
        }
    }
    
    // retorna a data
    function PegaData($formato){
        $formato = StrToLower($formato);
        if ($formato == "dia") return date("d");
        else if ($formato == "mes") return date("m");
        else if ($formato == "ano") return date("Y");
        else if ($formato == "hora") return date("H");
        else if ($formato == "minuto") return date("i");
        else if ($formato == "segundo") return date("s");
    }


}

?>