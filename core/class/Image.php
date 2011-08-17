<?php
/**
 * IMAGE
 *
 * Responsável por tratamento de imagens, uploads e carregamento
 *
 * @since v0.1.9, 26/08/2009
 */
class Image extends File
{

	/*
	 * OPÇÕES
	 */
		/**
		 * Endereço onde serão salvos os arquivos. Por padrão, uploads/.
		 * 
		 * @var string
		 */
		public $path = 'uploads/';

		public $prependedPath = '';
		/**
		 * Tamanho máximo permitido do arquivo. Valor em bytes.
		 *
		 * @var string
		 */
		public $max_filesize = "10000000"; // in bytes

		/**
		 * Largura máxima permitida caso o arquivo seja imagem.
		 *
		 * @var string
		 */
		public $max_width;
		/**
		 * Altura máxima permitida caso o arquivo seja imagem.
		 *
		 * @var string
		 */
		public $max_height;

		public $filenameType = "sha1";
		/**
		 * Última mensagem de erro.
		 *
		 * @var string
		 */
		public $error;

		/**
		 * Todas as mensagens de erro, se ocorreram.
		 *
		 * @var array
		 */
		public $allErrors = array();

		/**
		 * Endereço do último arquivo inserido
		 *
		 * @var string
		 */
		public $lastInsertPath;

		public $lastFilename;
		public $lastSize;
		public $lastType;

