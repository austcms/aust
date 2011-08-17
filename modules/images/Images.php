<?php
/**
 * Module's model class
 *
 * @since v0.1.5, 30/05/2009
 */

class Images extends Module
{
	public $mainTable = 'images';

	public $date = array(
		'standardFormat' => '%d/%m/%Y',
		'created_on' => 'created_on',
		'updated_on' => 'updated_on'
	);

	public $fieldsToLoad = array(
		'title', 'pageviews', 'file_systempath'
	);

	public $authorField = "admin_id";
	public $austField = "node_id";

	public $defaultLimit = 25;
	
	function __construct($param = ''){

		/**
		 * A classe Pai inicializa algumas varíaveis importantes. A linha a
		 * seguir assegura-se de que estas variáveis estarão presentes nesta
		 * classe.
		 */
		parent::__construct($param);
	
	}

	/**
	 * trataImagem
	 *
	 * Trata uma imagem
	 *
	 * @param array $files O mesmo $_FILE vindo de um formulário
	 * @param string $width Valor padrão de largura
	 * @param string $height Valor padrão de altura
	 * @return array
	 */
	function trataImagem($files, $width = "1024", $height = "768"){

		/*
		 * Toma dados de $files
		 */
		$frmarquivo = $files['frmarquivo']['tmp_name'];
		$frmarquivo_name = $files['frmarquivo']['name'];
		$frmarquivo_type = $files['frmarquivo']['type'];

		/*
		 * Abre o arquivo e tomas as informações
		 */
		$fppeq = fopen($frmarquivo,"rb");
		$arquivo = fread($fppeq, filesize($frmarquivo));
		fclose($fppeq);

		/*
		 * Cria a imagem e toma suas proporções
		 */
		$im = imagecreatefromstring($arquivo); //criar uma amostra da imagem original
		$largurao = imagesx($im);// pegar a largura da amostra
		$alturao = imagesy($im);// pegar a altura da amostra

		/*
		 * Configura o tamanho da nova imagem
		 */
		if($largurao > $width)
			$largurad = $width;
		else
			$largurad = $largurao; // definir a altura da miniatura em px

		$alturad = ($alturao*$largurad)/$largurao; // calcula a largura da imagem a partir da altura da miniatura
		$nova = imagecreatetruecolor($largurad,$alturad); // criar uma imagem em branco
		//imageantialias($nova,true);
		//imagecopyresized($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);
		imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

		ob_start();
		imagejpeg($nova, '', 100);
		$mynewimage = ob_get_contents();
		ob_end_clean();

		/*
		 * Prepara dados resultados para retornar
		 */
		imagedestroy($nova);

		$result["filesize"] = strlen($mynewimage);
		//$result["filedata"] = addslashes($mynewimage);
		$result["filedata"] = $mynewimage;
		$result["filename"] = $frmarquivo_name;
		$result["filetype"] = $frmarquivo_type;

		return $result;

	}


	 
}

?>