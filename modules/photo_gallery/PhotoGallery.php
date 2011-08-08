<?php

/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
  * @name Textos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */

class PhotoGallery extends Module
{

    public $mainTable = "photo_gallery";
    
    public $date = array(
        'standardFormat' => '%d/%m/%Y',
        'created_on' => 'created_on',
        'updated_on' => 'created_on'
    );
	public $authorField = "admin_id";

    function __construct($param = ''){

        /**
         * A classe Pai inicializa algumas varíaveis importantes. A linha a
         * seguir assegura-se de que estas variáveis estarão presentes nesta
         * classe.
         */
        parent::__construct($param);
	
    }
   
}
?>