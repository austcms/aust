<?php
class Administrador {
    var $login;
    /**
     *
     * @var class Classe responsável pela conexão com o banco de dados
     */
    protected $conexao;
    /**
     *
     * @var class Contém o tipo de usuário em modo legível
     */
    public $userInfo;

    public $id;

    public $forbiddenCode;

    function __construct($conexaoClass, $location = '') {
        $this->conexao = $conexaoClass;
        $this->tipo = $this->LeRegistro('tipo');

    }

    /**
     * type()
     *
     * Retorna o tipo do usuário atual.
     *
     * @return <string>
     */
    public function type(){
        return $this->tipo;
    } // end type()

    /**
     * tipo() alias-> type()
     *
     * @return <string>
     */
    public function tipo(){
        return $this->type();
    } // end tipo()

    /**
     * redirectForbiddenSession()
     *
     * Realiza o devido redirecionamento da sessão caso ela
     * seja proibida
     *
     * @return <bool>
     */
    public function redirectForbiddenSession(){
        if( !empty($this->forbiddenCode) ){
            header("Location: logout.php?status=".$this->forbiddenCode);
            exit();
            return true;
        }

        return false;
    }

    /**
     * verifySession()
     *
     * @return <bool>
     */
    function verifySession() {
        
        if( !empty($_SESSION['login']['is_blocked'])){
            if($_SESSION['login']['is_blocked'] == '1'){
                $this->forbiddenCode = '103';
                return false;
            }
        }

        $paginaatual = basename($_SERVER['PHP_SELF']);
        if( $paginaatual <> 'index.php' AND
            empty($_SESSION["loginid"]) )
        {
            $this->forbiddenCode = '100';
            return false;
        } else if( $paginaatual <> 'adm_main.php' AND
                   !empty($_SESSION['login']['id']))
        {
            header("Location: adm_main.php");
            return false;
        }

        return true;
    }

    /**
     * isLogged()
     *
     * Verifica se o usuário está logado.
     *
     * @return <bool>
     */
    public function isLogged(){
        if( !empty($_SESSION['login']['id']) AND
            $_SESSION['login']['id'] > 0 AND
            !empty( $_SESSION['login']['username'] ) )
        {
            return true;
        }
        /*
         * Não logado
         */
        else {
            return false;
        }
    } // end isLogged()

    /**
     * getId()
     * 
     * Retorna o Id do administrador
     * 
     * @return <string>
     */
    public function getId(){
        if( !empty($this->id) ){
            return $this->id;
        } else {
            $this->id = $this->LeRegistro('id');
            return $this->id;
        }
    } // end getId()

    /**
     * Lê um campo diretamente do DB do usuário atual
     *
     * @param string $campo qual campo deve ser lido do DB
     * @return string $dados retorno o valor lido no campo do DB
     */
    public function LeRegistro($campo) {
        if( !empty($this->userInfo[$campo]) )
            return $this->userInfo[$campo];

        if( $campo == 'tipo' ){
            $statement = "admins_tipos.nome as tipo";
        } else if( $campo == 'tipoid' ){
            $statement = "admins_tipos.id as tipoid";
        } else {
            $statement = "admins.$campo as $campo";
        }

        $sql = "SELECT
                    $statement,
                    admins.is_blocked
                FROM
                    admins
                LEFT JOIN
                    admins_tipos
                ON admins.tipo=admins_tipos.id
                WHERE
                    admins.id='".$_SESSION['login']['id']."'
                ";

        $query = $this->conexao->query($sql);

        if( empty($query) )
            return false;
        
        $dados = $query[0];
        $_SESSION['login'.$campo] = $dados[$campo];
        $_SESSION['login']['is_blocked'] = $dados['is_blocked'];
        $this->userInfo[$campo] = $dados[$campo];
        return $dados[$campo];

    }


}

?>