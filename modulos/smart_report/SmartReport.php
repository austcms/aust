<?php
/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Conteúdos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
class SmartReport extends Module
{
    public $mainTable = "textos";

    public $date = array(
        'standardFormat' => '%d/%m/%Y',
        'created_on' => 'adddate',
        'updated_on' => ''
    );

    function __construct(){
        parent::__construct(array());
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
            $instance[0] = new get_class();
        }

        return $instance[0];

    }

	function runFilter($w){
		
        $sql = "
                SELECT
                    *
                FROM
                    smart_reports_filters
                WHERE
                    id='$w'
                ";
        $firstQuery = reset( $this->connection->query($sql) );

		if( empty($firstQuery) )
			return array();
			
		$query['filter'] = $firstQuery;
		$query['results'] = array();
		
		if( !empty( $query['filter']['sql_filter'] ) ){
			$sqlFilter = $query['filter']['sql_filter'];

			$sqlFilter = preg_replace('/^select /i', "SELECT id as '_id', ", $sqlFilter);
			$filter = $this->connection->query($sqlFilter);
			$query['results'] = $filter;
		}
		
		return $query;
	}
	
    /**
     * loadSql()
     *
     * Retorna um SQL para uma listagem genérica dos dados deste módulo.
     *
     * @param <array> $options
     * @return <string>
     */
    public function loadSql($options = array()) {
		$sql = "SELECT
					*
				FROM
					smart_reports_filters
				WHERE
					node_id IN('". implode("','", array_keys($options['austNode']) ) ."')
				LIMIT 1000";
        return $sql;

    } // fim getSQLForListing()

	public function load($params = array()){
		$qry = parent::load($params);
		rsort($qry);
		
		return $qry;
	}

    /**
     * save()
     *
     * Salva dados da estrutura.
     *
     * @param <array> $post
     * @return <bool>
     */
    public function save($post = array() ){

        if( empty($post) )
            return false;
        $post['frmtitulo_encoded'] = encodeText($post['frmtitulo']);


        return parent::save($post);
        
    }
}
?>