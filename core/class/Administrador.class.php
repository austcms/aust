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

    function __construct($conexaoClass, $location = '') {
        $this->conexao = $conexaoClass;
        $this->tipo = $this->LeRegistro('tipo');
        $this->VerificaSession();
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

    // verifica se o usuário está logado ou não e redireciona
    function VerificaSession() {
        if( !empty($_SESSION['login']['is_blocked'])){
            if($_SESSION['login']['is_blocked'] == '1'){
                header("Location: logout.php?status=1022");
                exit();
            }
        }

        $paginaatual = basename($_SERVER['PHP_SELF']);
        if($paginaatual <> 'index.php') {
            if(empty($_SESSION["loginid"]))
                header("Location: index.php");
        } else {
            if(!empty($_SESSION["loginid"]))
                header("Location: adm_main.php");
        }
    }

    /**
     * getId()
     * 
     * Retorna o Id do administrador
     * 
     * @return <string>
     */
    public function getId(){
        return $this->LeRegistro('id');
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
                    admins.login='".$_SESSION['loginlogin']."' AND
                    admins.id='".$_SESSION['loginid']."'
                ";

        $query = $this->conexao->query($sql);
        $dados = $query[0];
        $_SESSION['login'.$campo] = $dados[$campo];
        $_SESSION['login']['is_blocked'] = $dados['is_blocked'];
        $this->userInfo[$campo] = $dados[$campo];
        return $dados[$campo];

    }


}

?>