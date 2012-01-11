<?php
/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */
class Textual extends Module
{
	public $mainTable = "textual";

	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'created_on',
		'updated_on' => 'updated_on'
	);

	public $authorField = "admin_id";

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

		return parent::loadSql($options);

	}

	public function load($params = array()){
		$qry = parent::load($params);
		#rsort($qry);
		
		return $qry;
	}

	/**
	 * save()
	 *
	 * Salva dados da estrutua.
	 *
	 * @param <array> $post
	 * @return <bool>
	 */
	public function save($post = array(), $files = array() ){

		if( empty($post) )
			return false;
		$post['frmtitle_encoded'] = encodeText($post['frmtitle']);

		/*
		 * Checks if there are files to be uploaded
		 */
		if( !empty($files) && !empty($files['frmcover_image']['size']) && $this->getStructureConfig("has_cover_image")){

			$fileHandler = File::getInstance();
			$value = $files['frmcover_image'];
			
			$finalName = $fileHandler->upload($value);
			
			$finalName['systemPath'] = addslashes($finalName['systemPath']);
			$finalName['webPath'] = addslashes($finalName['webPath']);

			$post['frmcover_image_file_path'] 			= $finalName['webPath'];
			$post['frmcover_image_file_systempath'] = $finalName['systemPath'];
			$post['frmcover_image_file_name'] 			= $finalName['new_filename'];
			$post['frmcover_image_file_size'] 			= $value['size'];
			$post['frmcover_image_file_type'] 						= $value['type'];
		}
		
		return parent::save($post);
		
	}
}
?>