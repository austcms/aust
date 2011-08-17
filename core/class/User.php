<?php
class User {
	public $login;
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

	function __construct() {
		$this->conexao = Connection::getInstance();
		$this->tipo = $this->LeRegistro('group');

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
			$instance[0] = new User;
		}

		return $instance[0];

	}

	public function rootType(){
		return "Webmaster";
	}
	
	/**
	 * type()
	 *
	 * Retorna o tipo do usuário atual.
	 *
	 * @return <string>
	 */
	public function type($newType = ""){
	
		if( !empty($newType) ){
			$this->tipo = $newType;
			return $newType;
		}
		
		if( empty($this->tipo) ){
			$this->tipo = $this->LeRegistro('group');
		}
		return $this->tipo;
	}

	public function isRoot(){
		return ($this->type() == $this->rootType());
	}

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

			if( !defined("TESTING") || TESTING !== true ){
				header("Location: logout.php?status=".$this->forbiddenCode);
				exit();
			}
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
		if( !empty($_SESSION['login']['id']) 	&&
			$_SESSION['login']['id'] > 0 		&&
			( !empty($_SESSION['login']['username']) || !empty( $_SESSION['login']['login']))
		)
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
	 * getTypeId()
	 *
	 * Retorna o Id do grupo de usuário
	 *
	 * @return <int>
	 */
	public function getTypeId(){
		return $this->LeRegistro('group_id');
	} // end getTypeId()

	/**
	 * Lê um campo diretamente do DB do usuário atual
	 *
	 * @param string $campo qual campo deve ser lido do DB
	 * @return string $dados retorno o valor lido no campo do DB
	 */
	public function LeRegistro($campo) {
		if( !empty($this->userInfo[$campo]) )
			return $this->userInfo[$campo];

		if( !isset($_SESSION) || empty($_SESSION['login']) )
			return false;

		if( $campo == 'group' ){
			$statement = "admin_groups.name as 'group'";
		} else if( $campo == 'group_id' ){
			$statement = "admin_groups.id as group_id";
		} else {
			$statement = "admins.$campo as $campo";
		}
		$sql = "SELECT
					$statement,
					admins.is_blocked
				FROM
					admins
				LEFT JOIN
					admin_groups
				ON admins.admin_group_id=admin_groups.id
				WHERE
					admins.id='".$_SESSION['login']['id']."'
				";

		$query = Connection::getInstance()->query($sql);

		if( empty($query) )
			return false;
		
		$dados = $query[0];
		$_SESSION['login'.$campo] = $dados[$campo];
		$_SESSION['login']['is_blocked'] = $dados['is_blocked'];
		$this->userInfo[$campo] = $dados[$campo];
		return $dados[$campo];

	}

	public function getNameById($id){
		$sql = "SELECT
					admins.name
				FROM
					admins
				WHERE
					id='$id'
				";

		$query = reset( Connection::getInstance()->query($sql) );
		$name = $query['name'];

		return $name;
	}
	/**
	 * getAllUser()
	 *
	 * Retorna todos os usuários cadastrados no sistema.
	 *
	 * @return <array>
	 */
	public function getAllUsers(){
		$sql = "SELECT
					admins.*,
					admin_groups.name as 'group',
					admin_groups.id as aid
				FROM
					admins
				LEFT JOIN
					admin_groups
				ON admins.admin_group_id=admin_groups.id
				ORDER BY
					admin_groups.id ASC
				LIMIT 100
				";

		$query = Connection::getInstance()->query($sql);

		return $query;
	}

	public function hasUser(){
		$sql = "SELECT
					admins.id
				FROM
					admins
				LEFT JOIN
					admin_groups
				ON
					admins.admin_group_id=admin_groups.id
				LIMIT 0,2";

		return (Connection::getInstance()->count($sql) > 0) ? true : false;
	}

	function reset(){
		$this->id 			= false;
		$this->type 		= false;
		$this->tipo 		= false;
		$this->userInfo 	= false;
		$_SESSION['login'] 	= false;
	}
}

?>