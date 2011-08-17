<?php
/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */
class Agenda extends Module
{
	public $mainTable = "st_agenda";

	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'created_on',
		'updated_on' => 'updated_on'
	);

	public $authorField = "autor";
	public $austField = 'categoria_id';
	public $order = 'actor_admin_id DESC';

	public $fieldsToLoad = array(
		'title',
		'start_datetime',
		'end_datetime',
		'occurs_all_day',
		'actor_admin_id',
		'actor_admin_name',
	);

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


	/**
	 * loadSql()
	 *
	 * Retorna um SQL para uma listagem genérica dos dados deste módulo.
	 *
	 * @param <array> $options
	 * @return <string>
	 */
	public function loadSql($options = array()) {

		if( empty($options['where']) )
			$options['where'] = "";

		return parent::loadSql($options);

	} // fim getSQLForListing()

	/**
	 * save()
	 *
	 * Salva dados da estrutua.
	 *
	 * @param <array> $post
	 * @return <bool>
	 */
	public function save($post = array(), $options = array() ){

		if( empty($post) )
			return false;
		return parent::save($post);
		
	}
}
?>