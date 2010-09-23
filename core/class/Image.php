<?php
/**
 * IMAGE
 *
 * Responsável por tratamento de imagens, uploads e carregamento
 *
 * @package Classes
 * @name Image
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.9, 26/08/2009
 */
class Image
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
    public function upload($file = ""){
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
     * trataImagem
     *
     * Trata uma imagem
     *
     * @param array $files O mesmo $_FILE vindo de um formulário
     * @param string $width Valor padrão de largura
     * @param string $height Valor padrão de altura
     * @return array
     */
    function resample($files, $width = "1280", $height = "1024"){

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
        //imagecopyresized($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);
        imagecopyresampled($nova,$im,0,0,0,0,$largurad,$alturad,$largurao,$alturao);

        ob_start();
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
        $result["type"] = 'image/jpeg';
        $result["error"] = '0';

        return $result;

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