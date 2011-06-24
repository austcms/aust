<?php
/**
 * PERMISSÕES DE ESTRUTURAS
 *
 * Classe Permissões de Módulos, não da UI do Aust
 *
 * Contém todas os atributos e métodos referentes a permissões
 *
 * @package Classes
 * @name Permissões
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

/**
 * REGRAS DE PERMISSÃO
 *
 * 1) Se o usuário ou grupo não tem configuração alguma de permissão, significa
 * que ele tem acesso total;
 *
 * 2) Se o usuário ou grupo tem alguma configuração de permissão, significa que
 * ele não tem permissão alguma, exceto aquelas configuradas.
 */

class StructurePermissions extends SQLObject {

    var $admins_id;
    var $admins_tipos_id;
    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    protected $conexao;

    var $permissoes = array();

    /**
     *
     * @param array $param Ids do usuário e grupo de usuário do agente
     * acessando o sistema.
     */
    function  __construct($param = '') {

        /**
         * Inicializa com dados do usuário atual
         */
        $user = User::getInstance();

        $this->admins_id = $user->getId();
        $this->admins_tipos_id = $user->getTypeId();

        $this->conexao = Connection::getInstance();
    }

    /**
     * getInstance()
     *
     * Para Singleton
     *
     * @staticvar <object> $instance
     * @return <StructurePermissions object>
     */
    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new StructurePermissions;
        }

        return $instance[0];

    }

    /**
     * canEdit()
     *
     * Retorna true se usuário pode acessar formulários de edição.
     *
     * @param <int> $austNode
     * @return <bool>
     */
    function canEdit($austNode){
        return $this->verify($austNode, 'edit');
    }

    /**
     * canCreate()
     *
     * Retorna true se usuário pode acessar formulários de criação
     * de conteúdos.
     *
     * @param <int> $austNode
     * @return <bool>
     */
    function canCreate($austNode){
        return $this->verify($austNode, 'create');
    }

    /**
     * canSave()
     *
     * Retorna true se usuário pode salvar conteúdos.
     *
     * @param <int> $austNode
     * @return <bool>
     */
    function canSave($austNode){
        return ( $this->canCreate($austNode) OR
                 $this->canEdit($austNode) );

        return false;
    }

    /**
     * canDelete()
     *
     * Retorna true se usuário pode excluir conteúdos.
     *
     * Se o usuário pode editar conteúdos, significa que
     * pode exclui-los também.
     *
     * @param <int> $austNode
     * @return <bool>
     */
    function canDelete($austNode){
        return $this->canEdit($austNode);
    }

    /**
     * read()
     *
     * [Executado automaticamente na instanciação da classe.]
     *
     * Lê e retorna permissões de estruturas com acesso permitido ao usuário
     *
     * @param array $param Contém atributos e condições de leitura
     *      - 'admins_id' => id do usuário a ser lido
     *      - 'admins_tipos_id' => id do grupo de usuários (tabela admins_tipos)
     *
     * @return array Retorna todas as estruturas e categorias permitidas em
     * formato simplicado (array(0 => 'categoria 1', 1 => 'categoria 2', ...))
     */
    function read($param = ''){
		include(PERMISSIONS_FILE);
        /**
         * Ajusta configuração de leitura, verifica a quem se refere
         * a permissão que será lida
         */
        /**
         * Se nenhum parâmetro de usuário for passado, lê o usuário atual
         */
        if( empty($param['admins_tipos_id']) and empty($param['admins_id']) ){
            $agente = array(
                'admins_id' => $this->admins_id,
                'admins_tipos_id' => $this->admins_tipos_id,
            );

        /**
         * Se nenhum dos dois estão vazios (usuário e grupo de usuário)
         */
        } elseif(!empty($param['admins_tipos_id']) and !empty($param['admins_id'])){
            $agente = array(
                'admins_id' => $this->admins_id,
                'admins_tipos_id' => $this->admins_tipos_id,
            );

        } else {
            /**
             * Se requerido permissões de um usuário específico
             */
            if( !empty($param['admins_id']) ){
                    $agente = array(
                    'admins_id' => $param['admins_id']
                );

            /**
             * Ou de um grupo de usuários específico
             */
            } elseif( !empty($param['admins_tipos_id']) ) {
                $agente = array(
                    'admins_tipos_id' => $param['admins_tipos_id']
                );
            }
            
        }

        /**
         * Carrega somente o SQL necessário para identificar permissões,
         * então usa a função internacional de acesso ao DB para buscar
         * resultados
         */
        $permissoesSql = $this->find(array(
                                        'table' => 'admins_permissions',
                                        'conditions' => array(
                                            'OR' => $agente,
                                        ),
                                        'fields' => array('categorias_id','action'),
                                    ), 'sql'
        );
        $query = Connection::getInstance()->query($permissoesSql) ;


        $permissoes = array();
        foreach($query as $valor){
            if( !empty($valor['action']) )
                $permissoes[ $valor['categorias_id'] ][$valor['action']] = true;
        }

        $this->permissoes = $permissoes;

        return $this->permissoes;
        
    }

    /**
     * Verifica se determinado usuário tem acesso a determinada estrutura (ou categoria)
     * e também a um determinado action.
     *
     * Se o usuário não tem permissão a nenhum action de uma estrutura,
     * retorna falso quando perguntado sobre uma estrutura
     *
     * @param array $austNode Informações para verificação de permissão
     *      'estrutura'
     *      'permissoes'
     * @return boolean True ou false, dependendo se o agente verificado tem acesso
     * à estrutura requerida
     */
    function verify($austNode, $action = ''){
        if( $action == 'save' )
            return $this->canSave($austNode);

        if( $action == 'delete' )
            return $this->canSave($austNode);

        if( is_string($austNode) OR is_int($austNode) ){
            if( empty($this->permissoes) ){
                return true;
            } else {
                /*
                 * Há permissões.
                 *
                 * Retorna falso se:
                 *      - não há configurações sobre action desejado;
                 *      - action == false
                 */
                /*
                 * Action vazio
                 *
                 * Verifica se o usuário tem alguma permissão de action na
                 * estrutura especificada.
                 */
                if( empty($action) ){
                    if( empty($this->permissoes[$austNode]) )
                        return false;

                    return true;
                }
                /*
                 * Action especificado
                 *
                 * Verifica se não está vazio a configuração do action
                 */
                else if( empty($this->permissoes[$austNode][$action]) ){
                    return false;
                }
                /*
                 * Se tem permissão de acesso ao action atual
                 */
                else if( $this->permissoes[$austNode][$action] == true ){
                    return true;
                }
            }
            
            return false;

        } else if( is_array($params) ){
            if(empty($austNode['estrutura'])){
                return true;
            } else {
                if(empty($austNode['permissoes'])){
                    $permissoes = $this->read(array());
                } else {
                    $permissoes = $austNode['permissoes'];
                }

                if(empty($permissoes)){
                    return true;
                } elseif(in_array($austNode['estrutura'], $permissoes)){
                    return true;
                }
            }
        }
        
    }

}

?>
