<?php
/**
 * @todo : acabar com esta classe. Todo carregamento de Widgets deve
 * ser feito em uma classe de UI. A classe Widget deve persistir para
 * se instanciar cada widget.
 */
class Widgets
{
	public $conexao;

	/**
	 * Id do usuário logado.
	 *
	 * @var <string>
	 */
	public $userId = '';

	public $isWidget = false;

	/**
	 * Contém todos os widgets instalados.
	 * 
	 * @var <array> 
	 */
	public $installedWidgets = array();

	public $_title;
	public $_html;

	function __construct($envParams, $userId, $widgetPath = ''){
		$this->envParams = $envParams;
		$this->conexao = $envParams['conexao'];
		$this->userId = $userId;

		if( !empty($widgetPath) ){
			
		}
	}

	/**
	 * getWidgets()
	 *
	 * Pega os widgets do usuário atual.
	 *
	 * @return <bool>
	 */
	public function getInstalledWidgets($params = array()){

		$column = empty($params['column']) ? ''	   : "AND column_nr='".$params['column']."'";
		$mode =   empty($params['mode'])   ? 'normal' : $params['mode'];

		$sql = "SELECT
					*
				FROM
					widgets
				WHERE
					admin_id='".$this->userId."'
					{$column}
				ORDER BY column_nr ASC, position_nr ASC
				";
		$query = Connection::getInstance()->query($sql);
		$result = array();

		if( $mode == 'list' ){
			return $result;
		} else {
			if( is_array($query) ){
				foreach( $query as $value ){
					$result[$value['column_nr']][$value['position_nr']] = $this->_instanciateWidget($value);
				}
			}
			$this->installedWidgets = $result;

		}

		return $result;
	} // end getInstalledWidgets()

	/**
	 * getInstalledWidgetsByColumn()
	 *
	 * Pega widgets de uma coluna.
	 *
	 * @param <string> $column
	 * @return <array>
	 */
	public function getInstalledWidgetsByColumn($column){

		if( !empty($this->installedWidgets[$column]) ){
			$widgets = $this->installedWidgets[$column];
		} else {
			$widgets = $this->getInstalledWidgets( array( 'column' => $column) );
		}
		
		$result = array();
		if( is_array($widgets) ){

			foreach( $widgets as $position_nr=>$value ){
				$result[$position_nr] = $value;
			}
		}
		return $result;
	} // end getInstalledWidgetsByColumn()

	public function getWidgets(){

		foreach (glob(WIDGETS_DIR."dashboard/*", GLOB_ONLYDIR) as $pastas) {
			include($pastas.'/core/conf.php');
			$name = basename($pastas);
			$result[$name] = $conf;

		}

		return $result;
	}

	function installWidget($params){
		if( empty($params['name']) ){
			return false;
		} else {
			$name = $params['name'];
		}
		
		if( empty($params['admin_id']) ){
			return false;
		} else {
			$admin_id = $params['admin_id'];
		}

		if( empty($params['column_nr']) ){
			return false;
		} else {
			$column_nr = $params['column_nr'];
		}
		
		if( empty($params['path']) ){
			return false;
		} else {
			$path = $params['path'];
		}

		if( empty($params['position_nr']) ){
			$position_nr = $this->getLastPositionOfGivenColumn($column_nr, $admin_id)+1;
		} else {
			$position_nr = $params['position_nr'];
		}
		
		$sql = "INSERT INTO
					widgets
					(name,path,column_nr,position_nr,admin_id)
				VALUES
					('".$name."','".$path."','".$column_nr."','".$position_nr."','".$admin_id."')
				";
		
		return Connection::getInstance()->exec($sql);

	}

	/**
	 * instantiateWidget()
	 *
	 * Instancia um Widget
	 *
	 * @param <type> $value
	 * @return class
	 */
	function _instanciateWidget($value){
		
		if( is_file(WIDGETS_DIR.$value['path'].'/core/conf.php') ){
			include(WIDGETS_DIR.$value['path'].'/core/conf.php');

			if( !empty($conf['class']) )
				$class = $conf['class'];
		}

		/*
		 * Widget tem classe própria
		 */
		if( !empty($class) ){
			include_once(WIDGETS_DIR.$value['path'].'/'.$class.'.php');
		}
		/*
		 * Widget NÃO tem classe própria
		 */
		else {
			$class = 'Widget';
		}

		//$result[$value['position_nr']] = ;
		return new $class($this->envParams, $value);
	}

	function getLastPositionOfGivenColumn($column_nr, $admin_id){
		$sql = "SELECT
					position_nr
				FROM
					widgets
				WHERE
					column_nr='".$column_nr."' AND
					admin_id='".$admin_id."'
				ORDER BY
					position_nr DESC
				LIMIT 10
				";

		$query = reset( Connection::getInstance()->query($sql) );
		if( !empty($query['position_nr'])
			AND $query['position_nr'] > 0 )
		{
			return $query['position_nr'];
		} else {
			return 0;
		}
		
	}

}
?>