<?php
/**
 * @todo : acabar com esta classe. Todo carregamento de Widgets deve
 * ser feito em uma classe de UI. A classe Widget deve persistir para
 * se instanciar cada widget.
 */
class Widgets
{
    var $conexao;

    /**
     * Id do usuário logado.
     *
     * @var <string>
     */
    var $userId = '';

    var $isWidget = false;

    /**
     * Contém todos os widgets instalados.
     * 
     * @var <array> 
     */
    var $installedWidgets = array();

    var $_title;
    var $_html;

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

        $column = empty($params['column']) ? '' : "AND column_nr='".$params['column']."'";

        $sql = "SELECT
                    *
                FROM
                    widgets
                WHERE
                    admin_id='".$this->userId."'
                    {$column}
                ";
        $query = $this->conexao->query($sql);
        $result = array();

        if( is_array($query) ){
            foreach( $query as $valor ){
                $result[$valor['column_nr']][$valor['position_nr']] = $valor;
            }
        }
        $this->installedWidgets = $result;

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
            $widgets = reset( $this->getInstalledWidgets(array('column' => $column)) );
        }

        $result = array();

        if( is_array($widgets) ){
            foreach( $widgets as $valor ){

                if( is_file(WIDGETS_DIR.$valor['path'].'/core/conf.php') ){
                    include(WIDGETS_DIR.$valor['path'].'/core/conf.php');
                    
                    if( !empty($conf['class']) )
                        $class = $conf['class'];
                }

                /*
                 * Widget tem classe própria
                 */
                if( !empty($class) ){
                    include_once(WIDGETS_DIR.$valor['path'].'/'.$class.'.php');
                }
                /*
                 * Widget NÃO tem classe própria
                 */
                else {
                    $class = 'Widget';
                }

                $result[$valor['position_nr']] = new $class($this->envParams, $valor['path']);
            }
        }

        return $result;
    } // end getInstalledWidgetsByColumn()

    /*
     *
     * *** FOR WIDGETS ONLY ***
     *
     */

}
?>