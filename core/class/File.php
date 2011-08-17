<?php
/**
 * FILE
 *
 * Responsável por tratamento de arquivos e uploads.
 *
 * @since v0.1.9, 26/08/2009
 */
class File
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
		public $max_filesize = "100000000"; // in bytes


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

	function __construct() {
	
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
			$instance[0] = new File;
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
	public function upload($file = ""){
		if( empty($file) )
			return false;

		/*
		 * Gera um nome único para o arquivo SHA1
		 */
		if( $this->filenameType == "sha1" )
			$fileName = sha1(uniqid(time())) . "." . $this->getExtension($file["name"]);
		else if( $this->filenameType == "md5" )
			$fileName = md5(uniqid(time())) . "." . $this->getExtension($file["name"]);
		else
			$fileName = $file["name"];

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
		if( move_uploaded_file($file["tmp_name"], $filePath) ){

			$this->lastSystemPath = $systemFilePath;
			$this->lastWebPath = $webFilePath;
			return array(
				'new_filename' => $fileName,
				'webPath' => $webFilePath,
				'systemPath' => $systemFilePath,
			);
		}

		return false;
	}

	/**
	 * isFlash()
	 *
	 * Verifica se um arquivo é Flash.
	 *
	 * @param string $fileType O tipo mimetype do arquivo
	 * @return bool
	 */
	public function isFlash($fileType){
		
		if( is_array($fileType) AND !empty($fileType['type']) )
			$fileType = $fileType['type'];
		
		if( !is_string($fileType) )
			return false;
		
		if( preg_match("/^application\/(x-shockwave-flash)$/i", $fileType) ){
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