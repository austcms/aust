<?php
class Config {

    public $self = "";

    public $options;

	public $table = "config";

    /**
     * Permissions to access the configurations.
     *
     * The correct format is:
     *
     *      array(
     *          type_1 => group_1,
     *          type_2 => array(
     *              group_1,
     *              group_2,
     *          ),
     *          type_3 => '*'
     *      )
     *
     * Interpretation:
     *
     *      - type_1 : only group_1 can access,
     *      - type_2 : only groups 1 e 2 can access,
     *      - type_3 : everyone have access
     *      - type_4 : not specified, no one can access, except root user.
     *
     * @var <array>
     */
    public $permissions = array('general'=>'*', 'Geral' => '*');
    public $_userType = '';
    public $_rootType = 'root';
    public $_missingConfig = array();
	public $neededConfig;

    
    function __construct( $params = "" ) {
		if( !empty($_SERVER['QUERY_STRING']) && !empty($_SERVER['PHP_SELF']) )
        	$this->self = $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

        $this->_userType = User::getInstance()->type();
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

            if( is_string($property) ){
                $type = Registry::read('configStandardType');
                $field = 'valor';
                $params = array(
                    'where' => "propriedade='".$property."'",
                    'mode' => 'value_only',

                );
            } else {
                return false;
            }

			$config = $this->getConfigs($params);
			if( is_string($config) && !empty($config) )
				return $config;

			if( !empty($config[0]) )
            	$result = reset( $config );
			else
				$result = $config;

			if( empty($result) )
				return false;
            $result = reset($result);
            return $result;

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
    public function getConfigs($params = array(), $valueOnly = false){

        /*
         * mode of return
         */
        $mode = (empty($params["mode"])) ? '' : $params["mode"];
        unset($params['mode']);

		$fields = "*";
		if( $mode == 'value_only' ){
			$valueOnly = true;
		}

        $where = (empty($params["where"])) ? '' : 'AND ( '. $params["where"].')';
        unset($params['where']);
		
		if( !empty($params) ){
			foreach( $params as $properties ){
				$tmpWhere[] = $properties;
			}
			if( !empty($tmpWhere) ){
				$where.= " AND propriedade IN ('".implode("','", $tmpWhere)."')";
			}
		}
		
        /**
         * Type of configuration, generally global
         */
        $type = (empty($params["type"])) ? array() : $params["type"];
        if( is_string($type) )
            $type = array($type);
        $type = (empty($type)) ? '' : ' AND tipo IN (\''. implode("','", $type) .'\')';

        $sql = "SELECT ".$fields." FROM
                    ".$this->table."
                WHERE
                    1=1
                    $where
                    $type
                ORDER BY tipo ASC
                ";
        $query = Connection::getInstance()->query($sql);

        $result = array();
		if( $valueOnly && !empty($query) ){
			foreach( $query as $valor ){
				$result[$valor['propriedade']] = $valor["valor"];
			}
		} else {
	        foreach( $query as $valor ){
	            $result[$valor['tipo']][] = $valor;
	        }
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
     * Verifies if an user has permission to see a given configuration
     * following the rules in $this->permission.
     *
     * @param <mixed> $param
     * @return <bool>
     */
    function hasPermission($type, $userType = ""){

		if( !empty($userType) )
			$uT = $userType;
		else
        	$uT = $this->_userType;

        if( $uT == $this->_rootType )
            return true;
		
        if( is_string($type) ){
            $givenType = $type;

            if( array_key_exists($givenType, $this->permissions) ){

                if( is_string( $this->permissions[$givenType]) ){
                    if( $uT == strtolower($this->permissions[$givenType]) )
                        return true;
                    if( $this->permissions[$givenType] == '*' )
                        return true;
                }
                elseif( is_array( $this->permissions[$givenType]) ){
                    foreach( $this->permissions[$givenType] as $typePermitted ){
                        if( $uT == strtolower($typePermitted) )
                            return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * checkIntegrity()
     *
     * Verifies configurations integrity.
     *
     * @return <bool>
     */
    public function checkIntegrity(){
		
		if( empty($this->neededConfig) )
        	$neededConfig = Registry::read('neededConfig');
		else
			$neededConfig = $this->neededConfig;

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

    }

    public function _initConfig(){

		if( empty($this->_missingConfig) )
	        if( $this->checkIntegrity() )
				return true;
		
        $i = 0;
        foreach( $this->_missingConfig as $neededConfig ){
            foreach( $neededConfig as $key=>$value ){
                $fields[$i][] = $key;
                $values[$i][] = $value;
            }
            $i++;
        }

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

    function updateOptions($params){
		$params = sanitizeString($params);
		$sql = "UPDATE ".$this->table." SET valor='".$params["valor"]."' WHERE id='".$params["id"]."'";
        Connection::getInstance()->exec($sql);

        return '<span style="color: green;">Configuração salva com sucesso!</span>';
    }
    
    // Adjusts the configuration
    function adjustOptions($params){
        $this->options[$params["tipo"]][$params["propriedade"]] = $params;
        return true;
    }

    // save into the configuration the DB
    public function save() {

        foreach( $this->options as $tipo=>$valor ){

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

}
?>