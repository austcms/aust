<?php
/**
 * EXPORT
 *
 * Responsible for exporting structure datas.
 *
 * @package Classes
 * @name Image
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2, 19/10/2010
 */
class Export
{

    /*
     * OPÇÕES
     */
	    /**
	     * Endereço onde serão salvos os arquivos. Por padrão, uploads/.
	     * 
	     * @var string
	     */
	    public $path = '';


	function __construct(){
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
            $instance[0] = new Export;
        }

        return $instance[0];

    }

	function getStructures(){
		
		$aust = Aust::getInstance();
		return $aust->getStructures();
		
	}
}


?>