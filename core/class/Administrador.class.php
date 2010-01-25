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
    public $tipo;

    function __construct($conexaoClass, $location = '') {
        $this->conexao = $conexaoClass;
        $this->VerificaSession();
        $this->tipo = $this->LeRegistro('tipo');
    }
    /*********************************
    *
    *	funções de ação
    *
    *********************************/


    /*********************************
    *
    *	funções de verificaçãoo ou leitura
    *
    *********************************/

    // verifica se o usuário está logado ou não e redireciona
    function VerificaSession() {
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
        $sql = "SELECT
                    admins.*, admins_tipos.nome as tipo, admins_tipos.id as tipoid
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
        return $dados[$campo];

    }


}

?>