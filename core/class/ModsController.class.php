<?php
/**
 * Controller genérico de todos os módulos.
 *
 * Cada módulo vai ser extended desta classe
 *
 * @package Controller
 * @name ModsController
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.5, 22/06/2009
 */
class ModsController extends Controller
{

    var $helpers = array();

    /**
     * VARIÁVEIS DE CONFIGURAÇÃO
     *
     * A seguir, todas as variáveis que são setadas antes do controller começar
     * a operar.
     */

    /**
     * Variáveis do sistema
     */
    public $conexao;
    public $connection;
    public $administrador;
    public $aust;
    public $model;
    /**
     *
     * @var Object Classe do módulo carregado atualmente
     */
    public $modulo;

    /**
     * Esta variável contém o id da estrutura atual. Ela é configurada em
     * $this->__construct();
     *
     * @var <type> ID da estrutura atual.
     */
    public $austNode;

    /**
     *
     * @var object Possui o objeto instanciado da classe do módulo.
     */
    //protected $modulo;
    /**
     *
     * @var string Diretório do módulo
     */
    protected $modDir;

    /**
     *
     * @var string Nome do controller acessado
     */
    protected $controllerName;
    /**
     *
     * @var string Qual é a action atual?
     */
    protected $action;

    /**
     * VARIÁVEIS DE APOIO
     *
     * A seguir, as variáveis que auxiliam o sistema a funcionar. Elas são
     * tratadas após a inicialização do objeto.
     */
    /**
     * Esta variável auxilia o sistema a não renderizar automaticamente views
     * mais de uma vez.
     *
     * @var bool TRUE se uma view já foi renderizada.
     */
    protected $isRendered;
    /**
     *
     * @var bool Renderiza views automaticamente por padrão
     */
    protected $autoRender = true;
    /**
     *
     * @var array Repositórios de variáveis que foram instanciadas e precisam
     * ser acessadas pelas views. ModsController::set() é o responsável
     * por tornar estas variáveis em variáveis locais para o método
     * ModsController::render().
     */
    protected $globalVars;