		/**
		 * Funcionalidade que cria subdiretórios em app/public/uploads
		 * automaticamente, ficando no formato app/public/uploads/ano/mês/
		 *
		 * @var bool
		 */
		public $autoOrganizeFolders = true;

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
			$instance[0] = new Image;
		}

		return $instance[0];

	}

	/**
	 * @TODO - testunit
	 */
	function path(){
		return $this->prependedPath.$this->path;
	}

	/**
	 * upload()
	 *
	 * Realiza o upload do arquivo passado como argumento.
	 *
	 * @param array $file O mesmo formato vindo do formulário
	 * @return mixed Retorna o endereço do arquivo salvo
	 */
	public function upload($file = "", $customFilename = ""){
		if( empty($file) )
			return false;

		/*
		 * VALIDAÇÃO
		 */
		if( !$this->validate($file) ){
			return false;
		}

		/*
		 * Gera um nome único para a imagem SHA1
		 */
	 	if( !empty($customFilename) )
			$fileName = $customFilename;
		else if( $this->filenameType == "sha1" )
			$fileName = sha1(uniqid(time())) . "." . $this->getExtension($file["name"]);
		else if( $this->filenameType == "md5" )
			$fileName = md5(uniqid(time())) . "." . $this->getExtension($file["name"]);
		else
			$fileName = $file["name"];
		
		$fileName = str_replace(" ", "", $fileName);
		/*
		 * Caminho de onde a imagem ficará
		 */
		$fileDir = $this->path();

		/*
		 * autoOrganizaFolders
		 *
		 * Cria diretório ano/mês/ para separar e organizar melhor os uploads
		 */

		$fileDir = $this->_organizeFolders($fileDir);
		$filePath = $fileDir . $fileName;

		$systemFilePath = getcwd() . "/" . $filePath;
		$webFilePath =  $filePath;

		/*
		 * Salva informações da imagem
		 */
		$this->lastSize = $file["size"];
		$this->lastType = $file["type"];
		$this->lastFilename = $file["name"];
		/*
		 * UPLOAD DA IMAGEM
		 */
		$uploadedFile = copy($file["tmp_name"], $filePath);
		unlink($file["tmp_name"]);
		
		if( $uploadedFile ){

			$this->lastSystemPath = $systemFilePath;
			$this->lastWebPath = $webFilePath;
			return array(
				'new_filename' => $fileName,
				'extension' => $this->getExtension($file['name']),
				'webPath' => $webFilePath,
				'systemPath' => $systemFilePath,
			);
		}

		return false;
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
	function resample($files, $dimensions = "1280x1024"){
		$dimensions = $this->getResampleDimensions($files, $dimensions);
		$largurad = $dimensions['width'];
		$alturad = $dimensions['height'];
		/*
		 * Toma dados de $files
		 */
		$frmarquivo = $files['tmp_name'];
		$frmarquivo_name = $files['name'];
		$frmarquivo_type = $files['type'];

		/*
		 * Abre o arquivo e tomas as informações
		 */
		$fppeq = fopen($frmarquivo,"rb");
		$arquivo = fread($fppeq, filesize($frmarquivo));
		fclose($fppeq);

		/*
		 * Cria a imagem e toma suas proporções
		 */
		if( $files['type'] == 'image/png' )
			$im = imagecreatefrompng($frmarquivo); //criar uma amostra da imagem original
		else if( $files['type'] == 'image/gif' ){
			// PHP as of 5.3 doesn't support GIF animations
			return $files;
 			$im = imagecreatefromgif($frmarquivo); //criar uma amostra da imagem original
			
		} else
			$im = imagecreatefromjpeg($frmarquivo); //criar uma amostra da imagem original


		$largurao = imagesx($im);// pegar a largura da amostra
		$alturao = imagesy($im);// pegar a altura da amostra

		/*
		 * Configura o tamanho da nova imagem
		 *
		if($largurao > $width)
			$largurad = $width;
		else
			$largurad = $largurao; // definir a altura da miniatura em px
*/
//		$alturad = ($alturao*$largurad)/$largurao; // calcula a largura da imagem a partir da altura da miniatura
		$nova = imagecreatetruecolor($largurad,$alturad); // criar uma imagem em branco

		// PNG ou GIF, ajusta transparência
		if( in_array($files['type'], array('image/png', 'image/gif') ) ){
			imagealphablending($nova, false);
			imagesavealpha($nova,true);
			$transparent = imagecolorallocatealpha($nova, 255, 255, 255, 127);
			imagefilledrectangle($nova, 0, 0, $largurad, $alturad, $transparent);
		}
		
		imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);


//		exit();
		ob_start();

		if( $files['type'] == 'image/png' )
			imagepng($nova);
		else if( $files['type'] == 'image/gif' )
			imagegif($nova);
		else
			imagejpeg($nova, null, 100);

		$mynewimage = ob_get_contents();
		ob_end_clean();

		/*
		 * Prepara dados resultados para retornar
		 */
		imagedestroy($nova);

		$fhandle = fopen($frmarquivo,"w+b");
		fwrite($fhandle, $mynewimage);
		fclose($fhandle);

		$result["size"] = strlen($mynewimage);
		$result["tmp_name"] = $files['tmp_name'];
		$result["name"] = $files['name'];
		$result["type"] = $files['type'];
		$result["error"] = '0';

		return $result;

	}

	public function getDimensions($filePath){
		
		if( is_string($filePath) )
			$extension = $this->getExtension($filePath);
		else if( is_array($filePath) ){
			$extension = $this->getExtension($filePath['name']);
			$filePath = $filePath["tmp_name"];
		}

		if( $extension == "jpeg" || $extension == "jpg" )
 			$image = imagecreatefromjpeg($filePath);
		else if( $extension == "png" || $extension == "png" )
 			$image = imagecreatefrompng($filePath);
		else
			return false;
		
		$result["width"] = imagesx($image);
		$result["height"] = imagesy($image);
		return $result;
	}
	
	public function getResampleDimensions($filePath, $wantedDimensions = false){

		$dimensions = $this->getDimensions($filePath);
		$originalWidth = $dimensions['width'];
		$originalHeight = $dimensions['height'];

		if( !$wantedDimensions ){
			return array(
				'width' => $originalWidth,
				'height' => $originalHeight
			);
		}
			
		$dimensions = trim($wantedDimensions);
		$noWidth = ( substr($dimensions, 0, 1) == "x" );
		$widthHeight = explode("x", $dimensions);
		
		if( $noWidth ){
			$wantedWidth = false;
			$wantedHeight = $widthHeight[1];
		} else {
			$wantedWidth = $widthHeight[0];
			$wantedHeight = false;
			if( !empty($widthHeight[1]) ){
				$wantedHeight = $widthHeight[1];
			}
		}

		if( is_numeric($wantedWidth) &&
			$wantedWidth > $originalWidth
		){
			$wantedWidth = $originalWidth;
		}
		if( is_numeric($wantedHeight) &&
			$wantedHeight > $originalHeight
		){
			$wantedHeight = $originalHeight;
		}

		if( empty($height) || empty($width) ){
			if( !empty($wantedWidth) && empty($wantedHeight) ){
				$ratio = $originalWidth / $wantedWidth;
				$height = $originalHeight / $ratio;
				$width = $wantedWidth;
			}
			else if( empty($wantedWidth) && !empty($wantedHeight) ){
				$ratio = $originalHeight / $wantedHeight;
				$width = $originalWidth / $ratio;
				$height = $wantedHeight;
			}
			else if( !empty($wantedWidth) && !empty($wantedHeight) ){
				$ratio = $originalWidth / $wantedWidth;
				$height = $originalHeight / $ratio;
				
				if( $height > $originalHeight ){
					$wantedHeight = $originalHeight;
					$height = $originalHeight;
					$ratio = $originalHeight / $wantedHeight;
					$width = $originalWidth / $ratio;
				}
				
				if( empty($width) )
					$width = $wantedWidth;
				if( empty($height) )
					$height = $wantedHeight;
			}
			else {
				return array(
					"width" => false,
					"height" => false
				);
			}
		}
		
		return array(
			'width' => $width,
			'height' => $height
		);
		
	}
	
	/*
	 *
	 * VALIDAÇÃO
	 *
	 * Valida se o arquivo pode ser uploaded
	 *
	 */
	public function validate($file){

		$valid = true;

		if( empty($file['tmp_name']) ){
			$this->_setError("max_filesize");
			return false;
		}
		/*
		 * Verifica tamanho do arquivo
		 */
		if($file["size"] > $this->max_filesize ){
			$this->_setError("max_filesize");
			$valid = false;
		}

		/*
		 * SE IMAGEM
		 */
		/*
		 * É imagem
		 *
		 * Verifica se o mime-type do arquivo é de imagem
		 */
		if( $this->isImage($file["type"]) ){

			/*
			 * Dimensões da imagem
			 */
			$imageSize = getimagesize($file["tmp_name"]);

			/*
			 * Verifica largura do arquivo
			 */
			if( !empty($this->max_width)
				AND $imageSize[0] > $this->max_width ){
				$this->_setError("max_width");
				$valid = false;
			}

			/*
			 * Verifica altura do arquivo
			 */
			if( !empty($this->max_height)
				AND $imageSize[1] > $this->max_height ){
				$this->_setError("max_height");
				$valid = false;
			}
		}

		return $valid;

	}

	/**
	 * isImage()
	 *
	 * Verifica se um arquivo é imagem.
	 *
	 * @param string $fileType O tipo mimetype do arquivo
	 * @return bool
	 */
	public function isImage($fileType){
		
		if( is_array($fileType) AND !empty($fileType['type']) )
			$fileType = $fileType['type'];
		
		if( !is_string($fileType) )
			return false;
		
		if( preg_match("/^image\/(tiff|pjpeg|jpeg|png|gif|bmp)$/i", $fileType) ){
			return true;
		}

		return false;
	}

	/**
	 * getExtension()
	 *
	 * Retorna a extensão de um arquivo de acordo com seu nome.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function getExtension($fileName){
		$ext = explode('.', $fileName);
		$ext = array_reverse($ext);
		
		// se não tem extensão
		if( empty($ext[1]) )
			return "";
		return strtolower( $ext[0] );
	}


	/*
	 *
	 * COMANDOS INTERNOS
	 *
	 */
	/**
	 * _organizeFolders()
	 *
	 * Organiza os diretórios dentro da pasta de upload para melhor
	 * visualização.
	 *
	 * @param string $dirToUpload Diretório a ser organizado
	 * @return string Diretório final criado
	 */
	public function _organizeFolders($dirToUpload){
		//$dirToUpload = getcwd()."/".$dirToUpload;
		//$dirToUpload = getcwd()."/".$dirToUpload;
		//$dirToUpload = getcwd()."/".$dirToUpload;
		//$dirToUpload = "/acidphp/app/public/".$dirToUpload;
		//$dirToUpload = "app/public/".$dirToUpload;

		if( $this->autoOrganizeFolders ){
			$dirToUpload.= date("Y")."/";

			if( !is_dir($dirToUpload) ){
				if( mkdir($dirToUpload, 0777) ){
					chmod($dirToUpload, 0777);

					$dirToUpload.= date("m") . "/";
					if( !is_dir($dirToUpload) ){
						if( mkdir($dirToUpload, 0777) ){
							chmod($dirToUpload, 0777);
						} else {
							showError("Permission denied on creating year/ dir for uploading files. Verify this.");
							return false;
						}
					}
				} else {
					showError("Permission denied on creating month/ dir for uploading files. Verify this.");
					return false;
				}

			} else {
				$dirToUpload.= date("m") . "/";
				if( !is_dir($dirToUpload) ){
					if( mkdir($dirToUpload, 0777) ){
						chmod($dirToUpload, 0777);
					} else {
						showError("Permission denied on creating month/ dir for uploading files. Verify this.");
						return false;
					}
				}
			}
		}

		return $dirToUpload;
		
	}

	/**
	 * _setError()
	 *
	 * Ajusta os erros ocorridos e salva em $this->error.
	 *
	 * @param string $str Mensagem de erro.
	 */
	public function _setError($str){
		$this->error = $str;
		
		if( empty($this->allErrors) ){
			$this->allErrors[] = $str;
		} else if( is_string($this->allErrors) ){
			$tmp = $this->allErrors;
			$this->allErrors = null;
			$this->allErrors[] = $tmp;
			$this->allErrors[] = $str;
		} else if( is_array($this->allErrors) ) {
			$this->allErrors[] = $str;
		}
	}

}


?>