<?php
/**
 * UI Permissions
 *
 * Classe contém métodos relacionados a permissões de acesso de usuários
 * a páginas da UI. Esta classe não diz respeito a estruturas e módulos.
 *
 * @package Classes
 * @name UiPermissions
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.8, 31/03/2010
 */
class UiPermissions {

    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    protected $connection;

    /**
     *
     * @var <array> Contém as configurações de permissão de tipos de usuários
     * à interface.
     */
    public $permissionConfigurations = array();

    /**
     *
     * @param array $param Ids do usuário e grupo de usuário do agente
     * acessando o sistema.
     */
    function  __construct() {

        /**
         * Inicializa com dados do usuário atual
         */
        
        $this->permissionConfigurations['navegation'] = NAVEGATION_PERMISSIONS_CONFIGURATIONS::$navegation;
        $this->permissionConfigurations['configurations'] = NAVEGATION_PERMISSIONS_CONFIGURATIONS::$configurations;
        $this->permissionConfigurations['widgets'] = NAVEGATION_PERMISSIONS_CONFIGURATIONS::$widgets;
        $this->connection = Connection::getInstance();
    }

    /**
     * getInstance()
     *
     * Para Singleton
     *
     * @staticvar <object> $instance
     * @return <Conexao object>
     */
    static function getInstance(){
        static $instance;

        if( !$instance ){
            $instance[0] = new UiPermissions;
        }

        return $instance[0];
    }

    /**
     * canAccessWidgets()
     *
     * Returna true se o usuário pode ver Widgets.
     *
     * @return <bool>
     */
    function canAccessWidgets(){
        $user = User::getInstance();
        if( in_array($user->LeRegistro('tipo'), $this->permissionConfigurations['widgets']) )
            return true;

        return false;
    } // canAccessWidgets()

    /**
     * isPermittedSection()
     *
     * Retorna permissões de páginas e sections. Não tem nada a ver
     * com structures.
     *
     * @return <bool>
     */
    function isPermittedSection(){

        $navPermissoes = $this->permissionConfigurations['navegation'];
        $administrador = User::getInstance();
        /**
         * Se uma seção foi especificada
         */
        if(!empty($_GET['section'])){

            /**
             * @todo - planejar carregamento em formato MVC
             */
            /**
             * Verifica se há permissões quanto à seção atual
             */
            if( !empty($navPermissoes[$_GET['section']]) AND is_array($navPermissoes[$_GET['section']])){

                /**
                 * Verifica se há permissões quanto ao action atual
                 */
                if( !empty($navPermissoes[$_GET['section']][$_GET['action']])
                    AND is_array($navPermissoes[$_GET['section']][$_GET['action']])
                ){

                    /**
                     * Verifica se o tipo de usuário conectado tem permissão quanto ao action atual
                     */
                    if(in_array(strtolower(User::getInstance()->LeRegistro('tipo')), arraytolower($navPermissoes[$_GET['section']][$_GET['action']]))){
                        /**
                         * Se está tudo ok, se há permissões para este usuário
                         */

                        return true;
                    } else {
                        /**
                         * Se o usuário não tem permissão para acessar esta página
                         */
                        return false;
                    }
                }
                /**
                 * Não há permissões definidas quanto ao action atual
                 */
                /**
                 * Verifica se o action atual não é bloqueado para todos os usuários
                 */
                elseif( !empty($navPermissoes[$_GET['section']]['au-permissao'])
                        AND is_array($navPermissoes[$_GET['section']]['au-permissao'])
                ){
                    /**
                     * O action é bloqueado a todos os usuários.
                     *
                     * Verifica o ranking do usuário atual e ve se este tem
                     * alguma permissão.
                     */
                    if(in_array(strtolower(User::getInstance()->LeRegistro('tipo')), arraytolower($navPermissoes[$_GET['section']]['au-permissao']))){
                        return true;
                    } else {
                        return false;
                    }
                }
                /**
                 * Este action é livre para acesso global
                 */
                else {
                    return true;
                }

            }
            /**
             * Esta section possui acesso liberado globalmente
             */
            else {
                return true;
            }
        } else {
            return true;
        }

        return true;
    }

}

?>