	public $testMode = false;
    /**
     * MÉTODOS
     *
     * 
     */
    /**
     * Inicializa a classe
     *
     * @param array $param
     *      'conexao': possui o objeto instanciado da conexão universal.
     *      'modulo' : objeto do módulo em questão.
     */
    function __construct($param){

        /**
         * OBJETOS INSTANCIADOS
         *
         * Agrega ao objeto atual todos os outros objetos criados
         */
        
        /**
         * austNode é o ID da estrutura sendo tratada
         */
        if( !empty($param["austNode"]) AND is_numeric($param["austNode"]) )
            $this->austNode = $param["austNode"];
        /**
         * $connection: configura a conexão universal a ser usada no controller
         */
        $this->connection = Connection::getInstance();;
        /**
         * $conexao: configura a conexão universal a ser usada no controller
         */
        $this->aust = (empty($param['aust'])) ? '' : $param['aust'];
        /**
         * $conexao: configura a conexão universal a ser usada no controller
         */
        $this->administrador = (empty($param['administrador'])) ? '' : $param['administrador'];

        $this->permissoes = (empty($param['permissoes'])) ? '' : $param['permissoes'];
        /**
         * $modulo: ajusta controller para receber objeto instanciado do modulo
         */
        $modulo = (empty($param['modulo'])) ? '' : $param['modulo'];
        $this->modulo = $modulo;
        /**
         * $controllerName: O nome do controller para carregar a pasta de Views
         * correta
         */
		$selfController = strtolower( str_replace("Controller", "", get_class($this)) );
        $this->controllerName = (empty($param['controllerName'])) ? $selfController : $param['controllerName'];

        /**
         * MODEL
         */
        $this->model = (empty($param['model'])) ? '' : $param['model'];

        /**
         * CONEXÃO
         */
        $this->conexao = (empty($param['conexao'])) ? '' : $param['conexao'];

        /**
         * Algumas variáveis globais precisam ser acessadas pelas Views
         *
         */
        $this->set('conexao', $this->conexao);
        $this->set('aust', $this->aust);
        $this->set('permissoes', $this->permissoes);
        $this->set('austNode', $this->austNode);
        $this->set('administrador', $this->administrador);


        /**
         * VARIÁVEIS GLOBAIS
         *
         * Agrega ao objeto atual as variáveis globais necessárias.
         */
        /**
         * $action: que ação será chamada neste módulo
         */
        $this->action = (empty($param['action'])) ? 'index' : $param['action'];

        /**
         * $_POST e $_FILES:
         *
         * 'data': se alguma coisa for enviada para ser salva no DB
         */
        if( !empty($_POST["data"])){
            if( is_array($_POST["data"]) ){
                $this->{"data"} = $_POST["data"];
            }
        }
        if( !empty($_FILES["data"]) AND is_array($_FILES["data"])){
			// percorre os models
			foreach( $_FILES["data"]['name'] as $model=>$fields ){
				
				// percorre os campos de um model
				foreach( $fields as $fieldName=>$values ){
					
					// percorre o valor de cada campo
					foreach( $values as $key=>$value ){
						
						$type = $_FILES["data"]['type'][$model][$fieldName][$key];
						$tmp_name = $_FILES["data"]['tmp_name'][$model][$fieldName][$key];
						$error = $_FILES["data"]['error'][$model][$fieldName][$key];
						$size = $_FILES["data"]['size'][$model][$fieldName][$key];
						
						if( empty($value) OR
							$size == 0 OR
							empty($tmp_name) OR
							empty($type) )
							continue;
							
						$this->{"data"}[$model][$fieldName][$key]['name'] = $value;
						$this->{"data"}[$model][$fieldName][$key]['type'] = $type;
						$this->{"data"}[$model][$fieldName][$key]['tmp_name'] = $tmp_name;
						$this->{"data"}[$model][$fieldName][$key]['error'] = $error;
						$this->{"data"}[$model][$fieldName][$key]['size'] = $size;
					}
				}
			}
	
        }
        /**
         * $modDir: diretório do módulo
         *
         * Verifica se foi passada um diretório válido
         */
        if ( !empty($param['modDir']) ){
            if( $param['modDir'][ strlen( $param['modDir'] ) -1 ] != '/' ){
                $param['modDir'].= '/';
            }
        }
        $this->modDir = (empty($param['modDir'])) ? '' : $param['modDir'];
        /**
         * EXECUTA
         *
         * Começa execução de métodos necessários.
         */
        /**
         * Ajuste de conexão é feito no pai da classe
         */
        parent::__construct($this->conexao);
        /**
         * trigger() é responsável por engatilhar todos os métodos
         * automáticos a serem rodados, como beforeFilter, render, etc.
         */
        $this->trigger( array( 'action' => $this->action ) );
    }

    /**
     * ACTIONS DE APOIO
     *
     * Métodos que desempenham funções que podem substituir actions
     * inexistentes.
     */
    protected function actions(){
        $this->set('aust', $this->aust);
        $this->render('actions', 'content_trigger');
    }

    /**
     * MÉTODOS DE SUPORTE
     *
     * Todos os métodos que dão suporte ao funcionamento do sistema.
     *      ex.: render, set, beforeFilter, afterFilter, trigger, ect
     */
    /**
     * TRIGGER
     *
     * É o responsável por chamar as funções:
     *      1. beforeFilter
     *      2. o método do action
     *      3. render
     *      4. afterFilter
     *
     * @param array $param
     *      'ation': qual método deve ser chamado
     */
    protected function trigger($param){
        /**
         * Se não há um action especificado, então assume-se index()
         */
        if( empty( $param['action'] ) ){
            $param['action'] = 'index';
        }

        /*
         * CLASSE DO MÓDULO
         *
         * A classe do módulo pode ser a
         */
        /*
         * $modulo pode ser acessível via $this->modulo
         */
        

        /**
         * $this->beforeFilter() é chamado sempre antes de qualquer ação
         */
        $this->beforeFilter();
        /**
         * Chama a action requerida.
         */
        //pr($param['action']);
        $this->{$param['action']}();
        //$this->{"save"}();
        //echo $this->action;
        //call_user_func(array($this, "saveß"));
        //echo "oijoij";

        /**
         * Se não foi renderizado ainda, renderiza automaticamente
         */
        if( !$this->isRendered AND $this->autoRender )
            $this->render( $param['action'] );
        /**
         * $this->afterFilter() é chamado sempre depois de qualquer ação
         */
        $this->afterFilter();
    }

