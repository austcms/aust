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

            unset($this->status['uploadFile'][$file['file']]);
            
            /*
             * Verifica se o mime-type do arquivo é válido
             */
            if( eregi("^image\/(".$this->invalidExtensions.")$", $file["type"]) ){
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

            $frmarquivo_filename = $this->_adjustFileName($file['name'], true);
            $frmarquivo_tipo = $file['type'];
            $frmarquivo_tamanho = $file['size'];

            //ajusta o $_POST para salvar dados no DB
            /*
            $_POST['frmarquivo_nome'] = $frmarquivo_nome;
            $_POST['frmarquivo_tipo'] = $frmarquivo_tipo;
            $_POST['frmarquivo_tamanho'] = $frmarquivo_tamanho;
            $_POST['frmarquivo_extensao'] = PegaExtensao($_POST['frmarquivo_nome']);
             * 
             */


            //$frmarquivo_nome = urlencode($file['name']);
            //$imagem_nome = str_replace($trocarIsso, $porIsso, $frmfilename);
            //$imagem_nome = urlencode($imagem_nome);
//                $imagem_nome = stri ($imagem_nome);

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
            $frmurl = $urlBaseDir.$upload_dir .'/'.$frmarquivo_nome;
            print_r($frmUrl);
            $_POST['frmurl'] = $frmurl;

            /*
             * System path
             * Pega $systemurl
             */
            $current_dir = getcwd();
            chdir($this->relativeDir);

            $frmSystemUrl = $this->_getSystemUrl( $upload_dir .'/'.$frmarquivo_nome );
            $_POST['frmsystemurl'] = $frmSystemUrl;

            //pr($_POST);
            //exit(0);
            /*
             * Faz o upload da imagem
             */
            if( !is_file($file["tmp_name"]) )
                return false;

            return $this->_moveUploadedFile($file["tmp_name"], $frmSystemUrl);

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
        $porIsso = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','_','_','_');

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
    public function _moveUploadedFile($filepath, $systemUrl){
        if( move_uploaded_file($filepath, $systemUrl) ){
            return true;
        } else {
            $this->status['uploadFile'][$file['name']] = 'upload_error';
            return false;
        }
    } // end _getSystemUrl()


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