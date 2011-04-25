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
class Conteudo extends Module
{
    public $mainTable = "textos";

    public $date = array(
        'standardFormat' => '%d/%m/%Y',
        'created_on' => 'adddate',
        'updated_on' => 'addate'
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

    } // fim getSQLForListing()

	public function load($params = array()){
		$qry = parent::load($params);
		rsort($qry);
		
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
        $post['frmtitulo_encoded'] = encodeText($post['frmtitulo']);

		/*
		 * Checks if there are files to be uploaded
		 */
		if( !empty($files) && !empty($files['file']['size']) ){

			$fileHandler = File::getInstance();
			$value = $files['file'];
			
			$finalName = $fileHandler->upload($value);
			
			$finalName['systemPath'] = addslashes($finalName['systemPath']);
			$finalName['webPath'] = addslashes($finalName['webPath']);

			$post['frmfile_path'] 			= $finalName['webPath'];
			$post['frmfile_systempath'] 	= $finalName['systemPath'];
			$post['frmfile_name'] 			= $finalName['new_filename'];
			$post['frmoriginal_file_name'] = $value['name'];
			$post['frmfile_size'] 			= $value['size'];
			$post['frmfile_ext'] 			= $fileHandler->getExtension($value['name']);
			$post['frmfile_type'] 			= $value['type'];

		}
		
        return parent::save($post);
        
    }
}
?>