    /**
     * Renderiza a view
     *
     * @param string $path Indica qual o view deve ser carregado.
     */
    protected function render($path, $includeType = ''){
	
        $this->set('modulo', $this->modulo);

		if( $path === false )
			return false;
		
        $includeBaseurl = $this->modDir;
        /**
         * DEFINE VARIÁVEIS PARA AS VIEWS
         *
         * Cria todas as variáveis para serem acessadas pela view diretamente.
         *
         * Ex.: $variavel estará disponível em vez de $this->variavel.
         */
        foreach( $this->globalVars as $chave=>$valor ){
            $$chave = $valor;
            /**
             * Agora as variáveis são locais a este método, sendo acessadas
             * pelo view, pois o view é acessado via include a seguir ainda
             * neste método.
             */
        }
        /**
         * Há arquivos padrões que podem substituir funcionalidades de um módulo
         * quando estes estão ausentes.
         *
         * Inclui a view correspondente desde módulo e action
         */
        if ( $includeType == 'content_trigger' ){
            include(CONTENT_TRIGGERS_DIR.$path.VIEW_FILE_STANDARD_EXTENSION);
        } else {
            include(MODULES_DIR.$this->modDir.MOD_VIEW_DIR.$this->controllerName.'/'.$path.VIEW_FILE_STANDARD_EXTENSION);
        }
        /**
         * Confirma que renderização foi feita para que não haja duplicação
         * da view
         */
        $this->isRendered = true;
        return true;
    }

    protected function set($varName, $varValue){
        $this->globalVars[$varName] = $varValue;
    }

    
    protected function beforeFilter(){

        return true;
    }

    
    protected function afterFilter(){

        return true;
    }

    /*
     *
     * MODELS
     *
     */
    /**
     * loadModel()
     *
     * Carrega models especiais do módulo atual. O model é alocado
     * em $this->{nome_do_model}.
     *
     * @param <string> $str
     * @return <bool>
     */
    public function loadModel($str = ""){

        if( !empty($this->{$str}) )
            return false;

        if( empty($str) )
            return false;
        if( !is_file(MODULES_DIR.$this->modDir.MOD_MODELS_DIR.$str.".php") )
            return false;

        include_once MODULES_DIR.$this->modDir.MOD_MODELS_DIR.$str.".php";
        $this->{$str} = new $str;

        return true;
    }

    /**
     * Tenta chamar alguma action não declarada de forma automática.
     *
     * @param string $function Que método foi chamado.
     * @param string $args Que argumentos foram passados.
     */
    public function __call($function, $args){

        /**
         * Se o arquivo existe no módulo.
         */
        if( is_file(MODULES_DIR.$this->modDir.MOD_VIEW_DIR.$function.VIEW_FILE_STANDARD_EXTENSION) ){
            $this->render( $function );
        }
        /**
         * Verifica se há algum arquivo no Core que desempenha a função desta
         * action.
         */
        elseif ( is_file(CONTENT_TRIGGERS_DIR.$function.VIEW_FILE_STANDARD_EXTENSION) ) {
            /**
             * Não implementado.
             */
            //$this->set('aust', $this->aust);
            //$this->render($function, 'content_trigger');
        }

    }

}

?>