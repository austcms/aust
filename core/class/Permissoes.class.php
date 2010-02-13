<?php
/**
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

class Permissoes extends SQLObject {

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
    function  __construct($param) {

        /**
         * Inicializa com dados do usuário atual
         */
        if(!empty($param)){
            $this->admins_id = $param['admins_id'];
            $this->admins_tipos_id = $param['admins_tipos_id'];
        }

        $this->conexao = (empty($param['conexao'])) ? '' : $param['conexao'];
    }

    /**
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

        /**
         * Ajusta configuração de leitura, verifica a quem se refere
         * a permissão que será lida
         */
        /**
         * Se nenhum parâmetro de usuário for passado, lê o usuário atual
         */
        if(empty($param['admins_tipos_id']) and empty($param['admins_id'])){
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
                                        'fields' => array('categorias_id'),
                                    ), 'sql'
        );
        $permissoes = $this->conexao->query($permissoesSql) ;
        $result = array();
        if( !empty($permissoes) ){
            foreach( $permissoes as $permissao ){
                $result[] = $permissao['categorias_id'];
            }
        }
        $this->permissoes = $result;

        return $result;
        
    }

    /**
     * Verifia se determinado usuário tem acesso a determinada estrutura (ou categoria).
     *
     * @param array $param Informações para verificação de permissão
     *      'estrutura'
     *      'permissoes'
     * @return boolean True ou false, dependendo se o agente verificado tem acesso
     * à estrutura requerida
     */
    function verify($param){
        if( is_string($param) OR is_int($param) ){
            if( empty($this->permissoes) ){
                return true;
            }

            if( in_array($param, $this->permissoes) ){
                return true;
            }

            return false;

        } else if( is_array($params) ){
            if(empty($param['estrutura'])){
                return true;
            } else {
                if(empty($param['permissoes'])){
                    $permissoes = $this->read(array());
                } else {
                    $permissoes = $param['permissoes'];
                }

                if(empty($permissoes)){
                    return true;
                } elseif(in_array($param['estrutura'], $permissoes)){
                    return true;
                }
            }
        }
        
    }

}

?>
