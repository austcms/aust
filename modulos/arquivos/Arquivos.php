<?php
/**
 * CLASSE DO MÓDULO
 *
 * Classe contendo funcionalidades deste módulo
 *
 * @package Modulos
 * @name Arquivos
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1.5, 30/05/2009
 */
class Arquivos extends Module
{
    /**
     *
     * @var <string> Tabela principal de dados
     */
    public $mainTable = 'arquivos';

    public $date = array(
        'standardFormat' => '%d/%m/%Y',
        'created_on' => 'adddate',
        'updated_on' => 'addate'
    );

    /**
     *
     * @var <string> Extensões proibidas
     */
    public $invalidExtensions = 'php|php3|html|htm|css|js';

    /**
     *
     * @var <string> Diretório que o upload acontecerá
     */
    public $uploadSubDir;

    /**
     *
     * @var <string> Diretório relativo para download
     */
    public $relativeDir;

    function __construct(){

        $this->uploadSubDir = 'uploads/'.date('Y').'/'.date('m').'/';

        $this->relativeDir = './';
        if( !empty($this->modOptions["upload_path"]["valor"]) )
            $this->relativeDir = $this->modOptions["upload_path"]["valor"];
                
        parent::__construct();
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
     * parseUrl()
     *
     * Converte urls com ../ para o formato correto.
     *
     * @param <string> $url
     * @return <string>
     */
    public function parseUrl($url){

        $url = str_replace("://", ":/()_+/", $url);
        $url = str_replace("//", "/", $url);
        $url = str_replace(":/()_+/", "://", $url);
        $workVar = explode("/", $url);

        foreach( $workVar as $nr=>$value ){
            if( $value == '..' ){
                unset( $workVar[$nr] );
                unset( $workVar[$nr-1] );
            }
        }
        $url = implode('/', $workVar);
        $url = str_replace("./", "", $url);

        return $url;
    }

    public function uploadFile($file){

        if( !is_array($file) )
            return false;


        if(  count($file) == 1 ){
            $file = reset($file);
            /*
             * Tamanho máximo do arquivo (em bytes)
             */
            $conf["tamanho"] = 500000000;

            if( !$file['name'] ){
                $this->status = 'empty_file';
                return false;
            }

            if( isset( $this->status['uploadFile'] ) ){
                unset($this->status['uploadFile'][$file['file']]);
            }
            /*
             * Verifica se o mime-type do arquivo é válido
             */
            if( preg_match("/^image\/(".$this->invalidExtensions.")$/i", $file["type"]) ){
                $this->status['uploadFile'][$file['name']] = 'invalid_extension';
                return false;
            }

            /*
             * Verifica tamanho do arquivo
             */
            if( !empty($con["tamanho"]) AND
                $file["size"] > $conf["tamanho"])
            {
                $this->status['uploadFile'][$file['name']] = 'filesize';
                return false;
            }

            // Verificação de dados OK, nenhum erro ocorrido, executa então o upload...
            // Pega extensão do arquivo

            $newFilename = $this->_adjustFileName($file['name'], true);
            $filename = $file['name'];

            //ajusta o $_POST para salvar dados no DB
            $this->forPost[$filename]['frmarquivo_nome'] = $newFilename;
            $this->forPost[$filename]['frmarquivo_tipo'] = $file['type'];
            $this->forPost[$filename]['frmarquivo_tamanho'] = $file['size'];
            if( !isset($_POST['frmarquivo_nome']) && $this->testMode )
                $_POST['frmarquivo_nome'] = "teste";

            $this->forPost[$filename]['frmarquivo_extensao'] = $this->getExtension($_POST['frmarquivo_nome']);

            /**
             * Caminho de onde a imagem ficará
             */
            $upload_dir = $this->uploadSubDir;

            if( !is_dir($this->relativeDir.$upload_dir) ){
                /*
                 * A permissão só vai funcionar para linux
                 */
                mkdir($this->relativeDir.$upload_dir, 0777, true);
                chmod($this->relativeDir.$upload_dir, 0777);
            }

            /*
             *
             * PATHS TO FILE
             *
             */
            /*
             * Url path
             */
            $urlBaseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']).$this->relativeDir;
            $frmurl = $urlBaseDir.$upload_dir .'/'.$newFilename;
            $this->forPost[$filename]['frmurl'] = $frmurl;
            $this->forPost[$filename]['frmoriginal_filename'] = $file['name'];

            /*
             * System path
             * Pega $systemurl
             */
            $current_dir = getcwd();
            chdir($this->relativeDir);

            $frmSystemUrl = $this->_getSystemUrl( $upload_dir .'/'.$newFilename );
            $this->forPost[$filename]['frmsystemurl'] = $frmSystemUrl;

            //pr($_POST);
            //exit(0);
            /*
             * Faz o upload da imagem
             */
            if( !is_file($file["tmp_name"]) )
                return false;

            if( $this->_moveUploadedFile($file["tmp_name"], $frmSystemUrl) )
                return true;
            else
                $this->status['uploadFile'][$file['name']] = 'upload_error';

            return false;

        }

    }

    /**
     * _adjustFilename()
     *
     * Mdofica o nome do arquivo. com $makeUnique = true, insere uma
     * string aleatória antes do nome.
     *
     * @param <string> $str
     * @param <bool> $makeUnique
     * @return <string>
     */
    public function _adjustFileName($str, $makeUnique = false){
        $trocarIsso = array(
            'à','á','â','ã','ä','å',
            'ç',
            'è','é','ê','ë',
            'ì','í','î','ï',
            'ñ',
            'ò','ó','ô','õ','ö',
            'ù','ü','ú','ÿ',
            'À','Á','Â','Ã','Ä','Å',
            'Ç',
            'È','É','Ê','Ë',
            'Ì','Í','Î','Ï',
            'Ñ',
            'Ò','Ó','Ô','Õ','Ö',
            'Ù','Ü','Ú','?',',',' ');
        $porIsso = array('a','a','a','a','a','a',
            'c',
            'e','e','e','e',
            'i','i','i','i',
            'n',
            'o','o','o','o','o',
            'u','u','u','y',
            'A','A','A','A','A','A',
            'C',
            'E','E','E','E',
            'I','I','I','I',
            'N',
            'O','O','O','O','O',
            'U','U','U','_','_','_');

        $str = str_replace($trocarIsso, $porIsso, $str);
        $str = strtolower($str);
        if( $makeUnique )
            return substr( sha1(strtolower( $str.date('Ymdhis') ) ), 0, 6 ).'_'.strtolower( $str );
        else
            return $str;
    } // end _adjustFilename()

    /**
     * _getSystemUrl()
     *
     * @param <string> $filepath
     * @return <string>
     */
    public function _getSystemUrl($filepath){

        $current_dir = getcwd();
        chdir($this->relativeDir);

        $str = getcwd().'/'.$filepath;
        chdir($current_dir);

        return str_replace('//', '/', $str);
    } // end _getSystemUrl()

    /**
     * _moveUploadedFile()
     *
     * @param <string> $filepath
     * @param <string> $systemUrl
     * @return <string>
     */
    public function _moveUploadedFile($filepath, $destination){
        if( move_uploaded_file($filepath, $destination) ){
            return true;
        } else {
            return false;
        }
    } // end _getSystemUrl()

    /**
     * getExtension()
     *
     * @param <string> $filename
     * @return <string>
     */
    public function getExtension($filename){
        $ext = explode('.', $filename);
        $ext = array_reverse($ext);

        if( count($ext) == 2 )
            return $ext[0];

        return false;
    }

    /*
     *
     * CRUD
     *
     */
    /**
     * save()
     *
     * Realiza upload de arquivos e salva tudo no DB
     *
     * @param <array> $post $_POST
     * @param <array> $files $_FILES
     * @return <bool>
     */
    public function save($post, $files){
        
        if( count($files) == 1 ){
            return $this->_saveEachFile($post, $files);
        } else if( empty($files) ) {
            return $this->_saveEachFile($post);
        }

        return false;
    }

    /**
     * _saveEachFile(){
     *
     * Salvará um arquivo apenas. Save() tem a responsabilidade de
     * separar os arquivos, se enviados todos de um vez só,
     * e chamar este métodos uma vez por arquivo
     *
     * @param <array> $post
     * @param <array> $file
     */
    public function _saveEachFile($post, $file = false){

        $method = $post['method'];

        /*
         * Tenta upload
         */
        if( $file ){
            $this->uploadFile($file);
            $filename = reset($file);
            $filename = $filename['name'];
            $post = array_merge( $post, $this->forPost[$filename] );
            $post['frmurl'] = $this->parseUrl($post['frmurl']);
        }

        return parent::save($post);

        return false;
    }
    /**
     * loadSql($params)
     *
     * @param <array> $param
     * @return <bool>
     */
    public function loadSql($param) {

        /*
         * Configura e ajusta as variáveis
         */
        $categorias = $param;
        /*
         * Se $categorias estiver vazio (nunca deverá acontecer)
         */
        if(!empty($categorias)) {
            $order = ' ORDER BY created_on DESC';
            $where = ' WHERE ';
            $c = 0;
            foreach($categorias as $key=>$valor) {
                if($c == 0)
                    $where = $where . 'categoria_id=\''.$key.'\'';
                else
                    $where = $where . ' OR categoria_id=\''.$key.'\'';
                $c++;
            }
        }
        // SQL para verificar na tabela CADASTRO_CONF quais campos existem
        $sql = "SELECT
                    *, DATE_FORMAT(created_on, '%d/%m/%Y %H:%i') as data, categoria_id AS cat,
                    (	SELECT
                        nome
                FROM
                    categorias AS c
                WHERE
                    id=cat
                    ) AS node
                FROM
                    arquivos AS conf
                ".$where.$order;
        return $sql;
    }

    /*
     * Função para retonar a tabela de dados de uma estrutura
    */
    public function LeTabelaDaEstrutura() {
        return $this->tabela_criar;
    }

    /*
     * Função para retonar a tabela de dados de uma estrutra da cadastro
    */
    public function LeTabelaDeDados($param) {
        if(is_int($param) or $param > 0) {
            $estrutura = "categorias.id='".$param."'";
        } elseif(is_string($param)) {
            $estrutura = "categorias.nome='".$param."'";
        }

        $sql = "SELECT
                    cadastros_conf.valor AS valor
                FROM
                    cadastros_conf, categorias
                WHERE
                    categorias.id=cadastros_conf.categorias_id AND
                {$estrutura} AND
                    cadastros_conf.tipo='estrutura' AND
                    cadastros_conf.chave='tabela'
                LIMIT 0,1";
        //echo $sql;
        $mysql = mysql_query($sql);
        $dados = mysql_fetch_array($mysql);
        return $dados['valor'];
    }

}

?>