<?php
/**
 * WIDGET
 *
 * Super-classe.
 *
 * @since v0.1.7, 25/01/2010
 */
class Widget
{
	/**
	 *
	 * @var <array_of_objects>
	 */
	public $envParams;
	
	public $conexao;

	/**
	 * Verifica se já foi renderizado
	 *
	 * @var <bool>
	 */
	public $_preRendered = false;
	/**
	 * Tooltips do Widget
	 *
	 * @var <array>
	 */
	public $_tt = '';

	function __construct($envParams, $data){
		$this->envParams = $envParams;
		$this->conexao = $envParams['conexao'];
		$this->path = $data['path'];
		$this->id = $data['id'];

	}

	function preRenderWidget(){

		$widgetPath = $this->path;
		$this->_preRendered = true;
		if( is_dir(WIDGETS_DIR.$widgetPath) ){

			foreach( $this->envParams as $var=>$value ){
				$$var = $value;
			}

			/*
			 * Configurações
			 */
			include( WIDGETS_DIR.$widgetPath.'/core/conf.php' );
			$this->_title = $conf['title'];

			$this->_tt = (empty($conf['tt'])) ? '' : $conf['tt'];

			if( !empty($conf['class']) AND is_string($conf['class']) ){
				include_once( WIDGETS_DIR.$widgetPath.'/'.$conf['class'].'.php' );
				$widget = $this;//new $conf['class'];
			}

			$this->isWidget = true;
			unset($conf);

			/*
			 * Variáveis Globais
			 */
			global $conexao;
			global $permissoes;
			global $categoriasPermitidas;

			/*
			 * Carrega HTML a ser mostrado.
			 */
			ob_start(); // start buffer
			include( WIDGETS_DIR.$widgetPath.'/html.php' );
			$html = ob_get_contents();
			ob_end_clean(); // end buffer

			$this->_html = $html;

			return $html;
			//include WIDGETS_DIR.$widgetPath;
		}
	}

	/**
	 * getTitle()
	 *
	 * @return <string>
	 */
	function getTitle(){
		if( empty($this->_title) ){
			include( WIDGETS_DIR.$this->path.'/core/conf.php' );
			$this->_title = $conf['title'];
		}

		return $this->_title;
	}

	/**
	 * getHtml()
	 *
	 * Retorna HTML deste widget
	 *
	 * @return <string>
	 */
	function getHtml(){

		$this->preRenderWidget();

		return $this->_html;
	}

	/**
	 * getId()
	 *
	 * @return <string>
	 */
	function getId(){
		return $this->id;
	}

	/**
	 * getName()
	 *
	 * @return <string>
	 */
	function getName(){
		$name = array_reverse( explode('/', $this->path) );
		return $name[0];
	}

	/**
	 * getPath()
	 *
	 * @return <string>
	 */
	function getPath(){
		return $this->path;
	}

	/**
	 * getTooltip()
	 *
	 * @return <string>
	 */
	function getTooltip($location = ''){

		if( empty($location) ){
			$location = 'title';
		}

		$this->preRenderWidget();

		if( empty($this->_tt[$location]) )
			return '';
		else
			return tt( $this->_tt[$location] );
	}
}
?>